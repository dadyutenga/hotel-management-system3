<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    // In your 2026_02_17_100000_refactor_reservation_booking_architecture.php migration

    public function up(): void
    {
        // Update any old status values to 'converted'
        DB::table('reservations')
            ->whereIn('status', ['checked_in', 'checked_out'])
            ->update(['status' => 'converted']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
