<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\RealTimePrice>
 */
class RealTimePriceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'price_per_kwh' => fake()->randomFloat(4, 0.01, 0.50),
            'currency' => fake()->randomElement(['EUR', 'USD', 'GBP']),
            'market_type' => fake()->randomElement(['spot', 'futures', 'forward', 'options']),
            'delivery_period' => fake()->randomElement(['hourly', 'daily', 'weekly', 'monthly']),
            'region' => fake()->randomElement(['north', 'south', 'east', 'west', 'central']),
            'country' => fake()->country(),
            'price_type' => fake()->randomElement(['peak', 'off_peak', 'base', 'shoulder']),
            'timestamp' => fake()->dateTimeBetween('-1 month', 'now'),
            'source' => fake()->company(),
            'bid_price' => fake()->optional()->randomFloat(4, 0.01, 0.50),
            'ask_price' => fake()->optional()->randomFloat(4, 0.01, 0.50),
            'volume_mwh' => fake()->optional()->randomFloat(2, 1, 1000),
            'change_percentage' => fake()->optional()->randomFloat(2, -20, 20),
            'change_amount' => fake()->optional()->randomFloat(4, -0.10, 0.10),
            'high_price' => fake()->optional()->randomFloat(4, 0.01, 0.50),
            'low_price' => fake()->optional()->randomFloat(4, 0.01, 0.50),
            'open_price' => fake()->optional()->randomFloat(4, 0.01, 0.50),
            'close_price' => fake()->optional()->randomFloat(4, 0.01, 0.50),
            'is_active' => fake()->boolean(95),
        ];
    }

    /**
     * Indicate that the price is peak hours.
     */
    public function peak(): static
    {
        return $this->state(fn (array $attributes) => [
            'price_type' => 'peak',
            'price_per_kwh' => fake()->randomFloat(4, 0.20, 0.50),
        ]);
    }
}


