<?php

namespace App\Http\Controllers\Restaurant;

use App\Http\Controllers\Controller;
use App\Models\StockLocation;
use App\Models\Table;
use App\Services\BuildingContext;
use App\Services\BuildingModuleGate;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class TableController extends Controller
{
    /**
     * GET /restaurant/tables
     */
    public function index(Request $request): View
    {
        $buildingId = BuildingContext::buildingId();

        $locations = StockLocation::whereIn('code', ['bar', 'kitchen'])
            ->when($buildingId, fn ($q) => $q->where('building_id', $buildingId))
            ->get();

        $tables = Table::with(['location', 'activeOrder'])
            ->forBuilding($buildingId)
            ->where('is_active', true)
            ->when($request->location_id, fn ($q) => $q->where('location_id', $request->location_id))
            ->orderBy('table_number')
            ->get();

        return view('restaurant.tables.index', compact('tables', 'locations'));
    }

    /**
     * POST /restaurant/tables
     */
    public function store(Request $request): RedirectResponse
    {
        $buildingId = BuildingContext::buildingId();

        $locationRule = Rule::exists('stock_locations', 'id');
        if ($buildingId) {
            $locationRule->where('building_id', $buildingId);
        }

        $data = $request->validate([
            'location_id' => ['required', 'uuid', $locationRule],
            'table_number' => 'required|string|max:20',
            'capacity' => 'required|integer|min:1|max:20',
        ]);

        $location = StockLocation::findOrFail($data['location_id']);
        if ($location->code === 'bar') {
            BuildingModuleGate::ensureBar($buildingId);
        } else {
            BuildingModuleGate::ensureRestaurant($buildingId);
        }

        Table::create([
            'building_id' => $buildingId,
            'location_id' => $data['location_id'],
            'table_number' => $data['table_number'],
            'capacity' => $data['capacity'],
            'status' => 'available',
        ]);

        return redirect()
            ->route('restaurant.tables.index')
            ->with('success', "Table {$data['table_number']} added.");
    }

    /**
     * POST /restaurant/tables/{table}/status
     */
    public function updateStatus(Request $request, Table $table): RedirectResponse
    {
        BuildingContext::enforce($table->building_id);

        $request->validate([
            'status' => 'required|in:available,occupied,reserved,cleaning',
        ]);

        $table->update(['status' => $request->status]);

        return redirect()
            ->route('restaurant.tables.index')
            ->with('success', "Table {$table->table_number} marked as {$request->status}.");
    }
}
