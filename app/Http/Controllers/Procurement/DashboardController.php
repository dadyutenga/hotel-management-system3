<?php
// app/Http/Controllers/Procurement/DashboardController.php

namespace App\Http\Controllers\Procurement;

use App\Http\Controllers\Controller;
use App\Models\GoodsReceivedNote;
use App\Models\LocalPurchaseOrder;
use App\Models\Supplier;
use App\Models\Role;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $summary = [
            'active_suppliers' => Supplier::where('is_active', true)->count(),
            'pending_lpos' => LocalPurchaseOrder::where('status', 'pending_approval')->count(),
            'active_lpos' => LocalPurchaseOrder::whereIn('status', ['approved', 'sent', 'partially_received'])->count(),
            'pending_grns' => GoodsReceivedNote::whereIn('status', [
                GoodsReceivedNote::STATUS_CONFIRMED_BY_STOREKEEPER,
                GoodsReceivedNote::STATUS_PENDING_MANAGER_APPROVAL,
            ])->count(),
        ];

        $isManager = auth()->user()?->hasRole(Role::MANAGER);

        $recentLpos = LocalPurchaseOrder::with(['supplier', 'creator'])
            ->whereIn('status', ['pending_approval', 'approved', 'sent'])
            ->latest()
            ->take(10)
            ->get();

        $recentGrns = GoodsReceivedNote::with(['lpo', 'receiver'])
            ->when(
                $isManager,
                fn ($query) => $query->whereIn('status', [
                    GoodsReceivedNote::STATUS_CONFIRMED_BY_STOREKEEPER,
                    GoodsReceivedNote::STATUS_PENDING_MANAGER_APPROVAL,
                ]),
                fn ($query) => $query->whereIn('status', [
                    GoodsReceivedNote::STATUS_DRAFT,
                    GoodsReceivedNote::STATUS_SUBMITTED,
                ])
            )
            ->latest()
            ->take(10)
            ->get();

        return view('procurement.dashboard.index', compact('summary', 'recentLpos', 'recentGrns'));
    }
}
