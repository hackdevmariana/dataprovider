<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ListItem>
 */
class ListItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'list_id' => fake()->numberBetween(1, 1000),
            'item_type' => fake()->randomElement(['text', 'link', 'image', 'file', 'checklist']),
            'title' => fake()->sentence(3),
            'description' => fake()->optional()->text(200),
            'content' => fake()->optional()->text(500),
            'url' => fake()->optional()->url(),
            'image_url' => fake()->optional()->imageUrl(),
            'file_path' => fake()->optional()->filePath(),
            'position' => fake()->numberBetween(1, 100),
            'is_completed' => fake()->boolean(30),
            'completed_at' => fake()->optional()->dateTimeBetween('-1 year', 'now'),
            'due_date' => fake()->optional()->dateTimeBetween('now', '+1 year'),
            'priority' => fake()->randomElement(['low', 'medium', 'high', 'urgent']),
            'tags' => fake()->words(3),
            'metadata' => fake()->optional()->text(200),
        ];
    }

    /**
     * Indicate that the item is completed.
     */
    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_completed' => true,
            'completed_at' => fake()->dateTimeBetween('-1 year', 'now'),
        ]);
    }
}




