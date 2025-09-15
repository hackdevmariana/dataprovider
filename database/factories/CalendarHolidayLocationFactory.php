<?php

namespace Database\Factories;

use App\Models\CalendarHoliday;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CalendarHolidayLocation>
 */
class CalendarHolidayLocationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'calendar_holiday_id' => CalendarHoliday::factory(),
            'location_type' => fake()->randomElement(['country', 'region', 'state', 'city', 'municipality']),
            'location_code' => fake()->optional()->countryCode(),
            'location_name' => fake()->city(),
            'is_primary' => fake()->boolean(20),
            'notes' => fake()->optional()->sentence(),
        ];
    }

    /**
     * Indicate that this is a primary location.
     */
    public function primary(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_primary' => true,
        ]);
    }
}