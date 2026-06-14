<?php

namespace Database\Seeders;

use App\Models\Building;
use App\Models\StockLocation;
use Illuminate\Database\Seeder;

class StockLocationSeeder extends Seeder
{
    public function run(): void
    {
        $buildings = Building::all();

        // If no buildings exist yet, fall back to legacy global locations so that
        // early seeders and fresh installs still have the three core locations.
        if ($buildings->isEmpty()) {
            $locations = [
                ['name' => 'Main Store', 'code' => 'main_store', 'description' => 'Central hotel inventory'],
                ['name' => 'Bar', 'code' => 'bar', 'description' => 'Bar section inventory'],
                ['name' => 'Kitchen', 'code' => 'kitchen', 'description' => 'Kitchen section inventory'],
            ];

            foreach ($locations as $loc) {
                StockLocation::updateOrCreate(['code' => $loc['code']], $loc);
            }

            return;
        }

        $locationCodes = [
            'main_store' => ['name' => 'Main Store', 'description' => 'Central inventory'],
            'bar' => ['name' => 'Bar', 'description' => 'Bar section inventory'],
            'kitchen' => ['name' => 'Kitchen', 'description' => 'Kitchen section inventory'],
        ];

        foreach ($buildings as $building) {
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
        }
    }
}
