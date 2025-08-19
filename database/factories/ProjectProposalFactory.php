<?php

namespace Database\Factories;

use App\Models\ProjectProposal;
use App\Models\User;
use App\Models\Cooperative;
use App\Models\Municipality;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProjectProposal>
 */
class ProjectProposalFactory extends Factory
{
    protected $model = ProjectProposal::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        $projectTitles = [
            'Instalación Solar Comunitaria Barrio Verde',
            'Proyecto Autoconsumo Colectivo Residencial',
            'Instalación Fotovoltaica Compartida El Sol',
            'Comunidad Energética Renovable',
            'Proyecto Solar Cooperativo Energía Limpia',
            'Instalación Comunitaria Techos Solares',
            'Autoconsumo Colectivo Sostenible',
            'Proyecto Energía Solar Vecinal',
            'Instalación Fotovoltaica Colaborativa',
            'Comunidad Solar Urbana Innovadora',
            'Proyecto Renovable Participativo',
            'Instalación Solar Multifamiliar',
            'Autoconsumo Compartido Ecológico',
            'Proyecto Energético Comunitario',
            'Instalación Solar Cooperativa Verde'
        ];

        $title = fake()->randomElement($projectTitles);
        $powerKw = fake()->randomFloat(2, 50, 500);
        $totalInvestment = $powerKw * fake()->numberBetween(800, 1500); // €800-1500 por kW
        $annualProduction = $powerKw * fake()->numberBetween(1200, 1800); // 1200-1800 kWh/kW/año
        
        return [
            'proposer_id' => fake()->randomElement(User::pluck('id')->toArray()),
            'cooperative_id' => fake()->optional(0.6)->randomElement(Cooperative::pluck('id')->toArray()),
            'title' => $title,
            'description' => $this->generateDescription($title, $powerKw),
            'summary' => "Instalación solar de {$powerKw}kW para autoconsumo colectivo con participación ciudadana",
            'objectives' => $this->generateObjectives(),
            'benefits' => $this->generateBenefits($annualProduction),
            'project_type' => fake()->randomElement([
                'community_installation', 'shared_installation', 'individual_installation',
                'energy_storage', 'efficiency_improvement'
            ]),
            'scale' => fake()->randomElement(['residential', 'commercial', 'community']),
            'municipality_id' => fake()->optional(0.8)->randomElement(Municipality::pluck('id')->toArray()),
            'specific_location' => fake()->optional(0.7)->address,
            'latitude' => fake()->optional(0.8)->latitude(35.0, 44.0), // España aproximadamente
            'longitude' => fake()->optional(0.8)->longitude(-10.0, 5.0), // España aproximadamente
            'estimated_power_kw' => $powerKw,
            'estimated_annual_production_kwh' => $annualProduction,
            'technical_specifications' => $this->generateTechnicalSpecs($powerKw),
            'total_investment_required' => $totalInvestment,
            'investment_raised' => fake()->randomFloat(2, 0, $totalInvestment * 0.8),
            'min_investment_per_participant' => fake()->numberBetween(500, 2000),
            'max_investment_per_participant' => fake()->numberBetween(5000, 20000),
            'max_participants' => fake()->numberBetween(10, 100),
            'current_participants' => fake()->numberBetween(0, 50),
            'estimated_roi_percentage' => fake()->randomFloat(1, 4.0, 12.0),
            'payback_period_years' => fake()->numberBetween(6, 15),
            'estimated_annual_savings' => fake()->randomFloat(2, 1000, 10000),
            'financial_projections' => $this->generateFinancialProjections(),
            'funding_deadline' => fake()->dateTimeBetween('now', '+6 months'),
            'project_start_date' => fake()->optional(0.7)->dateTimeBetween('+1 month', '+8 months'),
            'expected_completion_date' => fake()->optional(0.7)->dateTimeBetween('+3 months', '+12 months'),
            'estimated_duration_months' => fake()->numberBetween(2, 12),
            'project_milestones' => $this->generateMilestones(),
            'documents' => fake()->optional(0.5)->randomElements([
                'proyecto_tecnico.pdf', 'estudio_viabilidad.pdf', 'presupuesto_detallado.pdf'
            ], 2),
            'images' => fake()->optional(0.7)->randomElements([
                'ubicacion_proyecto.jpg', 'render_instalacion.jpg', 'planos_tecnicos.jpg'
            ], 2),
            'technical_reports' => fake()->optional(0.4)->randomElements([
                'informe_irradiacion.pdf', 'analisis_sombreado.pdf', 'estudio_estructural.pdf'
            ], 2),
            'has_permits' => fake()->boolean(60),
            'permits_status' => fake()->optional(0.6)->randomElements([
                'licencia_obras' => 'aprobada',
                'conexion_red' => 'tramitando',
                'licencia_actividad' => 'pendiente'
            ]),
            'is_technically_validated' => fake()->boolean(70),
            'technical_validator_id' => fake()->optional(0.7)->randomElement(User::pluck('id')->toArray()),
            'technical_validation_date' => fake()->optional(0.7)->dateTimeBetween('-1 month', 'now'),
            'status' => fake()->randomElement([
                'draft', 'under_review', 'approved', 'funding', 'funded', 'in_progress'
            ]),
            'status_notes' => fake()->optional(0.3)->sentence(),
            'views_count' => fake()->numberBetween(10, 1000),
            'likes_count' => fake()->numberBetween(0, 100),
            'comments_count' => fake()->numberBetween(0, 50),
            'shares_count' => fake()->numberBetween(0, 25),
            'bookmarks_count' => fake()->numberBetween(0, 75),
            'engagement_score' => fake()->randomFloat(2, 10, 500),
            'is_public' => fake()->boolean(90),
            'is_featured' => fake()->boolean(15),
            'allow_comments' => fake()->boolean(85),
            'allow_investments' => fake()->boolean(80),
            'notify_updates' => fake()->boolean(90),
        ];
    }

    /**
     * Generar descripción del proyecto.
     */
    private function generateDescription(string $title, float $powerKw): string
    {
        $descriptions = [
            "Este proyecto consiste en la instalación de una planta solar fotovoltaica de {$powerKw}kW destinada al autoconsumo colectivo. La instalación se ubicará en una zona con excelente irradiación solar y permitirá a los participantes reducir significativamente su factura eléctrica mientras contribuyen a la transición energética.",
            
            "Propuesta de instalación solar comunitaria de {$powerKw}kW que beneficiará a múltiples familias del barrio. El proyecto incluye paneles solares de última generación, sistema de monitorización inteligente y reparto equitativo de la energía producida entre todos los participantes.",
            
            "Iniciativa colaborativa para desarrollar una instalación fotovoltaica de {$powerKw}kW con participación ciudadana. Los inversores podrán beneficiarse tanto del ahorro energético como de los retornos económicos generados por la venta de excedentes a la red eléctrica.",
        ];

        return fake()->randomElement($descriptions);
    }

    /**
     * Generar objetivos del proyecto.
     */
    private function generateObjectives(): array
    {
        return [
            'Reducir la dependencia energética de la red eléctrica convencional',
            'Generar energía limpia y renovable para la comunidad',
            'Crear un modelo de participación ciudadana en proyectos energéticos',
            'Obtener rentabilidad económica sostenible para los inversores',
            'Contribuir a la reducción de emisiones de CO2',
            'Fomentar la conciencia ambiental en el barrio'
        ];
    }

    /**
     * Generar beneficios del proyecto.
     */
    private function generateBenefits(float $annualProduction): array
    {
        $co2Reduction = round($annualProduction * 0.3, 0); // kg CO2/año aproximado
        
        return [
            "Producción anual estimada de {$annualProduction} kWh de energía limpia",
            "Reducción de aproximadamente {$co2Reduction} kg de CO2 al año",
            'Ahorro en la factura eléctrica de hasta el 70%',
            'Rentabilidad económica atractiva para los inversores',
            'Contribución al desarrollo sostenible local',
            'Creación de empleo verde en la zona'
        ];
    }

    /**
     * Generar especificaciones técnicas.
     */
    private function generateTechnicalSpecs(float $powerKw): array
    {
        $panelCount = ceil($powerKw / 0.4); // Asumiendo paneles de 400W
        
        return [
            'panel_type' => fake()->randomElement(['monocristalino', 'policristalino']),
            'panel_power' => fake()->randomElement([400, 450, 500, 550]),
            'panel_count' => $panelCount,
            'inverter_type' => fake()->randomElement(['string', 'optimizadores', 'microinversores']),
            'monitoring_system' => true,
            'warranty_years' => fake()->randomElement([20, 25]),
            'expected_degradation' => fake()->randomFloat(2, 0.4, 0.8),
            'installation_type' => fake()->randomElement(['cubierta', 'suelo', 'pergola']),
        ];
    }

    /**
     * Generar proyecciones financieras.
     */
    private function generateFinancialProjections(): array
    {
        $years = [];
        for ($i = 1; $i <= 20; $i++) {
            $years["year_{$i}"] = [
                'production_kwh' => fake()->numberBetween(1000, 2000),
                'revenue_eur' => fake()->numberBetween(150, 350),
                'maintenance_cost_eur' => fake()->numberBetween(50, 150),
                'net_profit_eur' => fake()->numberBetween(100, 250),
            ];
        }
        
        return $years;
    }

    /**
     * Generar hitos del proyecto.
     */
    private function generateMilestones(): array
    {
        return [
            [
                'name' => 'Obtención de permisos',
                'description' => 'Tramitación y obtención de todas las licencias necesarias',
                'estimated_date' => fake()->dateTimeBetween('now', '+2 months')->format('Y-m-d'),
                'status' => 'pending'
            ],
            [
                'name' => 'Compra de equipamiento',
                'description' => 'Adquisición de paneles solares, inversores y material eléctrico',
                'estimated_date' => fake()->dateTimeBetween('+1 month', '+3 months')->format('Y-m-d'),
                'status' => 'pending'
            ],
            [
                'name' => 'Instalación',
                'description' => 'Montaje de la instalación fotovoltaica',
                'estimated_date' => fake()->dateTimeBetween('+2 months', '+4 months')->format('Y-m-d'),
                'status' => 'pending'
            ],
            [
                'name' => 'Conexión a red',
                'description' => 'Conexión y puesta en marcha de la instalación',
                'estimated_date' => fake()->dateTimeBetween('+3 months', '+5 months')->format('Y-m-d'),
                'status' => 'pending'
            ]
        ];
    }

    /**
     * Proyecto en fase de financiación.
     */
    public function funding(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'funding',
            'is_public' => true,
            'allow_investments' => true,
            'is_technically_validated' => true,
            'has_permits' => true,
            'funding_deadline' => fake()->dateTimeBetween('+1 month', '+4 months'),
        ]);
    }

    /**
     * Proyecto destacado.
     */
    public function featured(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_featured' => true,
            'status' => 'funding',
            'is_public' => true,
            'engagement_score' => fake()->randomFloat(2, 200, 1000),
            'views_count' => fake()->numberBetween(500, 3000),
            'likes_count' => fake()->numberBetween(50, 300),
        ]);
    }

    /**
     * Proyecto completamente financiado.
     */
    public function fullyFunded(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'funded',
                'investment_raised' => $attributes['total_investment_required'],
                'current_participants' => fake()->numberBetween(20, $attributes['max_participants']),
            ];
        });
    }
}
