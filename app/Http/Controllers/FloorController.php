<?php
namespace App\Http\Controllers;

use App\Models\Floor;
use App\Models\Building;
use Illuminate\Http\Request;

class FloorController extends Controller {
    public function index() {
        $floors = Floor::with('building')->withCount('rooms')->latest()->paginate(15);
        return view('floors.index', compact('floors'));
    }

    public function create() {
        $buildings = Building::where('is_active', true)->get();
        return view('floors.create', compact('buildings'));
    }

    public function store(Request $request) {
        $validated = $request->validate([
            'building_id' => 'required|exists:buildings,id',
            'name' => 'required|max:255',
            'floor_number' => 'required|integer',
            'is_active' => 'boolean',
        ]);

        Floor::create($validated);
        return redirect()->route('floors.index')->with('success', 'Floor created successfully.');
    }

    public function edit(Floor $floor) {
        $buildings = Building::where('is_active', true)->get();
        return view('floors.edit', compact('floor', 'buildings'));
    }

    public function update(Request $request, Floor $floor) {
        $validated = $request->validate([
            'building_id' => 'required|exists:buildings,id',
            'name' => 'required|max:255',
            'floor_number' => 'required|integer',
            'is_active' => 'boolean',
        ]);

        $floor->update($validated);
        return redirect()->route('floors.index')->with('success', 'Floor updated successfully.');
    }

    public function destroy(Floor $floor) {
        $floor->delete();
        return redirect()->route('floors.index')->with('success', 'Floor deleted successfully.');
    }
}