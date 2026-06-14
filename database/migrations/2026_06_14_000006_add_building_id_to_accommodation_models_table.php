<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $tables = [
            'bookings',
            'reservations',
            'guests',
            'conference_halls',
            'conference_bookings',
            'events',
            'organizations',
            'laundry_tasks',
            'cleaning_schedules',
        ];

        foreach ($tables as $tableName) {
            if (Schema::hasTable($tableName)) {
                Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                    if (! Schema::hasColumn($tableName, 'building_id')) {
                        $table->uuid('building_id')->nullable()->after('id');
                        $table->index('building_id');
                    }
                });
            }
        }

        // Conference halls already has nullable building_id; add FK if missing
        if (Schema::hasTable('conference_halls')) {
            Schema::table('conference_halls', function (Blueprint $table) {
                // Foreign key will be added in a separate safe migration or left as indexed column
            });
        }
    }

    public function down(): void
    {
        $tables = [
            'bookings',
            'reservations',
            'guests',
            'conference_halls',
            'conference_bookings',
            'events',
            'organizations',
            'laundry_tasks',
            'cleaning_schedules',
        ];

        foreach ($tables as $tableName) {
            if (Schema::hasTable($tableName)) {
                Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                    if (Schema::hasColumn($tableName, 'building_id')) {
                        $table->dropIndex(['building_id']);
                        $table->dropColumn('building_id');
                    }
                });
            }
        }
    }
};
