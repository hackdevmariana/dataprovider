<?php

namespace Database\Seeders;

use App\Models\ProjectInvestment;
use App\Models\ProjectProposal;
use App\Models\User;
use Illuminate\Database\Seeder;

class ProjectInvestmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('💰 Sembrando inversiones en proyectos...');

        // Obtener datos necesarios
        $projects = ProjectProposal::where('status', 'funding')->take(10)->get();
        $users = User::take(20)->get();

        if ($projects->isEmpty()) {
            $this->command->error('❌ No hay proyectos en estado de financiación. Ejecuta ProjectProposalSeeder primero.');
            return;
        }

        if ($users->isEmpty()) {
            $this->command->error('❌ No hay usuarios disponibles. Ejecuta UserSeeder primero.');
            return;
        }

        $investmentTypes = ['monetary', 'equipment', 'expertise', 'land_use'];
        $statuses = ['pending', 'paid', 'active', 'completed'];
        $returnFrequencies = ['monthly', 'quarterly', 'biannual', 'annual'];

        $createdCount = 0;

        // Crear inversiones para cada proyecto
        foreach ($projects as $project) {
            $numInvestments = fake()->numberBetween(3, 8);
            
            for ($i = 0; $i < $numInvestments; $i++) {
                $investor = $users->random();
                $investmentType = fake()->randomElement($investmentTypes);
                $status = fake()->randomElement($statuses);
                $returnFrequency = fake()->randomElement($returnFrequencies);
                
                $investmentAmount = fake()->randomFloat(2, 
                    $project->min_investment_per_participant ?? 100, 
                    min($project->max_investment_per_participant ?? 10000, 
                        $project->getRemainingInvestment())
                );

                $investment = ProjectInvestment::updateOrCreate(
                    [
                        'project_proposal_id' => $project->id,
                        'investor_id' => $investor->id,
                    ],
                    [
                    'investment_amount' => $investmentAmount,
                    'investment_percentage' => $this->calculateInvestmentPercentage($investmentAmount, $project->total_investment_required),
                    'investment_type' => $investmentType,
                    'investment_details' => $this->generateInvestmentDetails($investmentType),
                    'investment_description' => $this->generateInvestmentDescription($investmentType, $project->title),
                    'expected_return_percentage' => fake()->randomFloat(2, 3, 12),
                    'investment_term_years' => fake()->numberBetween(5, 20),
                    'return_frequency' => $returnFrequency,
                    'return_schedule' => $this->generateReturnSchedule($returnFrequency),
                    'reinvest_returns' => fake()->boolean(30),
                    'status' => $status,
                    'payment_method' => fake()->randomElement(['bank_transfer', 'credit_card', 'paypal', 'crypto']),
                    'payment_reference' => fake()->uuid(),
                    'payment_date' => fake()->dateTimeBetween('-6 months', 'now'),
                    'payment_confirmed_at' => $status === 'paid' || $status === 'active' ? fake()->dateTimeBetween('-6 months', 'now') : null,
                    'payment_confirmed_by' => $status === 'paid' || $status === 'active' ? $users->random()->id : null,
                    'legal_documents' => $this->generateLegalDocuments(),
                    'terms_accepted' => true,
                    'terms_accepted_at' => fake()->dateTimeBetween('-6 months', 'now'),
                    'digital_signature' => fake()->uuid(),
                    'contract_details' => $this->generateContractDetails($project, $investor),
                    'total_returns_received' => $status === 'active' || $status === 'completed' ? fake()->randomFloat(2, 0, $investmentAmount * 0.3) : 0,
                    'pending_returns' => $status === 'active' ? fake()->randomFloat(2, 0, $investmentAmount * 0.2) : 0,
                    'last_return_date' => $status === 'active' || $status === 'completed' ? fake()->dateTimeBetween('-3 months', 'now') : null,
                    'next_return_date' => $status === 'active' ? fake()->dateTimeBetween('now', '+3 months') : null,
                    'has_voting_rights' => fake()->boolean(80),
                    'voting_weight' => fake()->randomFloat(2, 0.1, 5.0),
                    'can_participate_decisions' => fake()->boolean(90),
                    'receives_project_updates' => true,
                    'notification_preferences' => [
                        'email_updates' => true,
                        'sms_updates' => fake()->boolean(30),
                        'push_notifications' => fake()->boolean(60),
                        'monthly_reports' => true,
                        'milestone_notifications' => true
                    ],
                    'public_investor' => fake()->boolean(70),
                    'investor_alias' => fake()->boolean(70) ? fake()->firstName() . ' ' . fake()->lastName() : null,
                    'current_roi' => $status === 'active' || $status === 'completed' ? fake()->randomFloat(2, -5, 25) : 0,
                    'projected_final_roi' => fake()->randomFloat(2, 5, 30),
                    'months_invested' => $status === 'active' || $status === 'completed' ? fake()->numberBetween(0, 36) : 0,
                    'performance_metrics' => $this->generatePerformanceMetrics($status),
                    'exit_requested' => fake()->boolean(5),
                    'exit_requested_at' => fake()->boolean(5) ? fake()->dateTimeBetween('-1 month', 'now') : null,
                    'exit_value' => fake()->boolean(5) ? fake()->randomFloat(2, $investmentAmount * 0.8, $investmentAmount * 1.2) : null,
                    'exit_terms' => fake()->boolean(5) ? fake()->sentence() : null,
                    ]
                );

                $createdCount++;
            }
        }

        $this->command->info("✅ Creadas {$createdCount} inversiones en proyectos");
        $this->showStatistics();
    }

    private function calculateInvestmentPercentage(float $amount, float $totalInvestment): float
    {
        if ($totalInvestment <= 0) {
            return 0;
        }
        return round(($amount / $totalInvestment) * 100, 2);
    }

    private function generateInvestmentDetails(string $type): array
    {
        return match($type) {
            'monetary' => [
                'source' => fake()->randomElement(['savings', 'investment_fund', 'pension_fund', 'business_profit']),
                'currency' => 'EUR',
                'transfer_method' => fake()->randomElement(['bank_transfer', 'sepa', 'instant_payment'])
            ],
            'equipment' => [
                'equipment_type' => fake()->randomElement(['solar_panels', 'inverters', 'batteries', 'monitoring_system']),
                'brand' => fake()->randomElement(['SMA', 'Fronius', 'Sungrow', 'Tesla', 'LG']),
                'model' => fake()->bothify('Model-###'),
                'condition' => fake()->randomElement(['new', 'used_excellent', 'used_good', 'refurbished']),
                'warranty_months' => fake()->numberBetween(12, 120)
            ],
            'expertise' => [
                'service_type' => fake()->randomElement(['installation', 'maintenance', 'monitoring', 'legal_advice', 'engineering']),
                'provider_company' => fake()->company(),
                'estimated_hours' => fake()->numberBetween(10, 200),
                'hourly_rate' => fake()->randomFloat(2, 25, 150),
                'delivery_timeline' => fake()->numberBetween(1, 12) . ' months'
            ],
            'land_use' => [
                'property_type' => fake()->randomElement(['roof_space', 'ground_mount', 'carport', 'facade']),
                'area_m2' => fake()->randomFloat(2, 50, 10000),
                'location_type' => fake()->randomElement(['residential', 'commercial', 'industrial', 'agricultural']),
                'lease_duration_years' => fake()->numberBetween(10, 30),
                'annual_rent_eur' => fake()->randomFloat(2, 1000, 50000)
            ],
            default => [
                'type' => $type,
                'description' => 'Investment details for ' . $type
            ]
        };
    }

    private function generateInvestmentDescription(string $type, string $projectTitle): string
    {
        return match($type) {
            'monetary' => "Inversión monetaria en el proyecto '{$projectTitle}' para contribuir a la transición energética sostenible.",
            'equipment' => "Contribución de equipamiento técnico para el proyecto '{$projectTitle}' incluyendo componentes de alta calidad.",
            'expertise' => "Provisión de servicios especializados para el desarrollo del proyecto '{$projectTitle}'.",
            'land_use' => "Cesión de espacio físico para la instalación del proyecto '{$projectTitle}' con condiciones favorables.",
            default => "Inversión en el proyecto '{$projectTitle}' mediante {$type}."
        };
    }

    private function generateReturnSchedule(string $frequency): array
    {
        return match($frequency) {
            'monthly' => [
                'frequency' => 'monthly',
                'payment_day' => fake()->numberBetween(1, 28),
                'first_payment' => fake()->dateTimeBetween('now', '+1 month')->format('Y-m-d')
            ],
            'quarterly' => [
                'frequency' => 'quarterly',
                'payment_month' => fake()->randomElement([3, 6, 9, 12]),
                'payment_day' => fake()->numberBetween(1, 28),
                'first_payment' => fake()->dateTimeBetween('now', '+3 months')->format('Y-m-d')
            ],
            'biannual' => [
                'frequency' => 'biannual',
                'payment_months' => [6, 12],
                'payment_day' => fake()->numberBetween(1, 28),
                'first_payment' => fake()->dateTimeBetween('now', '+6 months')->format('Y-m-d')
            ],
            'annual' => [
                'frequency' => 'annual',
                'payment_month' => 12,
                'payment_day' => fake()->numberBetween(1, 28),
                'first_payment' => fake()->dateTimeBetween('now', '+1 year')->format('Y-m-d')
            ]
        };
    }

    private function generateLegalDocuments(): array
    {
        return [
            [
                'type' => 'investment_agreement',
                'name' => 'Contrato de Inversión',
                'file_path' => 'documents/investments/agreement_' . fake()->uuid() . '.pdf',
                'signed_at' => fake()->dateTimeBetween('-6 months', 'now'),
                'version' => '1.0'
            ],
            [
                'type' => 'risk_disclosure',
                'name' => 'Declaración de Riesgos',
                'file_path' => 'documents/investments/risk_' . fake()->uuid() . '.pdf',
                'signed_at' => fake()->dateTimeBetween('-6 months', 'now'),
                'version' => '1.0'
            ]
        ];
    }

    private function generateContractDetails($project, $investor): array
    {
        return [
            'project_title' => $project->title,
            'investor_name' => $investor->name,
            'contract_date' => fake()->dateTimeBetween('-6 months', 'now')->format('Y-m-d'),
            'investment_purpose' => 'Participación en proyecto energético renovable',
            'governing_law' => 'Ley española',
            'dispute_resolution' => 'Arbitraje en Madrid'
        ];
    }

    private function generatePerformanceMetrics(string $status): array
    {
        if ($status === 'pending' || $status === 'paid') {
            return [];
        }

        return [
            'monthly_performance' => fake()->randomFloat(2, -2, 5),
            'quarterly_performance' => fake()->randomFloat(2, -5, 15),
            'annual_performance' => fake()->randomFloat(2, -10, 30),
            'volatility_score' => fake()->randomFloat(2, 0.1, 0.8),
            'risk_level' => fake()->randomElement(['low', 'medium', 'high']),
            'environmental_impact_score' => fake()->randomFloat(2, 0.7, 1.0)
        ];
    }

    private function showStatistics(): void
    {
        $total = ProjectInvestment::count();
        $byStatus = ProjectInvestment::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');
        
        $byType = ProjectInvestment::selectRaw('investment_type, COUNT(*) as count')
            ->groupBy('investment_type')
            ->pluck('count', 'investment_type');

        $totalInvested = ProjectInvestment::sum('investment_amount');
        $totalReturns = ProjectInvestment::sum('total_returns_received');
        $avgROI = ProjectInvestment::whereNotNull('current_roi')->avg('current_roi');

        $this->command->info("\n📊 Estadísticas de inversiones:");
        $this->command->info("   • Total de inversiones: {$total}");
        $this->command->info("   • Monto total invertido: €" . number_format($totalInvested, 2));
        $this->command->info("   • Retornos totales pagados: €" . number_format($totalReturns, 2));
        $this->command->info("   • ROI promedio: " . round($avgROI, 2) . "%");

        $this->command->info("\n📈 Por estado:");
        foreach ($byStatus as $status => $count) {
            $statusLabel = match($status) {
                'pending' => 'Pendiente',
                'paid' => 'Pagada',
                'active' => 'Activa',
                'completed' => 'Completada',
                default => ucfirst($status)
            };
            $this->command->info("   • {$statusLabel}: {$count}");
        }

        $this->command->info("\n💰 Por tipo de inversión:");
        foreach ($byType as $type => $count) {
            $typeLabel = match($type) {
                'monetary' => 'Monetaria',
                'equipment' => 'Equipamiento',
                'services' => 'Servicios',
                'land' => 'Terreno/Espacio',
                default => ucfirst($type)
            };
            $this->command->info("   • {$typeLabel}: {$count}");
        }
    }
}







