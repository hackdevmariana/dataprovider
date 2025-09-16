<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ContentVote>
 */
class ContentVoteFactory extends Factory
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
            'content_type' => fake()->randomElement(['post', 'article', 'comment', 'review']),
            'content_id' => fake()->numberBetween(1, 1000),
            'vote_type' => fake()->randomElement(['upvote', 'downvote', 'like', 'dislike']),
            'vote_value' => fake()->randomElement([1, -1]),
            'created_at' => fake()->dateTimeBetween('-1 year', 'now'),
        ];
    }

    /**
     * Indicate that the vote is positive.
     */
    public function positive(): static
    {
        return $this->state(fn (array $attributes) => [
            'vote_type' => fake()->randomElement(['upvote', 'like']),
            'vote_value' => 1,
        ]);
    }

    /**
     * Indicate that the vote is negative.
     */
    public function negative(): static
    {
        return $this->state(fn (array $attributes) => [
            'vote_type' => fake()->randomElement(['downvote', 'dislike']),
            'vote_value' => -1,
        ]);
    }
}


