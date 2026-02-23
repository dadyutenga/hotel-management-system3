<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\Checkout;
use App\Models\FinancePayment;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ReceiptController extends Controller
{
    /**
     * GET /finance/receipts/guest/{checkout}
     * Printable guest checkout receipt.
     */
    public function guest(Checkout $checkout): View
    {
        $checkout->load(['booking', 'charges', 'initiator', 'completer', 'payments']);

        $chargesByType = $checkout->charges->groupBy('charge_type');

        $exchangeRate = (float) $checkout->exchange_rate;

        return view('finance.receipts.guest', compact('checkout', 'chargesByType', 'exchangeRate'));
    }

    /**
     * GET /finance/receipts/walkin
     * Printable walk-in sale receipt.
     */
    public function walkin(Request $request): View
    {
        $orderId = $request->order_id;
        $order   = Order::with(['items.menuItem', 'table', 'location'])->findOrFail($orderId);

        $payment = FinancePayment::where('order_id', $orderId)
            ->where('status', 'completed')
            ->latest('paid_at')
            ->first();

        $exchangeRate = (float) (DB::table('system_settings')
            ->where('key', 'tzs_exchange_rate')->value('value') ?? 2500);

        return view('finance.receipts.walkin', compact('order', 'payment', 'exchangeRate'));
    }
}
