<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UserPrivilege>
 */
class UserPrivilegeFactory extends Factory
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
            'privilege_name' => fake()->randomElement(['admin', 'moderator', 'expert', 'verified', 'premium', 'beta_tester']),
            'privilege_type' => fake()->randomElement(['system', 'community', 'feature', 'access']),
            'description' => fake()->text(200),
            'granted_by' => fake()->optional()->numberBetween(1, 1000),
            'granted_at' => fake()->dateTimeBetween('-1 year', 'now'),
            'expires_at' => fake()->optional()->dateTimeBetween('now', '+1 year'),
            'is_active' => fake()->boolean(90),
            'is_permanent' => fake()->boolean(70),
            'permissions' => fake()->words(5),
            'restrictions' => fake()->optional()->words(3),
            'notes' => fake()->optional()->sentence(),
        ];
    }

    /**
     * Indicate that the privilege is active.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => true,
        ]);
    }
}


