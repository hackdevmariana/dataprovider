<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UserReputation>
 */
class UserReputationFactory extends Factory
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
            'reputation_score' => fake()->numberBetween(0, 10000),
            'reputation_level' => fake()->randomElement(['newbie', 'member', 'regular', 'expert', 'master', 'legend']),
            'reputation_category' => fake()->randomElement(['energy', 'community', 'expertise', 'leadership', 'innovation']),
            'points_earned' => fake()->numberBetween(0, 5000),
            'points_spent' => fake()->numberBetween(0, 1000),
            'achievements_count' => fake()->numberBetween(0, 100),
            'badges_count' => fake()->numberBetween(0, 50),
            'endorsements_count' => fake()->numberBetween(0, 100),
            'reviews_count' => fake()->numberBetween(0, 200),
            'projects_completed' => fake()->numberBetween(0, 50),
            'community_contributions' => fake()->numberBetween(0, 1000),
            'last_activity_date' => fake()->optional()->dateTimeBetween('-1 year', 'now'),
            'is_verified' => fake()->boolean(60),
            'verification_date' => fake()->optional()->dateTimeBetween('-1 year', 'now'),
            'notes' => fake()->optional()->sentence(),
        ];
    }

    /**
     * Indicate that the user has high reputation.
     */
    public function highReputation(): static
    {
        return $this->state(fn (array $attributes) => [
            'reputation_score' => fake()->numberBetween(5000, 10000),
            'reputation_level' => fake()->randomElement(['expert', 'master', 'legend']),
        ]);
    }
}




