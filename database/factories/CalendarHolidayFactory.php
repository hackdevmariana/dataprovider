<?php

namespace Database\Factories;

use App\Models\CalendarHoliday;
use Illuminate\Database\Eloquent\Factories\Factory;

class CalendarHolidayFactory extends Factory
{
    protected $model = CalendarHoliday::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->sentence(2),
            'date' => $this->faker->date('Y-m-d'),
            'slug' => $this->faker->unique()->slug,
            'municipality_id' => null,
        ];
    }
}
