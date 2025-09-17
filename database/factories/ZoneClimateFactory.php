<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ZoneClimate>
 */
class ZoneClimateFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->sentence(2),
            'description' => fake()->text(300),
            'climate_type' => fake()->randomElement(['tropical', 'subtropical', 'temperate', 'continental', 'polar', 'arid', 'semi-arid']),
            'temperature_min_celsius' => fake()->numberBetween(-50, 30),
            'temperature_max_celsius' => fake()->numberBetween(0, 50),
            'average_temperature_celsius' => fake()->numberBetween(-20, 40),
            'precipitation_mm' => fake()->numberBetween(0, 4000),
            'humidity_percentage' => fake()->numberBetween(10, 100),
            'wind_speed_kmh' => fake()->numberBetween(0, 200),
            'sunshine_hours' => fake()->numberBetween(0, 4000),
            'solar_irradiance_kwh_m2' => fake()->randomFloat(2, 1, 8),
            'country' => fake()->country(),
            'region' => fake()->optional()->word(),
            'latitude' => fake()->latitude(),
            'longitude' => fake()->longitude(),
            'elevation_meters' => fake()->numberBetween(0, 5000),
            'seasonal_variations' => fake()->optional()->text(200),
            'extreme_weather_events' => fake()->optional()->words(3),
            'is_active' => fake()->boolean(90),
            'notes' => fake()->optional()->sentence(),
        ];
    }

    /**
     * Indicate that the zone is active.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => true,
        ]);
    }
}




