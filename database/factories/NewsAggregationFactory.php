<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\NewsAggregation>
 */
class NewsAggregationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(4),
            'summary' => fake()->text(300),
            'content' => fake()->text(1000),
            'source_url' => fake()->url(),
            'source_name' => fake()->company(),
            'author' => fake()->optional()->name(),
            'published_at' => fake()->dateTimeBetween('-1 month', 'now'),
            'category' => fake()->randomElement(['energy', 'environment', 'technology', 'business', 'politics', 'science']),
            'tags' => fake()->words(5),
            'language' => fake()->randomElement(['es', 'en', 'fr', 'de', 'it']),
            'country' => fake()->optional()->country(),
            'region' => fake()->optional()->word(),
            'sentiment' => fake()->optional()->randomElement(['positive', 'negative', 'neutral']),
            'importance_score' => fake()->randomFloat(1, 1, 10),
            'is_verified' => fake()->boolean(70),
            'verification_date' => fake()->optional()->dateTimeBetween('-1 month', 'now'),
            'duplicate_count' => fake()->numberBetween(0, 10),
            'engagement_score' => fake()->numberBetween(0, 1000),
        ];
    }

    /**
     * Indicate that the news is verified.
     */
    public function verified(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_verified' => true,
            'verification_date' => fake()->dateTimeBetween('-1 month', 'now'),
        ]);
    }
}


