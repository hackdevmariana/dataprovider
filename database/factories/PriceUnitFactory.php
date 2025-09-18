<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PriceUnit>
 */
class PriceUnitFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->word(),
            'symbol' => fake()->randomElement(['€', '$', '£', '¥', '₹', '₽']),
            'code' => fake()->unique()->currencyCode(),
            'description' => fake()->optional()->sentence(),
            'decimal_places' => fake()->numberBetween(0, 4),
            'is_active' => fake()->boolean(90),
            'is_crypto' => fake()->boolean(10),
            'country' => fake()->optional()->country(),
            'region' => fake()->optional()->word(),
            'exchange_rate' => fake()->optional()->randomFloat(4, 0.1, 10),
            'base_currency' => fake()->optional()->randomElement(['EUR', 'USD', 'GBP']),
            'notes' => fake()->optional()->sentence(),
        ];
    }

    /**
     * Indicate that the unit is crypto.
     */
    public function crypto(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_crypto' => true,
            'code' => fake()->randomElement(['BTC', 'ETH', 'ADA', 'DOT', 'SOL']),
            'symbol' => fake()->randomElement(['₿', 'Ξ', '₳', '●', '◎']),
        ]);
    }
}








