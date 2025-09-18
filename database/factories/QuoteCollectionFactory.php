<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\QuoteCollection>
 */
class QuoteCollectionFactory extends Factory
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
            'name' => fake()->sentence(3),
            'description' => fake()->text(300),
            'theme' => fake()->randomElement(['daily_inspiration', 'motivational', 'wisdom', 'humor', 'life_lessons']),
            'is_public' => fake()->boolean(70),
            'is_featured' => fake()->boolean(10),
            'quote_count' => fake()->numberBetween(0, 100),
            'follower_count' => fake()->numberBetween(0, 1000),
            'view_count' => fake()->numberBetween(0, 5000),
            'tags' => fake()->words(5),
            'created_at' => fake()->dateTimeBetween('-1 year', 'now'),
        ];
    }

    /**
     * Indicate that the collection is public.
     */
    public function public(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_public' => true,
            'is_featured' => fake()->boolean(20),
        ]);
    }
}








