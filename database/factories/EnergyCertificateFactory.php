<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\EnergyCertificate>
 */
class EnergyCertificateFactory extends Factory
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
            'property_type' => fake()->randomElement(['residential', 'commercial', 'industrial']),
            'energy_rating' => fake()->randomElement(['A', 'B', 'C', 'D', 'E', 'F', 'G']),
            'energy_consumption_kwh' => fake()->randomFloat(2, 50, 5000),
            'co2_emissions_kg' => fake()->randomFloat(2, 10, 1000),
            'certificate_number' => fake()->unique()->regexify('[A-Z]{2}[0-9]{8}'),
            'issue_date' => fake()->dateTimeBetween('-5 years', 'now'),
            'expiry_date' => fake()->dateTimeBetween('now', '+5 years'),
            'certifier_name' => fake()->company(),
            'certifier_license' => fake()->optional()->regexify('[A-Z]{2}[0-9]{6}'),
            'property_area_m2' => fake()->numberBetween(50, 1000),
            'heating_system' => fake()->randomElement(['gas', 'electric', 'heat_pump', 'solar', 'biomass']),
            'hot_water_system' => fake()->randomElement(['gas', 'electric', 'solar', 'heat_pump']),
            'cooling_system' => fake()->optional()->randomElement(['air_conditioning', 'heat_pump', 'natural_ventilation']),
            'renewable_energy' => fake()->optional()->randomElement(['solar', 'wind', 'geothermal', 'biomass']),
            'improvement_recommendations' => fake()->optional()->text(500),
            'is_valid' => fake()->boolean(95),
        ];
    }

    /**
     * Indicate that the certificate has high energy rating.
     */
    public function highRating(): static
    {
        return $this->state(fn (array $attributes) => [
            'energy_rating' => fake()->randomElement(['A', 'B']),
            'energy_consumption_kwh' => fake()->randomFloat(2, 50, 200),
        ]);
    }
}




