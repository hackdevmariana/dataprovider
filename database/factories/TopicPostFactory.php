<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TopicPost>
 */
class TopicPostFactory extends Factory
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
            'title' => fake()->sentence(4),
            'content' => fake()->text(1000),
            'post_type' => fake()->randomElement(['discussion', 'question', 'announcement', 'poll', 'event']),
            'is_pinned' => fake()->boolean(5),
            'is_locked' => fake()->boolean(10),
            'is_approved' => fake()->boolean(90),
            'is_edited' => fake()->boolean(20),
            'edit_count' => fake()->numberBetween(0, 10),
            'last_edited_at' => fake()->optional()->dateTimeBetween('-1 year', 'now'),
            'views_count' => fake()->numberBetween(0, 1000),
            'likes_count' => fake()->numberBetween(0, 100),
            'dislikes_count' => fake()->numberBetween(0, 20),
            'comments_count' => fake()->numberBetween(0, 50),
            'shares_count' => fake()->numberBetween(0, 25),
            'reports_count' => fake()->numberBetween(0, 5),
            'is_anonymous' => fake()->boolean(10),
            'tags' => fake()->words(5),
            'attachments' => fake()->optional()->words(3),
            'metadata' => fake()->optional()->text(200),
            'notes' => fake()->optional()->sentence(),
        ];
    }

    /**
     * Indicate that the post is pinned.
     */
    public function pinned(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_pinned' => true,
            'is_approved' => true,
        ]);
    }
}








