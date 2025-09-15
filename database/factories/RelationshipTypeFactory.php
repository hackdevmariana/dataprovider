<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\RelationshipType>
 */
class RelationshipTypeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->randomElement(['parent', 'child', 'sibling', 'spouse', 'grandparent', 'grandchild', 'uncle', 'aunt', 'cousin', 'friend', 'colleague', 'mentor']),
            'description' => fake()->text(200),
            'category' => fake()->randomElement(['family', 'professional', 'personal', 'romantic', 'mentorship']),
            'is_biological' => fake()->boolean(60),
            'is_legal' => fake()->boolean(40),
            'is_symmetrical' => fake()->boolean(70),
            'opposite_relationship' => fake()->optional()->word(),
            'gender_specific' => fake()->boolean(20),
            'age_restriction' => fake()->optional()->text(100),
            'legal_implications' => fake()->optional()->text(200),
            'cultural_variations' => fake()->optional()->text(200),
            'is_active' => fake()->boolean(90),
            'sort_order' => fake()->numberBetween(1, 100),
        ];
    }

    /**
     * Indicate that the relationship is biological.
     */
    public function biological(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_biological' => true,
            'category' => 'family',
        ]);
    }
}

