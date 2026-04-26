<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\GoodsReceivedNote;
use App\Models\LocalPurchaseOrder;
use App\Models\StockLevel;
use App\Models\StockLocation;
use App\Models\StockMovement;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OversightController extends Controller
{
    public function lpoApprovals(Request $request): View
    {
        $suppliers = Supplier::active()->orderBy('name')->get();

        $lpos = LocalPurchaseOrder::with(['supplier', 'creator', 'approver', 'rejector', 'items'])
            ->when($request->status, fn ($q) => $q->where('status', $request->status))
            ->when($request->supplier_id, fn ($q) => $q->where('supplier_id', $request->supplier_id))
            ->when($request->date_from, fn ($q) => $q->whereDate('order_date', '>=', $request->date_from))
            ->when($request->date_to, fn ($q) => $q->whereDate('order_date', '<=', $request->date_to))
            ->latest('order_date')
            ->paginate(20)
            ->withQueryString();

        return view('manager.procurement.approvals', compact('lpos', 'suppliers'));
    }

    public function stockOverview(Request $request): View
    {
        $locations = StockLocation::where('is_active', true)->orderBy('name')->get();

        $levels = StockLevel::with(['product', 'location'])
            ->join('products', 'stock_levels.product_id', '=', 'products.id')
            ->where('products.is_active', true)
            ->when($request->location_id, fn ($q) => $q->where('stock_levels.location_id', $request->location_id))
            ->select('stock_levels.*')
            ->paginate(25)
            ->withQueryString();

        $categorySummary = StockLevel::query()
            ->join('products', 'stock_levels.product_id', '=', 'products.id')
            ->where('products.is_active', true)
            ->when($request->location_id, fn ($q) => $q->where('stock_levels.location_id', $request->location_id))
            ->selectRaw("COALESCE(products.category, 'Uncategorized') as category, COUNT(*) as items_count, SUM(stock_levels.quantity) as total_quantity")
            ->groupBy('products.category')
            ->orderBy('category')
            ->get();

        $lowStockCount = StockLevel::query()
            ->join('products', 'stock_levels.product_id', '=', 'products.id')
            ->where('products.is_active', true)
            ->when($request->location_id, fn ($q) => $q->where('stock_levels.location_id', $request->location_id))
            ->whereColumn('stock_levels.quantity', '<=', 'products.reorder_level')
            ->where('stock_levels.quantity', '>', 0)
            ->count();

        $outOfStockCount = StockLevel::query()
            ->join('products', 'stock_levels.product_id', '=', 'products.id')
            ->where('products.is_active', true)
            ->when($request->location_id, fn ($q) => $q->where('stock_levels.location_id', $request->location_id))
            ->where('stock_levels.quantity', '<=', 0)
            ->count();

        return view('manager.stock.overview', compact(
            'levels',
            'locations',
            'categorySummary',
            'lowStockCount',
            'outOfStockCount'
        ));
    }

    public function grnApprovals(Request $request): View
    {
        $suppliers = Supplier::active()->orderBy('name')->get();

        $grns = GoodsReceivedNote::with(['lpo', 'supplier', 'receiver', 'confirmer', 'approver', 'rejector'])
            ->when(
                $request->filled('status'),
                fn ($q) => $q->where('status', (string) $request->input('status')),
                fn ($q) => $q->whereIn('status', [
                    GoodsReceivedNote::STATUS_CONFIRMED_BY_STOREKEEPER,
                    GoodsReceivedNote::STATUS_PENDING_MANAGER_APPROVAL,
                ])
            )
            ->when($request->supplier_id, fn ($q) => $q->where('supplier_id', $request->supplier_id))
            ->when($request->date_from, fn ($q) => $q->whereDate('received_date', '>=', $request->date_from))
            ->when($request->date_to, fn ($q) => $q->whereDate('received_date', '<=', $request->date_to))
            ->latest('received_date')
            ->latest('created_at')
            ->paginate(20)
            ->withQueryString();

        return view('manager.procurement.grn-approvals', compact('grns', 'suppliers'));
    }

    public function stockMovements(Request $request): View
    {
        $locations = StockLocation::where('is_active', true)->orderBy('name')->get();

        $movements = StockMovement::with(['product', 'location', 'actor'])
            ->when($request->location_id, fn ($q) => $q->where('location_id', $request->location_id))
            ->when($request->type, fn ($q) => $q->where('type', $request->type))
            ->when($request->date_from, fn ($q) => $q->whereDate('created_at', '>=', $request->date_from))
            ->when($request->date_to, fn ($q) => $q->whereDate('created_at', '<=', $request->date_to))
            ->latest('created_at')
            ->paginate(30)
            ->withQueryString();

        return view('manager.stock.movements', compact('movements', 'locations'));
    }
}
