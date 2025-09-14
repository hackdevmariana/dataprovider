<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Devotion>
 */
class DevotionFactory extends Factory
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
            'description' => fake()->text(300),
            'devotion_type' => fake()->randomElement(['prayer', 'novena', 'litany', 'hymn', 'meditation']),
            'origin' => fake()->optional()->sentence(),
            'tradition' => fake()->randomElement(['catholic', 'orthodox', 'anglican', 'lutheran']),
            'language' => fake()->randomElement(['latin', 'spanish', 'english', 'french', 'italian']),
            'duration_minutes' => fake()->optional()->numberBetween(5, 120),
            'frequency' => fake()->randomElement(['daily', 'weekly', 'monthly', 'yearly', 'special']),
            'season' => fake()->optional()->randomElement(['advent', 'christmas', 'lent', 'easter', 'ordinary']),
            'is_approved' => fake()->boolean(80),
            'approval_date' => fake()->optional()->dateTimeBetween('-10 years', 'now'),
            'notes' => fake()->optional()->sentence(),
        ];
    }

    /**
     * Indicate that the devotion is approved.
     */
    public function approved(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_approved' => true,
            'approval_date' => fake()->dateTimeBetween('-10 years', 'now'),
        ]);
    }
}
