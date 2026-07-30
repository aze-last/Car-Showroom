<?php

namespace Database\Factories;

use App\Models\Unit;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UnitView>
 */
class UnitViewFactory extends Factory
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
            'user_id' => null,
            'visitor_hash' => hash('sha256', $this->faker->ipv4().'|'.$this->faker->userAgent()),
            'viewed_at' => now(),
        ];
    }
}
