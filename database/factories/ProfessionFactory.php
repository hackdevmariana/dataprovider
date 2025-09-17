<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Profession>
 */
class ProfessionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->jobTitle(),
            'description' => fake()->text(200),
            'category' => fake()->randomElement(['technology', 'healthcare', 'education', 'finance', 'energy', 'environment', 'arts', 'sports']),
            'industry' => fake()->randomElement(['technology', 'energy', 'finance', 'healthcare', 'education', 'manufacturing', 'services']),
            'education_required' => fake()->randomElement(['high_school', 'bachelor', 'master', 'phd', 'certification', 'apprenticeship']),
            'experience_required_years' => fake()->numberBetween(0, 10),
            'salary_range_min_eur' => fake()->optional()->numberBetween(20000, 100000),
            'salary_range_max_eur' => fake()->optional()->numberBetween(30000, 200000),
            'employment_type' => fake()->randomElement(['full_time', 'part_time', 'contract', 'freelance', 'consultant']),
            'work_environment' => fake()->randomElement(['office', 'remote', 'field', 'laboratory', 'studio', 'outdoor']),
            'skills_required' => fake()->words(5),
            'certifications' => fake()->optional()->words(3),
            'growth_prospects' => fake()->randomElement(['high', 'medium', 'low']),
            'job_market_demand' => fake()->randomElement(['high', 'medium', 'low']),
            'is_active' => fake()->boolean(90),
            'tags' => fake()->words(5),
        ];
    }

    /**
     * Indicate that the profession is in high demand.
     */
    public function highDemand(): static
    {
        return $this->state(fn (array $attributes) => [
            'job_market_demand' => 'high',
            'growth_prospects' => fake()->randomElement(['high', 'medium']),
        ]);
    }
}




