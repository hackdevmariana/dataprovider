<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TrendingTopic>
 */
class TrendingTopicFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'topic_id' => fake()->numberBetween(1, 1000),
            'trending_score' => fake()->randomFloat(2, 0, 100),
            'trend_direction' => fake()->randomElement(['up', 'down', 'stable']),
            'trend_percentage' => fake()->randomFloat(2, -50, 200),
            'period' => fake()->randomElement(['hourly', 'daily', 'weekly', 'monthly']),
            'date' => fake()->dateTimeBetween('-1 month', 'now'),
            'rank' => fake()->numberBetween(1, 100),
            'previous_rank' => fake()->optional()->numberBetween(1, 100),
            'rank_change' => fake()->optional()->numberBetween(-50, 50),
            'engagement_count' => fake()->numberBetween(0, 10000),
            'post_count' => fake()->numberBetween(0, 1000),
            'comment_count' => fake()->numberBetween(0, 5000),
            'share_count' => fake()->numberBetween(0, 1000),
            'view_count' => fake()->numberBetween(0, 100000),
            'is_active' => fake()->boolean(90),
            'notes' => fake()->optional()->sentence(),
        ];
    }

    /**
     * Indicate that the topic is trending up.
     */
    public function trendingUp(): static
    {
        return $this->state(fn (array $attributes) => [
            'trend_direction' => 'up',
            'trend_percentage' => fake()->randomFloat(2, 10, 200),
        ]);
    }
}




