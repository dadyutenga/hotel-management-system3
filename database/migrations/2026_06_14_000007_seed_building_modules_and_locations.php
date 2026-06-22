<?php

use App\Models\Building;
use App\Models\BuildingModule;
use App\Models\StockLocation;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        $buildings = Building::all();

        if ($buildings->isEmpty()) {
            return;
        }

        $firstBuilding = $buildings->first();

        // Assign any legacy global stock locations to the first building
        StockLocation::whereNull('building_id')->update(['building_id' => $firstBuilding->id]);

        $locationCodes = [
            'main_store' => ['name' => 'Main Store', 'description' => 'Central inventory'],
            'bar' => ['name' => 'Bar', 'description' => 'Bar section inventory'],
            'kitchen' => ['name' => 'Kitchen', 'description' => 'Kitchen section inventory'],
        ];

        foreach ($buildings as $building) {
            // Ensure each building has the three required stock locations
            foreach ($locationCodes as $code => $data) {
                StockLocation::firstOrCreate(
                    ['building_id' => $building->id, 'code' => $code],
                    [
                        'name' => $building->name.' '.$data['name'],
                        'description' => $data['description'],
                        'is_active' => true,
                    ]
                );
            }

            // Ensure each building has an active restaurant module
            BuildingModule::firstOrCreate(
                [
                    'building_id' => $building->id,
                    'type' => BuildingModule::TYPE_RESTAURANT,
                    'code' => 'default',
                ],
                [
                    'name' => $building->name.' Restaurant',
                    'status' => 'active',
                    'is_active' => true,
                ]
            );

            // Ensure each building has an active bar module
            BuildingModule::firstOrCreate(
                [
                    'building_id' => $building->id,
                    'type' => BuildingModule::TYPE_BAR,
                    'code' => 'default',
                ],
                [
                    'name' => $building->name.' Bar',
                    'status' => 'active',
                    'is_active' => true,
                ]
            );
        }
    }

    public function down(): void
    {
        // No-op: rollback of seed data is not safely reversible without data loss
    }
};
