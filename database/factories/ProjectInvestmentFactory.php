<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProjectInvestment>
 */
class ProjectInvestmentFactory extends Factory
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
            'project_id' => fake()->numberBetween(1, 1000),
            'investment_amount_eur' => fake()->randomFloat(2, 100, 100000),
            'investment_type' => fake()->randomElement(['equity', 'debt', 'crowdfunding', 'grant', 'loan']),
            'investment_date' => fake()->dateTimeBetween('-1 year', 'now'),
            'expected_return_rate' => fake()->optional()->randomFloat(2, 2, 20),
            'expected_return_amount_eur' => fake()->optional()->randomFloat(2, 200, 20000),
            'maturity_date' => fake()->optional()->dateTimeBetween('now', '+10 years'),
            'status' => fake()->randomElement(['active', 'completed', 'defaulted', 'cancelled']),
            'risk_level' => fake()->randomElement(['low', 'medium', 'high', 'very_high']),
            'sector' => fake()->randomElement(['renewable_energy', 'energy_efficiency', 'sustainability', 'technology']),
            'project_stage' => fake()->randomElement(['planning', 'development', 'construction', 'operation']),
            'expected_irr' => fake()->optional()->randomFloat(2, 5, 25),
            'expected_payback_years' => fake()->optional()->randomFloat(1, 3, 15),
            'guarantees' => fake()->optional()->text(200),
            'terms_and_conditions' => fake()->optional()->text(300),
            'notes' => fake()->optional()->sentence(),
        ];
    }

    /**
     * Indicate that the investment is active.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'active',
            'risk_level' => fake()->randomElement(['low', 'medium', 'high']),
        ]);
    }
}


