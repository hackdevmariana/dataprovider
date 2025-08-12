<?php

namespace Database\Factories;

use App\Models\Country;
use Illuminate\Database\Eloquent\Factories\Factory;

class CountryFactory extends Factory
{
    protected $model = Country::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->country,
            'slug' => $this->faker->unique()->slug,
            'iso_alpha2' => $this->faker->unique()->lexify('??'),
            'iso_alpha3' => $this->faker->unique()->lexify('???'),
            'iso_numeric' => $this->faker->unique()->numerify('###'),
        ];
    }
}
