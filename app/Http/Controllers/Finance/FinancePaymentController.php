<?php

namespace App\Http\Controllers\Finance;

use App\Helpers\CurrencyHelper;
use App\Http\Controllers\Controller;
use App\Models\FinancialTransaction;
use App\Models\FinancePayment;
use App\Models\Order;
use App\Models\PaymentItem;
use App\Services\Bartender\BarOrderStockService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class FinancePaymentController extends Controller
{
    /**
     * GET /finance/payments
     * Walk-in payments log.
     */
    public function index(Request $request): View
    {
        $payments = FinancePayment::with(['order', 'createdBy', 'transaction'])
            ->when($request->type,      fn($q) => $q->where('payment_type', $request->type))
            ->when($request->method,    fn($q) => $q->where('method', $request->method))
            ->when($request->currency,  fn($q) => $q->where('currency', $request->currency))
            ->when($request->date_from, fn($q) => $q->whereDate('created_at', '>=', $request->date_from))
            ->when($request->date_to,   fn($q) => $q->whereDate('created_at', '<=', $request->date_to))
            ->latest('created_at')
            ->paginate(30);

        $summary = [
            'total_usd' => FinancePayment::where('status', 'completed')
                ->whereDate('created_at', today())->sum('amount_usd'),
            'cash_usd'  => FinancePayment::where('status', 'completed')->where('method', 'cash')
                ->whereDate('created_at', today())->sum('amount_usd'),
            'card_usd'  => FinancePayment::where('status', 'completed')->where('method', 'card')
                ->whereDate('created_at', today())->sum('amount_usd'),
        ];

        return view('finance.payments.index', compact('payments', 'summary'));
    }

    /**
     * POST /finance/payments/walkin
     * Record a walk-in cash/card/mobile payment for a bar or restaurant order.
     */
    public function storeWalkin(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'order_id'  => 'required|uuid|exists:orders,id',
            'currency'  => 'required|in:USD,TZS',
            'amount'    => 'required|numeric|min:0.01',
            'method'    => 'required|in:cash,card,mobile_money,bank_transfer',
            'reference' => 'nullable|string|max:100',
            'notes'     => 'nullable|string|max:500',
        ]);

        $exchangeRate = CurrencyHelper::getExchangeRate();

        $amountUsd = FinancePayment::toUsd(
            (float) $data['amount'],
            $data['currency'],
            $exchangeRate
        );

        DB::transaction(function () use ($data, $amountUsd, $exchangeRate) {

            $order = Order::with('items.menuItem')->findOrFail($data['order_id']);
            abort_if($order->booking_id, 422, 'Booking-linked orders must be settled through the guest folio checkout flow.');
            abort_if(in_array($order->status, ['cancelled', 'settled', 'charged'], true), 422, 'This order cannot be settled through walk-in payment.');

            $payment = FinancePayment::create([
                'payment_type'  => 'walkin',
                'order_id'      => $order->id,
                'currency'      => $data['currency'],
                'amount'        => $data['amount'],
                'amount_usd'    => $amountUsd,
                'exchange_rate' => $exchangeRate,
                'method'        => $data['method'],
                'status'        => 'completed',
                'reference'     => $data['reference'] ?? null,
                'notes'         => $data['notes'] ?? null,
                'created_by'    => (string) Auth::id(),
                'paid_at'       => now(),
            ]);

            // Create payment items from order items
            foreach ($order->items()->where('status', '!=', 'cancelled')->get() as $item) {
                PaymentItem::create([
                    'payment_id'    => $payment->id,
                    'order_item_id' => $item->id,
                    'description'   => $item->menuItem->name,
                    'quantity'      => $item->quantity,
                    'unit_price'    => $item->unit_price,
                    'subtotal'      => $item->subtotal,
                    'currency'      => 'USD',
                ]);
            }

            // Determine source module
            $sourceModule = 'restaurant';
            if ($order->location) {
                $code = strtolower($order->location->code ?? '');
                if (str_contains($code, 'bar')) {
                    $sourceModule = 'bar';
                }
            }

            // Write to financial ledger
            FinancialTransaction::record([
                'type'           => 'walkin_sale',
                'source_module'  => $sourceModule,
                'payment_id'     => $payment->id,
                'order_id'       => $order->id,
                'currency'       => $data['currency'],
                'amount'         => $data['amount'],
                'amount_usd'     => $amountUsd,
                'exchange_rate'  => $exchangeRate,
                'payment_method' => $data['method'],
                'description'    => "Walk-in sale — Order {$order->order_number}",
            ], (string) Auth::id());

            app(BarOrderStockService::class)->deductForOrder($order, (string) Auth::id());

            $order->update([
                'status' => 'settled',
                'bartender_status' => 'served',
                'bartender_status_updated_at' => now(),
                'payment_method' => $data['method'],
                'settled_by' => (string) Auth::id(),
                'settled_at' => now(),
            ]);
        });

        return redirect()
            ->route('finance.receipt.walkin', ['order_id' => $data['order_id']])
            ->with('success', 'Payment recorded. Receipt ready.');
    }
}
