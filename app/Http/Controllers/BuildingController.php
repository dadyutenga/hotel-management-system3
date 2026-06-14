<?php

namespace App\Http\Controllers;

use App\Models\Building;
use App\Services\BuildingContext;
use Illuminate\Http\Request;

class BuildingController extends Controller
{
    public function index()
    {
        $buildings = Building::withCount('floors')
            ->when($buildingId = BuildingContext::buildingId(), fn ($q) => $q->where('id', $buildingId))
            ->latest()
            ->paginate(15);

        return view('buildings.index', compact('buildings'));
    }

    public function create()
    {
        return view('buildings.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|max:255',
            'code' => 'required|unique:buildings|max:255',
            'address' => 'nullable',
            'is_active' => 'boolean',
        ]);

        Building::create($validated);

        return redirect()->route('buildings.index')->with('success', 'Building created successfully.');
    }

    public function edit(Building $building)
    {
        BuildingContext::enforce($building->id);

        return view('buildings.edit', compact('building'));
    }

    public function update(Request $request, Building $building)
    {
        BuildingContext::enforce($building->id);
        $validated = $request->validate([
            'name' => 'required|max:255',
            'code' => 'required|unique:buildings,code,'.$building->id.'|max:255',
            'address' => 'nullable',
            'is_active' => 'boolean',
        ]);

        $building->update($validated);

        return redirect()->route('buildings.index')->with('success', 'Building updated successfully.');
    }

    public function destroy(Building $building)
    {
        BuildingContext::enforce($building->id);
        $hasRooms = $building->floors()->whereHas('rooms')->exists();
        if ($hasRooms) {
            return redirect()->route('buildings.index')
                ->with('error', 'Cannot delete "'.$building->name.'" because it has floors with assigned rooms. Remove the rooms first.');
        }

        $this->softDelete($building);

        return redirect()->route('buildings.index')->with('success', 'Building deleted successfully.');
    }

    public function archived()
    {
        $records = Building::onlyDeleted()
            ->when($buildingId = BuildingContext::buildingId(), fn ($q) => $q->where('id', $buildingId))
            ->latest('deleted_at')
            ->paginate(20);

        return view('buildings.archived', compact('records'));
    }

    public function restore(Building $building)
    {
        BuildingContext::enforce($building->id);
        $this->restoreModel($building);

        return redirect()->route('buildings.index')->with('success', 'Building restored successfully.');
    }
}
