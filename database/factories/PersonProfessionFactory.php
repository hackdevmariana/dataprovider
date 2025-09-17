<?php

namespace Database\Factories;

use App\Models\Person;
use App\Models\Profession;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PersonProfession>
 */
class PersonProfessionFactory extends Factory
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
            'profession_id' => Profession::factory(),
            'start_date' => fake()->dateTimeBetween('-50 years', 'now'),
            'end_date' => fake()->optional()->dateTimeBetween('-10 years', 'now'),
            'is_current' => fake()->boolean(70),
            'title' => fake()->optional()->jobTitle(),
            'company' => fake()->optional()->company(),
            'location' => fake()->optional()->city(),
            'description' => fake()->optional()->text(200),
            'achievements' => fake()->optional()->text(300),
            'salary_range' => fake()->optional()->randomElement(['low', 'medium', 'high', 'very_high']),
            'employment_type' => fake()->randomElement(['full_time', 'part_time', 'contract', 'freelance', 'consultant']),
            'industry' => fake()->optional()->randomElement(['technology', 'energy', 'finance', 'healthcare', 'education']),
            'notes' => fake()->optional()->sentence(),
        ];
    }

    /**
     * Indicate that the profession is current.
     */
    public function current(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_current' => true,
            'end_date' => null,
        ]);
    }
}




