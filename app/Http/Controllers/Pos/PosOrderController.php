<?php

namespace App\Http\Controllers\Pos;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\MenuCategory;
use App\Models\MenuItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\StockLocation;
use App\Services\OrderDispatchService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PosOrderController extends Controller
{
    /**
     * Show a simplified POS order-entry screen for waiters logged in via passkey.
     */
    public function create(Request $request): View
    {
        $staffUser = $request->attributes->get('staffUser');

        $kitchen = StockLocation::kitchen();

        $categories = MenuCategory::with(['menuItems' => function ($q) {
            $q->where('is_active', true)
                ->where('is_available', true)
                ->where('is_buffet', false)
                ->orderBy('name');
        }])
            ->where('is_active', true)
            ->when($kitchen, fn ($q) => $q->where('location_id', $kitchen->id))
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get()
            ->filter(fn ($cat) => $cat->menuItems->isNotEmpty())
            ->values();

        $activeBookings = Booking::with('guest')
            ->where('status', 'checked_in')
            ->orderBy('guest_name')
            ->get(['id', 'booking_number', 'guest_name', 'room_id']);

        return view('pos.orders.create', compact('categories', 'activeBookings', 'staffUser', 'kitchen'));
    }

    /**
     * Store a new order created from the staff POS.
     */
    public function store(Request $request): RedirectResponse
    {
        $staffUser = $request->attributes->get('staffUser');
        $staffId = (string) $staffUser->id;

        $kitchen = StockLocation::kitchen();
        abort_if(! $kitchen, 500, 'Kitchen stock location not configured.');

        $data = $request->validate([
            'customer_name' => 'nullable|string|max:150',
            'customer_phone' => 'nullable|string|max:30',
            'order_type' => 'required|in:walkin,guest',
            'booking_id' => 'required_if:order_type,guest|nullable|uuid|exists:bookings,id',
            'notes' => 'nullable|string|max:500',
            'items' => 'required|array|min:1',
            'items.*.menu_item_id' => 'required|uuid|exists:menu_items,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        $isGuestOrder = $data['order_type'] === 'guest';

        if ($isGuestOrder) {
            $booking = Booking::findOrFail($data['booking_id']);
            abort_if($booking->status !== 'checked_in', 422, 'Can only charge to a checked-in booking.');
        }

        $order = DB::transaction(function () use ($data, $kitchen, $isGuestOrder, $staffId) {
            $customerName = $data['customer_name'] ?: ($isGuestOrder ? 'Guest' : 'Walk-in Guest');

            $order = Order::create([
                'location_id' => $kitchen->id,
                'order_type' => $isGuestOrder ? 'guest' : 'walkin',
                'order_source' => 'pos_waiter',
                'booking_id' => $isGuestOrder ? $data['booking_id'] : null,
                'customer_name' => $customerName,
                'customer_phone' => $data['customer_phone'] ?? null,
                'status' => 'open',
                'payment_method' => $isGuestOrder ? 'charge_to_booking' : null,
                'notes' => $data['notes'] ?? null,
                'created_by' => $staffId,
            ]);

            foreach ($data['items'] as $line) {
                $menuItem = MenuItem::findOrFail($line['menu_item_id']);

                OrderItem::create([
                    'order_id' => $order->id,
                    'menu_item_id' => $menuItem->id,
                    'item_name_snapshot' => $menuItem->name,
                    'quantity' => $line['quantity'],
                    'base_unit_price' => (float) $menuItem->selling_price,
                    'unit_price' => (float) $menuItem->selling_price,
                    'subtotal' => (float) $menuItem->selling_price * $line['quantity'],
                    'status' => 'pending',
                ]);
            }

            $order->recalculate();

            // Send to kitchen preparation immediately
            app(OrderDispatchService::class)->splitAndDispatch($order);
            $order->update(['status' => 'sent']);

            return $order;
        });

        return redirect()
            ->route('pos.orders.create')
            ->with('success', "Order {$order->order_number} placed successfully.");
    }
}
