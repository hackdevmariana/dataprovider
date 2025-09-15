<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CarbonSavingLog>
 */
class CarbonSavingLogFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'activity_type' => fake()->randomElement(['energy_saved', 'transportation_reduced', 'waste_reduced', 'lifestyle_change']),
            'description' => fake()->sentence(),
            'co2_saved_kg' => fake()->randomFloat(2, 0.1, 50),
            'energy_saved_kwh' => fake()->optional()->randomFloat(2, 1, 100),
            'cost_saved_eur' => fake()->optional()->randomFloat(2, 1, 100),
            'activity_date' => fake()->dateTimeBetween('-1 year', 'now'),
            'verification_method' => fake()->randomElement(['manual', 'automatic', 'verified']),
            'is_verified' => fake()->boolean(70),
            'verification_date' => fake()->optional()->dateTimeBetween('-6 months', 'now'),
            'notes' => fake()->optional()->sentence(),
            'tags' => fake()->words(3),
        ];
    }

    /**
     * Indicate that the log is verified.
     */
    public function verified(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_verified' => true,
            'verification_method' => 'verified',
            'verification_date' => fake()->dateTimeBetween('-6 months', 'now'),
        ]);
    }
}