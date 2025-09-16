<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\HistoricalEvent>
 */
class HistoricalEventFactory extends Factory
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
            'description' => fake()->text(500),
            'event_date' => fake()->dateTimeBetween('-2000 years', 'now'),
            'end_date' => fake()->optional()->dateTimeBetween('-2000 years', 'now'),
            'event_type' => fake()->randomElement(['war', 'peace', 'discovery', 'invention', 'political', 'cultural', 'religious', 'scientific']),
            'importance_level' => fake()->randomElement(['low', 'medium', 'high', 'very_high']),
            'location' => fake()->city(),
            'country' => fake()->country(),
            'region' => fake()->optional()->word(),
            'participants' => fake()->optional()->words(5),
            'outcome' => fake()->optional()->text(300),
            'historical_significance' => fake()->text(400),
            'sources' => fake()->optional()->words(3),
            'tags' => fake()->words(5),
            'is_verified' => fake()->boolean(80),
            'verified_by' => fake()->optional()->name(),
        ];
    }

    /**
     * Indicate that the event is highly significant.
     */
    public function highlySignificant(): static
    {
        return $this->state(fn (array $attributes) => [
            'importance_level' => 'very_high',
            'is_verified' => true,
        ]);
    }
}


