<?php

namespace Database\Factories;

use App\Models\AutonomousCommunity;
use Illuminate\Database\Eloquent\Factories\Factory;

class AutonomousCommunityFactory extends Factory
{
    protected $model = AutonomousCommunity::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->state,
            'slug' => $this->faker->unique()->slug,
            'country_id' => \App\Models\Country::factory(),
        ];
    }
}
