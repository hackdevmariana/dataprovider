<?php

namespace Database\Seeders;

use App\Models\ConsultationService;
use App\Models\User;
use Illuminate\Database\Seeder;

class ConsultationServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('💼 Sembrando servicios de consultoría...');

        $users = User::all();

        if ($users->isEmpty()) {
            $this->command->warn('❌ No hay usuarios disponibles. Ejecuta UserSeeder primero.');
            return;
        }

        $consultationTypes = ['technical', 'legal', 'financial', 'installation', 'maintenance', 'custom'];
        $formats = ['online', 'onsite', 'hybrid', 'document_review', 'phone_call'];
        $statuses = ['requested', 'accepted', 'in_progress', 'completed', 'cancelled', 'disputed'];
        $currencies = ['EUR', 'USD'];

        $createdCount = 0;

        foreach ($users as $client) {
            // Cada usuario puede solicitar entre 0 y 3 consultorías
            $numConsultations = fake()->numberBetween(0, 3);
            
            for ($i = 0; $i < $numConsultations; $i++) {
                $consultant = $users->where('id', '!=', $client->id)->random();
                $type = fake()->randomElement($consultationTypes);
                $format = fake()->randomElement($formats);
                $status = fake()->randomElement($statuses);
                
                $requestedAt = fake()->dateTimeBetween('-1 year', 'now');
                $acceptedAt = $status !== 'requested' ? fake()->dateTimeBetween($requestedAt, 'now') : null;
                $startedAt = in_array($status, ['in_progress', 'completed']) ? fake()->dateTimeBetween($requestedAt, 'now') : null;
                $completedAt = $status === 'completed' ? fake()->dateTimeBetween($requestedAt, 'now') : null;

                $consultation = ConsultationService::create([
                    'consultant_id' => $consultant->id,
                    'client_id' => $client->id,
                    'title' => $this->generateTitle($type),
                    'description' => $this->generateDescription($type),
                    'type' => $type,
                    'format' => $format,
                    'status' => $status,
                    'hourly_rate' => fake()->randomFloat(2, 50, 200),
                    'fixed_price' => fake()->boolean(30) ? fake()->randomFloat(2, 500, 5000) : null,
                    'total_amount' => fake()->randomFloat(2, 500, 5000),
                    'currency' => fake()->randomElement($currencies),
                    'estimated_hours' => fake()->numberBetween(1, 40),
                    'actual_hours' => $status === 'completed' ? fake()->numberBetween(1, 40) : null,
                    'requested_at' => $requestedAt,
                    'accepted_at' => $acceptedAt,
                    'started_at' => $startedAt,
                    'completed_at' => $completedAt,
                    'deadline' => fake()->dateTimeBetween('+1 week', '+3 months'),
                    'requirements' => $this->generateRequirements($type),
                    'deliverables' => $this->generateDeliverables($type, $status),
                    'milestones' => $this->generateMilestones($type),
                    'client_notes' => fake()->boolean(60) ? fake()->paragraph() : null,
                    'consultant_notes' => fake()->boolean(40) ? fake()->paragraph() : null,
                    'client_rating' => $status === 'completed' ? fake()->numberBetween(3, 5) : null,
                    'consultant_rating' => $status === 'completed' ? fake()->numberBetween(3, 5) : null,
                    'client_review' => $status === 'completed' && fake()->boolean(70) ? fake()->paragraph() : null,
                    'consultant_review' => $status === 'completed' && fake()->boolean(50) ? fake()->paragraph() : null,
                    'platform_commission' => fake()->randomFloat(4, 0.05, 0.15),
                    'is_featured' => fake()->boolean(15),
                ]);

                $createdCount++;
            }
        }

        $this->command->info("✅ Creadas {$createdCount} consultorías");
        $this->showStatistics();
    }

    private function generateTitle(string $type): string
    {
        return match($type) {
            'technical' => fake()->randomElement([
                'Consulta técnica sobre instalación solar',
                'Revisión técnica de proyecto fotovoltaico',
                'Asesoramiento técnico para optimización energética',
                'Análisis técnico de viabilidad solar'
            ]),
            'legal' => fake()->randomElement([
                'Asesoramiento legal para permisos solares',
                'Consulta sobre normativa energética',
                'Revisión de contratos de instalación',
                'Asesoramiento en regulación fotovoltaica'
            ]),
            'financial' => fake()->randomElement([
                'Análisis financiero de proyecto solar',
                'Consulta sobre financiación renovable',
                'Asesoramiento en ROI de instalación',
                'Estudio de viabilidad económica'
            ]),
            'installation' => fake()->randomElement([
                'Supervisión de instalación fotovoltaica',
                'Consulta sobre montaje de paneles',
                'Asesoramiento en instalación solar',
                'Revisión de proceso de instalación'
            ]),
            'maintenance' => fake()->randomElement([
                'Plan de mantenimiento solar',
                'Consulta sobre mantenimiento fotovoltaico',
                'Asesoramiento en optimización de rendimiento',
                'Revisión de sistemas de monitoreo'
            ]),
            'custom' => fake()->randomElement([
                'Consulta personalizada sobre energía renovable',
                'Asesoramiento integral en proyecto solar',
                'Consulta especializada en sostenibilidad',
                'Servicio de consultoría a medida'
            ]),
            default => 'Servicio de consultoría'
        };
    }

    private function generateDescription(string $type): string
    {
        return match($type) {
            'technical' => 'Necesito asesoramiento técnico especializado para evaluar la viabilidad técnica de mi proyecto de instalación solar. Requiero análisis detallado de especificaciones técnicas, cálculo de rendimiento energético y recomendaciones para optimización del sistema.',
            'legal' => 'Busco asesoramiento legal para comprender la normativa vigente en materia de instalaciones fotovoltaicas, obtención de permisos necesarios y cumplimiento de regulaciones municipales y autonómicas aplicables.',
            'financial' => 'Requiero análisis financiero detallado para evaluar la rentabilidad de mi proyecto de instalación solar, incluyendo cálculo de ROI, período de amortización y análisis de diferentes opciones de financiación disponibles.',
            'installation' => 'Necesito supervisión y asesoramiento durante el proceso de instalación de mi sistema fotovoltaico para garantizar la correcta implementación y cumplimiento de estándares de calidad.',
            'maintenance' => 'Busco asesoramiento para establecer un plan de mantenimiento preventivo y correctivo para mi instalación solar, incluyendo protocolos de monitoreo y optimización del rendimiento.',
            'custom' => 'Requiero un servicio de consultoría personalizado que aborde aspectos específicos de mi proyecto de energía renovable, adaptado a mis necesidades particulares y objetivos.',
            default => 'Descripción del servicio de consultoría solicitado.'
        };
    }

    private function generateRequirements(string $type): array
    {
        return match($type) {
            'technical' => [
                'Análisis de ubicación y orientación',
                'Cálculo de producción energética',
                'Especificaciones técnicas del sistema',
                'Recomendaciones de optimización'
            ],
            'legal' => [
                'Revisión de normativa aplicable',
                'Lista de permisos necesarios',
                'Documentación legal requerida',
                'Asesoramiento en procedimientos'
            ],
            'financial' => [
                'Análisis de costes y beneficios',
                'Cálculo de ROI y período de amortización',
                'Opciones de financiación',
                'Proyecciones financieras'
            ],
            'installation' => [
                'Supervisión del proceso de instalación',
                'Verificación de cumplimiento técnico',
                'Protocolos de calidad y seguridad',
                'Documentación de la instalación'
            ],
            'maintenance' => [
                'Plan de mantenimiento preventivo',
                'Protocolos de monitoreo',
                'Procedimientos de optimización',
                'Calendario de revisiones'
            ],
            'custom' => [
                'Análisis personalizado de necesidades',
                'Propuesta de solución adaptada',
                'Seguimiento y soporte continuo',
                'Documentación específica del proyecto'
            ],
            default => ['Análisis y recomendaciones']
        };
    }

    private function generateDeliverables(string $type, string $status): array
    {
        if ($status !== 'completed') {
            return [];
        }

        return match($type) {
            'technical' => [
                'Informe técnico detallado',
                'Especificaciones del sistema recomendado',
                'Cálculos de producción energética',
                'Recomendaciones de optimización'
            ],
            'legal' => [
                'Informe legal con normativa aplicable',
                'Lista de permisos y trámites',
                'Documentación legal modelo',
                'Guía de procedimientos'
            ],
            'financial' => [
                'Análisis financiero completo',
                'Proyecciones de rentabilidad',
                'Comparativa de opciones de financiación',
                'Recomendaciones financieras'
            ],
            'installation' => [
                'Informe de supervisión de instalación',
                'Certificación de cumplimiento técnico',
                'Documentación fotográfica',
                'Manual de operación'
            ],
            'maintenance' => [
                'Plan de mantenimiento personalizado',
                'Protocolos de monitoreo',
                'Calendario de revisiones',
                'Manual de mantenimiento'
            ],
            'custom' => [
                'Informe personalizado',
                'Propuesta de solución',
                'Documentación específica',
                'Plan de seguimiento'
            ],
            default => ['Informe final']
        };
    }

    private function generateMilestones(string $type): array
    {
        return match($type) {
            'technical' => [
                'Análisis inicial de viabilidad',
                'Cálculos técnicos detallados',
                'Especificaciones del sistema',
                'Informe final con recomendaciones'
            ],
            'legal' => [
                'Revisión de normativa aplicable',
                'Identificación de permisos necesarios',
                'Preparación de documentación',
                'Asesoramiento en procedimientos'
            ],
            'financial' => [
                'Análisis de costes',
                'Cálculo de rentabilidad',
                'Evaluación de opciones de financiación',
                'Informe financiero final'
            ],
            'installation' => [
                'Planificación de la instalación',
                'Supervisión durante el montaje',
                'Verificación de cumplimiento',
                'Certificación final'
            ],
            'maintenance' => [
                'Análisis del sistema actual',
                'Diseño del plan de mantenimiento',
                'Implementación de protocolos',
                'Entrenamiento y documentación'
            ],
            'custom' => [
                'Análisis de necesidades',
                'Propuesta de solución',
                'Implementación de recomendaciones',
                'Seguimiento y soporte'
            ],
            default => ['Fase 1', 'Fase 2', 'Fase 3', 'Entrega final']
        };
    }

    private function showStatistics(): void
    {
        $total = ConsultationService::count();
        $requested = ConsultationService::requested()->count();
        $accepted = ConsultationService::accepted()->count();
        $inProgress = ConsultationService::inProgress()->count();
        $completed = ConsultationService::completed()->count();
        $overdue = ConsultationService::overdue()->count();
        
        $totalRevenue = ConsultationService::completed()->sum('total_amount');
        $avgRating = ConsultationService::whereNotNull('client_rating')->avg('client_rating');
        
        $byType = ConsultationService::selectRaw('type, COUNT(*) as count')
            ->groupBy('type')
            ->pluck('count', 'type');
        
        $byStatus = ConsultationService::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');
        
        $byFormat = ConsultationService::selectRaw('format, COUNT(*) as count')
            ->groupBy('format')
            ->pluck('count', 'format');

        $this->command->info("\n📊 Estadísticas de servicios de consultoría:");
        $this->command->info("   • Total de consultorías: {$total}");
        $this->command->info("   • Solicitadas: {$requested}");
        $this->command->info("   • Aceptadas: {$accepted}");
        $this->command->info("   • En progreso: {$inProgress}");
        $this->command->info("   • Completadas: {$completed}");
        $this->command->info("   • Vencidas: {$overdue}");
        $this->command->info("   • Ingresos totales: €" . number_format($totalRevenue, 2, ',', '.'));
        $this->command->info("   • Valoración promedio: " . round($avgRating, 1));

        $this->command->info("\n💼 Por tipo:");
        foreach ($byType as $type => $count) {
            $typeLabel = match($type) {
                'technical' => 'Técnica',
                'legal' => 'Legal',
                'financial' => 'Financiera',
                'installation' => 'Instalación',
                'maintenance' => 'Mantenimiento',
                'custom' => 'Personalizada',
                default => ucfirst($type)
            };
            $this->command->info("   • {$typeLabel}: {$count}");
        }

        $this->command->info("\n📈 Por estado:");
        foreach ($byStatus as $status => $count) {
            $statusLabel = match($status) {
                'requested' => 'Solicitada',
                'accepted' => 'Aceptada',
                'in_progress' => 'En progreso',
                'completed' => 'Completada',
                'cancelled' => 'Cancelada',
                'disputed' => 'En disputa',
                default => ucfirst($status)
            };
            $this->command->info("   • {$statusLabel}: {$count}");
        }

        $this->command->info("\n🎯 Por formato:");
        foreach ($byFormat as $format => $count) {
            $formatLabel = match($format) {
                'online' => 'Online',
                'onsite' => 'Presencial',
                'hybrid' => 'Híbrido',
                'document_review' => 'Revisión documentos',
                'phone_call' => 'Llamada telefónica',
                default => ucfirst($format)
            };
            $this->command->info("   • {$formatLabel}: {$count}");
        }

        // Mostrar algunas consultorías recientes
        $recentConsultations = ConsultationService::with(['consultant', 'client'])
            ->latest()
            ->take(5)
            ->get();

        if ($recentConsultations->isNotEmpty()) {
            $this->command->info("\n⭐ Últimas consultorías creadas:");
            foreach ($recentConsultations as $consultation) {
                $clientName = $consultation->client ? $consultation->client->name : 'Cliente Desconocido';
                $consultantName = $consultation->consultant ? $consultation->consultant->name : 'Consultor Desconocido';
                $this->command->info("   • {$clientName} solicitó consultoría '{$consultation->getTypeLabel()}' a {$consultantName} (Estado: {$consultation->getStatusLabel()})");
            }
        }
    }
}
