<?php

namespace Database\Seeders;

use App\Models\UserReputation;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserReputationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('⭐ Sembrando reputación de usuarios...');

        $users = User::all();

        if ($users->isEmpty()) {
            $this->command->warn('❌ No hay usuarios disponibles. Ejecuta UserSeeder primero.');
            return;
        }

        $categories = [
            'solar_technology',
            'project_management',
            'financial_analysis',
            'legal_compliance',
            'environmental_assessment',
            'installation_supervision',
            'maintenance_planning',
            'consulting_services',
            'team_leadership',
            'client_communication'
        ];

        $topics = [
            'solar_panel_installation',
            'energy_efficiency',
            'renewable_energy_policy',
            'financial_incentives',
            'environmental_impact',
            'system_optimization',
            'maintenance_protocols',
            'client_consultation',
            'project_development',
            'technical_analysis'
        ];

        $professionalCredentials = [
            'certified_solar_installer',
            'energy_auditor',
            'project_management_professional',
            'financial_analyst',
            'environmental_consultant',
            'electrical_engineer',
            'sustainability_specialist',
            'business_consultant',
            'team_leader',
            'client_relations_specialist'
        ];

        $expertiseAreas = [
            'residential_solar',
            'commercial_solar',
            'utility_scale_solar',
            'energy_storage',
            'grid_integration',
            'financial_modeling',
            'regulatory_compliance',
            'environmental_assessment',
            'system_design',
            'performance_optimization'
        ];

        $createdCount = 0;

        foreach ($users as $user) {
            // Verificar si ya existe reputación para este usuario
            if (UserReputation::where('user_id', $user->id)->exists()) {
                continue;
            }

            $totalReputation = fake()->numberBetween(50, 5000);
            $categoryReputation = [];
            $topicReputation = [];
            
            // Generar reputación por categoría
            foreach ($categories as $category) {
                if (fake()->boolean(70)) {
                    $categoryReputation[$category] = fake()->numberBetween(10, 500);
                }
            }
            
            // Generar reputación por tema
            foreach ($topics as $topicId => $topic) {
                if (fake()->boolean(60)) {
                    $topicReputation[$topicId] = fake()->numberBetween(5, 300);
                }
            }

            $reputation = UserReputation::create([
                'user_id' => $user->id,
                'total_reputation' => $totalReputation,
                'category_reputation' => $categoryReputation,
                'topic_reputation' => $topicReputation,
                'helpful_answers' => fake()->numberBetween(0, 100),
                'accepted_solutions' => fake()->numberBetween(0, 50),
                'quality_posts' => fake()->numberBetween(0, 200),
                'verified_contributions' => fake()->numberBetween(0, 150),
                'upvotes_received' => fake()->numberBetween(0, 300),
                'downvotes_received' => fake()->numberBetween(0, 50),
                'upvote_ratio' => fake()->randomFloat(2, 60, 100),
                'topics_created' => fake()->numberBetween(0, 20),
                'successful_projects' => fake()->numberBetween(0, 15),
                'mentorship_points' => fake()->numberBetween(0, 100),
                'warnings_received' => fake()->numberBetween(0, 5),
                'content_removed' => fake()->numberBetween(0, 10),
                'is_suspended' => fake()->boolean(5),
                'suspended_until' => fake()->boolean(5) ? fake()->dateTimeBetween('now', '+1 month') : null,
                'global_rank' => fake()->numberBetween(1, 1000),
                'category_ranks' => $this->generateCategoryRanks($categoryReputation),
                'monthly_rank' => fake()->numberBetween(1, 100),
                'is_verified_professional' => fake()->boolean(30),
                'professional_credentials' => $this->generateProfessionalCredentials(),
                'expertise_areas' => $this->generateExpertiseAreas(),
            ]);

            $createdCount++;
        }

        $this->command->info("✅ Creada reputación para {$createdCount} usuarios");
        $this->showStatistics();
    }

    private function generateCategoryRanks(array $categoryReputation): array
    {
        $ranks = [];
        foreach ($categoryReputation as $category => $reputation) {
            $ranks[$category] = fake()->numberBetween(1, 100);
        }
        return $ranks;
    }

    private function generateProfessionalCredentials(): array
    {
        $credentials = [];
        $availableCredentials = [
            'certified_solar_installer',
            'energy_auditor',
            'project_management_professional',
            'financial_analyst',
            'environmental_consultant',
            'electrical_engineer',
            'sustainability_specialist',
            'business_consultant',
            'team_leader',
            'client_relations_specialist'
        ];

        $numCredentials = fake()->numberBetween(0, 3);
        $selectedCredentials = fake()->randomElements($availableCredentials, $numCredentials);
        
        foreach ($selectedCredentials as $credential) {
            $credentials[] = [
                'name' => $credential,
                'issuer' => fake()->randomElement(['Solar Energy Institute', 'Energy Association', 'Professional Certification Board', 'Industry Standards Organization']),
                'issued_date' => fake()->dateTimeBetween('-5 years', 'now')->format('Y-m-d'),
                'expiry_date' => fake()->dateTimeBetween('now', '+2 years')->format('Y-m-d'),
                'verification_status' => fake()->randomElement(['verified', 'pending', 'expired'])
            ];
        }

        return $credentials;
    }

    private function generateExpertiseAreas(): array
    {
        $areas = [];
        $availableAreas = [
            'residential_solar',
            'commercial_solar',
            'utility_scale_solar',
            'energy_storage',
            'grid_integration',
            'financial_modeling',
            'regulatory_compliance',
            'environmental_assessment',
            'system_design',
            'performance_optimization'
        ];

        $numAreas = fake()->numberBetween(1, 5);
        $selectedAreas = fake()->randomElements($availableAreas, $numAreas);
        
        foreach ($selectedAreas as $area) {
            $areas[] = [
                'area' => $area,
                'experience_years' => fake()->numberBetween(1, 10),
                'skill_level' => fake()->randomElement(['beginner', 'intermediate', 'advanced', 'expert']),
                'verified' => fake()->boolean(60)
            ];
        }

        return $areas;
    }

    private function showStatistics(): void
    {
        $total = UserReputation::count();
        $verifiedProfessionals = UserReputation::where('is_verified_professional', true)->count();
        $suspended = UserReputation::where('is_suspended', true)->count();
        
        $avgReputation = UserReputation::avg('total_reputation');
        $avgUpvoteRatio = UserReputation::avg('upvote_ratio');
        $avgGlobalRank = UserReputation::avg('global_rank');
        
        $reputationLevels = [
            'novice' => UserReputation::where('total_reputation', '<', 100)->count(),
            'contributor' => UserReputation::whereBetween('total_reputation', [100, 499])->count(),
            'intermediate' => UserReputation::whereBetween('total_reputation', [500, 999])->count(),
            'advanced' => UserReputation::whereBetween('total_reputation', [1000, 4999])->count(),
            'leader' => UserReputation::whereBetween('total_reputation', [5000, 9999])->count(),
            'expert' => UserReputation::where('total_reputation', '>=', 10000)->count(),
        ];

        $this->command->info("\n📊 Estadísticas de reputación de usuarios:");
        $this->command->info("   • Total de usuarios con reputación: {$total}");
        $this->command->info("   • Profesionales verificados: {$verifiedProfessionals}");
        $this->command->info("   • Usuarios suspendidos: {$suspended}");
        $this->command->info("   • Reputación promedio: " . round($avgReputation, 1));
        $this->command->info("   • Ratio de upvotes promedio: " . round($avgUpvoteRatio, 1) . "%");
        $this->command->info("   • Ranking global promedio: " . round($avgGlobalRank, 1));

        $this->command->info("\n🏆 Por nivel de reputación:");
        foreach ($reputationLevels as $level => $count) {
            $levelLabel = match($level) {
                'novice' => 'Novato',
                'contributor' => 'Contribuidor',
                'intermediate' => 'Intermedio',
                'advanced' => 'Avanzado',
                'leader' => 'Líder',
                'expert' => 'Experto',
                default => ucfirst($level)
            };
            $this->command->info("   • {$levelLabel}: {$count}");
        }

        // Mostrar usuarios con mayor reputación
        $topUsers = UserReputation::with('user')
            ->orderBy('total_reputation', 'desc')
            ->take(5)
            ->get();

        if ($topUsers->isNotEmpty()) {
            $this->command->info("\n⭐ Usuarios con mayor reputación:");
            foreach ($topUsers as $reputation) {
                $userName = $reputation->user ? $reputation->user->name : 'Usuario Desconocido';
                $level = $reputation->getReputationLevel();
                $levelLabel = match($level) {
                    'novice' => 'Novato',
                    'contributor' => 'Contribuidor',
                    'intermediate' => 'Intermedio',
                    'advanced' => 'Avanzado',
                    'leader' => 'Líder',
                    'expert' => 'Experto',
                    default => ucfirst($level)
                };
                $this->command->info("   • {$userName}: {$reputation->total_reputation} puntos ({$levelLabel})");
            }
        }

        // Mostrar estadísticas por categoría
        $categoryStats = [];
        $reputations = UserReputation::whereNotNull('category_reputation')->get();
        
        foreach ($reputations as $reputation) {
            if ($reputation->category_reputation) {
                foreach ($reputation->category_reputation as $category => $points) {
                    if (!isset($categoryStats[$category])) {
                        $categoryStats[$category] = ['count' => 0, 'total_points' => 0];
                    }
                    $categoryStats[$category]['count']++;
                    $categoryStats[$category]['total_points'] += $points;
                }
            }
        }

        if (!empty($categoryStats)) {
            $this->command->info("\n🎯 Reputación por categoría:");
            foreach ($categoryStats as $category => $stats) {
                $avgPoints = $stats['count'] > 0 ? $stats['total_points'] / $stats['count'] : 0;
                $categoryLabel = match($category) {
                    'solar_technology' => 'Tecnología Solar',
                    'project_management' => 'Gestión de Proyectos',
                    'financial_analysis' => 'Análisis Financiero',
                    'legal_compliance' => 'Cumplimiento Legal',
                    'environmental_assessment' => 'Evaluación Ambiental',
                    'installation_supervision' => 'Supervisión de Instalación',
                    'maintenance_planning' => 'Planificación de Mantenimiento',
                    'consulting_services' => 'Servicios de Consultoría',
                    'team_leadership' => 'Liderazgo de Equipo',
                    'client_communication' => 'Comunicación con Clientes',
                    default => ucfirst(str_replace('_', ' ', $category))
                };
                $this->command->info("   • {$categoryLabel}: {$stats['count']} usuarios, " . round($avgPoints, 1) . " puntos promedio");
            }
        }
    }
}
