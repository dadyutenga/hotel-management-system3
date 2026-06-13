<?php

namespace App\Console\Commands;

use App\Models\Beverage;
use App\Models\BeverageCategory;
use App\Models\BeverageInventory;
use Illuminate\Console\Command;

class SeedBeverageBarcodes extends Command
{
    protected $signature = 'beverages:seed-barcodes {--count=50 : Number of beverages to seed}';

    protected $description = 'Seed dummy beverage records with realistic EAN-13 barcodes for testing';

    public function handle(): int
    {
        $count = (int) $this->option('count');

        $this->info('Seeding beverage categories...');

        $categories = collect(['Beer', 'Soft Drink', 'Water', 'Spirits', 'Wine', 'Juice', 'Energy Drink', 'Cider'])->map(function ($name) {
            return BeverageCategory::updateOrCreate(
                ['name' => $name],
                ['description' => "{$name} category", 'is_active' => true]
            );
        });

        $this->info("Created {$categories->count()} categories.");
        $this->info("Seeding {$count} beverages...");

        $beerBrands = ['Serengeti', 'Kilimanjaro', 'Tusker', 'Castle', 'Windhoek', 'Ndovu', 'Chief'];
        $softBrands = ['Coca-Cola', 'Fanta', 'Sprite', 'Stoney', 'Mirinda', '7UP', 'Pepsi'];
        $waterBrands = ['Dasani', 'Aquafina', 'Evian', 'Kinyasini', 'Mount Kenya'];
        $spiritBrands = ['Johnnie Walker', 'Hennessy', 'Ballantines', 'Jack Daniels', 'Smirnoff'];
        $wineBrands = ['Four Cousins', 'Carling', 'Banrock', 'Jacobs Creek'];
        $juiceBrands = ['Del Monte', 'Five Alive', 'Tropicana', 'Ceres'];
        $energyBrands = ['Red Bull', 'Monster', 'Sting', 'Speed'];

        $brandMap = [
            'Beer' => $beerBrands,
            'Soft Drink' => $softBrands,
            'Water' => $waterBrands,
            'Spirits' => $spiritBrands,
            'Wine' => $wineBrands,
            'Juice' => $juiceBrands,
            'Energy Drink' => $energyBrands,
            'Cider' => ['Savanna', 'Strongbow', 'Hunter'],
        ];

        $units = ['bottle', 'can', 'crate', 'pack'];

        $bar = $this->output->createProgressBar($count);
        $bar->start();

        for ($i = 0; $i < $count; $i++) {
            $category = $categories->random();
            $brands = $brandMap[$category->name] ?? ['Generic'];
            $brand = fake()->randomElement($brands);
            $size = fake()->randomElement(['330ml', '500ml', '750ml', '1L', '2L']);
            $unit = $category->name === 'Beer' ? fake()->randomElement(['bottle', 'can', 'crate']) : fake()->randomElement($units);

            $barcode = $this->generateEAN13();

            while (Beverage::where('barcode', $barcode)->exists()) {
                $barcode = $this->generateEAN13();
            }

            $beverage = Beverage::create([
                'barcode' => $barcode,
                'name' => "{$brand} {$size}",
                'category_id' => $category->id,
                'unit' => $unit,
                'buying_price' => fake()->randomFloat(2, 500, 50000),
                'selling_price' => fake()->randomFloat(2, 1000, 80000),
                'reorder_level' => fake()->numberBetween(5, 50),
                'is_active' => true,
                'created_by' => null,
            ]);

            BeverageInventory::create([
                'beverage_id' => $beverage->id,
                'quantity_on_hand' => fake()->numberBetween(0, 200),
            ]);

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info("Done! Seeded {$count} beverages with EAN-13 barcodes.");

        return Command::SUCCESS;
    }

    private function generateEAN13(): string
    {
        $code = '';
        for ($i = 0; $i < 12; $i++) {
            $code .= fake()->numberBetween(0, 9);
        }

        $sum = 0;
        for ($i = 0; $i < 12; $i++) {
            $sum += (int) $code[$i] * ($i % 2 === 0 ? 1 : 3);
        }

        $check = (10 - ($sum % 10)) % 10;

        return $code . $check;
    }
}
