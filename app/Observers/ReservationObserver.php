<?php
namespace App\Observers;

use App\Models\Reservation;

class ReservationObserver {
    public function updating(Reservation $reservation): void {
        if ($reservation->isDirty('status')) {
            match ($reservation->status) {
                'confirmed' => $reservation->room?->update(['status' => 'reserved']),
                'checked_in' => $reservation->room?->update(['status' => 'occupied']),
                'checked_out' => $reservation->room?->update(['status' => 'dirty']),
                'cancelled', 'no_show' => $reservation->room?->update(['status' => 'available']),
                default => null,
            };
        }
    }
}