<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Book>
 */
class BookFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(4),
            'subtitle' => fake()->optional()->sentence(),
            'isbn' => fake()->unique()->isbn13(),
            'isbn_10' => fake()->optional()->isbn10(),
            'author' => fake()->name(),
            'co_authors' => fake()->optional()->words(2),
            'publisher' => fake()->company(),
            'publication_date' => fake()->dateTimeBetween('-50 years', 'now'),
            'language' => fake()->randomElement(['es', 'en', 'fr', 'de', 'it', 'pt']),
            'pages' => fake()->numberBetween(50, 1000),
            'format' => fake()->randomElement(['hardcover', 'paperback', 'ebook', 'audiobook']),
            'genre' => fake()->randomElement(['fiction', 'non-fiction', 'biography', 'history', 'science', 'poetry']),
            'description' => fake()->text(500),
            'summary' => fake()->text(200),
            'price_eur' => fake()->randomFloat(2, 5, 50),
            'cover_image_url' => fake()->optional()->imageUrl(),
            'rating' => fake()->randomFloat(1, 1, 5),
            'review_count' => fake()->numberBetween(0, 1000),
            'availability' => fake()->randomElement(['available', 'out_of_stock', 'discontinued']),
            'tags' => fake()->words(5),
        ];
    }

    /**
     * Indicate that the book is a bestseller.
     */
    public function bestseller(): static
    {
        return $this->state(fn (array $attributes) => [
            'rating' => fake()->randomFloat(1, 4, 5),
            'review_count' => fake()->numberBetween(500, 1000),
        ]);
    }
}