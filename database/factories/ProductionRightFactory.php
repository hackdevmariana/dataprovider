<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProductionRight>
 */
class ProductionRightFactory extends Factory
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
            'property_address' => fake()->address(),
            'property_type' => fake()->randomElement(['residential', 'commercial', 'industrial', 'agricultural']),
            'roof_area_m2' => fake()->numberBetween(50, 2000),
            'solar_potential_kwh' => fake()->randomFloat(2, 1000, 50000),
            'estimated_production_kwh' => fake()->randomFloat(2, 500, 25000),
            'price_per_kwh_eur' => fake()->randomFloat(4, 0.05, 0.30),
            'total_value_eur' => fake()->randomFloat(2, 1000, 100000),
            'contract_duration_years' => fake()->numberBetween(10, 25),
            'start_date' => fake()->dateTimeBetween('-1 year', '+1 year'),
            'end_date' => fake()->dateTimeBetween('+10 years', '+25 years'),
            'status' => fake()->randomElement(['available', 'reserved', 'sold', 'installed']),
            'buyer_id' => fake()->optional()->numberBetween(1, 1000),
            'sale_date' => fake()->optional()->dateTimeBetween('-1 year', 'now'),
            'sale_price_eur' => fake()->optional()->randomFloat(2, 1000, 100000),
            'commission_rate' => fake()->optional()->randomFloat(2, 1, 10),
            'commission_amount_eur' => fake()->optional()->randomFloat(2, 10, 10000),
            'legal_documents' => fake()->optional()->words(3),
            'verification_status' => fake()->randomElement(['pending', 'verified', 'rejected']),
            'verification_date' => fake()->optional()->dateTimeBetween('-1 year', 'now'),
            'notes' => fake()->optional()->sentence(),
        ];
    }

    /**
     * Indicate that the right is sold.
     */
    public function sold(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'sold',
            'sale_date' => fake()->dateTimeBetween('-1 year', 'now'),
            'buyer_id' => fake()->numberBetween(1, 1000),
        ]);
    }
}




