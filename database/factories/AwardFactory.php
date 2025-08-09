<?php

namespace Database\Factories;

use App\Models\Award;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Award>
 */
class AwardFactory extends Factory
{
    protected $model = Award::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->words(2, true),
            'slug' => $this->faker->unique()->slug(),
            'description' => $this->faker->sentence(),
            'awarded_by' => $this->faker->company(),
            'first_year_awarded' => $this->faker->numberBetween(1900, (int) date('Y')),
            'category' => $this->faker->randomElement(['Cultura', 'Ciencia', 'Deporte']),
        ];
    }
}


