<?php

// app/Http/Controllers/DashboardController.php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Building;
use App\Models\GoodsReceivedNote;
use App\Models\InternalUsageRequest;
use App\Models\LaundryOrder;
use App\Models\LocalPurchaseOrder;
use App\Models\Order;
use App\Models\Product;
use App\Models\Reservation;
use App\Models\Room;
use App\Models\StockLevel;
use App\Models\StockLocation;
use App\Models\StockMovement;
use App\Models\StockTransfer;
use App\Models\StoreNotification;
use App\Models\Supplier;
use App\Models\Table;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

/**
 * DashboardController — role-specific dashboards.
 *
 * PERFORMANCE NOTE: All dashboards aggressively cache stats (30s TTL)
 * and consolidate multiple COUNT queries into single batch queries.
 */
class DashboardController extends Controller
{
    private const DASHBOARD_CACHE_TTL = 30; // seconds

    public function index()
    {
        $user = auth()->user();

        if ($user->isAdmin()) {
            return $this->adminDashboard();
        } elseif ($user->isAccountant()) {
            return redirect()->route('accountant.dashboard');
        } elseif ($user->isGeneralManager()) {
            return $this->managerDashboard();
        } elseif ($user->isStoreManager()) {
            return $this->storeManagerDashboard();
        } elseif ($user->isSupervisor()) {
            return $this->supervisorDashboard();
        } elseif ($user->isHouseHelp()) {
            return $this->houseHelpDashboard();
        } elseif ($user->isStoreKeeper()) {
            return $this->storeKeeperDashboard();
        } elseif ($user->isRestaurantManager()) {
            return $this->restaurantManagerDashboard();
        } elseif ($user->isBarTender()) {
            return $this->barTenderDashboard();
        } elseif ($user->isWaiter()) {
            return $this->waiterDashboard();
        } else {
            return $this->frontDeskDashboard();
        }
    }

    /**
     * Get dashboard stats keyed by role, cached for 30s.
     */
    private function cachedStats(string $role, callable $fn): array
    {
        $user = auth()->user();
        $key = "dash_stats_{$role}_{$user->id}";

        return Cache::remember($key, self::DASHBOARD_CACHE_TTL, $fn);
    }

    /**
     * Batch room counts: status distribution + active totals in one query.
     */
    private function batchRoomStats(): array
    {
        $rows = Room::selectRaw("
                COUNT(*) FILTER (WHERE is_active = true) AS total_active_rooms,
                COUNT(*) FILTER (WHERE status = 'occupied') AS occupied_rooms,
                COUNT(*) FILTER (WHERE status = 'available' AND is_active = true) AS available_rooms,
                COUNT(*) FILTER (WHERE status = 'reserved') AS reserved_rooms,
                COUNT(*) FILTER (WHERE status = 'dirty') AS dirty_rooms,
                COUNT(*) FILTER (WHERE status = 'out_of_order') AS out_of_order_rooms"
            )->first();

        return [
            'total_rooms' => Room::count(),
            'active_rooms' => (int) ($rows->total_active_rooms ?? 0),
            'occupied_rooms' => (int) ($rows->occupied_rooms ?? 0),
            'available_rooms' => (int) ($rows->available_rooms ?? 0),
            'reserved_rooms' => (int) ($rows->reserved_rooms ?? 0),
            'dirty_rooms' => (int) ($rows->dirty_rooms ?? 0),
            'out_of_order_rooms' => (int) ($rows->out_of_order_rooms ?? 0),
        ];
    }

    /**
     * Batch reservation/booking counts in one query each.
     */
    private function batchReservationBookingStats(): array
    {
        $resRows = Reservation::selectRaw("
                COUNT(*) AS total_reservations,
                COUNT(*) FILTER (WHERE status = 'pending') AS pending_reservations,
                COUNT(*) FILTER (WHERE status IN ('confirmed', 'pending') AND check_in_date = CURRENT_DATE) AS today_checkins"
            )->first();

        $bookRows = Booking::selectRaw("
                COUNT(*) FILTER (WHERE status = 'checked_in') AS active_bookings,
                COUNT(*) FILTER (WHERE status = 'checked_in' AND check_out_date = CURRENT_DATE) AS today_checkouts"
            )->first();

        return [
            'total_reservations' => (int) ($resRows->total_reservations ?? 0),
            'pending_reservations' => (int) ($resRows->pending_reservations ?? 0),
            'today_checkins' => (int) ($resRows->today_checkins ?? 0),
            'active_bookings' => (int) ($bookRows->active_bookings ?? 0),
            'today_checkouts' => (int) ($bookRows->today_checkouts ?? 0),
        ];
    }

    /**
     * Revenue stats in one query using FILTER.
     */
    private function batchRevenueStats(): array
    {
        $rows = Booking::selectRaw("
                COALESCE(SUM(total_amount) FILTER (WHERE created_at >= CURRENT_DATE AND status IN ('checked_in', 'checked_out')), 0) AS today_revenue,
                COALESCE(SUM(total_amount) FILTER (WHERE check_in_date >= date_trunc('week', CURRENT_DATE) AND status IN ('checked_in', 'checked_out')), 0) AS week_revenue,
                COALESCE(SUM(total_amount) FILTER (WHERE EXTRACT(MONTH FROM check_in_date) = EXTRACT(MONTH FROM CURRENT_DATE) AND EXTRACT(YEAR FROM check_in_date) = EXTRACT(YEAR FROM CURRENT_DATE) AND status IN ('checked_in', 'checked_out')), 0) AS month_revenue,
                COALESCE(SUM(total_amount) FILTER (WHERE status IN ('checked_in', 'checked_out')), 0) AS total_revenue"
            )->first();

        return [
            'today_revenue' => (float) ($rows->today_revenue ?? 0),
            'week_revenue' => (float) ($rows->week_revenue ?? 0),
            'month_revenue' => (float) ($rows->month_revenue ?? 0),
            'total_revenue' => (float) ($rows->total_revenue ?? 0),
        ];
    }

    /**
     * Room status distribution in one query.
     */
    private function batchRoomStatusCounts(): array
    {
        return Room::where('is_active', true)
            ->selectRaw("status, COUNT(*) as count")
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();
    }

    /**
     * Reservation status distribution in one query.
     */
    private function batchReservationStatusCounts(): array
    {
        return Reservation::selectRaw("status, COUNT(*) as count")
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();
    }

    private function adminDashboard()
    {
        $stats = $this->cachedStats('admin', function () {
            return array_merge(
                [
                    'total_buildings' => Building::count(),
                    'total_users' => User::where('is_active', true)->count(),
                    'pending_lpo_approvals' => LocalPurchaseOrder::where('status', 'pending_approval')->count(),
                    'supplier_count' => Supplier::where('is_active', true)->count(),
                    'low_stock_items' => StockLevel::join('products', 'stock_levels.product_id', '=', 'products.id')
                        ->where('products.is_active', true)
                        ->whereColumn('stock_levels.quantity', '<=', 'products.reorder_level')
                        ->where('stock_levels.quantity', '>', 0)
                        ->count(),
                    'out_of_stock_items' => StockLevel::join('products', 'stock_levels.product_id', '=', 'products.id')
                        ->where('products.is_active', true)
                        ->where('stock_levels.quantity', '<=', 0)
                        ->count(),
                ],
                $this->batchRoomStats(),
                $this->batchReservationBookingStats(),
                $this->batchRevenueStats()
            );
        });

        $roomStatusCounts = $this->batchRoomStatusCounts();
        $reservationStatusCounts = $this->batchReservationStatusCounts();

        $recentReservations = Reservation::with(['room', 'creator'])
            ->latest()
            ->limit(5)
            ->get();

        $recentUsers = User::with('role')
            ->latest()
            ->limit(5)
            ->get();

        $buildingStats = Building::withCount(['floors', 'rooms'])->get();

        return view('dashboards.admin', compact(
            'stats',
            'roomStatusCounts',
            'reservationStatusCounts',
            'recentReservations',
            'recentUsers',
            'buildingStats'
        ));
    }

    private function managerDashboard()
    {
        $stats = $this->cachedStats('manager', function () {
            $room = $this->batchRoomStats();
            $resBook = $this->batchReservationBookingStats();
            $room['occupancy_rate'] = $room['total_rooms'] > 0
                ? round(($room['occupied_rooms'] / $room['total_rooms']) * 100, 1)
                : 0;

            return array_merge(
                [
                    'total_buildings' => Building::count(),
                    'total_users' => User::where('is_active', true)->count(),
                    'pending_approvals' => InternalUsageRequest::where('status', 'pending')->count(),
                ],
                $room,
                $resBook,
                $this->batchRevenueStats()
            );
        });

        $roomStatusCounts = $this->batchRoomStatusCounts();
        $reservationStatusCounts = $this->batchReservationStatusCounts();

        $recentReservations = Reservation::with(['room', 'creator'])
            ->latest()
            ->limit(10)
            ->get();

        $buildingStats = Building::withCount(['floors', 'rooms'])->get();

        $staffByRole = User::with('role')
            ->where('is_active', true)
            ->get()
            ->groupBy(fn ($user) => ucwords(str_replace('_', ' ', $user->role->name ?? 'Unknown')))
            ->map->count();

        $pendingApprovals = InternalUsageRequest::with(['requester', 'location'])
            ->where('status', 'pending')
            ->latest()
            ->limit(10)
            ->get();

        $pendingLpoApprovals = LocalPurchaseOrder::with(['supplier', 'creator'])
            ->where('status', 'pending_approval')
            ->latest('order_date')
            ->limit(6)
            ->get();

        $stockAlerts = StockLevel::with(['product', 'location'])
            ->join('products', 'stock_levels.product_id', '=', 'products.id')
            ->where('products.is_active', true)
            ->where(function ($query) {
                $query->where('stock_levels.quantity', '<=', 0)
                    ->orWhereColumn('stock_levels.quantity', '<=', 'products.reorder_level');
            })
            ->select('stock_levels.*')
            ->orderBy('stock_levels.quantity')
            ->limit(8)
            ->get();

        $recentStockMovements = StockMovement::with(['product', 'location', 'actor'])
            ->latest('created_at')
            ->limit(8)
            ->get();

        return view('dashboards.manager', compact(
            'stats',
            'roomStatusCounts',
            'reservationStatusCounts',
            'recentReservations',
            'buildingStats',
            'staffByRole',
            'pendingApprovals',
            'pendingLpoApprovals',
            'stockAlerts',
            'recentStockMovements'
        ));
    }

    private function supervisorDashboard()
    {
        $stats = $this->cachedStats('supervisor', function () {
            $room = $this->batchRoomStats();
            $room['occupancy_rate'] = $room['total_rooms'] > 0
                ? round(($room['occupied_rooms'] / $room['total_rooms']) * 100, 1)
                : 0;

            $laundryRows = LaundryOrder::selectRaw("
                    COUNT(*) FILTER (WHERE status = 'received') AS pending_laundry,
                    COUNT(*) FILTER (WHERE status = 'processing') AS inprogress_laundry,
                    COUNT(*) FILTER (WHERE status = 'ready') AS completed_laundry,
                    COUNT(*) FILTER (WHERE status = 'delivered') AS delivered_laundry,
                    COUNT(*) FILTER (WHERE created_at >= CURRENT_DATE) AS today_laundry"
                )->first();

            return array_merge(
                $room,
                $this->batchReservationBookingStats(),
                $this->batchRevenueStats(),
                [
                    'pending_laundry' => (int) ($laundryRows->pending_laundry ?? 0),
                    'inprogress_laundry' => (int) ($laundryRows->inprogress_laundry ?? 0),
                    'completed_laundry' => (int) ($laundryRows->completed_laundry ?? 0),
                    'delivered_laundry' => (int) ($laundryRows->delivered_laundry ?? 0),
                    'today_laundry' => (int) ($laundryRows->today_laundry ?? 0),
                ]
            );
        });

        $roomStatusCounts = $this->batchRoomStatusCounts();

        $todayActivity = Reservation::with('room')
            ->whereDate('check_in_date', today())
            ->whereIn('status', ['confirmed', 'pending'])
            ->orderBy('check_in_date')
            ->get();

        $todayDepartures = Booking::with('room')
            ->whereDate('check_out_date', today())
            ->where('status', 'checked_in')
            ->orderBy('check_out_date')
            ->get();

        $upcomingArrivals = Reservation::with('room')
            ->whereBetween('check_in_date', [today(), today()->addDays(7)])
            ->whereIn('status', ['pending', 'confirmed'])
            ->orderBy('check_in_date')
            ->limit(10)
            ->get();

        $roomsNeedingAttention = Room::with(['floor.building', 'roomType'])
            ->whereIn('status', ['dirty', 'out_of_order'])
            ->get();

        $recentLaundryOrders = LaundryOrder::with(['guest', 'booking.room', 'creator'])
            ->latest()
            ->limit(5)
            ->get();

        return view('dashboards.supervisor', compact(
            'stats',
            'roomStatusCounts',
            'todayActivity',
            'todayDepartures',
            'upcomingArrivals',
            'roomsNeedingAttention',
            'recentLaundryOrders'
        ));
    }

    private function houseHelpDashboard()
    {
        $stats = [
            'pending_orders' => LaundryOrder::where('status', 'received')->count(),
            'inprogress_orders' => LaundryOrder::where('status', 'processing')->count(),
            'completed_orders' => LaundryOrder::where('status', 'ready')->count(),
            'delivered_orders' => LaundryOrder::where('status', 'delivered')->count(),
            'today_orders' => LaundryOrder::whereDate('created_at', today())->count(),
            'total_orders' => LaundryOrder::count(),
        ];

        $stats['dirty_rooms'] = Room::where('status', 'dirty')->count();
        $stats['out_of_order_rooms'] = Room::where('status', 'out_of_order')->count();
        $stats['my_assigned_rooms'] = Room::where('status', 'dirty')
            ->where('cleaning_assigned_to', (string) auth()->id())->count();
        $stats['my_pending_rooms'] = Room::where('status', 'dirty')
            ->where('cleaning_assigned_to', (string) auth()->id())
            ->whereNull('cleaning_completed_at')->count();

        $recentOrders = LaundryOrder::with(['guest', 'booking.room', 'creator'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        $ordersByStatus = LaundryOrder::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        $myAssignedRooms = Room::with(['floor.building', 'roomType'])
            ->where('status', 'dirty')
            ->where('cleaning_assigned_to', (string) auth()->id())
            ->orderBy('cleaning_assigned_at', 'desc')
            ->get();

        return view('dashboards.house-help', compact(
            'stats', 'recentOrders', 'ordersByStatus', 'myAssignedRooms'
        ));
    }

    private function frontDeskDashboard()
    {
        $stats = $this->cachedStats('front_desk', function () {
            $room = $this->batchRoomStats();

            return array_merge(
                $room,
                $this->batchReservationBookingStats()
            );
        });

        // Today's check-ins (Bookings)
        $todayActivity = Booking::with('room')
            ->whereDate('check_in_date', today())
            ->orderBy('check_in_date')
            ->get();

        // Today's check-outs (Bookings)
        $todayDepartures = Booking::with('room')
            ->whereDate('check_out_date', today())
            ->orderBy('check_out_date')
            ->get();

        // Expected arrivals from reservations (today + next 3 days)
        $upcomingArrivals = Reservation::with('room')
            ->whereBetween('check_in_date', [today(), today()->addDays(3)])
            ->whereIn('status', ['pending', 'confirmed', 'converted'])
            ->orderBy('check_in_date')
            ->get();

        // Recent reservations created by this user
        $myRecentReservations = Reservation::with('room')
            ->where('created_by', auth()->id())
            ->latest()
            ->limit(5)
            ->get();

        $availableRoomsByType = Room::with('roomType')
            ->where('status', 'available')
            ->where('is_active', true)
            ->select('room_type_id', DB::raw('count(*) as count'))
            ->groupBy('room_type_id')
            ->get();

        return view('dashboards.front-desk', compact(
            'stats',
            'todayActivity',
            'todayDepartures',
            'upcomingArrivals',
            'myRecentReservations',
            'availableRoomsByType'
        ));
    }

    private function storeManagerDashboard()
    {
        // Procurement-focused statistics
        $stats = [
            // Supplier stats
            'total_suppliers' => Supplier::count(),
            'active_suppliers' => Supplier::where('is_active', true)->count(),

            // Purchase Order stats
            'total_lpos' => LocalPurchaseOrder::count(),
            'pending_lpos' => LocalPurchaseOrder::where('status', 'pending')->count(),
            'approved_lpos' => LocalPurchaseOrder::where('status', 'approved')->count(),
            'sent_lpos' => LocalPurchaseOrder::where('status', 'sent')->count(),

            // GRN stats
            'total_grns' => GoodsReceivedNote::count(),
            'pending_grns' => GoodsReceivedNote::whereIn('status', [
                GoodsReceivedNote::STATUS_SUBMITTED,
                GoodsReceivedNote::STATUS_CONFIRMED_BY_STOREKEEPER,
                GoodsReceivedNote::STATUS_PENDING_MANAGER_APPROVAL,
            ])->count(),
            'confirmed_grns' => GoodsReceivedNote::where('status', GoodsReceivedNote::STATUS_APPROVED)->count(),

            // Product & Stock stats
            'total_products' => Product::count(),
            'low_stock_items' => StockLevel::whereColumn('quantity', '<=', 'reserved_qty')->count(),

            // Today's activity
            'today_lpos' => LocalPurchaseOrder::whereDate('created_at', today())->count(),
            'today_grns' => GoodsReceivedNote::whereDate('created_at', today())->count(),

            // Financial summary (procurement spending)
            'month_spending' => GoodsReceivedNote::whereMonth('received_date', now()->month)
                ->whereYear('received_date', now()->year)
                ->where('status', GoodsReceivedNote::STATUS_APPROVED)
                ->sum('grand_total'),
            'pending_orders_value' => LocalPurchaseOrder::whereIn('status', ['pending', 'approved', 'sent'])
                ->sum('grand_total'),
        ];

        // LPO status distribution
        $lpoStatusCounts = LocalPurchaseOrder::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        // GRN status distribution
        $grnStatusCounts = GoodsReceivedNote::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        // Recent Purchase Orders
        $recentLpos = LocalPurchaseOrder::with(['supplier', 'creator'])
            ->latest()
            ->limit(10)
            ->get();

        // Recent Goods Received Notes
        $recentGrns = GoodsReceivedNote::with(['supplier', 'lpo', 'receiver'])
            ->latest()
            ->limit(10)
            ->get();

        // Pending approvals (LPOs awaiting approval)
        $pendingApprovals = LocalPurchaseOrder::with(['supplier', 'creator'])
            ->where('status', 'pending')
            ->latest()
            ->limit(10)
            ->get();

        // Pending GRN confirmations
        $pendingGrnConfirmations = GoodsReceivedNote::with(['supplier', 'lpo', 'receiver'])
            ->whereIn('status', [
                GoodsReceivedNote::STATUS_SUBMITTED,
                GoodsReceivedNote::STATUS_CONFIRMED_BY_STOREKEEPER,
                GoodsReceivedNote::STATUS_PENDING_MANAGER_APPROVAL,
            ])
            ->latest()
            ->limit(10)
            ->get();

        // Top suppliers (by order count this month)
        $topSuppliers = Supplier::withCount(['purchaseOrders' => function ($query) {
            $query->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year);
        }])
            ->where('is_active', true)
            ->orderByDesc('purchase_orders_count')
            ->limit(5)
            ->get();

        // Low stock products needing reorder
        $lowStockProducts = Product::with(['stockLevels'])
            ->whereHas('stockLevels', function ($query) {
                $query->whereColumn('quantity', '<=', 'reserved_qty');
            })
            ->limit(10)
            ->get();

        return view('dashboards.store-manager', compact(
            'stats',
            'lpoStatusCounts',
            'grnStatusCounts',
            'recentLpos',
            'recentGrns',
            'pendingApprovals',
            'pendingGrnConfirmations',
            'topSuppliers',
            'lowStockProducts'
        ));
    }

    private function storeKeeperDashboard()
    {
        $stats = [
            'active_products' => Product::where('is_active', true)->count(),
            'low_stock_items' => StockLevel::query()
                ->whereHas('product', function ($query) {
                    $query->whereColumn('stock_levels.quantity', '<=', 'products.reorder_level');
                })
                ->count(),
            'pending_internal_requests' => InternalUsageRequest::whereIn('status', ['pending', 'approved'])->count(),
            'pending_transfers' => StockTransfer::where('status', 'pending')->count(),
        ];

        $lowStockProducts = StockLevel::with(['product', 'location'])
            ->whereHas('product', function ($query) {
                $query->whereColumn('stock_levels.quantity', '<=', 'products.reorder_level');
            })
            ->orderBy('quantity')
            ->limit(10)
            ->get();

        $pendingInternalRequests = InternalUsageRequest::with(['product', 'requester'])
            ->whereIn('status', ['pending', 'approved'])
            ->latest()
            ->limit(10)
            ->get();

        $recentTransfers = StockTransfer::with(['product', 'fromLocation', 'toLocation', 'requester'])
            ->latest()
            ->limit(10)
            ->get();

        return view('dashboards.store-keeper', compact(
            'stats',
            'lowStockProducts',
            'pendingInternalRequests',
            'recentTransfers'
        ));
    }

    private function restaurantManagerDashboard()
    {
        $barLocation = StockLocation::bar();
        $kitchenLocation = StockLocation::kitchen();

        $locationIds = collect([$barLocation, $kitchenLocation])->filter()->pluck('id');

        $stats = [
            'total_bar_products' => $barLocation ? StockLevel::where('location_id', $barLocation->id)->where('quantity', '>', 0)->count() : 0,
            'total_kitchen_products' => $kitchenLocation ? StockLevel::where('location_id', $kitchenLocation->id)->where('quantity', '>', 0)->count() : 0,
            'low_stock_items' => 0,
            'pending_transfers' => StockTransfer::where('status', 'pending')
                ->whereIn('to_location_id', $locationIds)
                ->count(),
            'today_movements' => StockMovement::whereDate('created_at', today())
                ->whereIn('location_id', $locationIds)
                ->count(),
            // Restaurant sales stats
            'today_orders' => Order::whereDate('created_at', today())->count(),
            'today_settled' => Order::whereDate('settled_at', today())->where('status', 'settled')->count(),
            'today_revenue' => (float) Order::whereDate('settled_at', today())->where('status', 'settled')->sum('total'),
        ];

        // Low stock across both locations
        foreach ([$barLocation, $kitchenLocation] as $loc) {
            if ($loc) {
                $stats['low_stock_items'] += StockLevel::where('location_id', $loc->id)
                    ->whereColumn('quantity', '<=', 'reserved_qty')
                    ->orWhere(function ($q) use ($loc) {
                        $q->where('location_id', $loc->id)
                            ->whereHas('product', function ($pq) {
                                $pq->whereColumn('stock_levels.quantity', '<=', 'products.reorder_level');
                            });
                    })->count();
            }
        }

        $recentMovements = StockMovement::with(['product', 'location', 'actor'])
            ->whereIn('location_id', $locationIds)
            ->latest()
            ->limit(10)
            ->get();

        $notifications = StoreNotification::where('user_id', auth()->id())
            ->where('is_read', false)
            ->latest()
            ->limit(5)
            ->get();

        return view('dashboards.restaurant-manager', compact('stats', 'recentMovements', 'notifications'));
    }

    private function barTenderDashboard()
    {
        $barLocation = StockLocation::bar();
        $stats = [
            'available_items' => $barLocation ? StockLevel::where('location_id', $barLocation->id)->where('quantity', '>', 0)->count() : 0,
            'today_served' => StockMovement::whereDate('created_at', today())
                ->where('type', 'internal_use')
                ->when($barLocation, fn ($q) => $q->where('location_id', $barLocation->id))
                ->count(),
        ];

        $stockLevels = StockLevel::with('product')
            ->when($barLocation, fn ($q) => $q->where('location_id', $barLocation->id))
            ->orderBy('quantity', 'asc')
            ->limit(20)
            ->get();

        return view('dashboards.bar-tender', compact('stats', 'stockLevels'));
    }

    private function waiterDashboard()
    {
        $kitchenLocation = StockLocation::kitchen();
        $barLocation = StockLocation::bar();
        $locationIds = collect([$kitchenLocation, $barLocation])->filter()->pluck('id');

        $stats = [
            'today_orders' => Order::whereDate('created_at', today())->count(),
            'pending_orders' => Order::whereIn('status', ['open', 'sent', 'ready'])
                ->when($locationIds->isNotEmpty(), fn ($q) => $q->whereIn('location_id', $locationIds))
                ->count(),
            'served_today' => Order::whereDate('created_at', today())
                ->whereIn('status', ['served', 'settled'])
                ->count(),
            'occupied_tables' => Table::where('status', 'occupied')->count(),
        ];

        $recentOrders = Order::with(['table', 'location'])
            ->whereDate('created_at', today())
            ->latest()
            ->limit(20)
            ->get();

        return view('dashboards.waiter', compact('stats', 'recentOrders'));
    }
}
