<?php

namespace App\Http\Controllers;

use App\Models\Building;
use App\Models\Floor;
use App\Services\BuildingContext;
use Illuminate\Http\Request;

class FloorController extends Controller
{
    public function index()
    {
        $floors = Floor::with('building')
            ->withCount('rooms')
            ->when($buildingId = BuildingContext::buildingId(), fn ($q) => $q->where('building_id', $buildingId))
            ->latest()
            ->paginate(15);

        return view('floors.index', compact('floors'));
    }

    public function create()
    {
        $buildings = Building::where('is_active', true)
            ->when($buildingId = BuildingContext::buildingId(), fn ($q) => $q->where('id', $buildingId))
            ->get();

        return view('floors.create', compact('buildings'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'building_id' => 'required|uuid|exists:buildings,id',
            'name' => 'required|max:255',
            'floor_number' => 'required|integer',
            'is_active' => 'boolean',
        ]);

        if (! BuildingContext::isAdmin()) {
            $validated['building_id'] = BuildingContext::buildingId();
        }

        BuildingContext::enforce($validated['building_id']);

        Floor::create($validated);

        return redirect()->route('floors.index')->with('success', 'Floor created successfully.');
    }

    public function edit(Floor $floor)
    {
        BuildingContext::enforce($floor->building_id);
        $buildings = Building::where('is_active', true)
            ->when($buildingId = BuildingContext::buildingId(), fn ($q) => $q->where('id', $buildingId))
            ->get();

        return view('floors.edit', compact('floor', 'buildings'));
    }

    public function update(Request $request, Floor $floor)
    {
        BuildingContext::enforce($floor->building_id);
        $validated = $request->validate([
            'building_id' => 'required|uuid|exists:buildings,id',
            'name' => 'required|max:255',
            'floor_number' => 'required|integer',
            'is_active' => 'boolean',
        ]);

        if (! BuildingContext::isAdmin()) {
            $validated['building_id'] = BuildingContext::buildingId();
        }

        BuildingContext::enforce($validated['building_id']);

        $floor->update($validated);

        return redirect()->route('floors.index')->with('success', 'Floor updated successfully.');
    }

    public function destroy(Floor $floor)
    {
        BuildingContext::enforce($floor->building_id);
        if ($floor->rooms()->exists()) {
            return redirect()->route('floors.index')
                ->with('error', 'Cannot delete "'.$floor->name.'" because it has assigned rooms. Remove the rooms first.');
        }

        $this->softDelete($floor);

        return redirect()->route('floors.index')->with('success', 'Floor deleted successfully.');
    }

    public function archived()
    {
        $records = Floor::onlyDeleted()
            ->with('building')
            ->when($buildingId = BuildingContext::buildingId(), fn ($q) => $q->where('building_id', $buildingId))
            ->latest('deleted_at')
            ->paginate(20);

        return view('floors.archived', compact('records'));
    }

    public function restore(Floor $floor)
    {
        BuildingContext::enforce($floor->building_id);
        $this->restoreModel($floor);

        return redirect()->route('floors.index')->with('success', 'Floor restored successfully.');
    }
}
