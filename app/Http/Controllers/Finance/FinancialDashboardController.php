<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\BookingCharge;
use App\Models\FinancialTransaction;
use App\Models\FinancePayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class FinancialDashboardController extends Controller
{
    /**
     * GET /finance/dashboard
     */
    public function index(Request $request): View
    {
        $dateFrom = $request->date_from ?? today()->startOfMonth()->toDateString();
        $dateTo   = $request->date_to   ?? today()->toDateString();

        // Revenue by source module
        $revenueByModule = FinancialTransaction::where('type', '!=', 'refund')
            ->whereDate('created_at', '>=', $dateFrom)
            ->whereDate('created_at', '<=', $dateTo)
            ->select('source_module', DB::raw('SUM(amount_usd) as total_usd'))
            ->groupBy('source_module')
            ->get();

        // Revenue by payment method
        $revenueByMethod = FinancialTransaction::where('type', '!=', 'refund')
            ->whereDate('created_at', '>=', $dateFrom)
            ->whereDate('created_at', '<=', $dateTo)
            ->select('payment_method', DB::raw('SUM(amount_usd) as total_usd'))
            ->groupBy('payment_method')
            ->get();

        // Daily revenue trend (last 30 days)
        $dailyRevenue = FinancialTransaction::where('type', '!=', 'refund')
            ->whereDate('created_at', '>=', today()->subDays(30))
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(amount_usd) as total_usd')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Today's summary
        $todaySummary = [
            'total_revenue'    => FinancialTransaction::whereDate('created_at', today())
                ->where('type', '!=', 'refund')->sum('amount_usd'),
            'checkout_revenue' => FinancialTransaction::whereDate('created_at', today())
                ->where('type', 'checkout_payment')->sum('amount_usd'),
            'walkin_revenue'   => FinancialTransaction::whereDate('created_at', today())
                ->where('type', 'walkin_sale')->sum('amount_usd'),
            'cash_total'       => FinancialTransaction::whereDate('created_at', today())
                ->where('payment_method', 'cash')->sum('amount_usd'),
            'card_total'       => FinancialTransaction::whereDate('created_at', today())
                ->where('payment_method', 'card')->sum('amount_usd'),
        ];

        // Outstanding charges (unpaid booking charges)
        $outstandingTotal = BookingCharge::where('status', 'unpaid')->sum('amount');

        // Recent transactions
        $recentTransactions = FinancialTransaction::with(['payment', 'actor'])
            ->latest('created_at')
            ->take(15)
            ->get();

        return view('finance.dashboard.index', compact(
            'revenueByModule', 'revenueByMethod', 'dailyRevenue',
            'todaySummary', 'outstandingTotal', 'recentTransactions',
            'dateFrom', 'dateTo'
        ));
    }
}
