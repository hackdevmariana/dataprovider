<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UserAchievement>
 */
class UserAchievementFactory extends Factory
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
            'achievement_id' => fake()->numberBetween(1, 1000),
            'unlocked_at' => fake()->dateTimeBetween('-1 year', 'now'),
            'progress_percentage' => fake()->numberBetween(0, 100),
            'is_completed' => fake()->boolean(80),
            'completion_date' => fake()->optional()->dateTimeBetween('-1 year', 'now'),
            'points_earned' => fake()->numberBetween(10, 1000),
            'badge_url' => fake()->optional()->url(),
            'certificate_url' => fake()->optional()->url(),
            'is_public' => fake()->boolean(70),
            'is_featured' => fake()->boolean(10),
            'notes' => fake()->optional()->sentence(),
        ];
    }

    /**
     * Indicate that the achievement is completed.
     */
    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_completed' => true,
            'progress_percentage' => 100,
            'completion_date' => fake()->dateTimeBetween('-1 year', 'now'),
        ]);
    }
}




