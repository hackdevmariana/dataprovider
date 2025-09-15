<?php

namespace Database\Factories;

use App\Models\Cooperative;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CooperativePost>
 */
class CooperativePostFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'cooperative_id' => Cooperative::factory(),
            'user_id' => User::factory(),
            'title' => fake()->sentence(4),
            'content' => fake()->text(500),
            'post_type' => fake()->randomElement(['announcement', 'update', 'discussion', 'event', 'news']),
            'is_pinned' => fake()->boolean(10),
            'is_public' => fake()->boolean(80),
            'allow_comments' => fake()->boolean(90),
            'tags' => fake()->words(3),
            'attachment_url' => fake()->optional()->url(),
            'attachment_type' => fake()->optional()->randomElement(['image', 'document', 'video']),
            'views_count' => fake()->numberBetween(0, 1000),
            'likes_count' => fake()->numberBetween(0, 100),
            'comments_count' => fake()->numberBetween(0, 50),
            'status' => fake()->randomElement(['published', 'draft', 'archived']),
        ];
    }

    /**
     * Indicate that the post is pinned.
     */
    public function pinned(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_pinned' => true,
            'status' => 'published',
        ]);
    }
}

