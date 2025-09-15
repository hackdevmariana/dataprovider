<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UserBadge>
 */
class UserBadgeFactory extends Factory
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
            'badge_type' => fake()->randomElement(['energy_saver', 'solar_champion', 'eco_warrior', 'community_leader', 'early_adopter']),
            'badge_name' => fake()->sentence(2),
            'description' => fake()->text(200),
            'icon_url' => fake()->optional()->url(),
            'color' => fake()->optional()->hexColor(),
            'earned_at' => fake()->dateTimeBetween('-1 year', 'now'),
            'is_active' => fake()->boolean(90),
            'is_featured' => fake()->boolean(20),
            'is_public' => fake()->boolean(80),
            'points_value' => fake()->numberBetween(10, 500),
            'rarity' => fake()->randomElement(['common', 'uncommon', 'rare', 'epic', 'legendary']),
            'category' => fake()->randomElement(['energy', 'environment', 'community', 'achievement', 'special']),
            'requirements' => fake()->optional()->text(200),
            'notes' => fake()->optional()->sentence(),
        ];
    }

    /**
     * Indicate that the badge is rare.
     */
    public function rare(): static
    {
        return $this->state(fn (array $attributes) => [
            'rarity' => fake()->randomElement(['rare', 'epic', 'legendary']),
            'points_value' => fake()->numberBetween(100, 500),
        ]);
    }
}

