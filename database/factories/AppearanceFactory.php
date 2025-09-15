<?php

namespace Database\Factories;

use App\Models\Person;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Appearance>
 */
class AppearanceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'person_id' => Person::factory(),
            'height_cm' => fake()->numberBetween(150, 200),
            'weight_kg' => fake()->numberBetween(50, 120),
            'eye_color' => fake()->randomElement(['brown', 'blue', 'green', 'hazel', 'gray', 'amber']),
            'hair_color' => fake()->randomElement(['black', 'brown', 'blonde', 'red', 'gray', 'white']),
            'hair_length' => fake()->randomElement(['short', 'medium', 'long']),
            'hair_style' => fake()->randomElement(['straight', 'wavy', 'curly', 'coily']),
            'skin_tone' => fake()->randomElement(['fair', 'light', 'medium', 'olive', 'tan', 'dark']),
            'body_type' => fake()->randomElement(['slim', 'athletic', 'average', 'muscular', 'curvy']),
            'distinguishing_features' => fake()->optional()->sentence(),
            'style_preferences' => fake()->optional()->words(3),
            'notes' => fake()->optional()->sentence(),
        ];
    }

    /**
     * Indicate that the person has distinctive features.
     */
    public function withDistinguishingFeatures(): static
    {
        return $this->state(fn (array $attributes) => [
            'distinguishing_features' => fake()->randomElement([
                'Scar on left cheek',
                'Tattoo on right arm',
                'Pierced ears',
                'Glasses',
                'Beard',
                'Mustache',
            ]),
        ]);
    }
}