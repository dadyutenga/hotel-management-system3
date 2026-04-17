<?php

namespace App\Http\Controllers\Restaurant;

use App\Http\Controllers\Controller;
use App\Models\BookingCharge;
use App\Models\MenuCategory;
use App\Models\MenuItem;
use App\Models\MenuOptionValue;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\StockLocation;
use App\Models\Table;
use App\Services\Billing\ModuleBillingService;
use App\Services\Bartender\BarOrderStockService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class OrderController extends Controller
{
    /**
     * GET /restaurant/orders
     */
    public function index(Request $request): View
    {
        $orders = Order::with(['table', 'location', 'items', 'creator'])
            ->when($request->status,      fn($q) => $q->where('status', $request->status))
            ->when($request->location_id, fn($q) => $q->where('location_id', $request->location_id))
            ->when($request->date,        fn($q) => $q->whereDate('created_at', $request->date))
            ->latest()
            ->paginate(30);

        $locations = StockLocation::whereIn('code', ['bar', 'kitchen'])->get();

        return view('restaurant.orders.index', compact('orders', 'locations'));
    }

    /**
     * GET /restaurant/orders/create
     */
    public function create(Request $request): View
    {
        $locations  = StockLocation::whereIn('code', ['bar', 'kitchen'])->get();
        $tables     = Table::where('is_active', true)
                          ->when($request->location_id, fn($q) => $q->where('location_id', $request->location_id))
                          ->get();
        $categories = MenuCategory::with(['menuItems' => fn($q) => $q->where('is_active', true)->with([
            'optionGroups' => fn($g) => $g->where('is_active', true)->with([
                'values' => fn($v) => $v->where('is_active', true),
            ]),
        ])])
                          ->where('is_active', true)
                          ->when($request->location_id, fn($q) => $q->where('location_id', $request->location_id))
                          ->orderBy('sort_order')
                          ->orderBy('name')
                          ->get();

        return view('restaurant.orders.create', compact('locations', 'tables', 'categories'));
    }

    /**
     * POST /restaurant/orders
     */
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'location_id'          => 'required|uuid|exists:stock_locations,id',
            'table_id'             => 'nullable|uuid|exists:tables,id',
            'order_type'           => 'required|in:guest,walkin',
            'booking_id'           => 'required_if:order_type,guest|nullable|uuid',
            'customer_name'        => 'required_if:order_type,walkin|nullable|string|max:150',
            'notes'                => 'nullable|string|max:500',
            'items'                => 'required|array|min:1',
            'items.*.menu_item_id' => 'required|uuid|exists:menu_items,id',
            'items.*.quantity'     => 'required|integer|min:1',
            'items.*.notes'        => 'nullable|string|max:255',
            'items.*.selected_option_value_ids' => 'nullable|array',
            'items.*.selected_option_value_ids.*' => 'uuid|exists:menu_option_values,id',
        ]);

        $order = DB::transaction(function () use ($data) {
            $location = StockLocation::findOrFail($data['location_id']);
            $isBarOrder = strtolower($location->code ?? '') === 'bar';

            $order = Order::create([
                'location_id'   => $data['location_id'],
                'table_id'      => $data['table_id'] ?? null,
                'order_type'    => $data['order_type'],
                'order_source'  => $isBarOrder ? 'restaurant' : null,
                'bartender_status' => $isBarOrder ? 'pending' : null,
                'bartender_status_updated_at' => $isBarOrder ? now() : null,
                'booking_id'    => $data['booking_id'] ?? null,
                'customer_name' => $data['customer_name'] ?? null,
                'status'        => 'open',
                'notes'         => $data['notes'] ?? null,
                'created_by'    => (string) Auth::id(),
            ]);

            foreach ($data['items'] as $item) {
                OrderItem::create($this->buildOrderItemPayload($order, $item));
            }

            // Mark table as occupied
            if ($order->table_id) {
                Table::where('id', $order->table_id)->update(['status' => 'occupied']);
            }

            $order->recalculate();

            return $order;
        });

        return redirect()
            ->route('restaurant.orders.show', $order)
            ->with('success', "Order {$order->order_number} created.");
    }

    /**
     * GET /restaurant/orders/{order}
     */
    public function show(Order $order): View
    {
        $order->load(['items.menuItem.ingredients.product', 'table', 'location', 'creator', 'settler']);
        $menuCategories = MenuCategory::with(['menuItems' => fn($q) => $q->where('is_active', true)->with([
            'optionGroups' => fn($g) => $g->where('is_active', true)->with([
                'values' => fn($v) => $v->where('is_active', true),
            ]),
        ])])
            ->where('is_active', true)
            ->where('location_id', $order->location_id)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return view('restaurant.orders.show', compact('order', 'menuCategories'));
    }

    /**
     * POST /restaurant/orders/{order}/send
     */
    public function send(Order $order): RedirectResponse
    {
        abort_if($order->status !== 'open', 422, 'Only open orders can be sent.');

        $order->update(['status' => 'sent']);

        return redirect()
            ->route('restaurant.orders.show', $order)
            ->with('success', 'Order sent to preparation.');
    }

    /**
     * POST /restaurant/orders/{order}/ready
     */
    public function ready(Order $order): RedirectResponse
    {
        abort_if($this->isBartenderManagedBarOrder($order), 422, 'Bar drink orders are prepared from the bartender desk.');
        abort_if($order->status !== 'sent', 422, 'Order must be sent before marking ready.');

        $order->update(['status' => 'ready']);

        return redirect()
            ->route('restaurant.orders.show', $order)
            ->with('success', 'Order marked as ready.');
    }

    /**
     * POST /restaurant/orders/{order}/serve
     */
    public function serve(Order $order): RedirectResponse
    {
        abort_if($this->isBartenderManagedBarOrder($order), 422, 'Bar drink orders are served from the bartender desk.');
        abort_if($order->status !== 'ready', 422, 'Order must be ready before serving.');

        DB::transaction(function () use ($order) {
            if ($order->booking_id) {
                app(ModuleBillingService::class)->syncOrderCharge($order, (string) Auth::id());
            }

            $order->update(['status' => 'served']);
        });

        return redirect()
            ->route('restaurant.orders.show', $order)
            ->with('success', 'Order marked as served.');
    }

    /**
     * POST /restaurant/orders/{order}/settle
     * This is where stock is deducted.
     * 
     * UNIFIED CHECKOUT FLOW:
     * - Guest orders: Create BookingCharge and redirect to Finance Checkout
     * - Walk-in orders: Use WalkinPaymentController (direct payment modal)
     */
    public function settle(Request $request, Order $order): RedirectResponse
    {
        // Prevent re-settlement of already charged or settled orders
        abort_if(in_array($order->status, ['charged', 'settled']), 422, 'Order already charged or settled.');
        abort_if($order->status === 'cancelled', 422, 'Cannot settle a cancelled order.');
        abort_if(!in_array($order->status, ['served', 'ready', 'sent', 'open']), 422, 'Order cannot be settled.');

        // For guest orders, we ONLY allow charge_to_booking (enforces checkout flow)
        // Walk-in direct payments are handled by WalkinPaymentController
        if ($order->order_type === 'guest') {
            if ($this->isBartenderManagedBarOrder($order)) {
                abort_if($order->bartender_status !== 'served', 422, 'Bartender must mark the drink order as served before billing it to the guest folio.');
            }

            $request->validate([
                'booking_id' => 'required|uuid|exists:bookings,id',
            ]);
            
            // Force charge_to_booking for guest orders
            $paymentMethod = 'charge_to_booking';
        } else {
            // Walk-in orders can settle directly (handled by WalkinPaymentController)
            // This path should NOT be used for walk-ins - they use the modal
            abort(422, 'Walk-in orders must be settled through the payment modal.');
        }

        $bookingId = $request->booking_id ?? $order->booking_id;
        abort_if(!$bookingId, 422, 'Booking ID is required for guest orders.');

        DB::transaction(function () use ($order, $bookingId) {
            // 1. Deduct stock only once per order
            app(BarOrderStockService::class)->deductForOrder($order, (string) Auth::id());

            // 2. Mark order as charged (NOT settled - will be settled at checkout)
            $order->update([
                'status'         => 'charged',
                'payment_method' => 'charge_to_booking',
                'booking_id'     => $bookingId,
                'billed_to_folio_at' => now(),
            ]);

            // 3. Create BookingCharge — payment will happen at Finance Checkout
            // Store amount in USD (converted from TZS) and also store TZS amount
            $exchangeRate = (float) (DB::table('system_settings')
                ->where('key', 'tzs_exchange_rate')->value('value') ?? 2500);
            $amountUsd = round($order->total / $exchangeRate, 2);

            app(ModuleBillingService::class)->syncOrderCharge($order->fresh(), (string) Auth::id());

            // 4. Free up the table
            if ($order->table_id) {
                Table::where('id', $order->table_id)->update(['status' => 'available']);
            }
        });

        // Award loyalty points for restaurant (50 points per 10,000 TZS)
        $freshOrder = $order->fresh();
        if ($freshOrder->booking_id) {
            $booking = \App\Models\Booking::with('guest')->find($freshOrder->booking_id);
            if ($booking && $booking->guest) {
                $pointsEarned = (int) floor(($freshOrder->total ?? 0) / 10000) * 50;
                if ($pointsEarned > 0) {
                    $booking->guest->addPoints($pointsEarned, 'restaurant', $freshOrder->id);
                }
            }
        }

        // Redirect to Finance Checkout page
        return redirect()
            ->route('finance.checkout.show', $bookingId)
            ->with('success', "Order {$order->order_number} charged to booking. Please complete payment at checkout.");
    }

    /**
     * POST /restaurant/orders/{order}/cancel
     */
    public function cancel(Order $order): RedirectResponse
    {
        abort_if($order->status === 'settled', 422, 'Cannot cancel a settled order.');

        DB::transaction(function () use ($order) {
            if ($order->stock_deducted_at && !$order->stock_reversed_at) {
                app(BarOrderStockService::class)->reverseForCancelledOrder($order, (string) Auth::id());
            }

            app(ModuleBillingService::class)->voidChargeForOrder($order);

            $order->update(['status' => 'cancelled']);

            // Free the table
            if ($order->table_id) {
                Table::where('id', $order->table_id)->update(['status' => 'available']);
            }
        });

        return redirect()
            ->route('restaurant.orders.index')
            ->with('success', "Order {$order->order_number} cancelled.");
    }

    /**
     * POST /restaurant/orders/{order}/items
     * Add item to an open order.
     */
    public function addItem(Request $request, Order $order): RedirectResponse
    {
        abort_if($order->status !== 'open', 422, 'Can only add items to open orders.');

        $request->validate([
            'menu_item_id' => 'required|uuid|exists:menu_items,id',
            'quantity'     => 'required|integer|min:1',
            'notes'        => 'nullable|string|max:255',
            'selected_option_value_ids' => 'nullable|array',
            'selected_option_value_ids.*' => 'uuid|exists:menu_option_values,id',
        ]);

        DB::transaction(function () use ($request, $order) {
            $payload = $this->buildOrderItemPayload($order, [
                'menu_item_id' => $request->menu_item_id,
                'quantity' => $request->quantity,
                'notes' => $request->notes,
                'selected_option_value_ids' => $request->selected_option_value_ids ?? [],
            ]);

            // If item already in order with same options, increase quantity
            $existing = $order->items()
                ->where('menu_item_id', $payload['menu_item_id'])
                ->where('options_signature', $payload['options_signature'])
                ->where('status', '!=', 'cancelled')
                ->first();

            if ($existing) {
                $newQty = $existing->quantity + $request->quantity;
                $existing->update([
                    'quantity' => $newQty,
                    'subtotal' => $newQty * $existing->unit_price,
                    'notes'    => $request->notes ?? $existing->notes,
                ]);
            } else {
                OrderItem::create($payload);
            }

            $order->recalculate();
        });

        return redirect()
            ->route('restaurant.orders.show', $order)
            ->with('success', __('general.restaurant.messages.order_item_added'));
    }

    /**
     * DELETE /restaurant/orders/{order}/items/{orderItem}
     * Remove item from open order.
     */
    public function removeItem(Order $order, OrderItem $orderItem): RedirectResponse
    {
        abort_if($order->status !== 'open', 422, 'Can only remove items from open orders.');

        DB::transaction(function () use ($order, $orderItem) {
            $orderItem->update(['status' => 'cancelled']);
            $order->recalculate();
        });

        return redirect()
            ->route('restaurant.orders.show', $order)
            ->with('success', 'Item removed from order.');
    }

    protected function isBartenderManagedBarOrder(Order $order): bool
    {
        return $order->order_source === 'restaurant'
            && str_contains(strtolower($order->location?->code ?? ''), 'bar');
    }

    protected function buildOrderItemPayload(Order $order, array $item): array
    {
        $menuItem = MenuItem::with([
            'category',
            'optionGroups' => fn($q) => $q->where('is_active', true)->with([
                'values' => fn($v) => $v->where('is_active', true),
            ]),
        ])->where('is_active', true)->findOrFail($item['menu_item_id']);

        abort_if(!$menuItem->is_available, 422, __('general.restaurant.messages.item_unavailable'));
        abort_if((string) $menuItem->category?->location_id !== (string) $order->location_id, 422, __('general.restaurant.messages.item_wrong_section'));

        $selectedIds = collect($item['selected_option_value_ids'] ?? [])->filter()->unique()->values();
        $selectedValues = MenuOptionValue::with('group')
            ->whereIn('id', $selectedIds)
            ->where('is_active', true)
            ->get();

        $selectedByGroup = $selectedValues->groupBy('menu_option_group_id');
        $allowedValueIds = $menuItem->optionGroups->flatMap(fn($group) => $group->values->pluck('id'))->map(fn($id) => (string) $id)->values();
        $invalidSelections = $selectedIds->diff($allowedValueIds);
        abort_if($invalidSelections->isNotEmpty(), 422, __('general.restaurant.messages.invalid_option_selection'));

        $selectedSnapshots = [];

        foreach ($menuItem->optionGroups as $group) {
            $groupSelections = $selectedByGroup->get($group->id, collect())->values();

            if ($group->is_required && $groupSelections->isEmpty()) {
                abort(422, __('general.restaurant.messages.required_option_missing', ['group' => $group->name]));
            }

            if ($group->selection_type === 'single' && $groupSelections->count() > 1) {
                abort(422, __('general.restaurant.messages.single_option_multiple_selected', ['group' => $group->name]));
            }

            if ($groupSelections->isNotEmpty()) {
                $selectedSnapshots[] = [
                    'group_id' => $group->id,
                    'group_name' => $group->name,
                    'selection_type' => $group->selection_type,
                    'required' => (bool) $group->is_required,
                    'values' => $groupSelections->map(fn($value) => [
                        'id' => $value->id,
                        'label' => $value->label,
                        'price_delta' => (float) $value->price_delta,
                    ])->values()->all(),
                ];
            }
        }

        $optionsUnitPrice = (float) $selectedValues->sum(fn($value) => (float) $value->price_delta);
        $basePrice = (float) $menuItem->selling_price;
        $unitPrice = $basePrice + $optionsUnitPrice;
        $quantity = (int) $item['quantity'];
        $signature = $selectedIds->isEmpty()
            ? 'none'
            : sha1($selectedIds->map(fn($id) => (string) $id)->sort()->implode(','));

        return [
            'order_id' => $order->id,
            'menu_item_id' => $menuItem->id,
            'item_name_snapshot' => $menuItem->name,
            'quantity' => $quantity,
            'base_unit_price' => $basePrice,
            'options_unit_price' => $optionsUnitPrice,
            'unit_price' => $unitPrice,
            'subtotal' => $unitPrice * $quantity,
            'selected_options_snapshot' => $selectedSnapshots,
            'options_signature' => $signature,
            'notes' => $item['notes'] ?? null,
            'status' => 'pending',
        ];
    }
}
