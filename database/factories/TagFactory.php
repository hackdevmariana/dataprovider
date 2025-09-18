<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Tag>
 */
class TagFactory extends Factory
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
            'slug' => fake()->slug(),
            'description' => fake()->optional()->text(200),
            'color' => fake()->optional()->hexColor(),
            'category' => fake()->optional()->randomElement(['energy', 'technology', 'environment', 'business', 'lifestyle']),
            'usage_count' => fake()->numberBetween(0, 1000),
            'is_active' => fake()->boolean(90),
            'is_trending' => fake()->boolean(10),
            'is_featured' => fake()->boolean(5),
            'created_by' => fake()->optional()->numberBetween(1, 1000),
            'parent_tag_id' => fake()->optional()->numberBetween(1, 100),
            'sort_order' => fake()->numberBetween(1, 100),
            'metadata' => fake()->optional()->text(200),
            'notes' => fake()->optional()->sentence(),
        ];
    }

    /**
     * Indicate that the tag is trending.
     */
    public function trending(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_trending' => true,
            'usage_count' => fake()->numberBetween(100, 1000),
        ]);
    }
}








