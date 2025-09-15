<?php

namespace Database\Factories;

use App\Models\Book;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\BookEdition>
 */
class BookEditionFactory extends Factory
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
            'edition_number' => fake()->numberBetween(1, 10),
            'edition_type' => fake()->randomElement(['first', 'revised', 'updated', 'special', 'collector']),
            'publication_date' => fake()->dateTimeBetween('-20 years', 'now'),
            'publisher' => fake()->company(),
            'isbn' => fake()->unique()->isbn13(),
            'pages' => fake()->numberBetween(50, 1000),
            'format' => fake()->randomElement(['hardcover', 'paperback', 'ebook', 'audiobook']),
            'language' => fake()->randomElement(['es', 'en', 'fr', 'de', 'it', 'pt']),
            'price_eur' => fake()->randomFloat(2, 5, 50),
            'cover_image_url' => fake()->optional()->imageUrl(),
            'description' => fake()->text(300),
            'changes_from_previous' => fake()->optional()->text(200),
            'availability' => fake()->randomElement(['available', 'out_of_stock', 'discontinued']),
            'print_run' => fake()->optional()->numberBetween(1000, 100000),
            'special_features' => fake()->optional()->words(3),
        ];
    }

    /**
     * Indicate that this is a first edition.
     */
    public function firstEdition(): static
    {
        return $this->state(fn (array $attributes) => [
            'edition_number' => 1,
            'edition_type' => 'first',
        ]);
    }
}