<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Stat>
 */
class StatFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->sentence(2),
            'value' => fake()->randomFloat(2, 0, 1000000),
            'unit' => fake()->randomElement(['count', 'percentage', 'eur', 'kwh', 'kg', 'users', 'projects']),
            'category' => fake()->randomElement(['user', 'energy', 'financial', 'environmental', 'system', 'engagement']),
            'period' => fake()->randomElement(['daily', 'weekly', 'monthly', 'yearly', 'all_time']),
            'date' => fake()->dateTimeBetween('-1 year', 'now'),
            'description' => fake()->optional()->text(200),
            'source' => fake()->optional()->word(),
            'is_public' => fake()->boolean(80),
            'is_active' => fake()->boolean(90),
            'metadata' => fake()->optional()->text(200),
            'notes' => fake()->optional()->sentence(),
        ];
    }

    /**
     * Indicate that the stat is public.
     */
    public function public(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_public' => true,
            'is_active' => true,
        ]);
    }
}








