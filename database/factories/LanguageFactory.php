<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Language>
 */
class LanguageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'code' => fake()->unique()->languageCode(),
            'name' => fake()->language(),
            'native_name' => fake()->language(),
            'script' => fake()->randomElement(['latin', 'cyrillic', 'arabic', 'chinese', 'devanagari', 'greek']),
            'family' => fake()->randomElement(['indo-european', 'sino-tibetan', 'afro-asiatic', 'austronesian', 'niger-congo']),
            'region' => fake()->country(),
            'speakers_millions' => fake()->numberBetween(1, 1000),
            'is_official' => fake()->boolean(30),
            'is_right_to_left' => fake()->boolean(10),
            'date_format' => fake()->randomElement(['dd/mm/yyyy', 'mm/dd/yyyy', 'yyyy-mm-dd']),
            'time_format' => fake()->randomElement(['12h', '24h']),
            'currency_code' => fake()->randomElement(['EUR', 'USD', 'GBP', 'JPY', 'CNY']),
            'is_active' => fake()->boolean(90),
            'notes' => fake()->optional()->sentence(),
        ];
    }

    /**
     * Indicate that the language is official.
     */
    public function official(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_official' => true,
        ]);
    }
}

