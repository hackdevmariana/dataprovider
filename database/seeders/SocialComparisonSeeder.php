<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\SocialComparison;
use App\Models\User;
use Carbon\Carbon;

class SocialComparisonSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Limpiar comparaciones existentes
        SocialComparison::truncate();

        $users = User::all();

        if ($users->isEmpty()) {
            $this->command->warn('No hay usuarios disponibles para crear comparaciones sociales.');
            return;
        }

        $comparisonTypes = [
            'energy_savings',
            'carbon_reduction', 
            'community_participation',
            'renewable_energy_usage',
            'energy_efficiency',
            'sustainability_score',
            'peer_engagement',
            'knowledge_sharing',
            'project_contribution',
            'innovation_impact'
        ];

        $periods = ['daily', 'weekly', 'monthly', 'yearly', 'all_time'];
        $scopes = ['personal', 'cooperative', 'regional', 'national', 'global'];

        // Crear comparaciones para cada usuario
        foreach ($users as $user) {
            $this->createComparisonsForUser($user, $comparisonTypes, $periods, $scopes);
        }

        $this->command->info('✅ Se han creado ' . SocialComparison::count() . ' comparaciones sociales.');
    }

    private function createComparisonsForUser($user, $comparisonTypes, $periods, $scopes): void
    {
        // Crear entre 5-12 comparaciones por usuario
        $comparisonsCount = rand(5, 12);
        
        for ($i = 0; $i < $comparisonsCount; $i++) {
            $comparisonType = $comparisonTypes[array_rand($comparisonTypes)];
            $period = $periods[array_rand($periods)];
            $scope = $scopes[array_rand($scopes)];
            
            // Generar valores realistas basados en el tipo de comparación
            $comparisonData = $this->generateComparisonData($comparisonType, $period, $scope);
            
            SocialComparison::create([
                'user_id' => $user->id,
                'comparison_type' => $comparisonType,
                'period' => $period,
                'scope' => $scope,
                'scope_id' => $this->getRandomScopeId($scope),
                'user_value' => $comparisonData['user_value'],
                'unit' => $comparisonData['unit'],
                'average_value' => $comparisonData['average_value'],
                'median_value' => $comparisonData['median_value'],
                'best_value' => $comparisonData['best_value'],
                'user_rank' => $comparisonData['user_rank'],
                'total_participants' => $comparisonData['total_participants'],
                'percentile' => $comparisonData['percentile'],
                'breakdown' => $comparisonData['breakdown'],
                'metadata' => $comparisonData['metadata'],
                'is_public' => rand(1, 10) <= 8, // 80% de probabilidad de ser público
                'comparison_date' => $this->getRandomComparisonDate($period),
            ]);
        }
    }

    private function generateComparisonData(string $comparisonType, string $period, string $scope): array
    {
        $data = match ($comparisonType) {
            'energy_savings' => $this->getEnergySavingsData($period, $scope),
            'carbon_reduction' => $this->getCarbonReductionData($period, $scope),
            'community_participation' => $this->getCommunityParticipationData($period, $scope),
            'renewable_energy_usage' => $this->getRenewableEnergyUsageData($period, $scope),
            'energy_efficiency' => $this->getEnergyEfficiencyData($period, $scope),
            'sustainability_score' => $this->getSustainabilityScoreData($period, $scope),
            'peer_engagement' => $this->getPeerEngagementData($period, $scope),
            'knowledge_sharing' => $this->getKnowledgeSharingData($period, $scope),
            'project_contribution' => $this->getProjectContributionData($period, $scope),
            'innovation_impact' => $this->getInnovationImpactData($period, $scope),
            default => $this->getGenericComparisonData($period, $scope),
        };

        // Calcular percentil basado en el ranking
        $data['percentile'] = $this->calculatePercentile($data['user_rank'], $data['total_participants']);
        
        return $data;
    }

    private function getEnergySavingsData(string $period, string $scope): array
    {
        $multiplier = $this->getPeriodMultiplier($period);
        $scopeMultiplier = $this->getScopeMultiplier($scope);
        
        $userValue = rand(50, 500) * $multiplier * $scopeMultiplier;
        $totalParticipants = $this->getTotalParticipants($scope);
        $userRank = rand(1, $totalParticipants);
        
        return [
            'user_value' => $userValue,
            'unit' => 'kWh',
            'average_value' => $userValue * rand(80, 120) / 100,
            'median_value' => $userValue * rand(85, 115) / 100,
            'best_value' => $userValue * rand(150, 300) / 100,
            'user_rank' => $userRank,
            'total_participants' => $totalParticipants,
            'breakdown' => [
                'solar_savings' => $userValue * 0.6,
                'efficiency_savings' => $userValue * 0.3,
                'behavioral_savings' => $userValue * 0.1,
            ],
            'metadata' => [
                'energy_source' => 'mixed',
                'savings_method' => 'efficiency_and_renewables',
                'comparison_accuracy' => 'high',
                'data_source' => 'smart_meter',
            ],
        ];
    }

    private function getCarbonReductionData(string $period, string $scope): array
    {
        $multiplier = $this->getPeriodMultiplier($period);
        $scopeMultiplier = $this->getScopeMultiplier($scope);
        
        $userValue = rand(100, 1000) * $multiplier * $scopeMultiplier;
        $totalParticipants = $this->getTotalParticipants($scope);
        $userRank = rand(1, $totalParticipants);
        
        return [
            'user_value' => $userValue,
            'unit' => 'kg_co2',
            'average_value' => $userValue * rand(80, 120) / 100,
            'median_value' => $userValue * rand(85, 115) / 100,
            'best_value' => $userValue * rand(150, 300) / 100,
            'user_rank' => $userRank,
            'total_participants' => $totalParticipants,
            'breakdown' => [
                'transport_reduction' => $userValue * 0.4,
                'energy_reduction' => $userValue * 0.35,
                'waste_reduction' => $userValue * 0.15,
                'consumption_reduction' => $userValue * 0.1,
            ],
            'metadata' => [
                'calculation_method' => 'lifecycle_assessment',
                'verification_status' => 'verified',
                'offset_programs' => ['tree_planting', 'renewable_energy'],
            ],
        ];
    }

    private function getCommunityParticipationData(string $period, string $scope): array
    {
        $multiplier = $this->getPeriodMultiplier($period);
        $scopeMultiplier = $this->getScopeMultiplier($scope);
        
        $userValue = rand(10, 100) * $multiplier * $scopeMultiplier;
        $totalParticipants = $this->getTotalParticipants($scope);
        $userRank = rand(1, $totalParticipants);
        
        return [
            'user_value' => $userValue,
            'unit' => 'points',
            'average_value' => $userValue * rand(80, 120) / 100,
            'median_value' => $userValue * rand(85, 115) / 100,
            'best_value' => $userValue * rand(150, 300) / 100,
            'user_rank' => $userRank,
            'total_participants' => $totalParticipants,
            'breakdown' => [
                'events_attended' => $userValue * 0.4,
                'volunteer_hours' => $userValue * 0.3,
                'knowledge_shared' => $userValue * 0.2,
                'mentoring_sessions' => $userValue * 0.1,
            ],
            'metadata' => [
                'participation_type' => 'active',
                'community_role' => 'contributor',
                'recognition_level' => 'high',
            ],
        ];
    }

    private function getRenewableEnergyUsageData(string $period, string $scope): array
    {
        $multiplier = $this->getPeriodMultiplier($period);
        $scopeMultiplier = $this->getScopeMultiplier($scope);
        
        $userValue = rand(200, 2000) * $multiplier * $scopeMultiplier;
        $totalParticipants = $this->getTotalParticipants($scope);
        $userRank = rand(1, $totalParticipants);
        
        return [
            'user_value' => $userValue,
            'unit' => 'kWh',
            'average_value' => $userValue * rand(80, 120) / 100,
            'median_value' => $userValue * rand(85, 115) / 100,
            'best_value' => $userValue * rand(150, 300) / 100,
            'user_rank' => $userRank,
            'total_participants' => $totalParticipants,
            'breakdown' => [
                'solar_generation' => $userValue * 0.7,
                'wind_generation' => $userValue * 0.2,
                'biomass_generation' => $userValue * 0.1,
            ],
            'metadata' => [
                'installation_type' => 'residential',
                'grid_connection' => 'hybrid',
                'battery_storage' => 'yes',
            ],
        ];
    }

    private function getEnergyEfficiencyData(string $period, string $scope): array
    {
        $multiplier = $this->getPeriodMultiplier($period);
        $scopeMultiplier = $this->getScopeMultiplier($scope);
        
        $userValue = rand(70, 95) * $multiplier * $scopeMultiplier;
        $totalParticipants = $this->getTotalParticipants($scope);
        $userRank = rand(1, $totalParticipants);
        
        return [
            'user_value' => $userValue,
            'unit' => 'percentage',
            'average_value' => $userValue * rand(80, 120) / 100,
            'median_value' => $userValue * rand(85, 115) / 100,
            'best_value' => $userValue * rand(150, 300) / 100,
            'user_rank' => $userRank,
            'total_participants' => $totalParticipants,
            'breakdown' => [
                'building_efficiency' => $userValue * 0.4,
                'appliance_efficiency' => $userValue * 0.3,
                'behavioral_efficiency' => $userValue * 0.2,
                'system_optimization' => $userValue * 0.1,
            ],
            'metadata' => [
                'certification' => 'energy_star',
                'audit_date' => now()->subMonths(rand(1, 12))->toISOString(),
                'improvement_potential' => 'medium',
            ],
        ];
    }

    private function getSustainabilityScoreData(string $period, string $scope): array
    {
        $multiplier = $this->getPeriodMultiplier($period);
        $scopeMultiplier = $this->getScopeMultiplier($scope);
        
        $userValue = rand(60, 95) * $multiplier * $scopeMultiplier;
        $totalParticipants = $this->getTotalParticipants($scope);
        $userRank = rand(1, $totalParticipants);
        
        return [
            'user_value' => $userValue,
            'unit' => 'points',
            'average_value' => $userValue * rand(80, 120) / 100,
            'median_value' => $userValue * rand(85, 115) / 100,
            'best_value' => $userValue * rand(150, 300) / 100,
            'user_rank' => $userRank,
            'total_participants' => $totalParticipants,
            'breakdown' => [
                'environmental_impact' => $userValue * 0.4,
                'social_responsibility' => $userValue * 0.3,
                'economic_sustainability' => $userValue * 0.2,
                'innovation_leadership' => $userValue * 0.1,
            ],
            'metadata' => [
                'assessment_framework' => 'esg_standards',
                'verification_body' => 'independent_auditor',
                'reporting_frequency' => 'quarterly',
            ],
        ];
    }

    private function getPeerEngagementData(string $period, string $scope): array
    {
        $multiplier = $this->getPeriodMultiplier($period);
        $scopeMultiplier = $this->getScopeMultiplier($scope);
        
        $userValue = rand(20, 150) * $multiplier * $scopeMultiplier;
        $totalParticipants = $this->getTotalParticipants($scope);
        $userRank = rand(1, $totalParticipants);
        
        return [
            'user_value' => $userValue,
            'unit' => 'interactions',
            'average_value' => $userValue * rand(80, 120) / 100,
            'median_value' => $userValue * rand(85, 115) / 100,
            'best_value' => $userValue * rand(150, 300) / 100,
            'user_rank' => $userRank,
            'total_participants' => $totalParticipants,
            'breakdown' => [
                'online_interactions' => $userValue * 0.6,
                'offline_meetings' => $userValue * 0.25,
                'collaborative_projects' => $userValue * 0.15,
            ],
            'metadata' => [
                'engagement_quality' => 'high',
                'network_growth' => 'positive',
                'influence_score' => 'medium',
            ],
        ];
    }

    private function getKnowledgeSharingData(string $period, string $scope): array
    {
        $multiplier = $this->getPeriodMultiplier($period);
        $scopeMultiplier = $this->getScopeMultiplier($scope);
        
        $userValue = rand(5, 50) * $multiplier * $scopeMultiplier;
        $totalParticipants = $this->getTotalParticipants($scope);
        $userRank = rand(1, $totalParticipants);
        
        return [
            'user_value' => $userValue,
            'unit' => 'contributions',
            'average_value' => $userValue * rand(80, 120) / 100,
            'median_value' => $userValue * rand(85, 115) / 100,
            'best_value' => $userValue * rand(150, 300) / 100,
            'user_rank' => $userRank,
            'total_participants' => $totalParticipants,
            'breakdown' => [
                'articles_published' => $userValue * 0.4,
                'presentations_given' => $userValue * 0.3,
                'mentoring_sessions' => $userValue * 0.2,
                'resource_creation' => $userValue * 0.1,
            ],
            'metadata' => [
                'expertise_level' => 'intermediate',
                'content_quality' => 'high',
                'audience_reach' => 'medium',
            ],
        ];
    }

    private function getProjectContributionData(string $period, string $scope): array
    {
        $multiplier = $this->getPeriodMultiplier($period);
        $scopeMultiplier = $this->getScopeMultiplier($scope);
        
        $userValue = rand(10, 100) * $multiplier * $scopeMultiplier;
        $totalParticipants = $this->getTotalParticipants($scope);
        $userRank = rand(1, $totalParticipants);
        
        return [
            'user_value' => $userValue,
            'unit' => 'hours',
            'average_value' => $userValue * rand(80, 120) / 100,
            'median_value' => $userValue * rand(85, 115) / 100,
            'best_value' => $userValue * rand(150, 300) / 100,
            'user_rank' => $userRank,
            'total_participants' => $totalParticipants,
            'breakdown' => [
                'project_planning' => $userValue * 0.3,
                'implementation' => $userValue * 0.4,
                'testing_validation' => $userValue * 0.2,
                'documentation' => $userValue * 0.1,
            ],
            'metadata' => [
                'project_type' => 'renewable_energy',
                'role' => 'contributor',
                'impact_level' => 'medium',
            ],
        ];
    }

    private function getInnovationImpactData(string $period, string $scope): array
    {
        $multiplier = $this->getPeriodMultiplier($period);
        $scopeMultiplier = $this->getScopeMultiplier($scope);
        
        $userValue = rand(1, 20) * $multiplier * $scopeMultiplier;
        $totalParticipants = $this->getTotalParticipants($scope);
        $userRank = rand(1, $totalParticipants);
        
        return [
            'user_value' => $userValue,
            'unit' => 'innovations',
            'average_value' => $userValue * rand(80, 120) / 100,
            'median_value' => $userValue * rand(85, 115) / 100,
            'best_value' => $userValue * rand(150, 300) / 100,
            'user_rank' => $userRank,
            'total_participants' => $totalParticipants,
            'breakdown' => [
                'process_innovations' => $userValue * 0.5,
                'product_innovations' => $userValue * 0.3,
                'service_innovations' => $userValue * 0.2,
            ],
            'metadata' => [
                'innovation_type' => 'incremental',
                'adoption_rate' => 'medium',
                'patent_status' => 'pending',
            ],
        ];
    }

    private function getGenericComparisonData(string $period, string $scope): array
    {
        $multiplier = $this->getPeriodMultiplier($period);
        $scopeMultiplier = $this->getScopeMultiplier($scope);
        
        $userValue = rand(50, 500) * $multiplier * $scopeMultiplier;
        $totalParticipants = $this->getTotalParticipants($scope);
        $userRank = rand(1, $totalParticipants);
        
        return [
            'user_value' => $userValue,
            'unit' => 'units',
            'average_value' => $userValue * rand(80, 120) / 100,
            'median_value' => $userValue * rand(85, 115) / 100,
            'best_value' => $userValue * rand(150, 300) / 100,
            'user_rank' => $userRank,
            'total_participants' => $totalParticipants,
            'breakdown' => [
                'component_a' => $userValue * 0.4,
                'component_b' => $userValue * 0.3,
                'component_c' => $userValue * 0.2,
                'component_d' => $userValue * 0.1,
            ],
            'metadata' => [
                'data_quality' => 'high',
                'comparison_method' => 'standard',
                'update_frequency' => 'monthly',
            ],
        ];
    }

    private function getPeriodMultiplier(string $period): float
    {
        return match ($period) {
            'daily' => 1,
            'weekly' => 7,
            'monthly' => 30,
            'yearly' => 365,
            'all_time' => 1095, // 3 years
            default => 1,
        };
    }

    private function getScopeMultiplier(string $scope): float
    {
        return match ($scope) {
            'personal' => 1,
            'cooperative' => 0.8,
            'regional' => 0.6,
            'national' => 0.4,
            'global' => 0.2,
            default => 1,
        };
    }

    private function getTotalParticipants(string $scope): int
    {
        return match ($scope) {
            'personal' => rand(10, 50),
            'cooperative' => rand(50, 200),
            'regional' => rand(200, 1000),
            'national' => rand(1000, 10000),
            'global' => rand(10000, 100000),
            default => rand(100, 1000),
        };
    }

    private function getRandomScopeId(string $scope): ?int
    {
        if (in_array($scope, ['personal', 'global'])) {
            return null;
        }
        
        return rand(1, 100);
    }

    private function calculatePercentile(int $rank, int $total): float
    {
        if ($total <= 1) {
            return 100.0;
        }
        
        return round(((($total - $rank) / $total) * 100), 2);
    }

    private function getRandomComparisonDate(string $period): Carbon
    {
        return match ($period) {
            'daily' => now()->subDays(rand(0, 7)),
            'weekly' => now()->subWeeks(rand(0, 4)),
            'monthly' => now()->subMonths(rand(0, 12)),
            'yearly' => now()->subYears(rand(0, 3)),
            'all_time' => now()->subYears(rand(1, 5)),
            default => now()->subDays(rand(0, 30)),
        };
    }
}
