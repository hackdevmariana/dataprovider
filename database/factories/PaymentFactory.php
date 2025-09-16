<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Payment>
 */
class PaymentFactory extends Factory
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
            'amount_eur' => fake()->randomFloat(2, 1, 1000),
            'currency' => fake()->randomElement(['EUR', 'USD', 'GBP']),
            'payment_method' => fake()->randomElement(['credit_card', 'debit_card', 'bank_transfer', 'paypal', 'stripe', 'crypto']),
            'payment_status' => fake()->randomElement(['pending', 'completed', 'failed', 'cancelled', 'refunded']),
            'transaction_id' => fake()->unique()->regexify('[A-Z0-9]{20}'),
            'gateway_transaction_id' => fake()->optional()->regexify('[A-Z0-9]{20}'),
            'gateway_response' => fake()->optional()->text(200),
            'payment_date' => fake()->dateTimeBetween('-1 year', 'now'),
            'description' => fake()->sentence(),
            'reference' => fake()->optional()->word(),
            'invoice_number' => fake()->optional()->regexify('INV-[0-9]{8}'),
            'tax_amount_eur' => fake()->optional()->randomFloat(2, 0, 100),
            'fee_amount_eur' => fake()->optional()->randomFloat(2, 0, 50),
            'refund_amount_eur' => fake()->optional()->randomFloat(2, 0, 1000),
            'refund_date' => fake()->optional()->dateTimeBetween('-1 year', 'now'),
            'refund_reason' => fake()->optional()->sentence(),
            'metadata' => fake()->optional()->text(200),
        ];
    }

    /**
     * Indicate that the payment is completed.
     */
    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'payment_status' => 'completed',
            'payment_date' => fake()->dateTimeBetween('-1 year', 'now'),
        ]);
    }
}


