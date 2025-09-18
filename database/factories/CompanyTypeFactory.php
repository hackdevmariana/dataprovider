<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CompanyType>
 */
class CompanyTypeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->randomElement(['S.L.', 'S.A.', 'Sociedad Cooperativa', 'Sociedad Anónima', 'Sociedad Limitada']),
            'description' => fake()->sentence(),
            'legal_form' => fake()->randomElement(['corporation', 'partnership', 'cooperative', 'nonprofit']),
            'country' => fake()->country(),
            'jurisdiction' => fake()->optional()->word(),
            'tax_classification' => fake()->optional()->word(),
            'liability_type' => fake()->randomElement(['limited', 'unlimited', 'joint']),
            'minimum_capital' => fake()->optional()->randomFloat(2, 1000, 100000),
            'currency' => fake()->randomElement(['EUR', 'USD', 'GBP']),
            'registration_requirements' => fake()->optional()->text(200),
            'is_active' => fake()->boolean(90),
        ];
    }

    /**
     * Indicate that the company type is for Spain.
     */
    public function spanish(): static
    {
        return $this->state(fn (array $attributes) => [
            'country' => 'Spain',
            'currency' => 'EUR',
            'name' => fake()->randomElement(['S.L.', 'S.A.', 'Sociedad Cooperativa']),
        ]);
    }
}








