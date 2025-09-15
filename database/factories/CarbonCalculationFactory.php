<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CarbonCalculation>
 */
class CarbonCalculationFactory extends Factory
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
            'calculation_type' => fake()->randomElement(['energy_consumption', 'transportation', 'food', 'waste', 'lifestyle']),
            'activity' => fake()->sentence(3),
            'amount' => fake()->randomFloat(2, 1, 1000),
            'unit' => fake()->randomElement(['kwh', 'km', 'kg', 'liters', 'hours']),
            'emission_factor' => fake()->randomFloat(4, 0.1, 2.0),
            'co2_emissions_kg' => fake()->randomFloat(2, 0.1, 100),
            'calculation_method' => fake()->randomElement(['direct', 'indirect', 'lifecycle']),
            'data_source' => fake()->randomElement(['official', 'estimated', 'measured', 'calculated']),
            'period_start' => fake()->dateTimeBetween('-1 year', 'now'),
            'period_end' => fake()->dateTimeBetween('now', '+1 month'),
            'notes' => fake()->optional()->sentence(),
            'is_verified' => fake()->boolean(60),
            'verification_date' => fake()->optional()->dateTimeBetween('-6 months', 'now'),
        ];
    }

    /**
     * Indicate that the calculation is verified.
     */
    public function verified(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_verified' => true,
            'verification_date' => fake()->dateTimeBetween('-6 months', 'now'),
        ]);
    }
}