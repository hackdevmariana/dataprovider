<?php

namespace Database\Factories;

use App\Models\Festival;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\FestivalActivity>
 */
class FestivalActivityFactory extends Factory
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
            'name' => fake()->sentence(3),
            'description' => fake()->text(300),
            'activity_type' => fake()->randomElement(['performance', 'workshop', 'exhibition', 'competition', 'ceremony', 'parade']),
            'start_time' => fake()->time(),
            'end_time' => fake()->time(),
            'duration_minutes' => fake()->numberBetween(30, 240),
            'location' => fake()->sentence(2),
            'capacity' => fake()->optional()->numberBetween(10, 1000),
            'age_restriction' => fake()->optional()->randomElement(['all_ages', 'adults_only', 'children_only', 'teens_and_up']),
            'ticket_price_eur' => fake()->optional()->randomFloat(2, 0, 50),
            'is_free' => fake()->boolean(40),
            'requires_registration' => fake()->boolean(60),
            'organizer' => fake()->company(),
            'contact_info' => fake()->optional()->email(),
            'equipment_needed' => fake()->optional()->words(3),
            'notes' => fake()->optional()->sentence(),
        ];
    }

    /**
     * Indicate that the activity is free.
     */
    public function free(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_free' => true,
            'ticket_price_eur' => null,
        ]);
    }
}








