<?php

namespace Database\Factories;

use App\Models\Festival;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\FestivalProgram>
 */
class FestivalProgramFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'festival_id' => Festival::factory(),
            'day_number' => fake()->numberBetween(1, 7),
            'date' => fake()->dateTimeBetween('-1 year', '+1 year'),
            'theme' => fake()->optional()->sentence(2),
            'description' => fake()->text(300),
            'opening_time' => fake()->time(),
            'closing_time' => fake()->time(),
            'main_events' => fake()->words(5),
            'special_guests' => fake()->optional()->words(3),
            'venue' => fake()->sentence(2),
            'weather_forecast' => fake()->optional()->randomElement(['sunny', 'cloudy', 'rainy', 'stormy']),
            'attendance_estimate' => fake()->optional()->numberBetween(100, 10000),
            'notes' => fake()->optional()->sentence(),
        ];
    }

    /**
     * Indicate that this is the opening day.
     */
    public function openingDay(): static
    {
        return $this->state(fn (array $attributes) => [
            'day_number' => 1,
            'theme' => 'Opening Ceremony',
        ]);
    }
}




