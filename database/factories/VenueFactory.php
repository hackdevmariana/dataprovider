<?php

namespace Database\Factories;

use App\Models\Venue;
use App\Models\Municipality;
use Illuminate\Database\Eloquent\Factories\Factory;

class VenueFactory extends Factory
{
    protected $model = Venue::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->company . ' Venue',
            'slug' => $this->faker->unique()->slug,
            'address' => $this->faker->address,
            'municipality_id' => Municipality::factory(),
            'latitude' => $this->faker->latitude,
            'longitude' => $this->faker->longitude,
            'capacity' => $this->faker->numberBetween(50, 1000),
            'description' => $this->faker->sentence(8),
            'venue_type' => $this->faker->randomElement(['auditorium', 'park', 'square', 'club', 'online', 'other']),
            'venue_status' => $this->faker->randomElement(['active', 'closed', 'under_construction']),
            'is_verified' => $this->faker->boolean,
        ];
    }
}
