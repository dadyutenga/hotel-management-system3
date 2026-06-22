<?php

namespace App\Http\Controllers;

use App\Models\Building;
use App\Models\BuildingModule;
use Illuminate\Http\Request;

class BuildingModuleController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $buildingQuery = Building::active()->with(['modules']);

        if (! $user->isAdmin()) {
            $buildingQuery->where('id', $user->building_id);
        }

        $buildings = $buildingQuery->orderBy('name')->paginate(15);

        return view('building-modules.index', compact('buildings'));
    }

    public function create(Request $request)
    {
        $user = auth()->user();
        $buildings = Building::active()->orderBy('name');

        if (! $user->isAdmin()) {
            $buildings->where('id', $user->building_id);
        }

        return view('building-modules.create', [
            'buildings' => $buildings->get(),
            'types' => [BuildingModule::TYPE_RESTAURANT, BuildingModule::TYPE_BAR],
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'building_id' => 'required|uuid|exists:buildings,id',
            'type' => 'required|in:'.BuildingModule::TYPE_RESTAURANT.','.BuildingModule::TYPE_BAR,
            'name' => 'required|string|max:150',
            'code' => 'nullable|string|max:50',
            'status' => 'required|in:active,inactive',
        ]);

        $user = auth()->user();
        if (! $user->isAdmin()) {
            $validated['building_id'] = $user->building_id;
        }

        BuildingModule::create([
            'building_id' => $validated['building_id'],
            'type' => $validated['type'],
            'name' => $validated['name'],
            'code' => $validated['code'] ?? 'default',
            'status' => $validated['status'],
            'is_active' => $validated['status'] === 'active',
        ]);

        return redirect()->route('building-modules.index')
            ->with('success', 'Building module created successfully.');
    }

    public function edit(BuildingModule $buildingModule)
    {
        $user = auth()->user();
        if (! $user->isAdmin() && $buildingModule->building_id !== $user->building_id) {
            abort(403, 'This module does not belong to your building.');
        }

        $buildings = Building::active()->orderBy('name');
        if (! $user->isAdmin()) {
            $buildings->where('id', $user->building_id);
        }

        return view('building-modules.edit', [
            'module' => $buildingModule,
            'buildings' => $buildings->get(),
            'types' => [BuildingModule::TYPE_RESTAURANT, BuildingModule::TYPE_BAR],
        ]);
    }

    public function update(Request $request, BuildingModule $buildingModule)
    {
        $user = auth()->user();
        if (! $user->isAdmin() && $buildingModule->building_id !== $user->building_id) {
            abort(403, 'This module does not belong to your building.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'code' => 'nullable|string|max:50',
            'status' => 'required|in:active,inactive',
        ]);

        $buildingModule->update([
            'name' => $validated['name'],
            'code' => $validated['code'] ?? $buildingModule->code,
            'status' => $validated['status'],
            'is_active' => $validated['status'] === 'active',
        ]);

        return redirect()->route('building-modules.index')
            ->with('success', 'Building module updated successfully.');
    }

    public function destroy(BuildingModule $buildingModule)
    {
        $user = auth()->user();
        if (! $user->isAdmin() && $buildingModule->building_id !== $user->building_id) {
            abort(403, 'This module does not belong to your building.');
        }

        $this->softDelete($buildingModule);

        return redirect()->route('building-modules.index')
            ->with('success', 'Building module deleted successfully.');
    }
}
