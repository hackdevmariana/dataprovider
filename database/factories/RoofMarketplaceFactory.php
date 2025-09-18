<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\RoofMarketplace>
 */
class RoofMarketplaceFactory extends Factory
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
            'roof_type' => fake()->randomElement(['flat', 'pitched', 'shed', 'gable', 'hip']),
            'roof_material' => fake()->randomElement(['tile', 'metal', 'concrete', 'membrane', 'shingle']),
            'roof_condition' => fake()->randomElement(['excellent', 'good', 'fair', 'poor']),
            'solar_potential_kwh' => fake()->randomFloat(2, 1000, 50000),
            'estimated_production_kwh' => fake()->randomFloat(2, 500, 25000),
            'price_per_kwh_eur' => fake()->randomFloat(4, 0.05, 0.30),
            'total_value_eur' => fake()->randomFloat(2, 1000, 100000),
            'contract_duration_years' => fake()->numberBetween(10, 25),
            'status' => fake()->randomElement(['available', 'reserved', 'sold', 'installed']),
            'listing_date' => fake()->dateTimeBetween('-1 year', 'now'),
            'expiry_date' => fake()->optional()->dateTimeBetween('now', '+1 year'),
            'view_count' => fake()->numberBetween(0, 1000),
            'inquiry_count' => fake()->numberBetween(0, 100),
            'offer_count' => fake()->numberBetween(0, 20),
            'highest_offer_eur' => fake()->optional()->randomFloat(2, 1000, 100000),
            'is_featured' => fake()->boolean(10),
            'is_verified' => fake()->boolean(70),
            'verification_date' => fake()->optional()->dateTimeBetween('-1 year', 'now'),
            'legal_documents' => fake()->optional()->words(3),
            'photos' => fake()->optional()->words(5),
            'description' => fake()->text(500),
            'notes' => fake()->optional()->sentence(),
        ];
    }

    /**
     * Indicate that the roof is available.
     */
    public function available(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'available',
            'is_verified' => true,
        ]);
    }
}








