<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AppSetting>
 */
class AppSettingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'key' => fake()->unique()->slug(2),
            'value' => fake()->sentence(),
            'type' => fake()->randomElement(['string', 'integer', 'boolean', 'json', 'array']),
            'description' => fake()->sentence(),
            'category' => fake()->randomElement(['general', 'energy', 'user', 'system', 'notification']),
            'is_public' => fake()->boolean(30),
            'is_editable' => fake()->boolean(80),
            'default_value' => fake()->sentence(),
            'validation_rules' => fake()->optional()->sentence(),
            'group' => fake()->optional()->word(),
            'order' => fake()->numberBetween(1, 100),
        ];
    }

    /**
     * Indicate that the setting is public.
     */
    public function public(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_public' => true,
        ]);
    }

    /**
     * Indicate that the setting is energy-related.
     */
    public function energyRelated(): static
    {
        return $this->state(fn (array $attributes) => [
            'category' => 'energy',
            'key' => 'energy_' . fake()->word(),
        ]);
    }
}