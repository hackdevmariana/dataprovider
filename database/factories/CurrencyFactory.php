<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Currency>
 */
class CurrencyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'code' => fake()->unique()->currencyCode(),
            'name' => fake()->currency(),
            'symbol' => fake()->randomElement(['€', '$', '£', '¥', '₹', '₽']),
            'decimal_places' => fake()->numberBetween(0, 4),
            'is_active' => fake()->boolean(90),
            'is_crypto' => fake()->boolean(10),
            'country' => fake()->country(),
            'description' => fake()->optional()->sentence(),
        ];
    }

    /**
     * Indicate that the currency is crypto.
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
