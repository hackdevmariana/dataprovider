<?php

namespace Database\Factories;

use App\Models\Person;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Alias>
 */
class AliasFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'person_id' => Person::factory(),
            'alias' => fake()->firstName() . ' ' . fake()->lastName(),
            'type' => fake()->randomElement(['nickname', 'stage_name', 'pen_name', 'legal_name', 'maiden_name']),
            'is_primary' => fake()->boolean(20),
            'language' => fake()->randomElement(['es', 'en', 'fr', 'de', 'it']),
            'notes' => fake()->optional()->sentence(),
        ];
    }

    /**
     * Indicate that the alias is primary.
     */
    public function primary(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_primary' => true,
        ]);
    }

    /**
     * Indicate that the alias is a stage name.
     */
    public function stageName(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'stage_name',
            'alias' => fake()->firstName() . ' ' . fake()->lastName(),
        ]);
    }
}