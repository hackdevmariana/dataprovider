<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ActivityFeed>
 */
class ActivityFeedFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $activityTypes = [
            'energy_saved', 'solar_generated', 'achievement_unlocked', 'project_funded',
            'installation_completed', 'cooperative_joined', 'carbon_milestone',
            'efficiency_improvement', 'grid_contribution', 'sustainability_goal',
            'investment_made', 'roof_published', 'production_right_sold',
            'community_contribution', 'topic_created', 'expert_verified'
        ];

        return [
            'user_id' => User::factory(),
            'activity_type' => fake()->randomElement($activityTypes),
            'related_type' => null,
            'related_id' => null,
            'activity_data' => [
                'metadata' => fake()->sentence(),
                'tags' => fake()->words(3),
            ],
            'description' => fake()->sentence(),
            'summary' => fake()->text(200),
            'energy_amount_kwh' => fake()->randomFloat(2, 0, 1000),
            'cost_savings_eur' => fake()->randomFloat(2, 0, 500),
            'co2_savings_kg' => fake()->randomFloat(2, 0, 100),
            'investment_amount_eur' => fake()->randomFloat(2, 0, 10000),
            'community_impact_score' => fake()->numberBetween(1, 100),
            'visibility' => fake()->randomElement(['public', 'private', 'cooperative', 'followers']),
            'is_featured' => fake()->boolean(20),
            'is_milestone' => fake()->boolean(10),
            'notify_followers' => fake()->boolean(70),
            'show_in_feed' => fake()->boolean(90),
            'allow_interactions' => fake()->boolean(85),
            'engagement_score' => fake()->numberBetween(0, 1000),
            'likes_count' => fake()->numberBetween(0, 100),
            'loves_count' => fake()->numberBetween(0, 50),
            'wow_count' => fake()->numberBetween(0, 30),
            'comments_count' => fake()->numberBetween(0, 50),
            'shares_count' => fake()->numberBetween(0, 25),
            'bookmarks_count' => fake()->numberBetween(0, 20),
            'views_count' => fake()->numberBetween(0, 500),
            'latitude' => fake()->latitude(),
            'longitude' => fake()->longitude(),
            'location_name' => fake()->city(),
            'activity_occurred_at' => fake()->dateTimeBetween('-1 year', 'now'),
            'is_real_time' => fake()->boolean(30),
            'activity_group' => fake()->optional()->word(),
            'parent_activity_id' => null,
            'relevance_score' => fake()->randomFloat(2, 0, 100),
            'boost_until' => fake()->optional()->dateTimeBetween('now', '+1 month'),
            'algorithm_data' => [
                'priority' => fake()->numberBetween(1, 10),
                'category' => fake()->word(),
            ],
            'status' => fake()->randomElement(['active', 'inactive', 'moderated']),
            'flags_count' => fake()->numberBetween(0, 5),
            'flag_reasons' => fake()->optional()->randomElements(['spam', 'inappropriate', 'misleading'], fake()->numberBetween(1, 3)),
            'moderated_by' => null,
            'moderated_at' => null,
        ];
    }

    /**
     * Indicate that the activity is featured.
     */
    public function featured(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_featured' => true,
            'visibility' => 'public',
        ]);
    }

    /**
     * Indicate that the activity is a milestone.
     */
    public function milestone(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_milestone' => true,
            'engagement_score' => fake()->numberBetween(500, 1000),
        ]);
    }

    /**
     * Indicate that the activity is energy-related.
     */
    public function energyRelated(): static
    {
        return $this->state(fn (array $attributes) => [
            'activity_type' => fake()->randomElement(['energy_saved', 'solar_generated', 'installation_completed']),
            'energy_amount_kwh' => fake()->randomFloat(2, 10, 500),
        ]);
    }
}