<?php

namespace Database\Factories;

use App\Models\Person;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\FamilyMember>
 */
class FamilyMemberFactory extends Factory
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
            'related_person_id' => Person::factory(),
            'relationship_type' => fake()->randomElement(['parent', 'child', 'sibling', 'spouse', 'grandparent', 'grandchild', 'uncle', 'aunt', 'cousin']),
            'relationship_side' => fake()->randomElement(['maternal', 'paternal', 'both']),
            'is_biological' => fake()->boolean(80),
            'is_adopted' => fake()->boolean(5),
            'is_step' => fake()->boolean(10),
            'is_in_law' => fake()->boolean(15),
            'marriage_date' => fake()->optional()->dateTimeBetween('-50 years', 'now'),
            'divorce_date' => fake()->optional()->dateTimeBetween('-30 years', 'now'),
            'notes' => fake()->optional()->sentence(),
        ];
    }

    /**
     * Indicate that the relationship is biological.
     */
    public function biological(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_biological' => true,
            'is_adopted' => false,
            'is_step' => false,
        ]);
    }
}




