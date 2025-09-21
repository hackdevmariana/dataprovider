<?php

namespace Database\Seeders;

use App\Models\ProjectProposal;
use App\Models\User;
use App\Models\Cooperative;
use App\Models\Municipality;
use Illuminate\Database\Seeder;

class ProjectProposalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🚀 Sembrando propuestas de proyectos energéticos...');

        // Obtener datos necesarios
        $users = User::take(10)->get();
        $cooperatives = Cooperative::take(5)->get();
        $municipalities = Municipality::take(20)->get();

        if ($users->isEmpty()) {
            $this->command->error('❌ No hay usuarios disponibles. Ejecuta UserSeeder primero.');
            return;
        }

        if ($municipalities->isEmpty()) {
            $this->command->error('❌ No hay municipios disponibles. Ejecuta MunicipalitySeeder primero.');
            return;
        }

        // Tipos de proyectos energéticos
        $projectTypes = [
            'individual_installation' => 'Instalación Individual',
            'community_installation' => 'Instalación Comunitaria',
            'shared_installation' => 'Instalación Compartida',
            'energy_storage' => 'Sistema de Almacenamiento',
            'smart_grid' => 'Red Inteligente',
            'efficiency_improvement' => 'Mejora de Eficiencia',
        ];

        $scales = ['residential', 'commercial', 'industrial', 'utility', 'community'];
        $statuses = ['draft', 'under_review', 'approved', 'funding', 'in_progress'];

        $createdCount = 0;

        // Crear proyectos de diferentes tipos y escalas
        foreach ($projectTypes as $type => $typeName) {
            for ($i = 0; $i < fake()->numberBetween(2, 3); $i++) {
                $municipality = $municipalities->random();
                $proposer = $users->random();
                $cooperative = $cooperatives->isNotEmpty() ? $cooperatives->random() : null;
                
                $scale = fake()->randomElement($scales);
                $status = fake()->randomElement($statuses);
                
                $project = ProjectProposal::create([
                    'proposer_id' => $proposer->id,
                    'cooperative_id' => $cooperative?->id,
                    'title' => $this->generateProjectTitle($typeName, $municipality->name),
                    'slug' => null, // Se generará automáticamente
                    'description' => $this->generateProjectDescription($typeName, $scale),
                    'summary' => fake()->sentence(15),
                    'objectives' => $this->generateObjectives($typeName),
                    'benefits' => $this->generateBenefits($typeName),
                    'project_type' => $type,
                    'scale' => $scale,
                    'municipality_id' => $municipality->id,
                    'specific_location' => fake()->address(),
                    'latitude' => fake()->randomFloat(6, 36.0, 43.0),
                    'longitude' => fake()->randomFloat(6, -9.0, 3.0),
                    'estimated_power_kw' => $this->getEstimatedPower($type, $scale),
                    'estimated_annual_production_kwh' => $this->getEstimatedProduction($type, $scale),
                    'technical_specifications' => $this->generateTechnicalSpecs($type),
                    'total_investment_required' => $this->getInvestmentAmount($type, $scale),
                    'investment_raised' => fake()->numberBetween(0, 50000),
                    'min_investment_per_participant' => fake()->randomFloat(2, 100, 1000),
                    'max_investment_per_participant' => fake()->randomFloat(2, 1000, 10000),
                    'max_participants' => fake()->numberBetween(10, 100),
                    'current_participants' => fake()->numberBetween(0, 20),
                    'estimated_roi_percentage' => fake()->randomFloat(2, 3, 12),
                    'payback_period_years' => fake()->randomFloat(1, 5, 15),
                    'estimated_annual_savings' => fake()->randomFloat(2, 5000, 50000),
                    'financial_projections' => $this->generateFinancialProjections($type),
                    'funding_deadline' => fake()->dateTimeBetween('+1 month', '+1 year'),
                    'project_start_date' => fake()->dateTimeBetween('now', '+6 months'),
                    'expected_completion_date' => fake()->dateTimeBetween('+1 year', '+3 years'),
                    'estimated_duration_months' => fake()->numberBetween(6, 36),
                    'project_milestones' => $this->generateMilestones($type),
                    'documents' => $this->generateDocuments($type),
                    'images' => $this->generateImages($type),
                    'technical_reports' => $this->generateTechnicalReports($type),
                    'has_permits' => fake()->boolean(70),
                    'permits_status' => $this->generatePermitsStatus(),
                    'is_technically_validated' => fake()->boolean(60),
                    'technical_validator_id' => fake()->boolean(60) ? $users->random()->id : null,
                    'technical_validation_date' => fake()->optional(0.6)->dateTimeBetween('-1 year', 'now'),
                    'status' => $status,
                    'status_notes' => $this->getStatusNotes($status),
                    'reviewed_by' => fake()->optional(0.4)->randomElement($users->pluck('id')->toArray()),
                    'reviewed_at' => fake()->optional(0.4)->dateTimeBetween('-6 months', 'now'),
                    'views_count' => fake()->numberBetween(0, 500),
                    'likes_count' => fake()->numberBetween(0, 50),
                    'comments_count' => fake()->numberBetween(0, 20),
                    'shares_count' => fake()->numberBetween(0, 30),
                    'bookmarks_count' => fake()->numberBetween(0, 15),
                    'engagement_score' => fake()->randomFloat(2, 0, 100),
                    'is_public' => fake()->boolean(85),
                    'is_featured' => fake()->boolean(15),
                    'allow_comments' => true,
                    'allow_investments' => in_array($status, ['funding', 'approved']),
                    'notify_updates' => true,
                ]);

                $createdCount++;
            }
        }

        $this->command->info("✅ Creadas {$createdCount} propuestas de proyectos energéticos");
        $this->showStatistics();
    }

    private function generateProjectTitle(string $typeName, string $municipality): string
    {
        $adjectives = ['Innovador', 'Sostenible', 'Eficiente', 'Renovable', 'Verde', 'Limpio'];
        $adjective = fake()->randomElement($adjectives);
        
        return "{$adjective} {$typeName} - {$municipality}";
    }

    private function generateProjectDescription(string $typeName, string $scale): string
    {
        $scaleText = match($scale) {
            'small' => 'pequeña escala',
            'medium' => 'escala media',
            'large' => 'gran escala',
            default => 'escala media'
        };

        return "Proyecto de {$typeName} de {$scaleText} que busca contribuir a la transición energética sostenible. " .
               "Este proyecto está diseñado para generar energía limpia y renovable, " .
               "reduciendo las emisiones de CO2 y promoviendo la independencia energética local.";
    }

    private function generateObjectives(string $typeName): array
    {
        return [
            "Generar energía renovable y limpia",
            "Reducir las emisiones de CO2",
            "Promover la sostenibilidad energética",
            "Crear empleo local",
            "Fomentar la participación ciudadana"
        ];
    }

    private function generateBenefits(string $typeName): array
    {
        return [
            "Reducción de costes energéticos para la comunidad",
            "Creación de empleo local cualificado",
            "Mejora de la imagen sostenible del municipio",
            "Independencia energética progresiva",
            "Beneficios económicos para participantes"
        ];
    }

    private function getEstimatedPower(string $type, string $scale): float
    {
        $basePower = match($type) {
            'individual_installation' => 5,
            'community_installation' => 100,
            'shared_installation' => 50,
            'energy_storage' => 200,
            'smart_grid' => 500,
            'efficiency_improvement' => 25,
            default => 100
        };

        $scaleMultiplier = match($scale) {
            'residential' => 0.1,
            'commercial' => 1.0,
            'industrial' => 5.0,
            'utility' => 20.0,
            'community' => 2.0,
            default => 1.0
        };

        return round($basePower * $scaleMultiplier, 2);
    }

    private function getEstimatedProduction(string $type, string $scale): float
    {
        $power = $this->getEstimatedPower($type, $scale);
        $capacityFactor = match($type) {
            'individual_installation' => 0.25,
            'community_installation' => 0.30,
            'shared_installation' => 0.28,
            'energy_storage' => 0.50,
            'smart_grid' => 0.40,
            'efficiency_improvement' => 0.35,
            default => 0.30
        };

        return round($power * 8760 * $capacityFactor, 2);
    }

    private function generateTechnicalSpecs(string $type): array
    {
        return match($type) {
            'individual_installation' => [
                'panel_type' => 'Monocristalino de alta eficiencia',
                'inverters' => 'Microinversores',
                'efficiency' => '22%',
                'warranty' => '25 años'
            ],
            'community_installation' => [
                'turbine_model' => 'Sistema comunitario',
                'capacity' => '100kW',
                'efficiency' => '30%',
                'maintenance' => 'Compartido'
            ],
            'energy_storage' => [
                'battery_type' => 'Litio-ferrofosfato',
                'capacity' => '200kWh',
                'efficiency' => '95%',
                'cycles' => '6000'
            ],
            default => [
                'technology' => 'Tecnología de última generación',
                'efficiency' => 'Alta eficiencia energética'
            ]
        };
    }

    private function getInvestmentAmount(string $type, string $scale): float
    {
        $baseCost = match($type) {
            'individual_installation' => 15000,
            'community_installation' => 150000,
            'shared_installation' => 75000,
            'energy_storage' => 100000,
            'smart_grid' => 500000,
            'efficiency_improvement' => 25000,
            default => 100000
        };

        $scaleMultiplier = match($scale) {
            'residential' => 0.5,
            'commercial' => 1.0,
            'industrial' => 3.0,
            'utility' => 10.0,
            'community' => 2.0,
            default => 1.0
        };

        return round($baseCost * $scaleMultiplier, 2);
    }

    private function generateFinancialProjections(string $type): array
    {
        return [
            'year_1' => [
                'revenue' => fake()->randomFloat(2, 50000, 200000),
                'costs' => fake()->randomFloat(2, 30000, 100000),
                'profit' => fake()->randomFloat(2, 20000, 100000)
            ],
            'year_5' => [
                'revenue' => fake()->randomFloat(2, 100000, 500000),
                'costs' => fake()->randomFloat(2, 50000, 150000),
                'profit' => fake()->randomFloat(2, 50000, 350000)
            ]
        ];
    }

    private function generateMilestones(string $type): array
    {
        return [
            [
                'name' => 'Obtención de permisos',
                'date' => fake()->dateTimeBetween('now', '+3 months'),
                'status' => fake()->randomElement(['pending', 'in_progress', 'completed'])
            ],
            [
                'name' => 'Financiación completada',
                'date' => fake()->dateTimeBetween('+3 months', '+6 months'),
                'status' => 'pending'
            ],
            [
                'name' => 'Inicio de construcción',
                'date' => fake()->dateTimeBetween('+6 months', '+12 months'),
                'status' => 'pending'
            ]
        ];
    }

    private function generateDocuments(string $type): array
    {
        return [
            [
                'name' => 'Estudio de viabilidad técnica',
                'type' => 'pdf',
                'size' => '2.5MB',
                'uploaded_at' => fake()->dateTimeBetween('-1 month', 'now')
            ],
            [
                'name' => 'Estudio de impacto ambiental',
                'type' => 'pdf',
                'size' => '5.2MB',
                'uploaded_at' => fake()->dateTimeBetween('-2 months', 'now')
            ]
        ];
    }

    private function generateImages(string $type): array
    {
        $imageCount = fake()->numberBetween(2, 4);
        $images = [];
        
        for ($i = 0; $i < $imageCount; $i++) {
            $images[] = [
                'url' => fake()->imageUrl(800, 600, 'technology'),
                'alt' => fake()->sentence(4),
                'type' => 'project_image',
                'uploaded_at' => fake()->dateTimeBetween('-1 month', 'now')
            ];
        }
        
        return $images;
    }

    private function generateTechnicalReports(string $type): array
    {
        return [
            [
                'title' => 'Análisis de recursos energéticos',
                'author' => 'Ingeniería Técnica Sostenible',
                'date' => fake()->dateTimeBetween('-2 months', 'now'),
                'summary' => 'Estudio detallado de los recursos disponibles en la zona'
            ]
        ];
    }

    private function generatePermitsStatus(): array
    {
        return [
            'environmental' => fake()->randomElement(['pending', 'approved', 'rejected']),
            'building' => fake()->randomElement(['pending', 'approved', 'rejected']),
            'electrical' => fake()->randomElement(['pending', 'approved', 'rejected']),
            'municipal' => fake()->randomElement(['pending', 'approved', 'rejected'])
        ];
    }

    private function getStatusNotes(string $status): ?string
    {
        return match($status) {
            'draft' => 'Proyecto en fase de desarrollo inicial',
            'under_review' => 'En proceso de revisión técnica y legal',
            'funding' => 'Buscando financiación y participantes',
            'approved' => 'Proyecto aprobado, listo para ejecución',
            'in_progress' => 'En fase de construcción e implementación',
            default => null
        };
    }

    private function showStatistics(): void
    {
        $total = ProjectProposal::count();
        $byType = ProjectProposal::selectRaw('project_type, COUNT(*) as count')
            ->groupBy('project_type')
            ->pluck('count', 'project_type');
        
        $byStatus = ProjectProposal::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');

        $totalInvestment = ProjectProposal::sum('total_investment_required');
        $totalRaised = ProjectProposal::sum('investment_raised');

        $this->command->info("\n📊 Estadísticas de propuestas de proyectos:");
        $this->command->info("   • Total de proyectos: {$total}");
        $this->command->info("   • Inversión total requerida: €" . number_format($totalInvestment, 2));
        $this->command->info("   • Inversión recaudada: €" . number_format($totalRaised, 2));

        $this->command->info("\n🔧 Por tipo de proyecto:");
        foreach ($byType as $type => $count) {
            $this->command->info("   • {$type}: {$count}");
        }

        $this->command->info("\n📈 Por estado:");
        foreach ($byStatus as $status => $count) {
            $this->command->info("   • {$status}: {$count}");
        }
    }
}
