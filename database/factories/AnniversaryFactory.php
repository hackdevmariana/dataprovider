<?php

namespace Database\Factories;

use App\Models\Anniversary;
use Illuminate\Database\Eloquent\Factories\Factory;

class AnniversaryFactory extends Factory
{
    protected $model = Anniversary::class;

    public function definition(): array
    {
        return [
            'day' => $this->faker->numberBetween(1, 28),
            'month' => $this->faker->numberBetween(1, 12),
            'year' => $this->faker->optional()->year(),
            'title' => $this->faker->sentence(3),
            'slug' => $this->faker->unique()->slug,
            'description' => $this->faker->paragraph,
        ];
    }
}
