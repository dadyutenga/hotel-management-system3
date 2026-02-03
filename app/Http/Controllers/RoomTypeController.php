<?php
namespace App\Http\Controllers;

use App\Models\RoomType;
use Illuminate\Http\Request;

class RoomTypeController extends Controller {
    public function index() {
        $roomTypes = RoomType::withCount('rooms')->latest()->paginate(15);
        return view('room-types.index', compact('roomTypes'));
    }

    public function create() {
        return view('room-types.create');
    }

    public function store(Request $request) {
        $validated = $request->validate([
            'name' => 'required|max:255',
            'code' => 'required|unique:room_types|max:255',
            'base_rate' => 'required|numeric|min:0',
            'max_occupancy' => 'required|integer|min:1',
            'description' => 'nullable',
        ]);

        RoomType::create($validated);
        return redirect()->route('room-types.index')->with('success', 'Room type created successfully.');
    }

    public function edit(RoomType $roomType) {
        return view('room-types.edit', compact('roomType'));
    }

    public function update(Request $request, RoomType $roomType) {
        $validated = $request->validate([
            'name' => 'required|max:255',
            'code' => 'required|unique:room_types,code,'.$roomType->id.'|max:255',
            'base_rate' => 'required|numeric|min:0',
            'max_occupancy' => 'required|integer|min:1',
            'description' => 'nullable',
        ]);

        $roomType->update($validated);
        return redirect()->route('room-types.index')->with('success', 'Room type updated successfully.');
    }

    public function destroy(RoomType $roomType) {
        $roomType->delete();
        return redirect()->route('room-types.index')->with('success', 'Room type deleted successfully.');
    }
}