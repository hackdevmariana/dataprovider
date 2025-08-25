<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Leaderboard;
use App\Models\User;
use App\Models\Cooperative;
use App\Models\Region;
use Carbon\Carbon;

class LeaderboardSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Limpiar leaderboards existentes
        Leaderboard::truncate();

        $users = User::all();
        $cooperatives = Cooperative::all();
        $regions = Region::all();

        if ($users->isEmpty()) {
            $this->command->warn('No hay usuarios disponibles para crear leaderboards.');
            return;
        }

        $leaderboardTypes = [
            'energy_savings',
            'reputation',
            'contributions',
            'projects',
            'community_engagement',
            'carbon_reduction',
            'knowledge_sharing',
            'innovation',
            'sustainability_score',
            'peer_mentoring'
        ];

        $periods = ['daily', 'weekly', 'monthly', 'yearly', 'all_time'];
        $scopes = ['global', 'cooperative', 'regional', 'topic'];

        // Crear leaderboards globales
        $this->createGlobalLeaderboards($leaderboardTypes, $periods);

        // Crear leaderboards por cooperativa
        foreach ($cooperatives->take(8) as $cooperative) {
            $this->createCooperativeLeaderboards($cooperative, $leaderboardTypes, $periods);
        }

        // Crear leaderboards por región
        foreach ($regions->take(10) as $region) {
            $this->createRegionalLeaderboards($region, $leaderboardTypes, $periods);
        }

        $this->command->info('✅ Se han creado ' . Leaderboard::count() . ' leaderboards.');
    }

    private function createGlobalLeaderboards(array $types, array $periods): void
    {
        foreach ($types as $type) {
            foreach ($periods as $period) {
                $this->createLeaderboard([
                    'name' => $this->getGlobalLeaderboardName($type, $period),
                    'type' => $type,
                    'period' => $period,
                    'scope' => 'global',
                    'scope_id' => null,
                    'criteria' => $this->getCriteriaForType($type),
                    'rules' => $this->getRulesForType($type),
                    'is_active' => true,
                    'is_public' => true,
                    'max_positions' => rand(50, 200),
                    'start_date' => $this->getStartDateForPeriod($period),
                    'end_date' => $this->getEndDateForPeriod($period),
                    'current_rankings' => $this->generateSampleRankings($type),
                    'metadata' => $this->getMetadataForType($type, $period),
                ]);
            }
        }
    }

    private function createCooperativeLeaderboards($cooperative, array $types, array $periods): void
    {
        // Crear 2-4 leaderboards por cooperativa
        $count = rand(2, 4);
        $selectedTypes = array_rand($types, min($count, count($types)));
        
        if (!is_array($selectedTypes)) {
            $selectedTypes = [$selectedTypes];
        }
        
        foreach ($selectedTypes as $typeIndex) {
            $type = $types[$typeIndex];
            $period = $periods[array_rand($periods)];
            
            $this->createLeaderboard([
                'name' => $this->getCooperativeLeaderboardName($type, $period, $cooperative->name),
                'type' => $type,
                'period' => $period,
                'scope' => 'cooperative',
                'scope_id' => $cooperative->id,
                'criteria' => $this->getCriteriaForType($type),
                'rules' => $this->getRulesForType($type),
                'is_active' => rand(1, 10) <= 8, // 80% de probabilidad de estar activo
                'is_public' => rand(1, 10) <= 7, // 70% de probabilidad de ser público
                'max_positions' => rand(20, 100),
                'start_date' => $this->getStartDateForPeriod($period),
                'end_date' => $this->getEndDateForPeriod($period),
                'current_rankings' => $this->generateSampleRankings($type, rand(10, 50)),
                'metadata' => $this->getMetadataForType($type, $period, 'cooperative'),
            ]);
        }
    }

    private function createRegionalLeaderboards($region, array $types, array $periods): void
    {
        // Crear 1-3 leaderboards por región
        $count = rand(1, 3);
        $selectedTypes = array_rand($types, min($count, count($types)));
        
        if (!is_array($selectedTypes)) {
            $selectedTypes = [$selectedTypes];
        }
        
        foreach ($selectedTypes as $typeIndex) {
            $type = $types[$typeIndex];
            $period = $periods[array_rand($periods)];
            
            $this->createLeaderboard([
                'name' => $this->getRegionalLeaderboardName($type, $period, $region->name),
                'type' => $type,
                'period' => $period,
                'scope' => 'regional',
                'scope_id' => $region->id,
                'criteria' => $this->getCriteriaForType($type),
                'rules' => $this->getRulesForType($type),
                'is_active' => rand(1, 10) <= 9, // 90% de probabilidad de estar activo
                'is_public' => rand(1, 10) <= 8, // 80% de probabilidad de ser público
                'max_positions' => rand(30, 150),
                'start_date' => $this->getStartDateForPeriod($period),
                'end_date' => $this->getEndDateForPeriod($period),
                'current_rankings' => $this->generateSampleRankings($type, rand(20, 100)),
                'metadata' => $this->getMetadataForType($type, $period, 'regional'),
            ]);
        }
    }

    private function createLeaderboard(array $data): void
    {
        Leaderboard::create($data);
    }

    private function getGlobalLeaderboardName(string $type, string $period): string
    {
        $typeLabels = [
            'energy_savings' => 'Ahorro de Energía',
            'reputation' => 'Reputación',
            'contributions' => 'Contribuciones',
            'projects' => 'Proyectos',
            'community_engagement' => 'Participación Comunitaria',
            'carbon_reduction' => 'Reducción de Carbono',
            'knowledge_sharing' => 'Compartir Conocimiento',
            'innovation' => 'Innovación',
            'sustainability_score' => 'Puntuación de Sostenibilidad',
            'peer_mentoring' => 'Mentoría entre Pares',
        ];

        $periodLabels = [
            'daily' => 'Diario',
            'weekly' => 'Semanal',
            'monthly' => 'Mensual',
            'yearly' => 'Anual',
            'all_time' => 'Histórico',
        ];

        return "🏆 {$typeLabels[$type]} - {$periodLabels[$period]} (Global)";
    }

    private function getCooperativeLeaderboardName(string $type, string $period, string $cooperativeName): string
    {
        $typeLabels = [
            'energy_savings' => 'Ahorro de Energía',
            'reputation' => 'Reputación',
            'contributions' => 'Contribuciones',
            'projects' => 'Proyectos',
            'community_engagement' => 'Participación Comunitaria',
            'carbon_reduction' => 'Reducción de Carbono',
            'knowledge_sharing' => 'Compartir Conocimiento',
            'innovation' => 'Innovación',
            'sustainability_score' => 'Puntuación de Sostenibilidad',
            'peer_mentoring' => 'Mentoría entre Pares',
        ];

        $periodLabels = [
            'daily' => 'Diario',
            'weekly' => 'Semanal',
            'monthly' => 'Mensual',
            'yearly' => 'Anual',
            'all_time' => 'Histórico',
        ];

        return "🥇 {$typeLabels[$type]} - {$periodLabels[$period]} - {$cooperativeName}";
    }

    private function getRegionalLeaderboardName(string $type, string $period, string $regionName): string
    {
        $typeLabels = [
            'energy_savings' => 'Ahorro de Energía',
            'reputation' => 'Reputación',
            'contributions' => 'Contribuciones',
            'projects' => 'Proyectos',
            'community_engagement' => 'Participación Comunitaria',
            'carbon_reduction' => 'Reducción de Carbono',
            'knowledge_sharing' => 'Compartir Conocimiento',
            'innovation' => 'Innovación',
            'sustainability_score' => 'Puntuación de Sostenibilidad',
            'peer_mentoring' => 'Mentoría entre Pares',
        ];

        $periodLabels = [
            'daily' => 'Diario',
            'weekly' => 'Semanal',
            'monthly' => 'Mensual',
            'yearly' => 'Anual',
            'all_time' => 'Histórico',
        ];

        return "🏅 {$typeLabels[$type]} - {$periodLabels[$period]} - {$regionName}";
    }

    private function getCriteriaForType(string $type): array
    {
        return match ($type) {
            'energy_savings' => [
                'metric' => 'kwh_saved',
                'calculation_method' => 'cumulative',
                'minimum_threshold' => 10,
                'bonus_multiplier' => 1.5,
                'penalty_multiplier' => 0.8,
            ],
            'reputation' => [
                'metric' => 'reputation_points',
                'calculation_method' => 'weighted_average',
                'minimum_threshold' => 100,
                'bonus_multiplier' => 1.2,
                'penalty_multiplier' => 0.9,
            ],
            'contributions' => [
                'metric' => 'contribution_count',
                'calculation_method' => 'sum',
                'minimum_threshold' => 5,
                'bonus_multiplier' => 1.3,
                'penalty_multiplier' => 0.7,
            ],
            'projects' => [
                'metric' => 'project_score',
                'calculation_method' => 'weighted_average',
                'minimum_threshold' => 50,
                'bonus_multiplier' => 1.4,
                'penalty_multiplier' => 0.8,
            ],
            'community_engagement' => [
                'metric' => 'engagement_points',
                'calculation_method' => 'cumulative',
                'minimum_threshold' => 20,
                'bonus_multiplier' => 1.25,
                'penalty_multiplier' => 0.85,
            ],
            'carbon_reduction' => [
                'metric' => 'kg_co2_reduced',
                'calculation_method' => 'cumulative',
                'minimum_threshold' => 100,
                'bonus_multiplier' => 1.6,
                'penalty_multiplier' => 0.7,
            ],
            'knowledge_sharing' => [
                'metric' => 'knowledge_points',
                'calculation_method' => 'weighted_average',
                'minimum_threshold' => 30,
                'bonus_multiplier' => 1.35,
                'penalty_multiplier' => 0.8,
            ],
            'innovation' => [
                'metric' => 'innovation_score',
                'calculation_method' => 'weighted_average',
                'minimum_threshold' => 75,
                'bonus_multiplier' => 1.5,
                'penalty_multiplier' => 0.6,
            ],
            'sustainability_score' => [
                'metric' => 'sustainability_points',
                'calculation_method' => 'weighted_average',
                'minimum_threshold' => 60,
                'bonus_multiplier' => 1.3,
                'penalty_multiplier' => 0.8,
            ],
            'peer_mentoring' => [
                'metric' => 'mentoring_hours',
                'calculation_method' => 'cumulative',
                'minimum_threshold' => 10,
                'bonus_multiplier' => 1.4,
                'penalty_multiplier' => 0.8,
            ],
            default => [
                'metric' => 'general_score',
                'calculation_method' => 'weighted_average',
                'minimum_threshold' => 25,
                'bonus_multiplier' => 1.2,
                'penalty_multiplier' => 0.8,
            ],
        };
    }

    private function getRulesForType(string $type): array
    {
        return match ($type) {
            'energy_savings' => [
                'update_frequency' => 'daily',
                'ranking_algorithm' => 'descending',
                'tie_breaker' => 'timestamp',
                'bonus_criteria' => ['renewable_energy', 'efficiency_improvement'],
                'penalty_criteria' => ['energy_waste', 'inefficient_usage'],
            ],
            'reputation' => [
                'update_frequency' => 'hourly',
                'ranking_algorithm' => 'descending',
                'tie_breaker' => 'activity_level',
                'bonus_criteria' => ['helpful_answers', 'quality_content'],
                'penalty_criteria' => ['spam', 'inappropriate_content'],
            ],
            'contributions' => [
                'update_frequency' => 'daily',
                'ranking_algorithm' => 'descending',
                'tie_breaker' => 'contribution_quality',
                'bonus_criteria' => ['high_quality', 'community_impact'],
                'penalty_criteria' => ['low_quality', 'minimal_impact'],
            ],
            'projects' => [
                'update_frequency' => 'weekly',
                'ranking_algorithm' => 'descending',
                'tie_breaker' => 'project_completion',
                'bonus_criteria' => ['innovation', 'sustainability'],
                'penalty_criteria' => ['delays', 'quality_issues'],
            ],
            'community_engagement' => [
                'update_frequency' => 'daily',
                'ranking_algorithm' => 'descending',
                'tie_breaker' => 'engagement_quality',
                'bonus_criteria' => ['active_participation', 'positive_impact'],
                'penalty_criteria' => ['passive_participation', 'negative_impact'],
            ],
            'carbon_reduction' => [
                'update_frequency' => 'daily',
                'ranking_algorithm' => 'descending',
                'tie_breaker' => 'verification_status',
                'bonus_criteria' => ['verified_reductions', 'long_term_impact'],
                'penalty_criteria' => ['unverified_claims', 'temporary_changes'],
            ],
            'knowledge_sharing' => [
                'update_frequency' => 'daily',
                'ranking_algorithm' => 'descending',
                'tie_breaker' => 'content_quality',
                'bonus_criteria' => ['original_content', 'helpful_responses'],
                'penalty_criteria' => ['copied_content', 'unhelpful_responses'],
            ],
            'innovation' => [
                'update_frequency' => 'weekly',
                'ranking_algorithm' => 'descending',
                'tie_breaker' => 'innovation_impact',
                'bonus_criteria' => ['breakthrough_ideas', 'practical_implementation'],
                'penalty_criteria' => ['unrealistic_proposals', 'poor_execution'],
            ],
            'sustainability_score' => [
                'update_frequency' => 'weekly',
                'ranking_algorithm' => 'descending',
                'tie_breaker' => 'verification_level',
                'bonus_criteria' => ['certified_practices', 'measurable_impact'],
                'penalty_criteria' => ['unverified_practices', 'minimal_impact'],
            ],
            'peer_mentoring' => [
                'update_frequency' => 'daily',
                'ranking_algorithm' => 'descending',
                'tie_breaker' => 'mentee_feedback',
                'bonus_criteria' => ['positive_feedback', 'mentee_success'],
                'penalty_criteria' => ['negative_feedback', 'mentee_failure'],
            ],
            default => [
                'update_frequency' => 'daily',
                'ranking_algorithm' => 'descending',
                'tie_breaker' => 'timestamp',
                'bonus_criteria' => ['quality', 'impact'],
                'penalty_criteria' => ['poor_quality', 'minimal_impact'],
            ],
        };
    }

    private function generateSampleRankings(string $type, int $maxPositions = 100): array
    {
        $rankings = [];
        $users = User::all();
        
        if ($users->isEmpty()) {
            return [];
        }

        $positions = min($maxPositions, $users->count());
        
        for ($i = 0; $i < $positions; $i++) {
            $user = $users[$i % $users->count()];
            $score = $this->generateScoreForType($type, $i + 1);
            
            $rankings[] = [
                'position' => $i + 1,
                'user_id' => $user->id,
                'user_name' => $user->name,
                'score' => $score,
                'change' => $this->getRandomChange(),
                'last_updated' => now()->subHours(rand(1, 24))->toISOString(),
                'metadata' => $this->getRankingMetadata($type, $score),
            ];
        }

        return $rankings;
    }

    private function generateScoreForType(string $type, int $position): float
    {
        $baseScore = match ($type) {
            'energy_savings' => rand(100, 5000),
            'reputation' => rand(500, 10000),
            'contributions' => rand(10, 500),
            'projects' => rand(50, 1000),
            'community_engagement' => rand(100, 2000),
            'carbon_reduction' => rand(200, 5000),
            'knowledge_sharing' => rand(50, 1000),
            'innovation' => rand(75, 1500),
            'sustainability_score' => rand(60, 95),
            'peer_mentoring' => rand(20, 200),
            default => rand(100, 1000),
        };

        // Aplicar bonificación por posición (primeros lugares tienen mejor puntuación)
        $positionBonus = max(1, (101 - $position) / 100);
        
        return round($baseScore * $positionBonus, 2);
    }

    private function getRandomChange(): string
    {
        $changes = ['up', 'down', 'stable', 'new'];
        return $changes[array_rand($changes)];
    }

    private function getRankingMetadata(string $type, float $score): array
    {
        return match ($type) {
            'energy_savings' => [
                'kwh_saved' => $score,
                'savings_percentage' => rand(15, 45),
                'renewable_energy_usage' => rand(20, 80),
            ],
            'reputation' => [
                'helpful_answers' => rand(10, 200),
                'quality_score' => rand(80, 100),
                'community_trust' => rand(70, 100),
            ],
            'contributions' => [
                'articles_written' => rand(5, 50),
                'code_contributions' => rand(10, 100),
                'documentation_improvements' => rand(20, 150),
            ],
            'projects' => [
                'projects_completed' => rand(1, 20),
                'success_rate' => rand(80, 100),
                'impact_score' => rand(70, 100),
            ],
            'community_engagement' => [
                'events_attended' => rand(5, 30),
                'volunteer_hours' => rand(10, 100),
                'mentoring_sessions' => rand(2, 20),
            ],
            'carbon_reduction' => [
                'kg_co2_reduced' => $score,
                'reduction_methods' => ['solar_panels', 'efficient_appliances', 'behavioral_changes'],
                'verification_status' => 'verified',
            ],
            'knowledge_sharing' => [
                'articles_published' => rand(3, 25),
                'presentations_given' => rand(1, 15),
                'mentoring_hours' => rand(5, 50),
            ],
            'innovation' => [
                'patents_filed' => rand(0, 5),
                'innovations_implemented' => rand(1, 10),
                'research_papers' => rand(0, 8),
            ],
            'sustainability_score' => [
                'environmental_impact' => rand(70, 95),
                'social_responsibility' => rand(75, 95),
                'economic_sustainability' => rand(65, 90),
            ],
            'peer_mentoring' => [
                'mentees_helped' => rand(2, 15),
                'mentoring_hours' => $score,
                'success_rate' => rand(80, 100),
            ],
            default => [
                'general_score' => $score,
                'activity_level' => rand(50, 100),
                'quality_rating' => rand(70, 100),
            ],
        };
    }

    private function getMetadataForType(string $type, string $period, string $scope = 'global'): array
    {
        return [
            'leaderboard_category' => $type,
            'update_schedule' => $this->getUpdateSchedule($period),
            'scope_type' => $scope,
            'ranking_algorithm' => 'weighted_score',
            'bonus_system' => 'enabled',
            'penalty_system' => 'enabled',
            'verification_required' => in_array($type, ['carbon_reduction', 'sustainability_score']),
            'community_voting' => in_array($type, ['reputation', 'contributions']),
            'expert_review' => in_array($type, ['innovation', 'projects']),
            'auto_calculation' => true,
            'manual_override' => false,
            'historical_data' => 'available',
            'trend_analysis' => 'enabled',
            'social_features' => [
                'sharing_enabled' => true,
                'comments_enabled' => true,
                'achievement_badges' => true,
                'milestone_celebrations' => true,
            ],
        ];
    }

    private function getUpdateSchedule(string $period): string
    {
        return match ($period) {
            'daily' => 'every_24_hours',
            'weekly' => 'every_7_days',
            'monthly' => 'every_30_days',
            'yearly' => 'every_365_days',
            'all_time' => 'on_demand',
            default => 'daily',
        };
    }

    private function getStartDateForPeriod(string $period): Carbon
    {
        return match ($period) {
            'daily' => now()->subDays(rand(1, 7)),
            'weekly' => now()->subWeeks(rand(1, 4)),
            'monthly' => now()->subMonths(rand(1, 12)),
            'yearly' => now()->subYears(rand(1, 3)),
            'all_time' => now()->subYears(rand(2, 5)),
            default => now()->subDays(rand(1, 30)),
        };
    }

    private function getEndDateForPeriod(string $period): ?Carbon
    {
        return match ($period) {
            'daily' => now()->addDays(rand(1, 7)),
            'weekly' => now()->addWeeks(rand(1, 4)),
            'monthly' => now()->addMonths(rand(1, 12)),
            'yearly' => now()->addYears(rand(1, 3)),
            'all_time' => null, // Los leaderboards históricos no tienen fecha de fin
            default => now()->addDays(rand(1, 30)),
        };
    }
}
