<?php

namespace Database\Factories;

use App\Models\Festival;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\FestivalSchedule>
 */
class FestivalScheduleFactory extends Factory
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
            'event_name' => fake()->sentence(3),
            'event_type' => fake()->randomElement(['concert', 'workshop', 'exhibition', 'ceremony', 'competition']),
            'start_datetime' => fake()->dateTimeBetween('-1 year', '+1 year'),
            'end_datetime' => fake()->dateTimeBetween('-1 year', '+1 year'),
            'venue' => fake()->sentence(2),
            'description' => fake()->text(200),
            'capacity' => fake()->optional()->numberBetween(50, 2000),
            'ticket_price_eur' => fake()->optional()->randomFloat(2, 5, 100),
            'is_sold_out' => fake()->boolean(20),
            'organizer' => fake()->company(),
            'contact_email' => fake()->optional()->email(),
            'special_requirements' => fake()->optional()->sentence(),
            'notes' => fake()->optional()->sentence(),
        ];
    }

    /**
     * Indicate that the event is sold out.
     */
    public function soldOut(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_sold_out' => true,
        ]);
    }
}


