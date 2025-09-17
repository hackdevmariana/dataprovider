<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Timeline>
 */
class TimelineFactory extends Factory
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
            'title' => fake()->sentence(4),
            'description' => fake()->text(300),
            'timeline_type' => fake()->randomElement(['personal', 'project', 'achievement', 'milestone', 'event']),
            'start_date' => fake()->dateTimeBetween('-10 years', 'now'),
            'end_date' => fake()->optional()->dateTimeBetween('-5 years', 'now'),
            'is_completed' => fake()->boolean(60),
            'completion_date' => fake()->optional()->dateTimeBetween('-5 years', 'now'),
            'priority' => fake()->randomElement(['low', 'medium', 'high', 'urgent']),
            'status' => fake()->randomElement(['planning', 'in_progress', 'completed', 'cancelled', 'on_hold']),
            'progress_percentage' => fake()->numberBetween(0, 100),
            'milestones' => fake()->optional()->words(5),
            'achievements' => fake()->optional()->text(200),
            'challenges' => fake()->optional()->text(200),
            'lessons_learned' => fake()->optional()->text(200),
            'is_public' => fake()->boolean(70),
            'is_featured' => fake()->boolean(10),
            'tags' => fake()->words(5),
            'metadata' => fake()->optional()->text(200),
        ];
    }

    /**
     * Indicate that the timeline is completed.
     */
    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_completed' => true,
            'status' => 'completed',
            'progress_percentage' => 100,
            'completion_date' => fake()->dateTimeBetween('-5 years', 'now'),
        ]);
    }
}




