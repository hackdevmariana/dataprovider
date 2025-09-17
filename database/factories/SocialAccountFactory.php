<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SocialAccount>
 */
class SocialAccountFactory extends Factory
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
            'platform' => fake()->randomElement(['facebook', 'twitter', 'instagram', 'linkedin', 'youtube', 'tiktok', 'github', 'discord']),
            'username' => fake()->userName(),
            'display_name' => fake()->optional()->name(),
            'profile_url' => fake()->optional()->url(),
            'avatar_url' => fake()->optional()->imageUrl(),
            'bio' => fake()->optional()->text(200),
            'follower_count' => fake()->numberBetween(0, 1000000),
            'following_count' => fake()->numberBetween(0, 10000),
            'post_count' => fake()->numberBetween(0, 10000),
            'is_verified' => fake()->boolean(10),
            'is_public' => fake()->boolean(80),
            'is_active' => fake()->boolean(90),
            'last_sync' => fake()->optional()->dateTimeBetween('-1 month', 'now'),
            'access_token' => fake()->optional()->sha256(),
            'refresh_token' => fake()->optional()->sha256(),
            'token_expires_at' => fake()->optional()->dateTimeBetween('now', '+1 year'),
            'permissions' => fake()->optional()->words(5),
            'metadata' => fake()->optional()->text(200),
            'notes' => fake()->optional()->sentence(),
        ];
    }

    /**
     * Indicate that the account is verified.
     */
    public function verified(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_verified' => true,
            'is_public' => true,
        ]);
    }
}




