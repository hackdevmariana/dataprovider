<?php

namespace Database\Factories;

use App\Models\Award;
use App\Models\Person;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AwardWinner>
 */
class AwardWinnerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'award_id' => Award::factory(),
            'person_id' => Person::factory(),
            'year' => fake()->numberBetween(1990, 2024),
            'category' => fake()->optional()->word(),
            'position' => fake()->randomElement(['winner', 'runner_up', 'finalist', 'nominee']),
            'prize_amount' => fake()->optional()->randomFloat(2, 1000, 100000),
            'prize_description' => fake()->optional()->sentence(),
            'ceremony_date' => fake()->dateTimeBetween('-10 years', 'now'),
            'ceremony_location' => fake()->city(),
            'presented_by' => fake()->name(),
            'acceptance_speech' => fake()->optional()->text(500),
            'media_coverage' => fake()->optional()->url(),
            'notes' => fake()->optional()->sentence(),
        ];
    }

    /**
     * Indicate that the winner is the main winner.
     */
    public function winner(): static
    {
        return $this->state(fn (array $attributes) => [
            'position' => 'winner',
            'prize_amount' => fake()->randomFloat(2, 10000, 100000),
        ]);
    }

    /**
     * Indicate that the winner is a nominee.
     */
    public function nominee(): static
    {
        return $this->state(fn (array $attributes) => [
            'position' => 'nominee',
            'prize_amount' => null,
        ]);
    }
}