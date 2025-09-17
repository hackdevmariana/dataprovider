<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PilgrimageSite>
 */
class PilgrimageSiteFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->sentence(3),
            'description' => fake()->text(500),
            'location' => fake()->city(),
            'country' => fake()->country(),
            'region' => fake()->optional()->word(),
            'latitude' => fake()->latitude(),
            'longitude' => fake()->longitude(),
            'site_type' => fake()->randomElement(['cathedral', 'basilica', 'monastery', 'shrine', 'chapel', 'sanctuary']),
            'religious_tradition' => fake()->randomElement(['catholic', 'orthodox', 'anglican', 'lutheran', 'methodist']),
            'patron_saint' => fake()->optional()->name(),
            'founding_year' => fake()->optional()->numberBetween(100, 2024),
            'architectural_style' => fake()->optional()->randomElement(['gothic', 'romanesque', 'baroque', 'renaissance', 'modern']),
            'pilgrimage_routes' => fake()->optional()->words(3),
            'annual_visitors' => fake()->optional()->numberBetween(1000, 1000000),
            'significance' => fake()->text(300),
            'history' => fake()->text(400),
            'religious_importance' => fake()->randomElement(['local', 'regional', 'national', 'international']),
            'facilities' => fake()->optional()->words(5),
            'accessibility' => fake()->optional()->text(200),
            'visiting_hours' => fake()->optional()->sentence(),
            'contact_info' => fake()->optional()->email(),
            'website' => fake()->optional()->url(),
            'is_active' => fake()->boolean(95),
            'tags' => fake()->words(5),
        ];
    }

    /**
     * Indicate that the site is internationally significant.
     */
    public function international(): static
    {
        return $this->state(fn (array $attributes) => [
            'religious_importance' => 'international',
            'annual_visitors' => fake()->numberBetween(100000, 1000000),
        ]);
    }
}




