<?php

namespace App\Http\Controllers\Reception;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\MenuCategory;
use App\Models\MenuItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\StockLevel;
use App\Models\StockLocation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DrinkRequestController extends Controller
{
    public function create(): View
    {
        $bar = StockLocation::bar();

        $checkedInGuests = Booking::with('room')
            ->where('status', 'checked_in')
            ->orderBy('guest_name')
            ->get();

        $barProducts = Product::where('product_type', 'bar')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $stockMap = [];
        $levels = StockLevel::where('location_id', $bar->id)
            ->with('product')
            ->get();

        foreach ($levels as $level) {
            if ($level->product) {
                $stockMap[$level->product->name] = (float) $level->available_qty;
            }
        }

        return view('reception.drinks.create', compact('checkedInGuests', 'barProducts', 'stockMap'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'booking_id' => 'required|uuid|exists:bookings,id',
            'items'      => 'required|array|min:1',
            'items.*.product_name' => 'required|string|max:150',
            'items.*.quantity'     => 'required|integer|min:1|max:99',
            'notes'      => 'nullable|string|max:500',
        ]);

        $bar = StockLocation::bar();
        $booking = Booking::with('room')->findOrFail($data['booking_id']);

        $defaultCategory = MenuCategory::query()
            ->where('location_id', $bar->id)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->first();

        DB::transaction(function () use ($data, $bar, $booking, $defaultCategory) {
            $order = Order::create([
                'location_id'    => $bar->id,
                'order_type'     => 'guest',
                'order_source'   => 'reception_drink',
                'booking_id'     => $booking->id,
                'customer_name'  => $booking->guest_name,
                'customer_phone' => $booking->guest_phone,
                'bartender_status' => 'pending',
                'bartender_status_updated_at' => now(),
                'status'         => 'open',
                'notes'          => $data['notes'] ?? null,
                'created_by'     => (string) Auth::id(),
            ]);

            $subtotal = 0;

            foreach ($data['items'] as $line) {
                $product = Product::where('name', $line['product_name'])
                    ->where('product_type', 'bar')
                    ->where('is_active', true)
                    ->first();

                if (!$product) {
                    continue;
                }

                $menuItem = $product->menuItem;
                if (!$menuItem && $defaultCategory) {
                    $menuItem = MenuItem::create([
                        'category_id'   => $defaultCategory->id,
                        'name'          => $product->name,
                        'description'   => $product->description,
                        'selling_price' => $product->selling_price,
                        'is_available'  => true,
                        'is_active'     => true,
                        'varieties'     => $product->varieties,
                        'created_by'    => (string) Auth::id(),
                    ]);
                }

                $unitPrice = (float) $product->selling_price;

                OrderItem::create([
                    'order_id'           => $order->id,
                    'menu_item_id'       => $menuItem?->id,
                    'item_name_snapshot' => $product->name,
                    'quantity'           => (int) $line['quantity'],
                    'unit_price'         => $unitPrice,
                    'subtotal'           => $unitPrice * (int) $line['quantity'],
                    'status'             => 'pending',
                ]);

                $subtotal += $unitPrice * (int) $line['quantity'];
            }

            $order->update([
                'subtotal' => $subtotal,
                'total'    => $subtotal,
            ]);
        });

        return redirect()
            ->route('reception.drinks.create')
            ->with('success', "Drink request for {$booking->guest_name} (Room {$booking->room->room_number}) sent to bar.");
    }
}
