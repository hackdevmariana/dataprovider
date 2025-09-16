<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SubscriptionPlan>
 */
class SubscriptionPlanFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->randomElement(['Basic', 'Pro', 'Premium', 'Enterprise', 'Starter', 'Advanced']),
            'description' => fake()->text(300),
            'price_eur' => fake()->randomFloat(2, 5, 100),
            'billing_period' => fake()->randomElement(['monthly', 'yearly', 'lifetime']),
            'currency' => fake()->randomElement(['EUR', 'USD', 'GBP']),
            'features' => fake()->words(10),
            'max_users' => fake()->optional()->numberBetween(1, 1000),
            'max_projects' => fake()->optional()->numberBetween(1, 100),
            'storage_gb' => fake()->optional()->numberBetween(1, 1000),
            'api_calls_per_month' => fake()->optional()->numberBetween(1000, 100000),
            'support_level' => fake()->randomElement(['basic', 'priority', 'dedicated']),
            'is_active' => fake()->boolean(90),
            'is_popular' => fake()->boolean(20),
            'is_featured' => fake()->boolean(10),
            'trial_days' => fake()->optional()->numberBetween(7, 30),
            'setup_fee_eur' => fake()->optional()->randomFloat(2, 0, 500),
            'cancellation_policy' => fake()->optional()->text(200),
            'refund_policy' => fake()->optional()->text(200),
            'terms_and_conditions' => fake()->optional()->text(500),
            'tags' => fake()->words(5),
        ];
    }

    /**
     * Indicate that the plan is popular.
     */
    public function popular(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_popular' => true,
            'is_featured' => true,
        ]);
    }
}


