<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UserSubscription>
 */
class UserSubscriptionFactory extends Factory
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
            'subscription_plan_id' => fake()->numberBetween(1, 1000),
            'status' => fake()->randomElement(['active', 'inactive', 'cancelled', 'expired', 'pending']),
            'start_date' => fake()->dateTimeBetween('-1 year', 'now'),
            'end_date' => fake()->optional()->dateTimeBetween('now', '+1 year'),
            'trial_start' => fake()->optional()->dateTimeBetween('-1 year', 'now'),
            'trial_end' => fake()->optional()->dateTimeBetween('-1 year', 'now'),
            'is_trial' => fake()->boolean(20),
            'auto_renew' => fake()->boolean(80),
            'payment_method' => fake()->randomElement(['credit_card', 'debit_card', 'bank_transfer', 'paypal', 'stripe']),
            'billing_cycle' => fake()->randomElement(['monthly', 'yearly', 'lifetime']),
            'amount_eur' => fake()->randomFloat(2, 5, 100),
            'currency' => fake()->randomElement(['EUR', 'USD', 'GBP']),
            'discount_percentage' => fake()->optional()->randomFloat(2, 5, 50),
            'discount_amount_eur' => fake()->optional()->randomFloat(2, 5, 50),
            'tax_amount_eur' => fake()->optional()->randomFloat(2, 0, 20),
            'total_amount_eur' => fake()->randomFloat(2, 5, 120),
            'last_payment_date' => fake()->optional()->dateTimeBetween('-1 year', 'now'),
            'next_payment_date' => fake()->optional()->dateTimeBetween('now', '+1 year'),
            'cancellation_date' => fake()->optional()->dateTimeBetween('-1 year', 'now'),
            'cancellation_reason' => fake()->optional()->sentence(),
            'notes' => fake()->optional()->sentence(),
        ];
    }

    /**
     * Indicate that the subscription is active.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'active',
            'end_date' => fake()->dateTimeBetween('now', '+1 year'),
        ]);
    }
}








