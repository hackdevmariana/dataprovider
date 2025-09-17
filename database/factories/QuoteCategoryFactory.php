<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\QuoteCategory>
 */
class QuoteCategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->word(),
            'description' => fake()->text(200),
            'color' => fake()->hexColor(),
            'icon' => fake()->optional()->word(),
            'parent_category_id' => fake()->optional()->numberBetween(1, 100),
            'sort_order' => fake()->numberBetween(1, 100),
            'is_active' => fake()->boolean(90),
            'quote_count' => fake()->numberBetween(0, 1000),
            'tags' => fake()->words(3),
        ];
    }

    /**
     * Indicate that the category is active.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => true,
        ]);
    }
}




