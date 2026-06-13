<?php

namespace App\Http\Controllers\Pos;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Order;
use App\Services\AccountingService;
use App\Services\Bartender\BarOrderStockService;
use App\Services\Billing\ModuleBillingService;
use App\Services\ReceiptService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PosCashierController extends Controller
{
    /**
     * List all open/served orders created by waiters via the staff POS.
     */
    public function index(Request $request): View
    {
        $staffUser = $request->attributes->get('staffUser');

        $orders = Order::with(['items.menuItem', 'table', 'location', 'creator', 'booking'])
            ->where('order_source', 'pos_waiter')
            ->whereIn('status', ['open', 'sent', 'ready', 'served'])
            ->latest()
            ->paginate(30);

        $activeBookings = Booking::with('guest')
            ->where('status', 'checked_in')
            ->orderBy('guest_name')
            ->get(['id', 'booking_number', 'guest_name', 'room_id']);

        return view('pos.cashier.orders', compact('orders', 'activeBookings', 'staffUser'));
    }

    /**
     * Settle a walk-in order (cash/mobile/card) or charge it to a booking.
     */
    public function settle(Request $request, Order $order): RedirectResponse
    {
        $staffUser = $request->attributes->get('staffUser');
        $staffId = (string) $staffUser->id;

        abort_if($order->order_source !== 'pos_waiter', 422, 'This order was not created from the staff POS.');
        abort_if(in_array($order->status, ['settled', 'charged', 'cancelled']), 422, 'Order is already finalised.');
        abort_if(! in_array($order->status, ['open', 'sent', 'ready', 'served']), 422, 'Order cannot be settled.');

        $data = $request->validate([
            'payment_method' => 'required|in:cash,mobile,card,charge_to_booking',
            'booking_id' => 'required_if:payment_method,charge_to_booking|nullable|uuid|exists:bookings,id',
        ]);

        $isChargeToBooking = $data['payment_method'] === 'charge_to_booking';

        if ($isChargeToBooking) {
            $booking = Booking::findOrFail($data['booking_id']);
            abort_if($booking->status !== 'checked_in', 422, 'Can only charge to a checked-in booking.');
        }

        DB::transaction(function () use ($order, $data, $isChargeToBooking, $staffId) {
            // Ensure stock is deducted exactly once
            if (! $order->stock_deducted_at) {
                app(BarOrderStockService::class)->deductForOrder($order, $staffId);
            }

            if ($isChargeToBooking) {
                $order->update([
                    'status' => 'charged',
                    'payment_method' => 'charge_to_booking',
                    'booking_id' => $data['booking_id'],
                    'billed_to_folio_at' => now(),
                    'settled_by' => $staffId,
                ]);

                app(ModuleBillingService::class)->syncOrderCharge($order->fresh(), $staffId);
            } else {
                $paymentMethod = match ($data['payment_method']) {
                    'mobile' => 'mobile_money',
                    default => $data['payment_method'],
                };

                $isCash = $paymentMethod === 'cash';

                $order->update([
                    'status' => 'settled',
                    'payment_method' => $paymentMethod,
                    'settled_by' => $staffId,
                    'settled_at' => now(),
                ]);

                if ($isCash) {
                    app(AccountingService::class)->postRestaurantSettlement(
                        orderNo: $order->order_number,
                        orderId: $order->id,
                        amount: (float) $order->total,
                        paymentMethod: $paymentMethod,
                        actorId: $staffId
                    );
                }
            }

            app(ReceiptService::class)->getOrCreateReceipt($order->fresh());
        });

        $message = $isChargeToBooking
            ? "Order {$order->order_number} charged to guest folio."
            : "Order {$order->order_number} settled successfully.";

        return redirect()
            ->route('pos.cashier.index')
            ->with('success', $message);
    }

    /**
     * Cancel an order from the cashier POS.
     */
    public function cancel(Request $request, Order $order): RedirectResponse
    {
        $staffUser = $request->attributes->get('staffUser');
        $staffId = (string) $staffUser->id;

        abort_if($order->order_source !== 'pos_waiter', 422, 'This order was not created from the staff POS.');
        abort_if(in_array($order->status, ['settled', 'charged']), 422, 'Paid orders cannot be cancelled.');

        DB::transaction(function () use ($order, $staffId) {
            if ($order->stock_deducted_at && ! $order->stock_reversed_at) {
                app(BarOrderStockService::class)->reverseForCancelledOrder($order, $staffId);
            }

            app(ModuleBillingService::class)->voidChargeForOrder($order);

            $order->update(['status' => 'cancelled']);
        });

        return redirect()
            ->route('pos.cashier.index')
            ->with('success', "Order {$order->order_number} cancelled.");
    }
}
