<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UserEndorsement>
 */
class UserEndorsementFactory extends Factory
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
            'endorser_id' => User::factory(),
            'endorsement_type' => fake()->randomElement(['skill', 'project', 'achievement', 'character', 'expertise']),
            'endorsed_item' => fake()->sentence(2),
            'endorsement_text' => fake()->text(300),
            'rating' => fake()->numberBetween(1, 5),
            'is_public' => fake()->boolean(80),
            'is_verified' => fake()->boolean(60),
            'verification_date' => fake()->optional()->dateTimeBetween('-1 year', 'now'),
            'endorsement_date' => fake()->dateTimeBetween('-1 year', 'now'),
            'category' => fake()->randomElement(['energy', 'technology', 'leadership', 'innovation', 'sustainability']),
            'tags' => fake()->words(3),
            'notes' => fake()->optional()->sentence(),
        ];
    }

    /**
     * Indicate that the endorsement is verified.
     */
    public function verified(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_verified' => true,
            'verification_date' => fake()->dateTimeBetween('-1 year', 'now'),
        ]);
    }
}


