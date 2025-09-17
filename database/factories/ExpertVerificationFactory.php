<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ExpertVerification>
 */
class ExpertVerificationFactory extends Factory
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
            'expert_type' => fake()->randomElement(['energy', 'solar', 'efficiency', 'financial', 'environmental']),
            'verification_status' => fake()->randomElement(['pending', 'verified', 'rejected', 'expired']),
            'verification_date' => fake()->optional()->dateTimeBetween('-1 year', 'now'),
            'expiry_date' => fake()->optional()->dateTimeBetween('now', '+2 years'),
            'certification_body' => fake()->company(),
            'certification_number' => fake()->optional()->regexify('[A-Z]{2}[0-9]{6}'),
            'expertise_level' => fake()->randomElement(['junior', 'senior', 'expert', 'master']),
            'years_experience' => fake()->numberBetween(1, 30),
            'specializations' => fake()->words(3),
            'languages' => fake()->randomElements(['es', 'en', 'fr', 'de'], fake()->numberBetween(1, 3)),
            'verification_documents' => fake()->optional()->words(2),
            'notes' => fake()->optional()->sentence(),
            'verified_by' => fake()->optional()->name(),
        ];
    }

    /**
     * Indicate that the verification is approved.
     */
    public function verified(): static
    {
        return $this->state(fn (array $attributes) => [
            'verification_status' => 'verified',
            'verification_date' => fake()->dateTimeBetween('-1 year', 'now'),
            'expiry_date' => fake()->dateTimeBetween('now', '+2 years'),
        ]);
    }
}




