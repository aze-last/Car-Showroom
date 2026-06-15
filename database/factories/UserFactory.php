<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'job_title' => 'Showroom Staff',
            'email' => fake()->unique()->safeEmail(),
            'google_id' => null,
            'auth_provider' => 'password',
            'avatar' => null,
            'phone' => null,
            'preferred_locale' => 'en_PH',
            'preferred_timezone' => 'Asia/Manila',
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'is_admin' => false,
            'is_employee' => false,
            'remember_token' => Str::random(10),
            'two_factor_secret' => null,
            'two_factor_recovery_codes' => null,
            'two_factor_confirmed_at' => null,
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    /**
     * Indicate that the model has two-factor authentication configured.
     */
    public function withTwoFactor(): static
    {
        return $this->state(fn (array $attributes) => [
            'two_factor_secret' => encrypt('secret'),
            'two_factor_recovery_codes' => encrypt(json_encode(['recovery-code-1'])),
            'two_factor_confirmed_at' => now(),
        ]);
    }

    public function admin(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_admin' => true,
            'is_employee' => false,
        ]);
    }

    public function employee(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_admin' => false,
            'is_employee' => true,
        ]);
    }

    public function withGoogle(?string $googleId = null): static
    {
        return $this->state(fn (array $attributes) => [
            'google_id' => $googleId ?? fake()->unique()->numerify('google-##########'),
            'auth_provider' => 'google',
            'password' => null,
            'email_verified_at' => now(),
        ]);
    }
}
