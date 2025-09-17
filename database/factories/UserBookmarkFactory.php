<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UserBookmark>
 */
class UserBookmarkFactory extends Factory
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
            'bookmarkable_type' => fake()->randomElement(['post', 'article', 'project', 'topic', 'comment']),
            'bookmarkable_id' => fake()->numberBetween(1, 1000),
            'bookmark_type' => fake()->randomElement(['save', 'favorite', 'watch', 'follow']),
            'folder' => fake()->optional()->word(),
            'tags' => fake()->words(3),
            'notes' => fake()->optional()->sentence(),
            'is_public' => fake()->boolean(20),
            'is_archived' => fake()->boolean(10),
            'bookmarked_at' => fake()->dateTimeBetween('-1 year', 'now'),
            'last_accessed_at' => fake()->optional()->dateTimeBetween('-1 year', 'now'),
            'access_count' => fake()->numberBetween(0, 100),
            'metadata' => fake()->optional()->text(200),
        ];
    }

    /**
     * Indicate that the bookmark is public.
     */
    public function public(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_public' => true,
        ]);
    }
}




