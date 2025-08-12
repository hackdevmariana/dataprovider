<?php

namespace Database\Factories;

use App\Models\Festival;
use Illuminate\Database\Eloquent\Factories\Factory;

class FestivalFactory extends Factory
{
    protected $model = Festival::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->company . ' Festival',
            'slug' => $this->faker->unique()->slug,
            'description' => $this->faker->optional()->sentence(8),
            'month' => $this->faker->optional()->numberBetween(1, 12),
            'usual_days' => $this->faker->optional()->word,
            'recurring' => $this->faker->boolean,
            'location_id' => null,
            'logo_url' => $this->faker->optional()->imageUrl,
            'color_theme' => $this->faker->optional()->safeColorName,
        ];
    }
}
