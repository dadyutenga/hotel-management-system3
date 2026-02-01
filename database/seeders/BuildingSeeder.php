<?php
namespace Database\Seeders;

use App\Models\Building;
use Illuminate\Database\Seeder;

class BuildingSeeder extends Seeder {
    public function run(): void {
        Building::create([
            'name' => 'Main Building',
            'code' => 'MAIN',
            'address' => '123 Hotel Street, City Center',
            'is_active' => true,
        ]);

        Building::create([
            'name' => 'West Wing',
            'code' => 'WEST',
            'address' => '125 Hotel Street, City Center',
            'is_active' => true,
        ]);
    }
}