<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ConsultationService;
use App\Models\User;
use Carbon\Carbon;

class ConsultationServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        
        if ($users->isEmpty()) {
            $this->command->warn('No hay usuarios disponibles. Ejecuta UserSeeder primero.');
            return;
        }

        $consultationTypes = ['technical', 'legal', 'financial', 'installation', 'maintenance', 'custom'];
        $consultationFormats = ['online', 'onsite', 'hybrid', 'document_review', 'phone_call'];
        $consultationStatuses = ['requested', 'accepted', 'in_progress', 'completed', 'cancelled', 'disputed'];
        $currencies = ['EUR', 'USD', 'GBP'];

        $consultations = [];

        // Crear consultas solicitadas
        for ($i = 0; $i < 10; $i++) {
            $type = $consultationTypes[array_rand($consultationTypes)];
            $format = $consultationFormats[array_rand($consultationFormats)];
            $status = 'requested';
            $consultant = $users->random();
            $client = $users->where('id', '!=', $consultant->id)->random();
            
            $consultations[] = [
                'consultant_id' => $consultant->id,
                'client_id' => $client->id,
                'title' => $this->getConsultationTitle($type),
                'description' => $this->getConsultationDescription($type),
                'type' => $type,
                'format' => $format,
                'status' => $status,
                'hourly_rate' => $this->getHourlyRate($type),
                'fixed_price' => $this->getFixedPrice($type),
                'total_amount' => null, // Se calculará automáticamente
                'currency' => $currencies[array_rand($currencies)],
                'estimated_hours' => rand(2, 20),
                'actual_hours' => null,
                'requested_at' => Carbon::now()->subDays(rand(1, 30)),
                'accepted_at' => null,
                'started_at' => null,
                'completed_at' => null,
                'deadline' => Carbon::now()->addDays(rand(7, 60)),
                'requirements' => $this->getRequirements($type),
                'deliverables' => $this->getDeliverables($type),
                'milestones' => $this->getMilestones($type),
                'client_notes' => $this->getClientNotes(),
                'consultant_notes' => null,
                'client_rating' => null,
                'consultant_rating' => null,
                'client_review' => null,
                'consultant_review' => null,
                'platform_commission' => 0.15,
                'is_featured' => rand(0, 1),
                'created_at' => Carbon::now()->subDays(rand(1, 30)),
                'updated_at' => Carbon::now()->subDays(rand(1, 30)),
            ];
        }

        // Crear consultas aceptadas
        for ($i = 0; $i < 8; $i++) {
            $type = $consultationTypes[array_rand($consultationTypes)];
            $format = $consultationFormats[array_rand($consultationFormats)];
            $status = 'accepted';
            $consultant = $users->random();
            $client = $users->where('id', '!=', $consultant->id)->random();
            $requestedAt = Carbon::now()->subDays(rand(5, 45));
            $acceptedAt = $requestedAt->copy()->addDays(rand(1, 7));
            
            $consultations[] = [
                'consultant_id' => $consultant->id,
                'client_id' => $client->id,
                'title' => $this->getConsultationTitle($type),
                'description' => $this->getConsultationDescription($type),
                'type' => $type,
                'format' => $format,
                'status' => $status,
                'hourly_rate' => $this->getHourlyRate($type),
                'fixed_price' => $this->getFixedPrice($type),
                'total_amount' => $this->calculateTotalAmount($this->getHourlyRate($type), rand(2, 20)),
                'currency' => $currencies[array_rand($currencies)],
                'estimated_hours' => rand(2, 20),
                'actual_hours' => null,
                'requested_at' => $requestedAt,
                'accepted_at' => $acceptedAt,
                'started_at' => null,
                'completed_at' => null,
                'deadline' => $acceptedAt->copy()->addDays(rand(7, 60)),
                'requirements' => $this->getRequirements($type),
                'deliverables' => $this->getDeliverables($type),
                'milestones' => $this->getMilestones($type),
                'client_notes' => $this->getClientNotes(),
                'consultant_notes' => $this->getConsultantAcceptanceNotes(),
                'client_rating' => null,
                'consultant_rating' => null,
                'client_review' => null,
                'consultant_review' => null,
                'platform_commission' => 0.15,
                'is_featured' => rand(0, 1),
                'created_at' => $requestedAt,
                'updated_at' => $acceptedAt,
            ];
        }

        // Crear consultas en progreso
        for ($i = 0; $i < 6; $i++) {
            $type = $consultationTypes[array_rand($consultationTypes)];
            $format = $consultationFormats[array_rand($consultationFormats)];
            $status = 'in_progress';
            $consultant = $users->random();
            $client = $users->where('id', '!=', $consultant->id)->random();
            $requestedAt = Carbon::now()->subDays(rand(10, 60));
            $acceptedAt = $requestedAt->copy()->addDays(rand(1, 7));
            $startedAt = $acceptedAt->copy()->addDays(rand(1, 5));
            $estimatedHours = rand(2, 20);
            $actualHours = rand(1, $estimatedHours - 1);
            
            $consultations[] = [
                'consultant_id' => $consultant->id,
                'client_id' => $client->id,
                'title' => $this->getConsultationTitle($type),
                'description' => $this->getConsultationDescription($type),
                'type' => $type,
                'format' => $format,
                'status' => $status,
                'hourly_rate' => $this->getHourlyRate($type),
                'fixed_price' => $this->getFixedPrice($type),
                'total_amount' => $this->calculateTotalAmount($this->getHourlyRate($type), $estimatedHours),
                'currency' => $currencies[array_rand($currencies)],
                'estimated_hours' => $estimatedHours,
                'actual_hours' => $actualHours,
                'requested_at' => $requestedAt,
                'accepted_at' => $acceptedAt,
                'started_at' => $startedAt,
                'completed_at' => null,
                'deadline' => $acceptedAt->copy()->addDays(rand(7, 60)),
                'requirements' => $this->getRequirements($type),
                'deliverables' => $this->getDeliverables($type),
                'milestones' => $this->getMilestones($type),
                'client_notes' => $this->getClientNotes(),
                'consultant_notes' => $this->getConsultantProgressNotes(),
                'client_rating' => null,
                'consultant_rating' => null,
                'client_review' => null,
                'consultant_review' => null,
                'platform_commission' => 0.15,
                'is_featured' => rand(0, 1),
                'created_at' => $requestedAt,
                'updated_at' => $startedAt,
            ];
        }

        // Crear consultas completadas
        for ($i = 0; $i < 12; $i++) {
            $type = $consultationTypes[array_rand($consultationTypes)];
            $format = $consultationFormats[array_rand($consultationFormats)];
            $status = 'completed';
            $consultant = $users->random();
            $client = $users->where('id', '!=', $consultant->id)->random();
            $requestedAt = Carbon::now()->subDays(rand(20, 120));
            $acceptedAt = $requestedAt->copy()->addDays(rand(1, 7));
            $startedAt = $acceptedAt->copy()->addDays(rand(1, 5));
            $completedAt = $startedAt->copy()->addDays(rand(3, 15));
            $estimatedHours = rand(2, 20);
            $actualHours = rand($estimatedHours, $estimatedHours + 2);
            $clientRating = rand(3, 5);
            $consultantRating = rand(3, 5);
            
            $consultations[] = [
                'consultant_id' => $consultant->id,
                'client_id' => $client->id,
                'title' => $this->getConsultationTitle($type),
                'description' => $this->getConsultationDescription($type),
                'type' => $type,
                'format' => $format,
                'status' => $status,
                'hourly_rate' => $this->getHourlyRate($type),
                'fixed_price' => $this->getFixedPrice($type),
                'total_amount' => $this->calculateTotalAmount($this->getHourlyRate($type), $actualHours),
                'currency' => $currencies[array_rand($currencies)],
                'estimated_hours' => $estimatedHours,
                'actual_hours' => $actualHours,
                'requested_at' => $requestedAt,
                'accepted_at' => $acceptedAt,
                'started_at' => $startedAt,
                'completed_at' => $completedAt,
                'deadline' => $acceptedAt->copy()->addDays(rand(7, 60)),
                'requirements' => $this->getRequirements($type),
                'deliverables' => $this->getDeliverables($type),
                'milestones' => $this->getMilestones($type),
                'client_notes' => $this->getClientNotes(),
                'consultant_notes' => $this->getConsultantCompletionNotes(),
                'client_rating' => $clientRating,
                'consultant_rating' => $consultantRating,
                'client_review' => $this->getClientReview($clientRating),
                'consultant_review' => $this->getConsultantReview($consultantRating),
                'platform_commission' => 0.15,
                'is_featured' => rand(0, 1),
                'created_at' => $requestedAt,
                'updated_at' => $completedAt,
            ];
        }

        // Crear consultas canceladas
        for ($i = 0; $i < 4; $i++) {
            $type = $consultationTypes[array_rand($consultationTypes)];
            $format = $consultationFormats[array_rand($consultationFormats)];
            $status = 'cancelled';
            $consultant = $users->random();
            $client = $users->where('id', '!=', $consultant->id)->random();
            $requestedAt = Carbon::now()->subDays(rand(15, 60));
            $acceptedAt = $requestedAt->copy()->addDays(rand(1, 7));
            
            $consultations[] = [
                'consultant_id' => $consultant->id,
                'client_id' => $client->id,
                'title' => $this->getConsultationTitle($type),
                'description' => $this->getConsultationDescription($type),
                'type' => $type,
                'format' => $format,
                'status' => $status,
                'hourly_rate' => $this->getHourlyRate($type),
                'fixed_price' => $this->getFixedPrice($type),
                'total_amount' => $this->calculateTotalAmount($this->getHourlyRate($type), rand(2, 20)),
                'currency' => $currencies[array_rand($currencies)],
                'estimated_hours' => rand(2, 20),
                'actual_hours' => null,
                'requested_at' => $requestedAt,
                'accepted_at' => $acceptedAt,
                'started_at' => null,
                'completed_at' => null,
                'deadline' => $acceptedAt->copy()->addDays(rand(7, 60)),
                'requirements' => $this->getRequirements($type),
                'deliverables' => $this->getDeliverables($type),
                'milestones' => $this->getMilestones($type),
                'client_notes' => $this->getClientNotes(),
                'consultant_notes' => $this->getCancellationNotes(),
                'client_rating' => null,
                'consultant_rating' => null,
                'client_review' => null,
                'consultant_review' => null,
                'platform_commission' => 0.15,
                'is_featured' => false,
                'created_at' => $requestedAt,
                'updated_at' => $acceptedAt,
            ];
        }

        // Crear consultas en disputa
        for ($i = 0; $i < 2; $i++) {
            $type = $consultationTypes[array_rand($consultationTypes)];
            $format = $consultationFormats[array_rand($consultationFormats)];
            $status = 'disputed';
            $consultant = $users->random();
            $client = $users->where('id', '!=', $consultant->id)->random();
            $requestedAt = Carbon::now()->subDays(rand(25, 90));
            $acceptedAt = $requestedAt->copy()->addDays(rand(1, 7));
            $startedAt = $acceptedAt->copy()->addDays(rand(1, 5));
            $estimatedHours = rand(2, 20);
            $actualHours = rand(1, $estimatedHours);
            
            $consultations[] = [
                'consultant_id' => $consultant->id,
                'client_id' => $client->id,
                'title' => $this->getConsultationTitle($type),
                'description' => $this->getConsultationDescription($type),
                'type' => $type,
                'format' => $format,
                'status' => $status,
                'hourly_rate' => $this->getHourlyRate($type),
                'fixed_price' => $this->getFixedPrice($type),
                'total_amount' => $this->calculateTotalAmount($this->getHourlyRate($type), $estimatedHours),
                'currency' => $currencies[array_rand($currencies)],
                'estimated_hours' => $estimatedHours,
                'actual_hours' => $actualHours,
                'requested_at' => $requestedAt,
                'accepted_at' => $acceptedAt,
                'started_at' => $startedAt,
                'completed_at' => null,
                'deadline' => $acceptedAt->copy()->addDays(rand(7, 60)),
                'requirements' => $this->getRequirements($type),
                'deliverables' => $this->getDeliverables($type),
                'milestones' => $this->getMilestones($type),
                'client_notes' => $this->getDisputeClientNotes(),
                'consultant_notes' => $this->getDisputeConsultantNotes(),
                'client_rating' => null,
                'consultant_rating' => null,
                'client_review' => null,
                'consultant_review' => null,
                'platform_commission' => 0.15,
                'is_featured' => false,
                'created_at' => $requestedAt,
                'updated_at' => $startedAt,
            ];
        }

        // Insertar todas las consultas
        foreach ($consultations as $consultation) {
            ConsultationService::create($consultation);
        }

        $this->command->info('✅ Se han creado ' . count($consultations) . ' servicios de consultoría.');
        $this->command->info('📊 Distribución por estado:');
        $this->command->info('   - Solicitadas: 10');
        $this->command->info('   - Aceptadas: 8');
        $this->command->info('   - En progreso: 6');
        $this->command->info('   - Completadas: 12');
        $this->command->info('   - Canceladas: 4');
        $this->command->info('   - En disputa: 2');
        $this->command->info('🏷️ Tipos de consultoría: Técnica, Legal, Financiera, Instalación, Mantenimiento, Personalizada');
        $this->command->info('📱 Formatos: Online, Presencial, Híbrido, Revisión documentos, Llamada telefónica');
        $this->command->info('💰 Tarifas: Por hora (25€-150€) y precios fijos (100€-2000€)');
    }

    /**
     * Obtener título de consultoría por tipo
     */
    private function getConsultationTitle(string $type): string
    {
        $titles = [
            'technical' => [
                'Análisis Técnico de Instalación Solar',
                'Optimización de Sistema de Energía',
                'Auditoría Técnica de Eficiencia Energética',
                'Diseño de Sistema de Almacenamiento',
                'Evaluación de Viabilidad Técnica',
            ],
            'legal' => [
                'Asesoría Legal en Permisos Energéticos',
                'Revisión de Contratos de Energía',
                'Cumplimiento Normativo Ambiental',
                'Asesoría en Regulación Energética',
                'Negociación de Acuerdos Comerciales',
            ],
            'financial' => [
                'Análisis Financiero de Proyecto Solar',
                'Evaluación de ROI de Instalación',
                'Asesoría en Financiamiento Verde',
                'Análisis de Costos de Mantenimiento',
                'Planificación Financiera Energética',
            ],
            'installation' => [
                'Supervisión de Instalación Solar',
                'Coordinación de Proyecto Energético',
                'Gestión de Instalación Residencial',
                'Supervisión de Instalación Comercial',
                'Coordinación de Instalación Industrial',
            ],
            'maintenance' => [
                'Plan de Mantenimiento Preventivo',
                'Optimización de Sistema Existente',
                'Auditoría de Mantenimiento',
                'Plan de Mantenimiento Predictivo',
                'Optimización de Eficiencia',
            ],
            'custom' => [
                'Consultoría Personalizada Energética',
                'Asesoría Especializada en Sostenibilidad',
                'Consultoría Integral de Proyecto',
                'Asesoría Personalizada en Energía',
                'Consultoría Especializada Verde',
            ],
        ];

        $typeTitles = $titles[$type] ?? $titles['technical'];
        return $typeTitles[array_rand($typeTitles)];
    }

    /**
     * Obtener descripción de consultoría por tipo
     */
    private function getConsultationDescription(string $type): string
    {
        $descriptions = [
            'technical' => 'Servicio de consultoría técnica especializada en sistemas de energía renovable, incluyendo análisis de viabilidad, diseño técnico, optimización de sistemas existentes y auditorías de eficiencia energética.',
            'legal' => 'Asesoría legal especializada en normativa energética, permisos ambientales, contratos de energía, cumplimiento regulatorio y negociación de acuerdos comerciales en el sector energético.',
            'financial' => 'Consultoría financiera especializada en proyectos de energía renovable, incluyendo análisis de viabilidad económica, evaluación de ROI, financiamiento verde y planificación financiera estratégica.',
            'installation' => 'Servicios de supervisión y coordinación de instalaciones de sistemas de energía renovable, incluyendo gestión de proyectos, supervisión técnica y coordinación de equipos de instalación.',
            'maintenance' => 'Consultoría en mantenimiento de sistemas de energía renovable, incluyendo planes preventivos, optimización de sistemas existentes y auditorías de eficiencia operativa.',
            'custom' => 'Servicio de consultoría personalizada adaptado a las necesidades específicas del cliente, combinando diferentes áreas de expertise para ofrecer soluciones integrales.',
        ];

        return $descriptions[$type] ?? $descriptions['technical'];
    }

    /**
     * Obtener tarifa por hora por tipo
     */
    private function getHourlyRate(string $type): float
    {
        return match ($type) {
            'technical' => rand(40, 80),
            'legal' => rand(60, 120),
            'financial' => rand(50, 100),
            'installation' => rand(35, 70),
            'maintenance' => rand(30, 60),
            'custom' => rand(45, 90),
            default => 50,
        };
    }

    /**
     * Obtener precio fijo por tipo
     */
    private function getFixedPrice(string $type): ?float
    {
        // 30% de probabilidad de tener precio fijo
        if (rand(1, 100) <= 30) {
            return match ($type) {
                'technical' => rand(200, 800),
                'legal' => rand(300, 1200),
                'financial' => rand(250, 1000),
                'installation' => rand(150, 600),
                'maintenance' => rand(100, 500),
                'custom' => rand(200, 1000),
                default => 300,
            };
        }

        return null;
    }

    /**
     * Calcular cantidad total
     */
    private function calculateTotalAmount(float $hourlyRate, int $hours): float
    {
        return $hourlyRate * $hours;
    }

    /**
     * Obtener requerimientos por tipo
     */
    private function getRequirements(string $type): array
    {
        $baseRequirements = [
            'project_description' => 'Descripción detallada del proyecto',
            'current_situation' => 'Situación actual del sistema o proyecto',
            'objectives' => 'Objetivos específicos a alcanzar',
            'constraints' => 'Limitaciones y restricciones del proyecto',
        ];

        $typeRequirements = match ($type) {
            'technical' => [
                'technical_specifications' => 'Especificaciones técnicas del sistema',
                'site_conditions' => 'Condiciones del sitio de instalación',
                'energy_requirements' => 'Requerimientos energéticos del proyecto',
            ],
            'legal' => [
                'legal_documents' => 'Documentos legales relevantes',
                'regulatory_requirements' => 'Requerimientos regulatorios específicos',
                'compliance_issues' => 'Problemas de cumplimiento identificados',
            ],
            'financial' => [
                'financial_data' => 'Datos financieros del proyecto',
                'budget_constraints' => 'Restricciones presupuestarias',
                'financial_goals' => 'Objetivos financieros del proyecto',
            ],
            'installation' => [
                'installation_specifications' => 'Especificaciones de instalación',
                'site_access' => 'Acceso al sitio de instalación',
                'equipment_requirements' => 'Requerimientos de equipamiento',
            ],
            'maintenance' => [
                'maintenance_history' => 'Historial de mantenimiento',
                'current_issues' => 'Problemas actuales del sistema',
                'performance_data' => 'Datos de rendimiento del sistema',
            ],
            'custom' => [
                'specific_needs' => 'Necesidades específicas del cliente',
                'project_scope' => 'Alcance del proyecto personalizado',
                'integration_requirements' => 'Requerimientos de integración',
            ],
            default => [],
        };

        return array_merge($baseRequirements, $typeRequirements);
    }

    /**
     * Obtener entregables por tipo
     */
    private function getDeliverables(string $type): array
    {
        $baseDeliverables = [
            'final_report' => 'Informe final de consultoría',
            'recommendations' => 'Recomendaciones específicas',
            'action_plan' => 'Plan de acción detallado',
        ];

        $typeDeliverables = match ($type) {
            'technical' => [
                'technical_analysis' => 'Análisis técnico detallado',
                'system_design' => 'Diseño del sistema propuesto',
                'implementation_guide' => 'Guía de implementación',
            ],
            'legal' => [
                'legal_opinion' => 'Dictamen legal',
                'compliance_report' => 'Informe de cumplimiento',
                'contract_templates' => 'Plantillas de contratos',
            ],
            'financial' => [
                'financial_analysis' => 'Análisis financiero',
                'roi_calculation' => 'Cálculo de retorno de inversión',
                'financial_projections' => 'Proyecciones financieras',
            ],
            'installation' => [
                'installation_plan' => 'Plan de instalación',
                'coordination_schedule' => 'Cronograma de coordinación',
                'quality_control_checklist' => 'Lista de control de calidad',
            ],
            'maintenance' => [
                'maintenance_plan' => 'Plan de mantenimiento',
                'optimization_report' => 'Informe de optimización',
                'preventive_schedule' => 'Cronograma preventivo',
            ],
            'custom' => [
                'custom_solution' => 'Solución personalizada',
                'integration_plan' => 'Plan de integración',
                'custom_guidelines' => 'Directrices personalizadas',
            ],
            default => [],
        };

        return array_merge($baseDeliverables, $typeDeliverables);
    }

    /**
     * Obtener hitos del proyecto
     */
    private function getMilestones(string $type): array
    {
        $baseMilestones = [
            'initial_assessment' => [
                'title' => 'Evaluación Inicial',
                'description' => 'Análisis inicial del proyecto y requerimientos',
                'estimated_days' => 3,
            ],
            'detailed_analysis' => [
                'title' => 'Análisis Detallado',
                'description' => 'Análisis profundo de todos los aspectos del proyecto',
                'estimated_days' => 7,
            ],
            'solution_design' => [
                'title' => 'Diseño de Solución',
                'description' => 'Desarrollo de la solución propuesta',
                'estimated_days' => 5,
            ],
            'final_delivery' => [
                'title' => 'Entrega Final',
                'description' => 'Entrega de todos los entregables acordados',
                'estimated_days' => 3,
            ],
        ];

        $typeMilestones = match ($type) {
            'technical' => [
                'technical_validation' => [
                    'title' => 'Validación Técnica',
                    'description' => 'Validación de la viabilidad técnica',
                    'estimated_days' => 4,
                ],
            ],
            'legal' => [
                'legal_review' => [
                    'title' => 'Revisión Legal',
                    'description' => 'Revisión completa de aspectos legales',
                    'estimated_days' => 6,
                ],
            ],
            'financial' => [
                'financial_modeling' => [
                    'title' => 'Modelado Financiero',
                    'description' => 'Desarrollo del modelo financiero',
                    'estimated_days' => 5,
                ],
            ],
            'installation' => [
                'site_preparation' => [
                    'title' => 'Preparación del Sitio',
                    'description' => 'Preparación del sitio para instalación',
                    'estimated_days' => 4,
                ],
            ],
            'maintenance' => [
                'system_audit' => [
                    'title' => 'Auditoría del Sistema',
                    'description' => 'Auditoría completa del sistema actual',
                    'estimated_days' => 5,
                ],
            ],
            'custom' => [
                'custom_development' => [
                    'title' => 'Desarrollo Personalizado',
                    'description' => 'Desarrollo de la solución personalizada',
                    'estimated_days' => 8,
                ],
            ],
            default => [],
        };

        return array_merge($baseMilestones, $typeMilestones);
    }

    /**
     * Obtener notas del cliente
     */
    private function getClientNotes(): string
    {
        $notes = [
            'Necesito asesoría experta para optimizar mi sistema de energía solar.',
            'Proyecto complejo que requiere análisis técnico detallado.',
            'Busco soluciones personalizadas para mi instalación energética.',
            'Requiero asesoría legal para cumplir con normativas ambientales.',
            'Necesito análisis financiero para justificar la inversión.',
            'Proyecto de instalación que requiere supervisión experta.',
            'Sistema existente que necesita optimización y mantenimiento.',
            'Consulta personalizada para necesidades específicas del proyecto.',
        ];

        return $notes[array_rand($notes)];
    }

    /**
     * Obtener notas de aceptación del consultor
     */
    private function getConsultantAcceptanceNotes(): string
    {
        $notes = [
            'Proyecto aceptado. Iniciaré el análisis detallado en los próximos días.',
            'Consulta aceptada. Comenzaré con la evaluación inicial esta semana.',
            'Proyecto aprobado. Desarrollaré un plan de trabajo detallado.',
            'Consulta aceptada. Procederé con la investigación y análisis.',
            'Proyecto confirmado. Iniciaré la fase de planificación.',
        ];

        return $notes[array_rand($notes)];
    }

    /**
     * Obtener notas de progreso del consultor
     */
    private function getConsultantProgressNotes(): string
    {
        $notes = [
            'Análisis en progreso. He completado la evaluación inicial y estoy trabajando en el análisis detallado.',
            'Proyecto en desarrollo. He identificado los principales desafíos y estoy desarrollando soluciones.',
            'Trabajo en curso. He completado el 60% del análisis y estoy en la fase de diseño de soluciones.',
            'Progreso satisfactorio. He finalizado la investigación y estoy en la fase de desarrollo de recomendaciones.',
            'Proyecto avanzando bien. He completado la auditoría técnica y estoy trabajando en el plan de optimización.',
        ];

        return $notes[array_rand($notes)];
    }

    /**
     * Obtener notas de finalización del consultor
     */
    private function getConsultantCompletionNotes(): string
    {
        $notes = [
            'Proyecto completado exitosamente. Todos los entregables han sido finalizados y entregados.',
            'Consulta finalizada. He completado el análisis completo y entregado todas las recomendaciones.',
            'Proyecto terminado. El informe final y plan de acción han sido entregados al cliente.',
            'Consulta completada. He finalizado la asesoría y entregado todos los documentos acordados.',
            'Proyecto exitoso. Todas las fases han sido completadas y los resultados entregados.',
        ];

        return $notes[array_rand($notes)];
    }

    /**
     * Obtener notas de cancelación
     */
    private function getCancellationNotes(): string
    {
        $notes = [
            'Consulta cancelada por el cliente debido a cambios en los requerimientos del proyecto.',
            'Proyecto cancelado por restricciones presupuestarias del cliente.',
            'Consulta cancelada por cambios en la prioridad del proyecto.',
            'Proyecto cancelado por limitaciones de tiempo del cliente.',
            'Consulta cancelada por cambios en la dirección estratégica.',
        ];

        return $notes[array_rand($notes)];
    }

    /**
     * Obtener notas de disputa del cliente
     */
    private function getDisputeClientNotes(): string
    {
        $notes = [
            'Los entregables no cumplen con la calidad esperada según el acuerdo inicial.',
            'El consultor no ha cumplido con los plazos establecidos en el contrato.',
            'La solución propuesta no aborda adecuadamente los requerimientos del proyecto.',
            'El análisis técnico no es suficientemente detallado como se acordó.',
            'Los costos finales exceden significativamente la estimación inicial.',
        ];

        return $notes[array_rand($notes)];
    }

    /**
     * Obtener notas de disputa del consultor
     */
    private function getDisputeConsultantNotes(): string
    {
        $notes = [
            'El cliente ha cambiado los requerimientos del proyecto sin previo aviso.',
            'Los plazos se han extendido debido a demoras en la provisión de información del cliente.',
            'El alcance del proyecto se ha expandido significativamente sin ajuste de presupuesto.',
            'El cliente no ha proporcionado acceso a la información técnica necesaria.',
            'Los cambios en las especificaciones del proyecto requieren tiempo adicional de desarrollo.',
        ];

        return $notes[array_rand($notes)];
    }

    /**
     * Obtener review del cliente
     */
    private function getClientReview(int $rating): string
    {
        $reviews = [
            5 => [
                'Excelente servicio de consultoría. El consultor demostró gran expertise y entregó resultados excepcionales.',
                'Muy satisfecho con la asesoría recibida. Profesionalismo y calidad excepcionales.',
                'Servicio sobresaliente. El consultor superó todas las expectativas del proyecto.',
            ],
            4 => [
                'Muy buen servicio de consultoría. El consultor cumplió con todos los objetivos del proyecto.',
                'Satisfecho con la asesoría. El consultor demostró competencia y entregó buenos resultados.',
                'Buen servicio en general. El consultor cumplió con los plazos y entregables acordados.',
            ],
            3 => [
                'Servicio aceptable. El consultor cumplió con los requisitos básicos del proyecto.',
                'Consulta satisfactoria. El consultor entregó los resultados esperados.',
                'Servicio adecuado. El consultor cumplió con las obligaciones del contrato.',
            ],
        ];

        $ratingReviews = $reviews[$rating] ?? $reviews[3];
        return $ratingReviews[array_rand($ratingReviews)];
    }

    /**
     * Obtener review del consultor
     */
    private function getConsultantReview(int $rating): string
    {
        $reviews = [
            5 => [
                'Cliente excelente para trabajar. Comunicación clara y colaboración efectiva.',
                'Muy buen proyecto. El cliente proporcionó toda la información necesaria.',
                'Cliente profesional y organizado. Proyecto muy satisfactorio.',
            ],
            4 => [
                'Buen cliente para trabajar. Comunicación efectiva y colaboración adecuada.',
                'Proyecto satisfactorio. El cliente cumplió con sus responsabilidades.',
                'Cliente responsable. El proyecto se desarrolló sin mayores inconvenientes.',
            ],
            3 => [
                'Cliente adecuado. El proyecto se completó según lo acordado.',
                'Proyecto aceptable. El cliente cumplió con los términos del contrato.',
                'Cliente correcto. El proyecto se desarrolló de manera satisfactoria.',
            ],
        ];

        $ratingReviews = $reviews[$rating] ?? $reviews[3];
        return $ratingReviews[array_rand($ratingReviews)];
    }
}
