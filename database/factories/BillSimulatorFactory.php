<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\BillSimulator>
 */
class BillSimulatorFactory extends Factory
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
            'name' => fake()->sentence(3),
            'description' => fake()->text(200),
            'current_consumption_kwh' => fake()->randomFloat(2, 100, 5000),
            'current_cost_eur' => fake()->randomFloat(2, 50, 1000),
            'simulated_consumption_kwh' => fake()->randomFloat(2, 50, 4000),
            'simulated_cost_eur' => fake()->randomFloat(2, 30, 800),
            'savings_percentage' => fake()->randomFloat(2, 5, 50),
            'savings_amount_eur' => fake()->randomFloat(2, 10, 300),
            'energy_source' => fake()->randomElement(['solar', 'wind', 'hydro', 'nuclear', 'gas', 'coal']),
            'installation_cost_eur' => fake()->randomFloat(2, 5000, 50000),
            'payback_period_years' => fake()->randomFloat(1, 3, 15),
            'co2_reduction_kg' => fake()->randomFloat(2, 100, 2000),
            'parameters' => [
                'tariff_type' => fake()->randomElement(['fixed', 'variable', 'time_of_use']),
                'peak_hours' => fake()->randomElement(['morning', 'afternoon', 'evening']),
                'off_peak_hours' => fake()->randomElement(['night', 'weekend']),
            ],
            'is_public' => fake()->boolean(20),
            'tags' => fake()->words(3),
        ];
    }

    /**
     * Indicate that the simulation shows high savings.
     */
    public function highSavings(): static
    {
        return $this->state(fn (array $attributes) => [
            'savings_percentage' => fake()->randomFloat(2, 30, 50),
            'savings_amount_eur' => fake()->randomFloat(2, 200, 500),
        ]);
    }
}