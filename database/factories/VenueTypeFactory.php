<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\VenueType>
 */
class VenueTypeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->randomElement(['conference_center', 'hotel', 'restaurant', 'bar', 'club', 'theater', 'stadium', 'park', 'museum', 'gallery']),
            'description' => fake()->text(200),
            'category' => fake()->randomElement(['entertainment', 'business', 'dining', 'sports', 'cultural', 'outdoor']),
            'capacity_min' => fake()->optional()->numberBetween(10, 100),
            'capacity_max' => fake()->optional()->numberBetween(100, 10000),
            'amenities' => fake()->words(5),
            'accessibility_features' => fake()->optional()->words(3),
            'parking_available' => fake()->boolean(80),
            'parking_capacity' => fake()->optional()->numberBetween(10, 1000),
            'public_transport_access' => fake()->boolean(70),
            'wifi_available' => fake()->boolean(90),
            'catering_available' => fake()->boolean(60),
            'audio_visual_equipment' => fake()->boolean(50),
            'outdoor_space' => fake()->boolean(40),
            'is_active' => fake()->boolean(90),
            'sort_order' => fake()->numberBetween(1, 100),
            'notes' => fake()->optional()->sentence(),
        ];
    }

    /**
     * Indicate that the venue type is active.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => true,
        ]);
    }
}


