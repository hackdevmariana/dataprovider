<?php

namespace Database\Factories;

use App\Models\Municipality;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Municipality>
 */
class MunicipalityFactory extends Factory
{
    protected $model = Municipality::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->city(),
            'slug' => $this->faker->unique()->slug(),
            'latitude' => $this->faker->latitude(),
            'longitude' => $this->faker->longitude(),
            'province_id' => \App\Models\Province::factory(),
            'autonomous_community_id' => \App\Models\AutonomousCommunity::factory(),
            'country_id' => \App\Models\Country::factory(),
        ];
    }
}


