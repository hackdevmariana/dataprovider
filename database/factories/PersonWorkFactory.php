<?php

namespace Database\Factories;

use App\Models\Person;
use App\Models\Work;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PersonWork>
 */
class PersonWorkFactory extends Factory
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
            'work_id' => Work::factory(),
            'role' => fake()->randomElement(['author', 'co_author', 'editor', 'translator', 'illustrator', 'composer', 'director', 'producer']),
            'contribution_percentage' => fake()->optional()->randomFloat(1, 10, 100),
            'is_primary' => fake()->boolean(30),
            'start_date' => fake()->optional()->dateTimeBetween('-50 years', 'now'),
            'end_date' => fake()->optional()->dateTimeBetween('-10 years', 'now'),
            'notes' => fake()->optional()->sentence(),
        ];
    }

    /**
     * Indicate that the person is the primary contributor.
     */
    public function primary(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_primary' => true,
            'role' => fake()->randomElement(['author', 'composer', 'director', 'producer']),
        ]);
    }
}




