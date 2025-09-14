<?php

namespace Database\Factories;

use App\Models\Organization;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CompanyCertification>
 */
class CompanyCertificationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'organization_id' => Organization::factory(),
            'certification_name' => fake()->sentence(3),
            'certification_body' => fake()->company(),
            'certification_number' => fake()->unique()->regexify('[A-Z]{2}[0-9]{6}'),
            'certification_type' => fake()->randomElement(['iso', 'energy', 'environmental', 'quality', 'safety']),
            'issue_date' => fake()->dateTimeBetween('-5 years', 'now'),
            'expiry_date' => fake()->dateTimeBetween('now', '+3 years'),
            'status' => fake()->randomElement(['active', 'expired', 'suspended', 'revoked']),
            'scope' => fake()->optional()->text(200),
            'certificate_url' => fake()->optional()->url(),
            'verification_url' => fake()->optional()->url(),
            'notes' => fake()->optional()->sentence(),
            'is_public' => fake()->boolean(80),
        ];
    }

    /**
     * Indicate that the certification is active.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'active',
            'expiry_date' => fake()->dateTimeBetween('now', '+3 years'),
        ]);
    }
}