<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Safety net: match existing property_code values to buildings.code
        // and set building_id for users that have property_code but no building_id
        $orphans = DB::table('users')
            ->whereNotNull('property_code')
            ->where('property_code', '!=', '')
            ->whereNull('building_id')
            ->get();

        foreach ($orphans as $user) {
            $building = DB::table('buildings')
                ->where('code', $user->property_code)
                ->first();

            if ($building) {
                DB::table('users')
                    ->where('id', $user->id)
                    ->update(['building_id' => $building->id]);

                Log::info('Migration: matched user property_code to building', [
                    'user_id' => $user->id,
                    'property_code' => $user->property_code,
                    'building_id' => $building->id,
                ]);
            } else {
                Log::warning('Migration: user has property_code but no matching building.code', [
                    'user_id' => $user->id,
                    'property_code' => $user->property_code,
                ]);
            }
        }

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('property_code');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('property_code', 50)->nullable()->after('login_type');
        });
    }
};
