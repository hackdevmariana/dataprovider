<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProjectVerification>
 */
class ProjectVerificationFactory extends Factory
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
            'project_id' => fake()->numberBetween(1, 1000),
            'verification_type' => fake()->randomElement(['technical', 'financial', 'environmental', 'legal', 'safety']),
            'verification_status' => fake()->randomElement(['pending', 'in_progress', 'approved', 'rejected', 'requires_changes']),
            'verification_date' => fake()->optional()->dateTimeBetween('-1 year', 'now'),
            'verifier_name' => fake()->optional()->name(),
            'verifier_organization' => fake()->optional()->company(),
            'verifier_license' => fake()->optional()->regexify('[A-Z]{2}[0-9]{6}'),
            'verification_report' => fake()->optional()->text(500),
            'findings' => fake()->optional()->text(300),
            'recommendations' => fake()->optional()->text(300),
            'compliance_score' => fake()->optional()->randomFloat(1, 1, 10),
            'risk_assessment' => fake()->optional()->randomElement(['low', 'medium', 'high', 'very_high']),
            'certification_issued' => fake()->optional()->boolean(),
            'certification_number' => fake()->optional()->regexify('[A-Z]{2}[0-9]{8}'),
            'certification_expiry' => fake()->optional()->dateTimeBetween('now', '+5 years'),
            'follow_up_required' => fake()->boolean(30),
            'follow_up_date' => fake()->optional()->dateTimeBetween('now', '+1 year'),
            'notes' => fake()->optional()->sentence(),
        ];
    }

    /**
     * Indicate that the verification is approved.
     */
    public function approved(): static
    {
        return $this->state(fn (array $attributes) => [
            'verification_status' => 'approved',
            'verification_date' => fake()->dateTimeBetween('-1 year', 'now'),
            'compliance_score' => fake()->randomFloat(1, 7, 10),
        ]);
    }
}




