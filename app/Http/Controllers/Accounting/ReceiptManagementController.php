<?php

namespace App\Http\Controllers\Accounting;

use App\Http\Controllers\Controller;
use App\Models\Checkout;
use App\Models\LaundryOrder;
use App\Models\Order;
use App\Models\Receipt;
use App\Models\WalkinTransaction;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Illuminate\View\View;

class ReceiptManagementController extends Controller
{
    public function index(Request $request): View
    {
        $filters = [
            'date_from' => $request->string('date_from')->toString(),
            'date_to' => $request->string('date_to')->toString(),
            'module' => $request->string('module')->toString(),
            'receipt_number' => $request->string('receipt_number')->toString(),
            'payment_method' => $request->string('payment_method')->toString(),
            'status' => $request->string('status')->toString(),
            'q' => $request->string('q')->toString(),
        ];

        $query = Receipt::query()
            ->with('cashier')
            ->orderByDesc('issued_at')
            ->orderByDesc('id');

        $this->applyFilters($query, $filters);

        $receipts = $query->paginate(25)->withQueryString();

        $todayQuery = Receipt::query()->whereDate('issued_at', today());
        $summary = [
            'total_receipts_today' => (clone $todayQuery)->count(),
            'total_amount_today' => (float) ((clone $todayQuery)->sum('total') ?? 0),
            'by_payment_method' => (clone $todayQuery)
                ->selectRaw("COALESCE(payment_method, 'unknown') as payment_method, COUNT(*) as receipt_count, SUM(total) as total_amount")
                ->groupBy('payment_method')
                ->orderByDesc('total_amount')
                ->get(),
        ];

        $modules = Receipt::query()
            ->select('module')
            ->whereNotNull('module')
            ->distinct()
            ->orderBy('module')
            ->pluck('module');

        $paymentMethods = Receipt::query()
            ->select('payment_method')
            ->whereNotNull('payment_method')
            ->where('payment_method', '!=', '')
            ->distinct()
            ->orderBy('payment_method')
            ->pluck('payment_method');

        $statuses = Receipt::query()
            ->select('payment_status')
            ->whereNotNull('payment_status')
            ->where('payment_status', '!=', '')
            ->distinct()
            ->orderBy('payment_status')
            ->pluck('payment_status');

        return view('accountant.receipts.index', compact(
            'receipts',
            'filters',
            'summary',
            'modules',
            'paymentMethods',
            'statuses'
        ));
    }

    public function show(Receipt $receipt): View
    {
        $receipt->load(['cashier', 'receiptable']);

        if (!$receipt->receiptable && $receipt->receiptable_type && $receipt->receiptable_id) {
            Log::warning('Missing receipt source linkage', [
                'receipt_id' => $receipt->id,
                'receipt_number' => $receipt->receipt_number,
                'receiptable_type' => $receipt->receiptable_type,
                'receiptable_id' => $receipt->receiptable_id,
            ]);
        }

        $sourceLink = $this->buildSourceLink($receipt);
        $financeLink = $this->buildFinanceLink($receipt);

        return view('accountant.receipts.show', compact('receipt', 'sourceLink', 'financeLink'));
    }

    private function applyFilters(Builder $query, array $filters): void
    {
        if (!empty($filters['date_from'])) {
            $query->whereDate('issued_at', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->whereDate('issued_at', '<=', $filters['date_to']);
        }

        if (!empty($filters['module'])) {
            $query->where('module', $filters['module']);
        }

        if (!empty($filters['receipt_number'])) {
            $query->where('receipt_number', 'like', '%' . $filters['receipt_number'] . '%');
        }

        if (!empty($filters['payment_method'])) {
            $query->where('payment_method', $filters['payment_method']);
        }

        if (!empty($filters['status'])) {
            $query->where('payment_status', $filters['status']);
        }

        if (!empty($filters['q'])) {
            $search = $filters['q'];
            $query->where(function (Builder $inner) use ($search) {
                $inner->where('customer_name', 'like', '%' . $search . '%')
                    ->orWhere('customer_phone', 'like', '%' . $search . '%')
                    ->orWhere('transaction_reference', 'like', '%' . $search . '%');
            });
        }
    }

    private function buildSourceLink(Receipt $receipt): ?array
    {
        $type = ltrim((string) $receipt->receiptable_type, '\\');
        $id = $receipt->receiptable_id;
        $source = $receipt->receiptable;

        if ($type === LaundryOrder::class && $id && Route::has('laundry.orders.show')) {
            return [
                'label' => __('accountant.receipts.source_laundry_order'),
                'reference' => $source?->order_number ?? $id,
                'url' => route('laundry.orders.show', $id),
            ];
        }

        if ($type === Order::class && $id && Route::has('accountant.source.restaurant-order')) {
            return [
                'label' => __('accountant.receipts.source_restaurant_order'),
                'reference' => $source?->order_number ?? $id,
                'url' => route('accountant.source.restaurant-order', $id),
            ];
        }

        if ($type === Checkout::class && $source?->booking_id && Route::has('finance.checkout.show')) {
            return [
                'label' => __('accountant.receipts.source_checkout'),
                'reference' => $source->receipt_number ?? $id,
                'url' => route('finance.checkout.show', $source->booking_id),
            ];
        }

        if ($type === WalkinTransaction::class && $id && Route::has('finance.payments.index')) {
            return [
                'label' => __('accountant.receipts.source_walkin'),
                'reference' => $source?->transaction_number ?? $id,
                'url' => route('finance.payments.index'),
            ];
        }

        if ($type === 'App\\Models\\SupplierPayment' && $id && Route::has('accountant.payments.apply')) {
            return [
                'label' => __('accountant.receipts.source_supplier_payment'),
                'reference' => $source?->reference ?? $id,
                'url' => route('accountant.payments.apply', $id),
            ];
        }

        return null;
    }

    private function buildFinanceLink(Receipt $receipt): ?array
    {
        $type = ltrim((string) $receipt->receiptable_type, '\\');
        $source = $receipt->receiptable;

        if ($receipt->transaction_reference && $type === WalkinTransaction::class && Route::has('accountant.source.walkin-payment.status')) {
            return [
                'label' => __('accountant.receipts.finance_transaction'),
                'reference' => $receipt->transaction_reference,
                'url' => route('accountant.source.walkin-payment.status', $receipt->transaction_reference),
            ];
        }

        if ($type === WalkinTransaction::class && $source?->transaction_number && Route::has('finance.payments.index')) {
            return [
                'label' => __('accountant.receipts.finance_transaction'),
                'reference' => $source->transaction_number,
                'url' => route('finance.payments.index'),
            ];
        }

        if ($receipt->transaction_reference) {
            return [
                'label' => __('accountant.receipts.finance_transaction'),
                'reference' => $receipt->transaction_reference,
                'url' => null,
            ];
        }

        return null;
    }
}

