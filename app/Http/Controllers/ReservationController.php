<?php
namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReservationController extends Controller {
    public function index() {
        $reservations = Reservation::with(['room', 'creator'])->latest()->paginate(20);
        return view('reservations.index', compact('reservations'));
    }

    public function create() {
        return view('reservations.create');
    }

    public function store(Request $request) {
        $validated = $request->validate([
            'guest_name' => 'required|max:255',
            'guest_phone' => 'required|max:255',
            'guest_email' => 'nullable|email|max:255',
            'check_in_date' => 'required|date',
            'check_out_date' => 'required|date|after:check_in_date',
            'number_of_guests' => 'required|integer|min:1',
            'room_id' => 'nullable|exists:rooms,id',
            'total_amount' => 'required|numeric|min:0',
            'status' => 'required|in:pending,confirmed,checked_in,checked_out,cancelled,no_show',
        ]);

        $validated['created_by'] = auth()->id();
        Reservation::create($validated);
        
        return redirect()->route('reservations.index')->with('success', 'Reservation created successfully.');
    }

    public function edit(Reservation $reservation) {
        $checkIn = $reservation->check_in_date->format('Y-m-d');
        $checkOut = $reservation->check_out_date->format('Y-m-d');
        
        $availableRooms = Room::where('is_active', true)
            ->whereDoesntHave('reservations', function ($query) use ($checkIn, $checkOut, $reservation) {
                $query->where('id', '!=', $reservation->id)
                    ->where('status', '!=', 'cancelled')
                    ->where(function ($q) use ($checkIn, $checkOut) {
                        $q->whereBetween('check_in_date', [$checkIn, $checkOut])
                          ->orWhereBetween('check_out_date', [$checkIn, $checkOut])
                          ->orWhere(function ($q2) use ($checkIn, $checkOut) {
                              $q2->where('check_in_date', '<=', $checkIn)
                                 ->where('check_out_date', '>=', $checkOut);
                          });
                    });
            })
            ->get();
            
        return view('reservations.edit', compact('reservation', 'availableRooms'));
    }

    public function update(Request $request, Reservation $reservation) {
        $validated = $request->validate([
            'guest_name' => 'required|max:255',
            'guest_phone' => 'required|max:255',
            'guest_email' => 'nullable|email|max:255',
            'check_in_date' => 'required|date',
            'check_out_date' => 'required|date|after:check_in_date',
            'number_of_guests' => 'required|integer|min:1',
            'room_id' => 'nullable|exists:rooms,id',
            'total_amount' => 'required|numeric|min:0',
            'status' => 'required|in:pending,confirmed,checked_in,checked_out,cancelled,no_show',
        ]);

        $reservation->update($validated);
        return redirect()->route('reservations.index')->with('success', 'Reservation updated successfully.');
    }

    public function destroy(Reservation $reservation) {
        $reservation->delete();
        return redirect()->route('reservations.index')->with('success', 'Reservation deleted successfully.');
    }

    public function checkIn(Reservation $reservation) {
        $reservation->update(['status' => 'checked_in']);
        $reservation->room?->update(['status' => 'occupied']);
        return back()->with('success', 'Guest checked in successfully.');
    }

    public function checkOut(Reservation $reservation) {
        $reservation->update(['status' => 'checked_out']);
        $reservation->room?->update(['status' => 'dirty']);
        return back()->with('success', 'Guest checked out successfully.');
    }

    public function cancel(Reservation $reservation) {
        $reservation->update(['status' => 'cancelled']);
        $reservation->room?->update(['status' => 'available']);
        return back()->with('success', 'Reservation cancelled successfully.');
    }
}