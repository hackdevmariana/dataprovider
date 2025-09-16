<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProjectCommission>
 */
class ProjectCommissionFactory extends Factory
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
            'project_id' => fake()->numberBetween(1, 1000),
            'commission_type' => fake()->randomElement(['referral', 'sale', 'installation', 'consultation', 'maintenance']),
            'commission_rate' => fake()->randomFloat(2, 1, 15),
            'commission_amount_eur' => fake()->randomFloat(2, 50, 5000),
            'base_amount_eur' => fake()->randomFloat(2, 1000, 50000),
            'status' => fake()->randomElement(['pending', 'approved', 'paid', 'cancelled']),
            'payment_date' => fake()->optional()->dateTimeBetween('-1 year', 'now'),
            'payment_method' => fake()->optional()->randomElement(['bank_transfer', 'paypal', 'stripe', 'crypto']),
            'transaction_id' => fake()->optional()->regexify('[A-Z0-9]{20}'),
            'description' => fake()->sentence(),
            'notes' => fake()->optional()->sentence(),
            'created_at' => fake()->dateTimeBetween('-1 year', 'now'),
        ];
    }

    /**
     * Indicate that the commission is paid.
     */
    public function paid(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'paid',
            'payment_date' => fake()->dateTimeBetween('-1 year', 'now'),
        ]);
    }
}


