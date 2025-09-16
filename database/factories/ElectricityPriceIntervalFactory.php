<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ElectricityPriceInterval>
 */
class ElectricityPriceIntervalFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'start_time' => fake()->time(),
            'end_time' => fake()->time(),
            'price_per_kwh' => fake()->randomFloat(4, 0.01, 0.50),
            'interval_type' => fake()->randomElement(['peak', 'off_peak', 'valley', 'intermediate']),
            'day_type' => fake()->randomElement(['weekday', 'weekend', 'holiday']),
            'season' => fake()->randomElement(['summer', 'winter', 'spring', 'autumn']),
            'tariff_name' => fake()->sentence(2),
            'is_active' => fake()->boolean(90),
            'effective_from' => fake()->dateTimeBetween('-1 year', 'now'),
            'effective_to' => fake()->optional()->dateTimeBetween('now', '+1 year'),
            'notes' => fake()->optional()->sentence(),
        ];
    }

    /**
     * Indicate that the interval is peak hours.
     */
    public function peak(): static
    {
        return $this->state(fn (array $attributes) => [
            'interval_type' => 'peak',
            'price_per_kwh' => fake()->randomFloat(4, 0.20, 0.50),
        ]);
    }
}


