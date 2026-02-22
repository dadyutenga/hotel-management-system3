<?php

namespace App\Http\Controllers\Restaurant;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ReportController extends Controller
{
    /**
     * GET /restaurant/reports/daily-sales
     */
    public function dailySales(Request $request): View
    {
        $date = $request->date ?? today()->toDateString();

        $orders = Order::with(['items.menuItem', 'location', 'table'])
            ->where('status', 'settled')
            ->whereDate('settled_at', $date)
            ->get();

        $summary = [
            'total_orders'   => $orders->count(),
            'total_revenue'  => $orders->sum('total'),
            'cash_revenue'   => $orders->where('payment_method', 'cash')->sum('total'),
            'card_revenue'   => $orders->where('payment_method', 'card')->sum('total'),
            'guest_charges'  => $orders->where('payment_method', 'charge_to_booking')->sum('total'),
        ];

        return view('restaurant.reports.daily-sales', compact('orders', 'summary', 'date'));
    }

    /**
     * GET /restaurant/reports/popular-items
     */
    public function popularItems(Request $request): View
    {
        $dateFrom = $request->date_from ?? today()->startOfMonth()->toDateString();
        $dateTo   = $request->date_to   ?? today()->toDateString();

        $items = OrderItem::with('menuItem.category')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('orders.status', 'settled')
            ->where('order_items.status', '!=', 'cancelled')
            ->whereDate('orders.settled_at', '>=', $dateFrom)
            ->whereDate('orders.settled_at', '<=', $dateTo)
            ->select([
                'order_items.menu_item_id',
                DB::raw('SUM(order_items.quantity) as total_qty'),
                DB::raw('COUNT(DISTINCT order_items.order_id) as order_count'),
                DB::raw('SUM(order_items.subtotal) as total_revenue'),
            ])
            ->groupBy('order_items.menu_item_id')
            ->orderByDesc('total_qty')
            ->take(20)
            ->get();

        return view('restaurant.reports.popular-items', compact('items', 'dateFrom', 'dateTo'));
    }
}
