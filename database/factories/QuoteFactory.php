<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Quote>
 */
class QuoteFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'text' => fake()->sentence(10),
            'author' => fake()->name(),
            'source' => fake()->optional()->sentence(2),
            'category' => fake()->randomElement(['inspirational', 'motivational', 'wisdom', 'humor', 'life', 'success', 'failure', 'love']),
            'language' => fake()->randomElement(['es', 'en', 'fr', 'de', 'it']),
            'context' => fake()->optional()->text(200),
            'date_quoted' => fake()->optional()->dateTimeBetween('-100 years', 'now'),
            'popularity_score' => fake()->numberBetween(0, 1000),
            'is_verified' => fake()->boolean(70),
            'verification_source' => fake()->optional()->sentence(),
            'tags' => fake()->words(5),
            'is_public' => fake()->boolean(90),
            'usage_count' => fake()->numberBetween(0, 100),
            'favorite_count' => fake()->numberBetween(0, 50),
            'notes' => fake()->optional()->sentence(),
        ];
    }

    /**
     * Indicate that the quote is inspirational.
     */
    public function inspirational(): static
    {
        return $this->state(fn (array $attributes) => [
            'category' => 'inspirational',
            'popularity_score' => fake()->numberBetween(500, 1000),
        ]);
    }
}

