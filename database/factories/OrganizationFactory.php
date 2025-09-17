<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Organization>
 */
class OrganizationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->company(),
            'legal_name' => fake()->company(),
            'description' => fake()->text(300),
            'organization_type' => fake()->randomElement(['company', 'ngo', 'government', 'cooperative', 'association', 'foundation']),
            'industry' => fake()->randomElement(['energy', 'technology', 'environment', 'finance', 'education', 'healthcare']),
            'founded_year' => fake()->numberBetween(1900, 2024),
            'headquarters' => fake()->city(),
            'country' => fake()->country(),
            'website' => fake()->optional()->url(),
            'email' => fake()->optional()->email(),
            'phone' => fake()->optional()->phoneNumber(),
            'address' => fake()->optional()->address(),
            'postal_code' => fake()->optional()->postcode(),
            'tax_id' => fake()->optional()->regexify('[A-Z]{2}[0-9]{8}'),
            'registration_number' => fake()->optional()->regexify('[A-Z]{2}[0-9]{6}'),
            'legal_form' => fake()->randomElement(['S.L.', 'S.A.', 'Sociedad Cooperativa', 'Asociación', 'Fundación']),
            'employees_count' => fake()->optional()->numberBetween(1, 10000),
            'annual_revenue_eur' => fake()->optional()->randomFloat(2, 10000, 1000000000),
            'mission' => fake()->optional()->text(200),
            'vision' => fake()->optional()->text(200),
            'values' => fake()->optional()->words(5),
            'is_active' => fake()->boolean(90),
            'is_verified' => fake()->boolean(70),
            'verification_date' => fake()->optional()->dateTimeBetween('-1 year', 'now'),
            'tags' => fake()->words(5),
        ];
    }

    /**
     * Indicate that the organization is verified.
     */
    public function verified(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_verified' => true,
            'verification_date' => fake()->dateTimeBetween('-1 year', 'now'),
        ]);
    }
}




