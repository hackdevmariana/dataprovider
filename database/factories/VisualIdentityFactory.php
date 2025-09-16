<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\VisualIdentity>
 */
class VisualIdentityFactory extends Factory
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
            'logo_url' => fake()->optional()->url(),
            'avatar_url' => fake()->optional()->url(),
            'banner_url' => fake()->optional()->url(),
            'primary_color' => fake()->hexColor(),
            'secondary_color' => fake()->hexColor(),
            'accent_color' => fake()->optional()->hexColor(),
            'font_family' => fake()->optional()->randomElement(['Arial', 'Helvetica', 'Times New Roman', 'Georgia', 'Verdana']),
            'font_size' => fake()->optional()->numberBetween(12, 24),
            'theme' => fake()->randomElement(['light', 'dark', 'auto', 'custom']),
            'layout_style' => fake()->randomElement(['minimal', 'modern', 'classic', 'creative']),
            'brand_guidelines' => fake()->optional()->text(500),
            'is_public' => fake()->boolean(70),
            'is_active' => fake()->boolean(90),
            'version' => fake()->randomFloat(1, 1.0, 5.0),
            'last_updated' => fake()->dateTimeBetween('-1 year', 'now'),
            'notes' => fake()->optional()->sentence(),
        ];
    }

    /**
     * Indicate that the identity is active.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => true,
            'is_public' => true,
        ]);
    }
}


