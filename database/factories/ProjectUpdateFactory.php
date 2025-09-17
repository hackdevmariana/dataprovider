<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProjectUpdate>
 */
class ProjectUpdateFactory extends Factory
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
            'project_id' => fake()->numberBetween(1, 1000),
            'update_type' => fake()->randomElement(['progress', 'milestone', 'issue', 'completion', 'delay']),
            'title' => fake()->sentence(4),
            'content' => fake()->text(500),
            'progress_percentage' => fake()->optional()->numberBetween(0, 100),
            'milestone_reached' => fake()->optional()->sentence(),
            'issues_encountered' => fake()->optional()->text(200),
            'solutions_implemented' => fake()->optional()->text(200),
            'next_steps' => fake()->optional()->text(200),
            'estimated_completion_date' => fake()->optional()->dateTimeBetween('now', '+1 year'),
            'actual_completion_date' => fake()->optional()->dateTimeBetween('-1 year', 'now'),
            'budget_impact_eur' => fake()->optional()->randomFloat(2, -10000, 10000),
            'timeline_impact_days' => fake()->optional()->numberBetween(-30, 30),
            'attachments' => fake()->optional()->words(2),
            'is_public' => fake()->boolean(80),
            'notify_stakeholders' => fake()->boolean(90),
            'tags' => fake()->words(3),
        ];
    }

    /**
     * Indicate that the update is a milestone.
     */
    public function milestone(): static
    {
        return $this->state(fn (array $attributes) => [
            'update_type' => 'milestone',
            'progress_percentage' => fake()->numberBetween(25, 100),
        ]);
    }
}




