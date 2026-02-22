<?php

namespace Database\Seeders;

use App\Models\MenuCategory;
use App\Models\StockLocation;
use Illuminate\Database\Seeder;

class MenuCategorySeeder extends Seeder
{
    public function run(): void
    {
        $bar     = StockLocation::where('code', 'bar')->first();
        $kitchen = StockLocation::where('code', 'kitchen')->first();

        if (!$bar || !$kitchen) {
            $this->command->warn('Run StockLocationSeeder first.');
            return;
        }

        $categories = [
            // Bar categories
            ['name' => 'Spirits',     'location_id' => $bar->id,     'description' => 'Whisky, vodka, gin, rum'],
            ['name' => 'Beers',       'location_id' => $bar->id,     'description' => 'Bottled and draft beers'],
            ['name' => 'Cocktails',   'location_id' => $bar->id,     'description' => 'Mixed drinks and cocktails'],
            ['name' => 'Soft Drinks', 'location_id' => $bar->id,     'description' => 'Non-alcoholic beverages'],
            ['name' => 'Wines',       'location_id' => $bar->id,     'description' => 'Red, white, and sparkling wines'],
            // Kitchen/restaurant categories
            ['name' => 'Starters',    'location_id' => $kitchen->id, 'description' => 'Soups and starters'],
            ['name' => 'Mains',       'location_id' => $kitchen->id, 'description' => 'Main course meals'],
            ['name' => 'Grills',      'location_id' => $kitchen->id, 'description' => 'Grilled meats and fish'],
            ['name' => 'Desserts',    'location_id' => $kitchen->id, 'description' => 'Desserts and sweets'],
            ['name' => 'Breakfast',   'location_id' => $kitchen->id, 'description' => 'Breakfast menu items'],
        ];

        foreach ($categories as $cat) {
            MenuCategory::updateOrCreate(
                ['name' => $cat['name'], 'location_id' => $cat['location_id']],
                $cat
            );
        }
    }
}
