<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Unit;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Unit>
 */
class UnitFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'category_id' => Category::factory(),
            'name' => ucwords($this->faker->words(3, true)),
            'price_php' => $this->faker->numberBetween(250000, 5000000),
            'description' => $this->faker->optional()->paragraph(),
            'status' => Unit::STATUS_AVAILABLE,
            'show_price' => true,
        ];
    }

    public function sold(): static
    {
        return $this->state(fn () => [
            'status' => Unit::STATUS_SOLD,
        ]);
    }

    public function hiddenPrice(): static
    {
        return $this->state(fn () => [
            'show_price' => false,
        ]);
    }
}
