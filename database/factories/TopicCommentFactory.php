<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TopicComment>
 */
class TopicCommentFactory extends Factory
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
            'parent_comment_id' => fake()->optional()->numberBetween(1, 1000),
            'content' => fake()->text(500),
            'is_approved' => fake()->boolean(90),
            'is_pinned' => fake()->boolean(5),
            'is_edited' => fake()->boolean(20),
            'edit_count' => fake()->numberBetween(0, 10),
            'last_edited_at' => fake()->optional()->dateTimeBetween('-1 year', 'now'),
            'likes_count' => fake()->numberBetween(0, 100),
            'dislikes_count' => fake()->numberBetween(0, 20),
            'replies_count' => fake()->numberBetween(0, 50),
            'reports_count' => fake()->numberBetween(0, 5),
            'is_anonymous' => fake()->boolean(10),
            'ip_address' => fake()->optional()->ipv4(),
            'user_agent' => fake()->optional()->userAgent(),
            'metadata' => fake()->optional()->text(200),
            'notes' => fake()->optional()->sentence(),
        ];
    }

    /**
     * Indicate that the comment is approved.
     */
    public function approved(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_approved' => true,
        ]);
    }
}




