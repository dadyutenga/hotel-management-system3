<?php

namespace App\Http\Controllers;

use App\Models\Floor;
use App\Models\Room;
use App\Models\RoomType;
use App\Services\BuildingContext;
use Illuminate\Http\Request;

class RoomController extends Controller
{
    public function index()
    {
        $rooms = Room::with(['floor.building', 'roomType'])
            ->forUserBuilding()
            ->latest()
            ->paginate(20);

        $statusCounts = Room::forUserBuilding()
            ->selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        return view('rooms.index', compact('rooms', 'statusCounts'));
    }

    public function show(Room $room)
    {
        BuildingContext::enforce($room->building_id);
        $room->load(['floor.building', 'roomType', 'cleaningAssignee', 'outOfOrderBy', 'cleaningConfirmer']);

        return view('rooms.show', compact('room'));
    }

    public function create()
    {
        $floors = Floor::with('building')
            ->where('is_active', true)
            ->when($buildingId = BuildingContext::buildingId(), fn ($q) => $q->where('building_id', $buildingId))
            ->get();
        $roomTypes = RoomType::all();

        return view('rooms.create', compact('floors', 'roomTypes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'floor_id' => 'required|uuid|exists:floors,id',
            'room_type_id' => 'required|uuid|exists:room_types,id',
            'room_number' => 'required|max:255',
            'status' => 'required|in:available,reserved,occupied,dirty,out_of_order',
            'is_active' => 'boolean',
        ]);

        $floor = Floor::findOrFail($validated['floor_id']);
        BuildingContext::enforce($floor->building_id);

        $validated['building_id'] = $floor->building_id;

        Room::create($validated);

        return redirect()->route('rooms.index')->with('success', 'Room created successfully.');
    }

    public function edit(Room $room)
    {
        BuildingContext::enforce($room->building_id);
        $floors = Floor::with('building')
            ->where('is_active', true)
            ->when($buildingId = BuildingContext::buildingId(), fn ($q) => $q->where('building_id', $buildingId))
            ->get();
        $roomTypes = RoomType::all();

        return view('rooms.edit', compact('room', 'floors', 'roomTypes'));
    }

    public function update(Request $request, Room $room)
    {
        BuildingContext::enforce($room->building_id);
        $validated = $request->validate([
            'floor_id' => 'required|uuid|exists:floors,id',
            'room_type_id' => 'required|uuid|exists:room_types,id',
            'room_number' => 'required|max:255',
            'status' => 'required|in:available,reserved,occupied,dirty,out_of_order',
            'is_active' => 'boolean',
        ]);

        $floor = Floor::findOrFail($validated['floor_id']);
        BuildingContext::enforce($floor->building_id);

        $validated['building_id'] = $floor->building_id;

        $room->update($validated);

        return redirect()->route('rooms.index')->with('success', 'Room updated successfully.');
    }

    public function destroy(Room $room)
    {
        BuildingContext::enforce($room->building_id);
        $this->softDelete($room);

        return redirect()->route('rooms.index')->with('success', 'Room deleted successfully.');
    }

    public function archived()
    {
        $rooms = Room::onlyDeleted()
            ->forUserBuilding()
            ->with(['floor.building', 'roomType'])
            ->latest('deleted_at')
            ->paginate(20);

        return view('rooms.archived', compact('rooms'));
    }

    public function restore(Room $room)
    {
        BuildingContext::enforce($room->building_id);
        $this->restoreModel($room);

        return redirect()->route('rooms.index')->with('success', 'Room restored successfully.');
    }

    public function toggleStatus(Request $request, Room $room)
    {
        BuildingContext::enforce($room->building_id);
        $validated = $request->validate([
            'status' => 'required|in:available,reserved,occupied,dirty,out_of_order',
        ]);

        $room->update(['status' => $validated['status']]);

        return back()->with('success', 'Room status updated successfully.');
    }
}
