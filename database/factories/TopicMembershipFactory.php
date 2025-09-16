<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TopicMembership>
 */
class TopicMembershipFactory extends Factory
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
            'topic_id' => fake()->numberBetween(1, 1000),
            'role' => fake()->randomElement(['member', 'moderator', 'admin', 'owner']),
            'permissions' => fake()->words(5),
            'joined_at' => fake()->dateTimeBetween('-1 year', 'now'),
            'left_at' => fake()->optional()->dateTimeBetween('-1 year', 'now'),
            'is_active' => fake()->boolean(90),
            'is_banned' => fake()->boolean(5),
            'ban_reason' => fake()->optional()->sentence(),
            'ban_expires_at' => fake()->optional()->dateTimeBetween('now', '+1 year'),
            'contribution_score' => fake()->numberBetween(0, 1000),
            'post_count' => fake()->numberBetween(0, 100),
            'comment_count' => fake()->numberBetween(0, 500),
            'like_count' => fake()->numberBetween(0, 1000),
            'notes' => fake()->optional()->sentence(),
        ];
    }

    /**
     * Indicate that the membership is active.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => true,
            'is_banned' => false,
            'left_at' => null,
        ]);
    }
}


