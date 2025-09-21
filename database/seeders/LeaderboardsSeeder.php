<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Leaderboard;
use Carbon\Carbon;

class LeaderboardsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $leaderboards = [
            [
                'name' => 'Top Ahorradores de Energía',
                'type' => 'energy_savings',
                'period' => 'monthly',
                'scope' => 'global',
                'scope_id' => null,
                'criteria' => [
                    'metric' => 'energy_saved_kwh',
                    'aggregation' => 'sum',
                    'timeframe' => '30_days',
                ],
                'rules' => [
                    'min_participants' => 10,
                    'update_frequency' => 'daily',
                    'exclude_inactive' => true,
                ],
                'is_active' => true,
                'is_public' => true,
                'max_positions' => 50,
                'start_date' => Carbon::now()->subMonth(),
                'end_date' => Carbon::now()->addMonth(),
                'last_calculated_at' => Carbon::now()->subHours(2),
                'current_rankings' => [
                    ['user_id' => 1, 'score' => 1250.5, 'rank' => 1],
                    ['user_id' => 2, 'score' => 1180.2, 'rank' => 2],
                    ['user_id' => 3, 'score' => 1100.8, 'rank' => 3],
                ],
                'metadata' => [
                    'description' => 'Ranking de usuarios que más energía han ahorrado este mes',
                    'badge_color' => '#4CAF50',
                    'icon' => 'energy-saving',
                ],
            ],
            [
                'name' => 'Líderes en Producción Solar',
                'type' => 'solar_production',
                'period' => 'weekly',
                'scope' => 'cooperative',
                'scope_id' => 1,
                'criteria' => [
                    'metric' => 'solar_production_kwh',
                    'aggregation' => 'sum',
                    'timeframe' => '7_days',
                ],
                'rules' => [
                    'min_participants' => 5,
                    'update_frequency' => 'hourly',
                    'require_verification' => true,
                ],
                'is_active' => true,
                'is_public' => true,
                'max_positions' => 25,
                'start_date' => Carbon::now()->subWeek(),
                'end_date' => Carbon::now()->addWeek(),
                'last_calculated_at' => Carbon::now()->subMinutes(30),
                'current_rankings' => [
                    ['user_id' => 4, 'score' => 850.3, 'rank' => 1],
                    ['user_id' => 5, 'score' => 780.7, 'rank' => 2],
                    ['user_id' => 6, 'score' => 720.1, 'rank' => 3],
                ],
                'metadata' => [
                    'description' => 'Ranking semanal de producción solar en la cooperativa',
                    'badge_color' => '#FF9800',
                    'icon' => 'solar-panel',
                ],
            ],
            [
                'name' => 'Campeones de Sostenibilidad',
                'type' => 'sustainability',
                'period' => 'yearly',
                'scope' => 'regional',
                'scope_id' => 1,
                'criteria' => [
                    'metric' => 'sustainability_score',
                    'aggregation' => 'average',
                    'timeframe' => '365_days',
                ],
                'rules' => [
                    'min_participants' => 20,
                    'update_frequency' => 'weekly',
                    'weighted_scoring' => true,
                ],
                'is_active' => true,
                'is_public' => true,
                'max_positions' => 100,
                'start_date' => Carbon::now()->subYear(),
                'end_date' => Carbon::now()->addYear(),
                'last_calculated_at' => Carbon::now()->subDays(1),
                'current_rankings' => [
                    ['user_id' => 7, 'score' => 95.8, 'rank' => 1],
                    ['user_id' => 8, 'score' => 92.3, 'rank' => 2],
                    ['user_id' => 9, 'score' => 89.7, 'rank' => 3],
                ],
                'metadata' => [
                    'description' => 'Ranking anual de sostenibilidad por región',
                    'badge_color' => '#2196F3',
                    'icon' => 'leaf',
                ],
            ],
            [
                'name' => 'Expertos en Eficiencia',
                'type' => 'efficiency',
                'period' => 'quarterly',
                'scope' => 'topic',
                'scope_id' => 1,
                'criteria' => [
                    'metric' => 'efficiency_improvement',
                    'aggregation' => 'percentage',
                    'timeframe' => '90_days',
                ],
                'rules' => [
                    'min_participants' => 15,
                    'update_frequency' => 'daily',
                    'require_baseline' => true,
                ],
                'is_active' => true,
                'is_public' => true,
                'max_positions' => 30,
                'start_date' => Carbon::now()->subMonths(3),
                'end_date' => Carbon::now()->addMonths(3),
                'last_calculated_at' => Carbon::now()->subHours(6),
                'current_rankings' => [
                    ['user_id' => 10, 'score' => 45.2, 'rank' => 1],
                    ['user_id' => 11, 'score' => 42.8, 'rank' => 2],
                    ['user_id' => 12, 'score' => 40.1, 'rank' => 3],
                ],
                'metadata' => [
                    'description' => 'Ranking trimestral de mejora en eficiencia energética',
                    'badge_color' => '#9C27B0',
                    'icon' => 'efficiency',
                ],
            ],
            [
                'name' => 'Comunidad Más Activa',
                'type' => 'community_engagement',
                'period' => 'monthly',
                'scope' => 'cooperative',
                'scope_id' => 2,
                'criteria' => [
                    'metric' => 'engagement_score',
                    'aggregation' => 'sum',
                    'timeframe' => '30_days',
                ],
                'rules' => [
                    'min_participants' => 8,
                    'update_frequency' => 'daily',
                    'include_social_actions' => true,
                ],
                'is_active' => true,
                'is_public' => true,
                'max_positions' => 20,
                'start_date' => Carbon::now()->subMonth(),
                'end_date' => Carbon::now()->addMonth(),
                'last_calculated_at' => Carbon::now()->subHours(1),
                'current_rankings' => [
                    ['user_id' => 13, 'score' => 2850.0, 'rank' => 1],
                    ['user_id' => 14, 'score' => 2650.5, 'rank' => 2],
                    ['user_id' => 15, 'score' => 2400.8, 'rank' => 3],
                ],
                'metadata' => [
                    'description' => 'Ranking de participación comunitaria en la cooperativa',
                    'badge_color' => '#E91E63',
                    'icon' => 'community',
                ],
            ],
        ];

        foreach ($leaderboards as $leaderboardData) {
            Leaderboard::create($leaderboardData);
        }
    }
}
