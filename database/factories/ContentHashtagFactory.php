<?php

namespace Database\Factories;

use App\Models\Hashtag;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ContentHashtag>
 */
class ContentHashtagFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'hashtag_id' => Hashtag::factory(),
            'content_type' => fake()->randomElement(['post', 'article', 'comment', 'review']),
            'content_id' => fake()->numberBetween(1, 1000),
            'position' => fake()->numberBetween(1, 10),
            'is_primary' => fake()->boolean(20),
            'created_at' => fake()->dateTimeBetween('-1 year', 'now'),
        ];
    }

    /**
     * Indicate that the hashtag is primary.
     */
    public function primary(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_primary' => true,
            'position' => 1,
        ]);
    }
}




