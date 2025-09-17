<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\DataSource>
 */
class DataSourceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->company(),
            'url' => fake()->url(),
            'description' => fake()->text(200),
            'data_type' => fake()->randomElement(['energy', 'weather', 'financial', 'environmental', 'news']),
            'update_frequency' => fake()->randomElement(['real_time', 'hourly', 'daily', 'weekly', 'monthly']),
            'last_update' => fake()->dateTimeBetween('-1 month', 'now'),
            'status' => fake()->randomElement(['active', 'inactive', 'maintenance', 'error']),
            'api_endpoint' => fake()->optional()->url(),
            'api_key_required' => fake()->boolean(60),
            'rate_limit' => fake()->optional()->numberBetween(100, 10000),
            'data_format' => fake()->randomElement(['json', 'xml', 'csv', 'rss']),
            'reliability_score' => fake()->randomFloat(1, 1, 10),
            'notes' => fake()->optional()->sentence(),
        ];
    }

    /**
     * Indicate that the data source is active.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'active',
            'last_update' => fake()->dateTimeBetween('-1 week', 'now'),
        ]);
    }
}




