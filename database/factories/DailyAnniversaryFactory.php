<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\DailyAnniversary>
 */
class DailyAnniversaryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'date' => fake()->date(),
            'anniversary_type' => fake()->randomElement(['birth', 'death', 'event', 'achievement', 'historical']),
            'title' => fake()->sentence(4),
            'description' => fake()->text(300),
            'person_name' => fake()->optional()->name(),
            'year' => fake()->optional()->numberBetween(1800, 2024),
            'category' => fake()->randomElement(['historical', 'cultural', 'scientific', 'political', 'religious']),
            'importance_level' => fake()->randomElement(['low', 'medium', 'high', 'very_high']),
            'country' => fake()->optional()->country(),
            'tags' => fake()->words(3),
            'is_public' => fake()->boolean(90),
            'source' => fake()->optional()->sentence(),
        ];
    }

    /**
     * Indicate that the anniversary is historical.
     */
    public function historical(): static
    {
        return $this->state(fn (array $attributes) => [
            'anniversary_type' => 'historical',
            'category' => 'historical',
            'importance_level' => fake()->randomElement(['high', 'very_high']),
        ]);
    }
}


