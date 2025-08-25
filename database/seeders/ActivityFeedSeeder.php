<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ActivityFeed;
use App\Models\User;
use App\Models\Cooperative;
use App\Models\EnergyInstallation;
use App\Models\TopicPost;
use App\Models\UserAchievement;
use App\Models\UserChallenge;
use Carbon\Carbon;

class ActivityFeedSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Limpiar activity-feeds existentes
        ActivityFeed::truncate();

        $users = User::all();
        $cooperatives = Cooperative::all();
        $installations = EnergyInstallation::all();
        $posts = TopicPost::all();
        $achievements = UserAchievement::all();
        $challenges = UserChallenge::all();

        if ($users->isEmpty()) {
            $this->command->warn('No hay usuarios disponibles para crear activity-feeds.');
            return;
        }

        // Crear activity-feeds para cada usuario
        foreach ($users as $user) {
            $this->createActivityFeedsForUser($user, $cooperatives, $installations, $posts, $achievements, $challenges);
        }

        $this->command->info('✅ Se han creado ' . ActivityFeed::count() . ' activity-feeds.');
    }

    private function createActivityFeedsForUser($user, $cooperatives, $installations, $posts, $achievements, $challenges): void
    {
        // Crear entre 8-20 activity-feeds por usuario
        $feedsCount = rand(8, 20);
        
        for ($i = 0; $i < $feedsCount; $i++) {
            $this->createActivityFeed($user, $cooperatives, $installations, $posts, $achievements, $challenges);
        }
    }

    private function createActivityFeed($user, $cooperatives, $installations, $posts, $achievements, $challenges): void
    {
        $activityType = $this->getRandomActivityType();
        $relatedData = $this->getRelatedData($activityType, $cooperatives, $installations, $posts, $achievements, $challenges);
        
        if (!$relatedData) {
            return; // No hay datos relacionados disponibles
        }

        $activityData = $this->generateActivityData($activityType, $relatedData);
        $metrics = $this->generateMetrics($activityType, $relatedData);
        
        $activityFeedData = [
            'user_id' => $user->id,
            'activity_type' => $activityType,
            'related_type' => $relatedData['type'],
            'related_id' => $relatedData['id'],
            'activity_data' => $activityData,
            'description' => $this->generateDescription($activityType, $relatedData),
            'summary' => $this->generateSummary($activityType, $relatedData),
            'energy_amount_kwh' => $metrics['energy_amount_kwh'],
            'cost_savings_eur' => $metrics['cost_savings_eur'],
            'co2_savings_kg' => $metrics['co2_savings_kg'],
            'investment_amount_eur' => $metrics['investment_amount_eur'],
            'community_impact_score' => $metrics['community_impact_score'],
            'visibility' => $this->getRandomVisibility(),
            'is_featured' => $this->shouldBeFeatured(),
            'is_milestone' => $this->shouldBeMilestone($activityType),
            'notify_followers' => rand(1, 100) <= 80, // 80% de probabilidad
            'show_in_feed' => rand(1, 100) <= 90, // 90% de probabilidad
            'allow_interactions' => rand(1, 100) <= 85, // 85% de probabilidad
            'engagement_score' => rand(0, 1000),
            'likes_count' => rand(0, 50),
            'loves_count' => rand(0, 20),
            'wow_count' => rand(0, 15),
            'comments_count' => rand(0, 30),
            'shares_count' => rand(0, 25),
            'bookmarks_count' => rand(0, 10),
            'views_count' => rand(10, 200),
            'latitude' => $this->getRandomLatitude(),
            'longitude' => $this->getRandomLongitude(),
            'location_name' => $this->getRandomLocationName(),
            'activity_occurred_at' => $this->getRandomActivityDate(),
            'is_real_time' => rand(1, 100) <= 70, // 70% de probabilidad
            'activity_group' => $this->getActivityGroup($activityType),
            'parent_activity_id' => null, // Por ahora sin agrupación
            'relevance_score' => rand(50, 100),
            'boost_until' => $this->shouldBeBoosted() ? now()->addDays(rand(1, 7)) : null,
            'algorithm_data' => $this->generateAlgorithmData($activityType),
            'status' => 'active',
            'flags_count' => 0,
            'flag_reasons' => null,
            'moderated_by' => null,
            'moderated_at' => null,
        ];

        ActivityFeed::create($activityFeedData);
    }

    private function getRandomActivityType(): string
    {
        $types = [
            'energy_saved' => 20,
            'solar_generated' => 15,
            'achievement_unlocked' => 10,
            'project_funded' => 8,
            'installation_completed' => 12,
            'cooperative_joined' => 5,
            'roof_published' => 8,
            'investment_made' => 6,
            'production_right_sold' => 4,
            'challenge_completed' => 8,
            'milestone_reached' => 6,
            'content_published' => 10,
            'expert_verified' => 3,
            'review_published' => 5,
            'topic_created' => 7,
            'community_contribution' => 8,
            'carbon_milestone' => 6,
            'efficiency_improvement' => 9,
            'grid_contribution' => 5,
            'sustainability_goal' => 7,
            'other' => 5,
        ];

        $random = rand(1, 100);
        $cumulative = 0;
        
        foreach ($types as $type => $probability) {
            $cumulative += $probability;
            if ($random <= $cumulative) {
                return $type;
            }
        }
        
        return 'energy_saved'; // Fallback
    }

    private function getRelatedData(string $activityType, $cooperatives, $installations, $posts, $achievements, $challenges): ?array
    {
        return match ($activityType) {
            'energy_saved', 'solar_generated', 'installation_completed', 'efficiency_improvement' => $this->getInstallationData($installations),
            'achievement_unlocked' => $this->getAchievementData($achievements),
            'challenge_completed', 'milestone_reached' => $this->getChallengeData($challenges),
            'cooperative_joined', 'community_contribution' => $this->getCooperativeData($cooperatives),
            'content_published', 'topic_created', 'review_published' => $this->getPostData($posts),
            'project_funded', 'investment_made', 'production_right_sold' => $this->getInvestmentData(),
            'roof_published' => $this->getRoofData(),
            'expert_verified' => $this->getExpertData(),
            'carbon_milestone', 'sustainability_goal' => $this->getSustainabilityData(),
            'grid_contribution' => $this->getGridData(),
            default => $this->getGenericData(),
        };
    }

    private function getInstallationData($installations): ?array
    {
        if ($installations->isEmpty()) return null;
        
        $installation = $installations->random();
        return [
            'type' => get_class($installation),
            'id' => $installation->id,
            'name' => $installation->name ?? 'Instalación Energética',
            'capacity' => $installation->capacity ?? rand(5, 50),
            'efficiency' => $installation->efficiency ?? rand(80, 95),
        ];
    }

    private function getAchievementData($achievements): ?array
    {
        if ($achievements->isEmpty()) return null;
        
        $achievement = $achievements->random();
        return [
            'type' => get_class($achievement),
            'id' => $achievement->id,
            'name' => $achievement->achievement->name ?? 'Logro Desbloqueado',
            'points' => $achievement->points_earned ?? rand(10, 100),
        ];
    }

    private function getChallengeData($challenges): ?array
    {
        if ($challenges->isEmpty()) return null;
        
        $challenge = $challenges->random();
        return [
            'type' => get_class($challenge),
            'id' => $challenge->id,
            'name' => $challenge->challenge->name ?? 'Reto Completado',
            'points' => $challenge->points_earned ?? rand(50, 200),
        ];
    }

    private function getCooperativeData($cooperatives): ?array
    {
        if ($cooperatives->isEmpty()) return null;
        
        $cooperative = $cooperatives->random();
        return [
            'type' => get_class($cooperative),
            'id' => $cooperative->id,
            'name' => $cooperative->name,
            'members' => $cooperative->member_count ?? rand(50, 500),
        ];
    }

    private function getPostData($posts): ?array
    {
        if ($posts->isEmpty()) return null;
        
        $post = $posts->random();
        return [
            'type' => get_class($post),
            'id' => $post->id,
            'title' => $post->title ?? 'Contenido Publicado',
            'views' => rand(10, 100),
        ];
    }

    private function getInvestmentData(): array
    {
        return [
            'type' => 'App\Models\Investment',
            'id' => rand(1, 100),
            'amount' => rand(1000, 50000),
            'type_investment' => ['solar_panel', 'battery', 'efficiency', 'community_project'][array_rand(['solar_panel', 'battery', 'efficiency', 'community_project'])],
        ];
    }

    private function getRoofData(): array
    {
        return [
            'type' => 'App\Models\Roof',
            'id' => rand(1, 100),
            'area' => rand(50, 200),
            'potential' => rand(5, 20),
        ];
    }

    private function getExpertData(): array
    {
        return [
            'type' => 'App\Models\ExpertVerification',
            'id' => rand(1, 100),
            'specialty' => ['energy', 'sustainability', 'technology', 'community'][array_rand(['energy', 'sustainability', 'technology', 'community'])],
        ];
    }

    private function getSustainabilityData(): array
    {
        return [
            'type' => 'App\Models\SustainabilityGoal',
            'id' => rand(1, 100),
            'goal_type' => ['carbon_reduction', 'energy_efficiency', 'renewable_adoption'][array_rand(['carbon_reduction', 'energy_efficiency', 'renewable_adoption'])],
        ];
    }

    private function getGridData(): array
    {
        return [
            'type' => 'App\Models\GridContribution',
            'id' => rand(1, 100),
            'contribution_type' => ['energy_export', 'grid_stabilization', 'demand_response'][array_rand(['energy_export', 'grid_stabilization', 'demand_response'])],
        ];
    }

    private function getGenericData(): array
    {
        return [
            'type' => 'App\Models\GenericActivity',
            'id' => rand(1, 100),
            'name' => 'Actividad Generada',
        ];
    }

    private function generateActivityData(string $activityType, array $relatedData): array
    {
        $baseData = [
            'timestamp' => now()->toISOString(),
            'user_agent' => 'Seeder Generated',
            'source' => 'system',
        ];

        return match ($activityType) {
            'energy_saved' => array_merge($baseData, [
                'savings_period' => rand(1, 12) . ' months',
                'efficiency_improvement' => rand(15, 40) . '%',
                'previous_consumption' => rand(500, 2000),
                'new_consumption' => rand(300, 1500),
            ]),
            'solar_generated' => array_merge($baseData, [
                'generation_period' => rand(1, 12) . ' months',
                'peak_hours' => rand(4, 8),
                'weather_conditions' => ['sunny', 'partly_cloudy', 'optimal'][array_rand(['sunny', 'partly_cloudy', 'optimal'])],
                'system_performance' => rand(85, 98) . '%',
            ]),
            'achievement_unlocked' => array_merge($baseData, [
                'achievement_category' => ['energy', 'community', 'innovation', 'sustainability'][array_rand(['energy', 'community', 'innovation', 'sustainability'])],
                'difficulty_level' => rand(1, 5),
                'time_to_complete' => rand(1, 90) . ' days',
            ]),
            'challenge_completed' => array_merge($baseData, [
                'challenge_duration' => rand(7, 30) . ' days',
                'team_size' => rand(1, 10),
                'final_score' => rand(80, 100),
            ]),
            'installation_completed' => array_merge($baseData, [
                'installation_type' => ['solar_panel', 'battery', 'heat_pump', 'smart_meter'][array_rand(['solar_panel', 'battery', 'heat_pump', 'smart_meter'])],
                'installation_time' => rand(1, 5) . ' days',
                'certification' => ['CE', 'ISO', 'Local'][array_rand(['CE', 'ISO', 'Local'])],
            ]),
            default => $baseData,
        };
    }

    private function generateMetrics(string $activityType, array $relatedData): array
    {
        $metrics = [
            'energy_amount_kwh' => null,
            'cost_savings_eur' => null,
            'co2_savings_kg' => null,
            'investment_amount_eur' => null,
            'community_impact_score' => null,
        ];

        switch ($activityType) {
            case 'energy_saved':
                $metrics['energy_amount_kwh'] = rand(100, 2000);
                $metrics['cost_savings_eur'] = rand(50, 500);
                $metrics['co2_savings_kg'] = rand(20, 200);
                break;
            case 'solar_generated':
                $metrics['energy_amount_kwh'] = rand(500, 5000);
                $metrics['cost_savings_eur'] = rand(100, 1000);
                $metrics['co2_savings_kg'] = rand(50, 500);
                break;
            case 'installation_completed':
                $metrics['investment_amount_eur'] = rand(2000, 25000);
                $metrics['energy_amount_kwh'] = rand(100, 1000);
                break;
            case 'achievement_unlocked':
                $metrics['community_impact_score'] = rand(50, 100);
                break;
            case 'challenge_completed':
                $metrics['community_impact_score'] = rand(60, 100);
                $metrics['cost_savings_eur'] = rand(25, 200);
                break;
            case 'cooperative_joined':
                $metrics['community_impact_score'] = rand(70, 100);
                break;
            case 'investment_made':
                $metrics['investment_amount_eur'] = rand(1000, 10000);
                $metrics['community_impact_score'] = rand(40, 80);
                break;
        }

        return $metrics;
    }

    private function generateDescription(string $activityType, array $relatedData): string
    {
        return match ($activityType) {
            'energy_saved' => "¡Excelente! He logrado ahorrar {$relatedData['capacity']} kWh de energía este mes, mejorando la eficiencia de mi instalación en un " . rand(15, 40) . "%.",
            'solar_generated' => "Mi instalación solar ha generado {$relatedData['capacity']} kWh de energía limpia este mes, contribuyendo a un futuro más sostenible.",
            'achievement_unlocked' => "¡Logro desbloqueado! He completado '{$relatedData['name']}' y ganado {$relatedData['points']} puntos. ¡Sigamos avanzando!",
            'challenge_completed' => "¡Reto completado! '{$relatedData['name']}' ha sido una experiencia increíble. Gané {$relatedData['points']} puntos y aprendí mucho.",
            'installation_completed' => "¡Instalación completada! Mi nueva {$relatedData['name']} está funcionando perfectamente y ya estoy viendo los beneficios.",
            'cooperative_joined' => "¡Me he unido a {$relatedData['name']}! Una cooperativa con {$relatedData['members']} miembros comprometidos con la sostenibilidad energética.",
            'content_published' => "He publicado '{$relatedData['title']}' para compartir conocimientos sobre sostenibilidad energética con la comunidad.",
            'investment_made' => "He realizado una inversión de €{$relatedData['amount']} en un proyecto de energía renovable. ¡Invertir en el futuro!",
            'milestone_reached' => "¡Hito alcanzado! He superado un nuevo objetivo en mi camino hacia la sostenibilidad energética.",
            'community_contribution' => "He contribuido a la comunidad de {$relatedData['name']} compartiendo conocimientos y experiencias sobre energía renovable.",
            default => "He completado una nueva actividad relacionada con la sostenibilidad energética. ¡Cada paso cuenta!",
        };
    }

    private function generateSummary(string $activityType, array $relatedData): string
    {
        return match ($activityType) {
            'energy_saved' => "Ahorro energético de " . rand(100, 2000) . " kWh",
            'solar_generated' => "Generación solar de " . rand(500, 5000) . " kWh",
            'achievement_unlocked' => "Logro desbloqueado: {$relatedData['name']}",
            'challenge_completed' => "Reto completado: {$relatedData['name']}",
            'installation_completed' => "Instalación completada: {$relatedData['name']}",
            'cooperative_joined' => "Unido a: {$relatedData['name']}",
            'content_published' => "Contenido publicado: {$relatedData['title']}",
            'investment_made' => "Inversión realizada: €{$relatedData['amount']}",
            'milestone_reached' => "Hito alcanzado en sostenibilidad",
            'community_contribution' => "Contribución a {$relatedData['name']}",
            default => "Nueva actividad de sostenibilidad",
        };
    }

    private function getRandomVisibility(): string
    {
        $visibilities = [
            'public' => 70,      // 70% público
            'cooperative' => 20, // 20% cooperativa
            'followers' => 8,    // 8% seguidores
            'private' => 2,      // 2% privado
        ];

        $random = rand(1, 100);
        $cumulative = 0;
        
        foreach ($visibilities as $visibility => $probability) {
            $cumulative += $probability;
            if ($random <= $cumulative) {
                return $visibility;
            }
        }
        
        return 'public'; // Fallback
    }

    private function shouldBeFeatured(): bool
    {
        // 10% de probabilidad de ser destacado
        return rand(1, 100) <= 10;
    }

    private function shouldBeMilestone(string $activityType): bool
    {
        // 15% de probabilidad de ser hito para ciertos tipos
        $milestoneTypes = ['energy_saved', 'solar_generated', 'achievement_unlocked', 'challenge_completed'];
        return in_array($activityType, $milestoneTypes) && rand(1, 100) <= 15;
    }

    private function getRandomLatitude(): ?float
    {
        // Coordenadas aproximadas de España
        return rand(1, 100) <= 80 ? (35.0 + (rand(0, 1000) / 1000)) : null;
    }

    private function getRandomLongitude(): ?float
    {
        // Coordenadas aproximadas de España
        return rand(1, 100) <= 80 ? (-10.0 + (rand(0, 1000) / 1000)) : null;
    }

    private function getRandomLocationName(): ?string
    {
        $locations = [
            'Madrid, España', 'Barcelona, España', 'Valencia, España', 'Sevilla, España',
            'Zaragoza, España', 'Málaga, España', 'Murcia, España', 'Palma, España',
            'Las Palmas, España', 'Bilbao, España', 'Alicante, España', 'Córdoba, España',
            'Valladolid, España', 'Vigo, España', 'Gijón, España', 'Oviedo, España',
        ];
        
        return rand(1, 100) <= 70 ? $locations[array_rand($locations)] : null;
    }

    private function getRandomActivityDate(): Carbon
    {
        // Fecha de actividad entre 30 días atrás y ahora
        return now()->subDays(rand(0, 30));
    }

    private function getActivityGroup(string $activityType): ?string
    {
        $groups = [
            'energy_saved' => 'energy_efficiency',
            'solar_generated' => 'renewable_energy',
            'achievement_unlocked' => 'gamification',
            'challenge_completed' => 'community_challenges',
            'installation_completed' => 'infrastructure',
            'cooperative_joined' => 'community_building',
            'content_published' => 'knowledge_sharing',
            'investment_made' => 'financial_activities',
            'milestone_reached' => 'personal_goals',
            'community_contribution' => 'social_impact',
        ];

        return $groups[$activityType] ?? null;
    }

    private function shouldBeBoosted(): bool
    {
        // 5% de probabilidad de ser impulsado
        return rand(1, 100) <= 5;
    }

    private function generateAlgorithmData(string $activityType): array
    {
        return [
            'category_weight' => rand(1, 10),
            'user_interest_score' => rand(50, 100),
            'trending_factor' => rand(1, 5),
            'engagement_potential' => rand(60, 100),
            'content_quality_score' => rand(70, 100),
            'relevance_boost' => rand(1, 3),
        ];
    }
}
