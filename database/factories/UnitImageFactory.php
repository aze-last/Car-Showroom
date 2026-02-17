<?php

namespace Database\Factories;

use App\Models\Unit;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\UnitImage>
 */
class UnitImageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'unit_id' => Unit::factory(),
            'url' => 'units/'.$this->faker->numberBetween(1, 999).'/'.$this->faker->uuid().'.jpg',
            'sort_order' => $this->faker->numberBetween(0, 8),
        ];
    }
}
