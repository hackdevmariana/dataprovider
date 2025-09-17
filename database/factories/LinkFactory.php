<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Link>
 */
class LinkFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'url' => fake()->url(),
            'title' => fake()->sentence(4),
            'description' => fake()->optional()->text(200),
            'link_type' => fake()->randomElement(['website', 'article', 'video', 'document', 'social_media', 'news']),
            'domain' => fake()->domainName(),
            'is_active' => fake()->boolean(90),
            'is_verified' => fake()->boolean(70),
            'verification_date' => fake()->optional()->dateTimeBetween('-1 year', 'now'),
            'last_checked' => fake()->optional()->dateTimeBetween('-1 month', 'now'),
            'status_code' => fake()->optional()->numberBetween(200, 500),
            'response_time_ms' => fake()->optional()->numberBetween(100, 5000),
            'tags' => fake()->words(3),
            'notes' => fake()->optional()->sentence(),
        ];
    }

    /**
     * Indicate that the link is verified.
     */
    public function verified(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_verified' => true,
            'verification_date' => fake()->dateTimeBetween('-1 year', 'now'),
            'status_code' => 200,
        ]);
    }
}




