<?php

namespace Database\Factories;

use App\Models\Book;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\BookReview>
 */
class BookReviewFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'book_id' => Book::factory(),
            'user_id' => User::factory(),
            'rating' => fake()->numberBetween(1, 5),
            'title' => fake()->sentence(4),
            'content' => fake()->text(500),
            'summary' => fake()->text(100),
            'pros' => fake()->optional()->text(200),
            'cons' => fake()->optional()->text(200),
            'recommendation' => fake()->randomElement(['highly_recommend', 'recommend', 'neutral', 'not_recommend']),
            'reading_time_hours' => fake()->randomFloat(1, 1, 20),
            'is_spoiler_free' => fake()->boolean(80),
            'helpful_votes' => fake()->numberBetween(0, 100),
            'not_helpful_votes' => fake()->numberBetween(0, 20),
            'is_verified_purchase' => fake()->boolean(70),
            'language' => fake()->randomElement(['es', 'en', 'fr', 'de', 'it']),
            'tags' => fake()->words(3),
        ];
    }

    /**
     * Indicate that the review is highly rated.
     */
    public function highlyRated(): static
    {
        return $this->state(fn (array $attributes) => [
            'rating' => fake()->numberBetween(4, 5),
            'recommendation' => fake()->randomElement(['highly_recommend', 'recommend']),
        ]);
    }
}