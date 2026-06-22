<?php

namespace Database\Factories;

use App\Models\Building;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Building>
 */
class BuildingFactory extends Factory
{
    protected $model = Building::class;

    public function definition(): array
    {
        return [
            'name' => fake()->company().' Building',
            'code' => strtoupper(fake()->unique()->lexify('BLD???')),
            'address' => fake()->address(),
            'is_active' => true,
        ];
    }
}
