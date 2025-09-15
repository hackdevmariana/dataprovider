<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Color>
 */
class ColorFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->colorName(),
            'hex_code' => fake()->hexColor(),
            'rgb_red' => fake()->numberBetween(0, 255),
            'rgb_green' => fake()->numberBetween(0, 255),
            'rgb_blue' => fake()->numberBetween(0, 255),
            'hsl_hue' => fake()->numberBetween(0, 360),
            'hsl_saturation' => fake()->numberBetween(0, 100),
            'hsl_lightness' => fake()->numberBetween(0, 100),
            'category' => fake()->randomElement(['primary', 'secondary', 'accent', 'neutral', 'warm', 'cool']),
            'description' => fake()->optional()->sentence(),
            'usage_context' => fake()->optional()->randomElement(['brand', 'ui', 'print', 'web']),
            'is_accessible' => fake()->boolean(80),
            'contrast_ratio' => fake()->optional()->randomFloat(1, 1, 21),
        ];
    }

    /**
     * Indicate that the color is accessible.
     */
    public function accessible(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_accessible' => true,
            'contrast_ratio' => fake()->randomFloat(1, 4.5, 21),
        ]);
    }
}