<?php
// app/Http/Controllers/ReservationController.php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\Room;
use Illuminate\Http\Request;

class ReservationController extends Controller
{
    public function index()
    {
        $reservations = Reservation::with(['room.roomType', 'creator'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('reservations.index', compact('reservations'));
    }

    public function create()
    {
        // Get all available rooms
        $availableRooms = Room::where('status', 'available')
            ->where('is_active', true)
            ->with(['roomType', 'floor.building'])
            ->orderBy('room_number')
            ->get();

        return view('reservations.create', compact('availableRooms'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'guest_name' => 'required|string|max:255',
            'guest_phone' => 'required|string|max:20',
            'guest_email' => 'nullable|email|max:255',
            'check_in_date' => 'required|date|after_or_equal:today',
            'check_out_date' => 'required|date|after:check_in_date',
            'number_of_guests' => 'required|integer|min:1',
            'total_amount' => 'required|numeric|min:0',
            'room_id' => 'nullable|exists:rooms,id',
            'status' => 'required|in:pending,confirmed',
        ]);

        // Generate unique reservation number
        $validated['reservation_number'] = 'RES-' . strtoupper(uniqid());
        $validated['created_by'] = auth()->id();

        $reservation = Reservation::create($validated);

        return redirect()->route('reservations.index')
            ->with('success', 'Reservation created successfully.');
    }

    public function edit(Reservation $reservation)
    {
        // Get available rooms plus the currently assigned room
        $availableRooms = Room::where(function($query) use ($reservation) {
                $query->where('status', 'available')
                      ->where('is_active', true);
                
                // Include the currently assigned room even if it's not available
                if ($reservation->room_id) {
                    $query->orWhere('id', $reservation->room_id);
                }
            })
            ->with(['roomType', 'floor.building'])
            ->orderBy('room_number')
            ->get();

        return view('reservations.edit', compact('reservation', 'availableRooms'));
    }

    public function update(Request $request, Reservation $reservation)
    {
        $validated = $request->validate([
            'guest_name' => 'required|string|max:255',
            'guest_phone' => 'required|string|max:20',
            'guest_email' => 'nullable|email|max:255',
            'check_in_date' => 'required|date',
            'check_out_date' => 'required|date|after:check_in_date',
            'number_of_guests' => 'required|integer|min:1',
            'total_amount' => 'required|numeric|min:0',
            'room_id' => 'nullable|exists:rooms,id',
            'status' => 'required|in:pending,confirmed,checked_in,checked_out,cancelled,no_show',
        ]);

        $reservation->update($validated);

        return redirect()->route('reservations.index')
            ->with('success', 'Reservation updated successfully.');
    }

    public function destroy(Reservation $reservation)
    {
        $reservation->delete();

        return redirect()->route('reservations.index')
            ->with('success', 'Reservation deleted successfully.');
    }

    public function checkIn(Reservation $reservation)
    {
        if ($reservation->status !== 'confirmed') {
            return back()->with('error', 'Only confirmed reservations can be checked in.');
        }

        if (!$reservation->room_id) {
            return back()->with('error', 'Please assign a room before checking in.');
        }

        $reservation->update(['status' => 'checked_in']);

        return back()->with('success', 'Guest checked in successfully.');
    }

    public function checkOut(Reservation $reservation)
    {
        if ($reservation->status !== 'checked_in') {
            return back()->with('error', 'Only checked-in reservations can be checked out.');
        }

        $reservation->update(['status' => 'checked_out']);

        return back()->with('success', 'Guest checked out successfully.');
    }

    public function cancel(Reservation $reservation)
    {
        if (!in_array($reservation->status, ['pending', 'confirmed'])) {
            return back()->with('error', 'Only pending or confirmed reservations can be cancelled.');
        }

        $reservation->update(['status' => 'cancelled']);

        return back()->with('success', 'Reservation cancelled successfully.');
    }
}