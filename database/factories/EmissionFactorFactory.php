<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\EmissionFactor>
 */
class EmissionFactorFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'activity' => fake()->sentence(3),
            'emission_factor' => fake()->randomFloat(4, 0.1, 2.0),
            'unit' => fake()->randomElement(['kg CO2/kWh', 'kg CO2/km', 'kg CO2/kg', 'kg CO2/liter']),
            'category' => fake()->randomElement(['energy', 'transportation', 'food', 'waste', 'lifestyle']),
            'subcategory' => fake()->optional()->word(),
            'country' => fake()->optional()->country(),
            'year' => fake()->numberBetween(2020, 2024),
            'source' => fake()->company(),
            'methodology' => fake()->optional()->sentence(),
            'uncertainty' => fake()->optional()->randomFloat(2, 0.1, 0.5),
            'is_active' => fake()->boolean(90),
            'notes' => fake()->optional()->sentence(),
        ];
    }

    /**
     * Indicate that the factor is for energy.
     */
    public function energy(): static
    {
        return $this->state(fn (array $attributes) => [
            'category' => 'energy',
            'unit' => 'kg CO2/kWh',
            'emission_factor' => fake()->randomFloat(4, 0.1, 1.0),
        ]);
    }
}

