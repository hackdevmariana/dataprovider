<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Work>
 */
class WorkFactory extends Factory
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
            'work_type' => fake()->randomElement(['book', 'article', 'poem', 'song', 'film', 'painting', 'sculpture', 'photograph', 'documentary']),
            'genre' => fake()->randomElement(['fiction', 'non-fiction', 'biography', 'history', 'science', 'poetry', 'drama', 'comedy', 'thriller']),
            'language' => fake()->randomElement(['es', 'en', 'fr', 'de', 'it', 'pt']),
            'publication_date' => fake()->optional()->dateTimeBetween('-100 years', 'now'),
            'publisher' => fake()->optional()->company(),
            'isbn' => fake()->optional()->isbn13(),
            'pages' => fake()->optional()->numberBetween(50, 1000),
            'duration_minutes' => fake()->optional()->numberBetween(5, 300),
            'description' => fake()->text(500),
            'summary' => fake()->text(200),
            'excerpt' => fake()->optional()->text(300),
            'rating' => fake()->optional()->randomFloat(1, 1, 5),
            'review_count' => fake()->optional()->numberBetween(0, 1000),
            'awards' => fake()->optional()->words(3),
            'tags' => fake()->words(5),
            'is_public' => fake()->boolean(90),
            'is_featured' => fake()->boolean(10),
            'notes' => fake()->optional()->sentence(),
        ];
    }

    /**
     * Indicate that the work is featured.
     */
    public function featured(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_featured' => true,
            'rating' => fake()->randomFloat(1, 4, 5),
        ]);
    }
}




