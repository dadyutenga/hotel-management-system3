<?php

namespace App\Http\Controllers\Accounting;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\JournalEntry;
use App\Models\JournalLine;
use App\Models\Receipt;
use App\Models\Supplier;
use App\Models\SupplierPayable;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AccountingReportController extends Controller
{
    // GET /accounting/reports/profit-loss
    public function profitLoss(Request $request): View
    {
        $dateFrom = $request->date_from ?? now()->startOfMonth()->toDateString();
        $dateTo   = $request->date_to   ?? now()->toDateString();

        // Revenue accounts (4xxx)
        $revenueAccounts = Account::where('type', 'revenue')
            ->where('is_active', true)
            ->get()
            ->map(fn($acc) => [
                'account'  => $acc,
                'balance'  => $acc->getBalance($dateFrom, $dateTo),
            ]);

        // COGS accounts (5xxx)
        $cogsAccounts = Account::where('type', 'cogs')
            ->where('is_active', true)
            ->get()
            ->map(fn($acc) => [
                'account' => $acc,
                'balance' => $acc->getBalance($dateFrom, $dateTo),
            ]);

        // Expense accounts (6xxx)
        $expenseAccounts = Account::where('type', 'expense')
            ->where('is_active', true)
            ->get()
            ->map(fn($acc) => [
                'account' => $acc,
                'balance' => $acc->getBalance($dateFrom, $dateTo),
            ]);

        $totalRevenue  = $revenueAccounts->sum('balance');
        $totalCogs     = $cogsAccounts->sum('balance');
        $grossProfit   = $totalRevenue - $totalCogs;
        $totalExpenses = $expenseAccounts->sum('balance');
        $netProfit     = $grossProfit - $totalExpenses;

        return view('accounting.reports.profit-loss', compact(
            'revenueAccounts', 'cogsAccounts', 'expenseAccounts',
            'totalRevenue', 'totalCogs', 'grossProfit',
            'totalExpenses', 'netProfit',
            'dateFrom', 'dateTo'
        ));
    }

    // GET /accounting/reports/balance-sheet
    public function balanceSheet(Request $request): View
    {
        $asOf = $request->as_of ?? now()->toDateString();

        $assets      = Account::where('type', 'asset')->where('is_active', true)->get()
                          ->map(fn($a) => ['account' => $a, 'balance' => $a->getBalance(null, $asOf)]);
        $liabilities = Account::where('type', 'liability')->where('is_active', true)->get()
                          ->map(fn($a) => ['account' => $a, 'balance' => $a->getBalance(null, $asOf)]);
        $equity      = Account::where('type', 'equity')->where('is_active', true)->get()
                          ->map(fn($a) => ['account' => $a, 'balance' => $a->getBalance(null, $asOf)]);

        $totalAssets      = $assets->sum('balance');
        $totalLiabilities = $liabilities->sum('balance');
        $totalEquity      = $equity->sum('balance');

        return view('accounting.reports.balance-sheet', compact(
            'assets', 'liabilities', 'equity',
            'totalAssets', 'totalLiabilities', 'totalEquity', 'asOf'
        ));
    }

    // GET /accounting/reports/trial-balance
    public function trialBalance(Request $request): View
    {
        $asOf = $request->as_of ?? now()->toDateString();

        $accounts = Account::where('is_active', true)
            ->orderBy('code')
            ->get()
            ->map(function ($acc) use ($asOf) {
                $rawBalance = $acc->getBalance(null, $asOf);
                return [
                    'account' => $acc,
                    'debit'   => $acc->normal_balance === 'debit'  && $rawBalance > 0 ? $rawBalance : 0,
                    'credit'  => $acc->normal_balance === 'credit' && $rawBalance > 0 ? $rawBalance : 0,
                ];
            })
            ->filter(fn($row) => $row['debit'] + $row['credit'] > 0);

        $totalDebits  = $accounts->sum('debit');
        $totalCredits = $accounts->sum('credit');

        return view('accounting.reports.trial-balance', compact(
            'accounts', 'totalDebits', 'totalCredits', 'asOf'
        ));
    }

    // GET /accounting/reports/vat
    public function vatReport(Request $request): View
    {
        $dateFrom = $request->date_from ?? now()->startOfMonth()->toDateString();
        $dateTo   = $request->date_to   ?? now()->toDateString();

        // Output VAT collected (account 2200)
        $outputVatAccount = Account::findByCode('2200');
        $outputVat = $outputVatAccount->getBalance($dateFrom, $dateTo);

        // Input VAT paid on purchases (account 2300)
        $inputVatAccount = Account::findByCode('2300');
        $inputVat = $inputVatAccount->getBalance($dateFrom, $dateTo);

        $vatPayable = $outputVat - $inputVat;

        // Detailed VAT lines for TRA filing
        $vatLines = JournalLine::with(['entry', 'account'])
            ->whereHas('account', fn($q) => $q->whereIn('code', ['2200', '2300']))
            ->whereHas('entry', fn($q) => $q
                ->where('status', 'posted')
                ->whereDate('entry_date', '>=', $dateFrom)
                ->whereDate('entry_date', '<=', $dateTo)
            )
            ->get();

        return view('accounting.reports.vat', compact(
            'outputVat', 'inputVat', 'vatPayable', 'vatLines', 'dateFrom', 'dateTo'
        ));
    }

    // GET /accounting/ledger
    public function ledger(Request $request): View
    {
        $accounts  = Account::where('is_active', true)->orderBy('code')->get();
        $account   = null;
        $lines     = collect();

        if ($request->account_id) {
            $account  = Account::findOrFail($request->account_id);
            $dateFrom = $request->date_from ?? now()->startOfMonth()->toDateString();
            $dateTo   = $request->date_to   ?? now()->toDateString();

            $lines = JournalLine::with('entry')
                ->where('account_id', $account->id)
                ->whereHas('entry', fn($q) => $q
                    ->where('status', 'posted')
                    ->whereDate('entry_date', '>=', $dateFrom)
                    ->whereDate('entry_date', '<=', $dateTo)
                )
                ->orderBy('created_at')
                ->get();
        }

        return view('accounting.ledger.index', compact('accounts', 'account', 'lines'));
    }

    public function supplierPayables(Request $request): View
    {
        $dateFrom = $request->date_from ?? now()->startOfMonth()->toDateString();
        $dateTo   = $request->date_to ?? now()->toDateString();
        $apAccount = Account::findByCode('2100');

        $lines = JournalLine::with(['entry.supplier', 'account'])
            ->where('account_id', $apAccount->id)
            ->whereHas('entry', fn ($q) => $q
                ->where('status', 'posted')
                ->whereNotNull('supplier_id')
                ->whereDate('entry_date', '>=', $dateFrom)
                ->whereDate('entry_date', '<=', $dateTo)
            )
            ->get();

        $supplierRows = Supplier::whereIn('id', $lines->pluck('entry.supplier_id')->filter()->unique())
            ->get()
            ->map(function ($supplier) use ($lines) {
                $supplierLines = $lines->filter(fn ($line) => $line->entry?->supplier_id === $supplier->id);
                $credits = (float) $supplierLines->where('type', 'credit')->sum('amount');
                $debits = (float) $supplierLines->where('type', 'debit')->sum('amount');

                return [
                    'supplier' => $supplier,
                    'invoiced' => $credits,
                    'paid' => $debits,
                    'balance' => $credits - $debits,
                    'entries' => $supplierLines->pluck('entry')->filter()->unique('id')->sortByDesc('entry_date')->values(),
                ];
            })
            ->sortByDesc('balance')
            ->values();

        $recentProcurementEntries = JournalEntry::with(['supplier', 'lines.account'])
            ->where('source', 'procurement')
            ->whereNotNull('supplier_id')
            ->whereDate('entry_date', '>=', $dateFrom)
            ->whereDate('entry_date', '<=', $dateTo)
            ->latest('entry_date')
            ->limit(10)
            ->get();

        $totalOutstanding = (float) $supplierRows->sum('balance');
        $totalInvoiced = (float) $supplierRows->sum('invoiced');
        $totalPaid = (float) $supplierRows->sum('paid');

        return view('accounting.reports.supplier-payables', compact(
            'supplierRows',
            'recentProcurementEntries',
            'totalOutstanding',
            'totalInvoiced',
            'totalPaid',
            'dateFrom',
            'dateTo'
        ));
    }

    public function cashflowSummary(Request $request): View
    {
        $dateFrom = $request->date_from ?? now()->startOfMonth()->toDateString();
        $dateTo = $request->date_to ?? now()->toDateString();

        $cashAccounts = Account::whereIn('code', ['1100', '1200'])
            ->where('is_active', true)
            ->get();

        $cashLines = JournalLine::with(['entry', 'account'])
            ->whereIn('account_id', $cashAccounts->pluck('id'))
            ->whereHas('entry', fn ($query) => $query
                ->where('status', 'posted')
                ->whereDate('entry_date', '>=', $dateFrom)
                ->whereDate('entry_date', '<=', $dateTo)
            )
            ->get();

        $rows = $cashLines->map(function (JournalLine $line) {
            $isDebitIncrease = $line->account?->normal_balance === 'debit';
            $inflow = $line->type === ($isDebitIncrease ? 'debit' : 'credit') ? (float) $line->amount : 0.0;
            $outflow = $line->type === ($isDebitIncrease ? 'credit' : 'debit') ? (float) $line->amount : 0.0;

            return [
                'entry' => $line->entry,
                'account' => $line->account,
                'reference' => $line->entry?->reference,
                'description' => $line->entry?->description,
                'inflow' => $inflow,
                'outflow' => $outflow,
                'net' => $inflow - $outflow,
            ];
        })->sortBy('entry.entry_date')->values();

        $accountSummaries = $rows
            ->groupBy(fn (array $row) => $row['account']?->id)
            ->map(function ($groupedRows) {
                $first = $groupedRows->first();

                return [
                    'account' => $first['account'],
                    'inflow' => (float) collect($groupedRows)->sum('inflow'),
                    'outflow' => (float) collect($groupedRows)->sum('outflow'),
                    'net' => (float) collect($groupedRows)->sum('net'),
                ];
            })
            ->values();

        $totalInflow = (float) $rows->sum('inflow');
        $totalOutflow = (float) $rows->sum('outflow');
        $netCashMovement = $totalInflow - $totalOutflow;

        return view('accounting.reports.cashflow-summary', compact(
            'rows',
            'accountSummaries',
            'totalInflow',
            'totalOutflow',
            'netCashMovement',
            'dateFrom',
            'dateTo'
        ));
    }

    public function apAging(Request $request): View
    {
        $asOf = $request->as_of ?? now()->toDateString();
        $asOfDate = Carbon::parse($asOf);

        $supplierAging = SupplierPayable::with('supplier')
            ->where('balance', '>', 0)
            ->whereDate('payable_date', '<=', $asOf)
            ->get()
            ->groupBy('supplier_id')
            ->map(function ($payables) use ($asOfDate) {
                $buckets = [
                    'current' => 0.0,
                    'days_31_60' => 0.0,
                    'days_61_90' => 0.0,
                    'days_90_plus' => 0.0,
                ];

                foreach ($payables as $payable) {
                    $days = Carbon::parse($payable->payable_date)->diffInDays($asOfDate);
                    $balance = (float) $payable->balance;

                    if ($days <= 30) {
                        $buckets['current'] += $balance;
                    } elseif ($days <= 60) {
                        $buckets['days_31_60'] += $balance;
                    } elseif ($days <= 90) {
                        $buckets['days_61_90'] += $balance;
                    } else {
                        $buckets['days_90_plus'] += $balance;
                    }
                }

                $total = array_sum($buckets);

                return [
                    'supplier' => $payables->first()->supplier,
                    'current' => $buckets['current'],
                    'days_31_60' => $buckets['days_31_60'],
                    'days_61_90' => $buckets['days_61_90'],
                    'days_90_plus' => $buckets['days_90_plus'],
                    'total' => $total,
                ];
            })
            ->sortByDesc('total')
            ->values();

        $totals = [
            'current' => (float) $supplierAging->sum('current'),
            'days_31_60' => (float) $supplierAging->sum('days_31_60'),
            'days_61_90' => (float) $supplierAging->sum('days_61_90'),
            'days_90_plus' => (float) $supplierAging->sum('days_90_plus'),
            'total' => (float) $supplierAging->sum('total'),
        ];

        return view('accounting.reports.ap-aging', compact('supplierAging', 'totals', 'asOf'));
    }

    public function receiptsSummary(Request $request): View
    {
        $dateFrom = $request->date_from ?? now()->startOfMonth()->toDateString();
        $dateTo = $request->date_to ?? now()->toDateString();

        $receipts = Receipt::query()
            ->whereDate('issued_at', '>=', $dateFrom)
            ->whereDate('issued_at', '<=', $dateTo)
            ->orderByDesc('issued_at')
            ->get();

        $totals = [
            'count' => $receipts->count(),
            'gross_total' => (float) $receipts->sum('total'),
            'amount_paid' => (float) $receipts->sum('amount_paid'),
            'balance' => (float) $receipts->sum('balance'),
        ];

        $byModule = $receipts->groupBy('module')
            ->map(fn ($grouped) => [
                'module' => $grouped->first()->module,
                'count' => $grouped->count(),
                'total' => (float) $grouped->sum('total'),
                'amount_paid' => (float) $grouped->sum('amount_paid'),
            ])
            ->sortByDesc('total')
            ->values();

        $byPaymentMethod = $receipts->groupBy('payment_method')
            ->map(fn ($grouped, $method) => [
                'method' => $method ?: 'N/A',
                'count' => $grouped->count(),
                'total' => (float) $grouped->sum('total'),
            ])
            ->sortByDesc('total')
            ->values();

        return view('accounting.reports.receipts-summary', compact(
            'receipts',
            'totals',
            'byModule',
            'byPaymentMethod',
            'dateFrom',
            'dateTo'
        ));
    }
}
