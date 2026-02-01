<?php
namespace Database\Seeders;

use App\Models\RoomType;
use Illuminate\Database\Seeder;

class RoomTypeSeeder extends Seeder {
    public function run(): void {
        RoomType::create([
            'name' => 'Standard Single',
            'code' => 'STD-SGL',
            'base_rate' => 80.00,
            'max_occupancy' => 1,
            'description' => 'Standard room with single bed',
        ]);

        RoomType::create([
            'name' => 'Standard Double',
            'code' => 'STD-DBL',
            'base_rate' => 120.00,
            'max_occupancy' => 2,
            'description' => 'Standard room with double bed',
        ]);

        RoomType::create([
            'name' => 'Deluxe',
            'code' => 'DLX',
            'base_rate' => 180.00,
            'max_occupancy' => 2,
            'description' => 'Deluxe room with king bed and city view',
        ]);

        RoomType::create([
            'name' => 'Suite',
            'code' => 'STE',
            'base_rate' => 300.00,
            'max_occupancy' => 4,
            'description' => 'Executive suite with separate living area',
        ]);

        RoomType::create([
            'name' => 'Family Room',
            'code' => 'FAM',
            'base_rate' => 220.00,
            'max_occupancy' => 4,
            'description' => 'Spacious room with two double beds',
        ]);
    }
}