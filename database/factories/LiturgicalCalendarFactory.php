<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\LiturgicalCalendar>
 */
class LiturgicalCalendarFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'date' => fake()->dateTimeBetween('-1 year', '+1 year'),
            'liturgical_season' => fake()->randomElement(['advent', 'christmas', 'ordinary_time', 'lent', 'easter', 'pentecost']),
            'liturgical_color' => fake()->randomElement(['white', 'red', 'green', 'purple', 'rose', 'gold']),
            'feast_level' => fake()->randomElement(['solemnity', 'feast', 'memorial', 'optional_memorial', 'ferial']),
            'celebration_name' => fake()->sentence(3),
            'celebration_type' => fake()->randomElement(['saint', 'mystery', 'dedication', 'commemoration']),
            'readings' => fake()->optional()->words(3),
            'psalm' => fake()->optional()->word(),
            'gospel' => fake()->optional()->word(),
            'first_reading' => fake()->optional()->word(),
            'second_reading' => fake()->optional()->word(),
            'responsorial_psalm' => fake()->optional()->word(),
            'alleluia' => fake()->optional()->word(),
            'is_sunday' => fake()->boolean(15),
            'is_holy_day' => fake()->boolean(5),
            'notes' => fake()->optional()->sentence(),
        ];
    }

    /**
     * Indicate that this is a Sunday.
     */
    public function sunday(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_sunday' => true,
            'liturgical_color' => fake()->randomElement(['white', 'red', 'green', 'purple', 'rose']),
        ]);
    }
}




