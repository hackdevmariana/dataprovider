<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ConsultationService>
 */
class ConsultationServiceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'service_name' => fake()->sentence(3),
            'description' => fake()->text(300),
            'service_type' => fake()->randomElement(['energy_audit', 'solar_assessment', 'efficiency_consultation', 'financial_advice']),
            'expertise_area' => fake()->randomElement(['residential', 'commercial', 'industrial', 'agricultural']),
            'hourly_rate_eur' => fake()->randomFloat(2, 50, 200),
            'availability' => fake()->randomElement(['available', 'busy', 'unavailable']),
            'location' => fake()->city(),
            'service_radius_km' => fake()->numberBetween(10, 200),
            'languages' => fake()->randomElements(['es', 'en', 'fr', 'de'], fake()->numberBetween(1, 3)),
            'certifications' => fake()->optional()->words(3),
            'experience_years' => fake()->numberBetween(1, 30),
            'rating' => fake()->randomFloat(1, 1, 5),
            'review_count' => fake()->numberBetween(0, 100),
            'is_verified' => fake()->boolean(70),
            'tags' => fake()->words(5),
        ];
    }

    /**
     * Indicate that the service is verified.
     */
    public function verified(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_verified' => true,
            'rating' => fake()->randomFloat(1, 4, 5),
        ]);
    }
}




