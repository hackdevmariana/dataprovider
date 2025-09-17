<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UserReview>
 */
class UserReviewFactory extends Factory
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
            'reviewed_user_id' => User::factory(),
            'review_type' => fake()->randomElement(['service', 'project', 'collaboration', 'expertise', 'character']),
            'rating' => fake()->numberBetween(1, 5),
            'title' => fake()->sentence(4),
            'content' => fake()->text(500),
            'pros' => fake()->optional()->text(200),
            'cons' => fake()->optional()->text(200),
            'recommendation' => fake()->randomElement(['highly_recommend', 'recommend', 'neutral', 'not_recommend']),
            'is_verified' => fake()->boolean(70),
            'verification_date' => fake()->optional()->dateTimeBetween('-1 year', 'now'),
            'is_public' => fake()->boolean(80),
            'is_anonymous' => fake()->boolean(20),
            'helpful_votes' => fake()->numberBetween(0, 100),
            'not_helpful_votes' => fake()->numberBetween(0, 20),
            'response' => fake()->optional()->text(300),
            'response_date' => fake()->optional()->dateTimeBetween('-1 year', 'now'),
            'tags' => fake()->words(3),
            'notes' => fake()->optional()->sentence(),
        ];
    }

    /**
     * Indicate that the review is verified.
     */
    public function verified(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_verified' => true,
            'verification_date' => fake()->dateTimeBetween('-1 year', 'now'),
        ]);
    }
}




