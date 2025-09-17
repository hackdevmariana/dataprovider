<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Font>
 */
class FontFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->word() . ' ' . fake()->randomElement(['Regular', 'Bold', 'Light', 'Medium', 'Heavy']),
            'family' => fake()->randomElement(['serif', 'sans-serif', 'monospace', 'script', 'display']),
            'style' => fake()->randomElement(['normal', 'italic', 'oblique']),
            'weight' => fake()->randomElement(['100', '200', '300', '400', '500', '600', '700', '800', '900']),
            'file_path' => fake()->filePath(),
            'file_size_kb' => fake()->numberBetween(10, 1000),
            'format' => fake()->randomElement(['ttf', 'otf', 'woff', 'woff2', 'eot']),
            'license' => fake()->randomElement(['free', 'commercial', 'open_source', 'custom']),
            'author' => fake()->optional()->name(),
            'version' => fake()->randomFloat(1, 1.0, 5.0),
            'description' => fake()->optional()->sentence(),
            'languages_supported' => fake()->randomElements(['latin', 'cyrillic', 'greek', 'arabic', 'chinese'], fake()->numberBetween(1, 3)),
            'is_active' => fake()->boolean(90),
            'usage_count' => fake()->numberBetween(0, 1000),
        ];
    }

    /**
     * Indicate that the font is free.
     */
    public function free(): static
    {
        return $this->state(fn (array $attributes) => [
            'license' => 'free',
        ]);
    }
}




