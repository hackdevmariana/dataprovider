<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\NewsSource>
 */
class NewsSourceFactory extends Factory
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
            'url' => fake()->url(),
            'description' => fake()->text(200),
            'language' => fake()->randomElement(['es', 'en', 'fr', 'de', 'it']),
            'country' => fake()->country(),
            'category' => fake()->randomElement(['general', 'energy', 'environment', 'technology', 'business', 'politics']),
            'reliability_score' => fake()->randomFloat(1, 1, 10),
            'bias_score' => fake()->randomFloat(1, -5, 5),
            'update_frequency' => fake()->randomElement(['real_time', 'hourly', 'daily', 'weekly']),
            'last_update' => fake()->optional()->dateTimeBetween('-1 week', 'now'),
            'is_active' => fake()->boolean(90),
            'is_verified' => fake()->boolean(80),
            'verification_date' => fake()->optional()->dateTimeBetween('-1 year', 'now'),
            'contact_email' => fake()->optional()->email(),
            'rss_feed_url' => fake()->optional()->url(),
            'api_endpoint' => fake()->optional()->url(),
            'notes' => fake()->optional()->sentence(),
        ];
    }

    /**
     * Indicate that the source is reliable.
     */
    public function reliable(): static
    {
        return $this->state(fn (array $attributes) => [
            'reliability_score' => fake()->randomFloat(1, 7, 10),
            'is_verified' => true,
        ]);
    }
}


