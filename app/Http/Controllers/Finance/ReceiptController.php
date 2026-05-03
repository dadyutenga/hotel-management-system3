<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\Checkout;
use App\Models\Order;
use App\Models\Role;
use App\Services\ReceiptService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ReceiptController extends Controller
{
    public function __construct(
        protected ReceiptService $receiptService
    ) {}

    /**
     * GET /finance/receipts/guest/{checkout}
     * Renders the guest folio receipt with booking and room details.
     */
    public function guest(Checkout $checkout): View
    {
        abort_unless(Auth::user()?->hasAnyRole([Role::FRONT_DESK, Role::MANAGER, Role::ADMIN, Role::ACCOUNTANT]), 403);

        $receipt = $this->receiptService->getOrCreateReceipt($checkout);

        $booking = $checkout->booking;
        $extraFields = [
            __('general.receipt.booking_number')  => $booking?->booking_number,
            __('general.receipt.room_number')     => $booking?->room?->room_number,
            __('general.receipt.check_in')        => $booking?->check_in_date?->format('d M Y'),
            __('general.receipt.check_out')       => $booking?->check_out_date?->format('d M Y'),
            __('general.receipt.nights')          => $booking?->nights,
            __('general.receipt.guest_name')      => $booking?->guest_name,
        ];

        return view('receipts.print', compact('receipt', 'extraFields'));
    }

    /**
     * GET /finance/receipts/walkin?order_id=...
     * Creates the receipt then redirects to the unified receipt show view.
     */
    public function walkin(Request $request): RedirectResponse
    {
        $orderId = $request->order_id;
        $order   = Order::findOrFail($orderId);

        $receipt = $this->receiptService->getOrCreateReceipt($order);

        return redirect()->route('receipts.show', $receipt->uuid);
    }
}
