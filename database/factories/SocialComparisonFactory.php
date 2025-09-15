<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SocialComparison>
 */
class SocialComparisonFactory extends Factory
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
            'comparison_type' => fake()->randomElement(['energy_savings', 'carbon_footprint', 'solar_generation', 'efficiency_rating', 'cost_savings']),
            'period' => fake()->randomElement(['daily', 'weekly', 'monthly', 'yearly']),
            'user_value' => fake()->randomFloat(2, 0, 1000),
            'user_unit' => fake()->randomElement(['kwh', 'kg_co2', 'eur', 'percentage']),
            'average_value' => fake()->randomFloat(2, 0, 1000),
            'average_unit' => fake()->randomElement(['kwh', 'kg_co2', 'eur', 'percentage']),
            'percentile' => fake()->numberBetween(1, 100),
            'rank' => fake()->numberBetween(1, 1000),
            'total_participants' => fake()->numberBetween(100, 10000),
            'comparison_date' => fake()->dateTimeBetween('-1 year', 'now'),
            'region' => fake()->optional()->word(),
            'country' => fake()->optional()->country(),
            'age_group' => fake()->optional()->randomElement(['18-25', '26-35', '36-45', '46-55', '56-65', '65+']),
            'income_group' => fake()->optional()->randomElement(['low', 'medium', 'high', 'very_high']),
            'household_size' => fake()->optional()->numberBetween(1, 10),
            'property_type' => fake()->optional()->randomElement(['apartment', 'house', 'condo', 'townhouse']),
            'is_anonymous' => fake()->boolean(20),
            'notes' => fake()->optional()->sentence(),
        ];
    }

    /**
     * Indicate that the comparison is for energy savings.
     */
    public function energySavings(): static
    {
        return $this->state(fn (array $attributes) => [
            'comparison_type' => 'energy_savings',
            'user_unit' => 'kwh',
            'average_unit' => 'kwh',
        ]);
    }
}

