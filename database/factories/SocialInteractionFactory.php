<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SocialInteraction>
 */
class SocialInteractionFactory extends Factory
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
            'interactable_type' => fake()->randomElement(['post', 'comment', 'activity', 'achievement', 'project']),
            'interactable_id' => fake()->numberBetween(1, 1000),
            'interaction_type' => fake()->randomElement(['like', 'love', 'wow', 'share', 'comment', 'bookmark', 'follow']),
            'interaction_data' => fake()->optional()->text(200),
            'is_public' => fake()->boolean(80),
            'is_anonymous' => fake()->boolean(10),
            'interaction_date' => fake()->dateTimeBetween('-1 year', 'now'),
            'metadata' => fake()->optional()->text(200),
            'notes' => fake()->optional()->sentence(),
        ];
    }

    /**
     * Indicate that the interaction is a like.
     */
    public function like(): static
    {
        return $this->state(fn (array $attributes) => [
            'interaction_type' => 'like',
            'is_public' => true,
        ]);
    }
}

