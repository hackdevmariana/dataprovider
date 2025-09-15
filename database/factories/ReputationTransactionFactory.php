<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ReputationTransaction>
 */
class ReputationTransactionFactory extends Factory
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
            'transaction_type' => fake()->randomElement(['earn', 'spend', 'transfer', 'bonus', 'penalty']),
            'amount' => fake()->randomFloat(2, -100, 100),
            'balance_after' => fake()->randomFloat(2, 0, 1000),
            'description' => fake()->sentence(),
            'source_type' => fake()->randomElement(['achievement', 'review', 'referral', 'activity', 'purchase', 'sale']),
            'source_id' => fake()->optional()->numberBetween(1, 1000),
            'reference' => fake()->optional()->word(),
            'metadata' => fake()->optional()->text(200),
            'transaction_date' => fake()->dateTimeBetween('-1 year', 'now'),
            'is_verified' => fake()->boolean(80),
            'verification_date' => fake()->optional()->dateTimeBetween('-1 year', 'now'),
            'notes' => fake()->optional()->sentence(),
        ];
    }

    /**
     * Indicate that the transaction is earning reputation.
     */
    public function earning(): static
    {
        return $this->state(fn (array $attributes) => [
            'transaction_type' => 'earn',
            'amount' => fake()->randomFloat(2, 1, 50),
        ]);
    }
}

