<?php

namespace Database\Factories;

use App\Models\Organization;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\OrganizationFeature>
 */
class OrganizationFeatureFactory extends Factory
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
            'feature_name' => fake()->sentence(2),
            'feature_type' => fake()->randomElement(['service', 'product', 'capability', 'certification', 'award']),
            'description' => fake()->text(300),
            'category' => fake()->randomElement(['energy', 'sustainability', 'technology', 'innovation', 'quality']),
            'is_active' => fake()->boolean(90),
            'is_public' => fake()->boolean(80),
            'start_date' => fake()->optional()->dateTimeBetween('-5 years', 'now'),
            'end_date' => fake()->optional()->dateTimeBetween('now', '+5 years'),
            'certification_body' => fake()->optional()->company(),
            'certification_number' => fake()->optional()->regexify('[A-Z]{2}[0-9]{6}'),
            'award_organization' => fake()->optional()->company(),
            'award_year' => fake()->optional()->numberBetween(2020, 2024),
            'value_proposition' => fake()->optional()->text(200),
            'target_audience' => fake()->optional()->words(3),
            'competitive_advantage' => fake()->optional()->text(200),
            'notes' => fake()->optional()->sentence(),
        ];
    }

    /**
     * Indicate that the feature is active.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => true,
            'is_public' => true,
        ]);
    }
}


