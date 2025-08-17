<?php

namespace Database\Factories;

use App\Models\Region;
use Illuminate\Database\Eloquent\Factories\Factory;

class RegionFactory extends Factory
{
    protected $model = Region::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->state,
            'slug' => $this->faker->unique()->slug,
            'province_id' => \App\Models\Province::factory(),
            'autonomous_community_id' => \App\Models\AutonomousCommunity::factory(),
            'country_id' => \App\Models\Country::factory(),
            'timezone_id' => null,
        ];
    }
}
