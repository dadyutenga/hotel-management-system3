<?php

namespace Database\Seeders;

use App\Models\StockLocation;
use Illuminate\Database\Seeder;

class StockLocationSeeder extends Seeder
{
    public function run(): void
    {
        $locations = [
            ['name' => 'Main Store', 'code' => 'main_store', 'description' => 'Central hotel inventory'],
            ['name' => 'Bar',        'code' => 'bar',        'description' => 'Bar section inventory'],
            ['name' => 'Kitchen',    'code' => 'kitchen',    'description' => 'Kitchen section inventory'],
        ];

        foreach ($locations as $loc) {
            StockLocation::updateOrCreate(['code' => $loc['code']], $loc);
        }
    }
}
