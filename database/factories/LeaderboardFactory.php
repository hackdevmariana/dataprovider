<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Leaderboard>
 */
class LeaderboardFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->sentence(3),
            'description' => fake()->text(200),
            'leaderboard_type' => fake()->randomElement(['energy_savings', 'carbon_reduction', 'points', 'achievements', 'community_impact']),
            'period' => fake()->randomElement(['daily', 'weekly', 'monthly', 'yearly', 'all_time']),
            'start_date' => fake()->dateTimeBetween('-1 year', 'now'),
            'end_date' => fake()->optional()->dateTimeBetween('now', '+1 year'),
            'is_active' => fake()->boolean(80),
            'is_public' => fake()->boolean(90),
            'max_entries' => fake()->optional()->numberBetween(10, 1000),
            'prize_description' => fake()->optional()->sentence(),
            'prize_value_eur' => fake()->optional()->randomFloat(2, 10, 1000),
            'rules' => fake()->optional()->text(300),
            'tags' => fake()->words(3),
        ];
    }

    /**
     * Indicate that the leaderboard is active.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => true,
            'is_public' => true,
        ]);
    }
}

