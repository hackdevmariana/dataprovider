<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ActivityFeed;
use App\Models\User;
use Carbon\Carbon;

class ActivityFeedsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::take(25)->get();
        
        if ($users->isEmpty()) {
            $this->command->warn('No hay usuarios disponibles. Ejecuta primero UserSeeder.');
            return;
        }

        $activityTypes = [
            'energy_saved', 'solar_generated', 'achievement_unlocked', 
            'challenge_completed', 'project_funded', 'installation_completed',
            'cooperative_joined', 'roof_published', 'investment_made', 'milestone_reached'
        ];

        $relatedTypes = [
            'App\\Models\\Challenge', 'App\\Models\\Achievement', 'App\\Models\\Topic',
            'App\\Models\\Cooperative', 'App\\Models\\User', 'App\\Models\\Post'
        ];

        $descriptions = [
            'Ahorró energía en su hogar',
            'Produjo energía solar',
            'Mejoró la eficiencia energética',
            'Completó un reto',
            'Desbloqueó un logro',
            'Creó una publicación',
            'Comentó en una publicación',
            'Dio like a una publicación',
            'Compartió contenido',
            'Agregó un bookmark'
        ];

        for ($i = 0; $i < 80; $i++) {
            ActivityFeed::create([
                'user_id' => $users->random()->id,
                'activity_type' => $activityTypes[array_rand($activityTypes)],
                'related_type' => $relatedTypes[array_rand($relatedTypes)],
                'related_id' => rand(1, 10),
                'activity_data' => [
                    'energy_amount' => rand(10, 500),
                    'cost_savings' => rand(5, 100),
                    'co2_savings' => rand(1, 50),
                ],
                'description' => $descriptions[array_rand($descriptions)],
                'summary' => 'Resumen de la actividad: ' . ($i + 1),
                'energy_amount_kwh' => rand(10, 500),
                'cost_savings_eur' => rand(5, 100),
                'co2_savings_kg' => rand(1, 50),
                'investment_amount_eur' => rand(100, 5000),
                'community_impact_score' => rand(1, 100),
                'visibility' => ['public', 'cooperative', 'followers', 'private'][array_rand(['public', 'cooperative', 'followers', 'private'])],
                'is_featured' => rand(0, 10) == 0,
                'is_milestone' => rand(0, 20) == 0,
                'notify_followers' => rand(0, 1) == 1,
                'show_in_feed' => rand(0, 1) == 1,
                'allow_interactions' => rand(0, 1) == 1,
                'engagement_score' => rand(1, 100),
                'likes_count' => rand(0, 50),
                'loves_count' => rand(0, 20),
                'wow_count' => rand(0, 15),
                'comments_count' => rand(0, 30),
                'shares_count' => rand(0, 25),
                'bookmarks_count' => rand(0, 10),
                'views_count' => rand(0, 200),
                'latitude' => rand(-90, 90),
                'longitude' => rand(-180, 180),
                'location_name' => 'Ubicación ' . ($i + 1),
                'activity_occurred_at' => Carbon::now()->subDays(rand(0, 30)),
                'is_real_time' => rand(0, 1) == 1,
                'activity_group' => 'group_' . rand(1, 5),
                'parent_activity_id' => null,
            ]);
        }
    }
}
