<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // ─── Step 1: Convert existing checked_in/checked_out reservations to 'converted' ───
        DB::table('reservations')
            ->whereIn('status', ['checked_in', 'checked_out'])
            ->update(['status' => 'converted']);

        // Mark reservations that have a booking_id as converted
        DB::table('reservations')
            ->whereNotNull('booking_id')
            ->update(['status' => 'converted']);

        // ─── Step 2: Rename total_amount → estimated_amount on reservations ───
        Schema::table('reservations', function (Blueprint $table) {
            $table->renameColumn('total_amount', 'estimated_amount');
        });

        // ─── Step 3: Make created_by nullable on reservations (for public/online reservations) ───
        Schema::table('reservations', function (Blueprint $table) {
            $table->uuid('created_by')->nullable()->change();
        });

        // Note: SQLite stores status as text, so no ENUM alteration needed.
        // The model layer enforces valid statuses: pending, confirmed, cancelled, no_show, converted.
    }

    public function down(): void
    {
        // Revert created_by to required
        Schema::table('reservations', function (Blueprint $table) {
            $table->uuid('created_by')->nullable(false)->change();
        });

        // Rename estimated_amount back to total_amount
        Schema::table('reservations', function (Blueprint $table) {
            $table->renameColumn('estimated_amount', 'total_amount');
        });

        // Revert converted statuses back
        DB::table('reservations')
            ->where('status', 'converted')
            ->update(['status' => 'pending']);
    }
};
