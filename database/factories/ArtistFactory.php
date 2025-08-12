<?php

namespace Database\Factories;

use App\Models\Artist;
use Illuminate\Database\Eloquent\Factories\Factory;

class ArtistFactory extends Factory
{
    protected $model = Artist::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->name . ' Artist',
            'slug' => $this->faker->unique()->slug,
            'description' => $this->faker->sentence(8),
            'birth_date' => $this->faker->optional()->date(),
            'genre' => $this->faker->optional()->word,
            'person_id' => null,
            'stage_name' => $this->faker->optional()->userName,
            'group_name' => $this->faker->optional()->company,
            'active_years_start' => $this->faker->optional()->year,
            'active_years_end' => $this->faker->optional()->year,
            'bio' => $this->faker->optional()->paragraph,
            'photo' => $this->faker->optional()->imageUrl,
            'social_links' => [],
            'language_id' => null,
        ];
    }
}
