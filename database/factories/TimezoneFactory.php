<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Timezone>
 */
class TimezoneFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->timezone(),
            'abbreviation' => fake()->randomElement(['CET', 'CEST', 'GMT', 'UTC', 'EST', 'PST', 'MST', 'CST']),
            'offset_hours' => fake()->numberBetween(-12, 14),
            'offset_minutes' => fake()->numberBetween(0, 59),
            'country' => fake()->country(),
            'region' => fake()->optional()->word(),
            'city' => fake()->optional()->city(),
            'is_dst' => fake()->boolean(50),
            'dst_start' => fake()->optional()->dateTimeBetween('-1 year', '+1 year'),
            'dst_end' => fake()->optional()->dateTimeBetween('-1 year', '+1 year'),
            'is_active' => fake()->boolean(90),
            'notes' => fake()->optional()->sentence(),
        ];
    }

    /**
     * Indicate that the timezone is active.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => true,
        ]);
    }
}

