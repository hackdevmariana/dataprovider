<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SponsoredContent>
 */
class SponsoredContentFactory extends Factory
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
            'sponsor_name' => fake()->company(),
            'sponsor_email' => fake()->optional()->email(),
            'sponsor_website' => fake()->optional()->url(),
            'content_type' => fake()->randomElement(['post', 'article', 'video', 'image', 'story']),
            'title' => fake()->sentence(4),
            'content' => fake()->text(1000),
            'media_url' => fake()->optional()->url(),
            'media_type' => fake()->optional()->randomElement(['image', 'video', 'audio', 'document']),
            'target_audience' => fake()->optional()->words(3),
            'budget_eur' => fake()->randomFloat(2, 100, 10000),
            'cost_per_click_eur' => fake()->optional()->randomFloat(4, 0.01, 2.00),
            'cost_per_impression_eur' => fake()->optional()->randomFloat(4, 0.001, 0.10),
            'start_date' => fake()->dateTimeBetween('-1 year', 'now'),
            'end_date' => fake()->dateTimeBetween('now', '+1 year'),
            'status' => fake()->randomElement(['draft', 'pending', 'approved', 'active', 'paused', 'completed', 'cancelled']),
            'impressions' => fake()->numberBetween(0, 100000),
            'clicks' => fake()->numberBetween(0, 10000),
            'engagement_rate' => fake()->optional()->randomFloat(2, 0.1, 10.0),
            'conversion_rate' => fake()->optional()->randomFloat(2, 0.1, 5.0),
            'is_verified' => fake()->boolean(70),
            'verification_date' => fake()->optional()->dateTimeBetween('-1 year', 'now'),
            'notes' => fake()->optional()->sentence(),
        ];
    }

    /**
     * Indicate that the content is active.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'active',
            'start_date' => fake()->dateTimeBetween('-1 month', 'now'),
            'end_date' => fake()->dateTimeBetween('now', '+1 month'),
        ]);
    }
}


