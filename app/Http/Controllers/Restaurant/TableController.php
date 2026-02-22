<?php

namespace App\Http\Controllers\Restaurant;

use App\Http\Controllers\Controller;
use App\Models\StockLocation;
use App\Models\Table;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TableController extends Controller
{
    /**
     * GET /restaurant/tables
     */
    public function index(Request $request): View
    {
        $locations = StockLocation::whereIn('code', ['bar', 'kitchen'])->get();

        $tables = Table::with(['location', 'activeOrder'])
            ->where('is_active', true)
            ->when($request->location_id, fn($q) => $q->where('location_id', $request->location_id))
            ->orderBy('table_number')
            ->get();

        return view('restaurant.tables.index', compact('tables', 'locations'));
    }

    /**
     * POST /restaurant/tables
     */
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'location_id'  => 'required|uuid|exists:stock_locations,id',
            'table_number' => 'required|string|max:20',
            'capacity'     => 'required|integer|min:1|max:20',
        ]);

        Table::create([...$data, 'status' => 'available']);

        return redirect()
            ->route('restaurant.tables.index')
            ->with('success', "Table {$data['table_number']} added.");
    }

    /**
     * POST /restaurant/tables/{table}/status
     */
    public function updateStatus(Request $request, Table $table): RedirectResponse
    {
        $request->validate([
            'status' => 'required|in:available,occupied,reserved,cleaning',
        ]);

        $table->update(['status' => $request->status]);

        return redirect()
            ->route('restaurant.tables.index')
            ->with('success', "Table {$table->table_number} marked as {$request->status}.");
    }
}
