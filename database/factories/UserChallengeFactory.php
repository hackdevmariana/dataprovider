<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UserChallenge>
 */
class UserChallengeFactory extends Factory
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
            'challenge_id' => fake()->numberBetween(1, 1000),
            'status' => fake()->randomElement(['not_started', 'in_progress', 'completed', 'failed', 'abandoned']),
            'started_at' => fake()->optional()->dateTimeBetween('-1 year', 'now'),
            'completed_at' => fake()->optional()->dateTimeBetween('-1 year', 'now'),
            'progress_percentage' => fake()->numberBetween(0, 100),
            'current_streak' => fake()->numberBetween(0, 365),
            'longest_streak' => fake()->numberBetween(0, 365),
            'total_points_earned' => fake()->numberBetween(0, 10000),
            'bonus_points' => fake()->numberBetween(0, 1000),
            'is_public' => fake()->boolean(70),
            'is_featured' => fake()->boolean(10),
            'notes' => fake()->optional()->sentence(),
            'metadata' => fake()->optional()->text(200),
        ];
    }

    /**
     * Indicate that the challenge is completed.
     */
    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'completed',
            'progress_percentage' => 100,
            'completed_at' => fake()->dateTimeBetween('-1 year', 'now'),
        ]);
    }
}


