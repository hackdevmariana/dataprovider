<?php

namespace Database\Factories;

use App\Models\Event;
use Illuminate\Database\Eloquent\Factories\Factory;

class EventFactory extends Factory
{
    protected $model = Event::class;

    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(3),
            'slug' => $this->faker->unique()->slug,
            'description' => $this->faker->paragraph,
            'start_datetime' => $this->faker->dateTimeBetween('-1 week', '+1 week'),
            'end_datetime' => $this->faker->dateTimeBetween('+1 week', '+2 weeks'),
            'venue_id' => null,
            'event_type_id' => null,
            'festival_id' => null,
            'language_id' => null,
            'timezone_id' => null,
            'municipality_id' => null,
            'point_of_interest_id' => null,
            'work_id' => null,
            'price' => $this->faker->randomFloat(2, 0, 100),
            'is_free' => $this->faker->boolean,
            'audience_size_estimate' => $this->faker->numberBetween(10, 1000),
            'source_url' => $this->faker->url,
        ];
    }
}
