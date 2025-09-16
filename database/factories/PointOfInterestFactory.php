<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PointOfInterest>
 */
class PointOfInterestFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->sentence(3),
            'description' => fake()->text(300),
            'location' => fake()->city(),
            'country' => fake()->country(),
            'region' => fake()->optional()->word(),
            'latitude' => fake()->latitude(),
            'longitude' => fake()->longitude(),
            'poi_type' => fake()->randomElement(['historical', 'natural', 'cultural', 'religious', 'entertainment', 'shopping', 'dining']),
            'category' => fake()->randomElement(['monument', 'museum', 'park', 'beach', 'mountain', 'church', 'restaurant', 'hotel']),
            'importance_level' => fake()->randomElement(['local', 'regional', 'national', 'international']),
            'visitor_rating' => fake()->optional()->randomFloat(1, 1, 5),
            'review_count' => fake()->optional()->numberBetween(0, 1000),
            'annual_visitors' => fake()->optional()->numberBetween(100, 1000000),
            'opening_hours' => fake()->optional()->sentence(),
            'admission_price_eur' => fake()->optional()->randomFloat(2, 0, 50),
            'contact_info' => fake()->optional()->email(),
            'website' => fake()->optional()->url(),
            'facilities' => fake()->optional()->words(5),
            'accessibility' => fake()->optional()->text(200),
            'best_time_to_visit' => fake()->optional()->sentence(),
            'nearby_attractions' => fake()->optional()->words(3),
            'is_active' => fake()->boolean(95),
            'tags' => fake()->words(5),
        ];
    }

    /**
     * Indicate that the POI is internationally significant.
     */
    public function international(): static
    {
        return $this->state(fn (array $attributes) => [
            'importance_level' => 'international',
            'annual_visitors' => fake()->numberBetween(100000, 1000000),
        ]);
    }
}


