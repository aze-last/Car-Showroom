<?php

namespace Database\Factories;

use App\Models\Unit;
use App\Models\UnitStatusLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<UnitStatusLog>
 */
class UnitStatusLogFactory extends Factory
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
            'user_id' => User::factory(),
            'action' => UnitStatusLog::ACTION_SET_SOLD,
            'from_status' => Unit::STATUS_AVAILABLE,
            'to_status' => Unit::STATUS_SOLD,
            'request_id' => $this->faker->uuid(),
            'reason' => $this->faker->optional()->sentence(6),
            'changes' => [
                'status' => [
                    'from' => Unit::STATUS_AVAILABLE,
                    'to' => Unit::STATUS_SOLD,
                ],
            ],
            'ip_address' => $this->faker->ipv4(),
            'user_agent' => $this->faker->userAgent(),
        ];
    }
}
