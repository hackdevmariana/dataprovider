<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\EnergyTransaction>
 */
class EnergyTransactionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'transaction_type' => fake()->randomElement(['buy', 'sell', 'transfer', 'exchange']),
            'energy_amount_kwh' => fake()->randomFloat(2, 1, 1000),
            'price_per_kwh' => fake()->randomFloat(4, 0.01, 0.50),
            'total_amount_eur' => fake()->randomFloat(2, 1, 500),
            'currency' => fake()->randomElement(['EUR', 'USD', 'GBP']),
            'transaction_date' => fake()->dateTimeBetween('-1 year', 'now'),
            'status' => fake()->randomElement(['pending', 'completed', 'failed', 'cancelled']),
            'counterparty' => fake()->optional()->company(),
            'transaction_hash' => fake()->optional()->sha256(),
            'blockchain_network' => fake()->optional()->randomElement(['ethereum', 'polygon', 'solana']),
            'gas_fee_eur' => fake()->optional()->randomFloat(2, 0.01, 10),
            'description' => fake()->optional()->sentence(),
            'metadata' => fake()->optional()->text(200),
        ];
    }

    /**
     * Indicate that the transaction is completed.
     */
    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'completed',
            'transaction_date' => fake()->dateTimeBetween('-6 months', 'now'),
        ]);
    }
}




