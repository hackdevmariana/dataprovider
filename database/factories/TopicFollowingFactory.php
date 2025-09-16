<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TopicFollowing>
 */
class TopicFollowingFactory extends Factory
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
            'follow_type' => fake()->randomElement(['follow', 'subscribe', 'bookmark', 'mute']),
            'notification_preferences' => fake()->randomElement(['all', 'mentions_only', 'none']),
            'is_active' => fake()->boolean(90),
            'followed_at' => fake()->dateTimeBetween('-1 year', 'now'),
            'unfollowed_at' => fake()->optional()->dateTimeBetween('-1 year', 'now'),
            'reason' => fake()->optional()->sentence(),
            'notes' => fake()->optional()->sentence(),
        ];
    }

    /**
     * Indicate that the following is active.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => true,
            'unfollowed_at' => null,
        ]);
    }
}


