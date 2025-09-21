<?php

namespace Database\Seeders;

use App\Models\ProjectUpdate;
use App\Models\ProjectProposal;
use App\Models\User;
use Illuminate\Database\Seeder;

class ProjectUpdateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('📝 Sembrando actualizaciones de proyectos...');

        // Obtener datos necesarios
        $projects = ProjectProposal::take(20)->get();
        $users = User::take(15)->get();

        if ($projects->isEmpty()) {
            $this->command->error('❌ No hay proyectos disponibles. Ejecuta ProjectProposalSeeder primero.');
            return;
        }

        if ($users->isEmpty()) {
            $this->command->error('❌ No hay usuarios disponibles. Ejecuta UserSeeder primero.');
            return;
        }

        $updateTypes = ['general', 'milestone', 'financial', 'technical', 'success', 'issue'];
        $priorities = ['low', 'medium', 'high', 'urgent'];
        $visibilities = ['public', 'investors_only', 'team_only', 'private'];

        $createdCount = 0;

        // Crear actualizaciones para cada proyecto
        foreach ($projects as $project) {
            $numUpdates = fake()->numberBetween(2, 8);
            
            for ($i = 0; $i < $numUpdates; $i++) {
                $author = $users->random();
                $type = fake()->randomElement($updateTypes);
                $priority = fake()->randomElement($priorities);
                $visibility = fake()->randomElement($visibilities);
                $isMilestone = $type === 'milestone' || fake()->boolean(15);
                
                $progressPercentage = $type === 'progress' ? fake()->randomFloat(2, 0, 100) : null;
                $currentProduction = $type === 'technical' || $type === 'progress' ? fake()->randomFloat(2, 0, $project->estimated_annual_production_kwh ?? 1000) : null;
                $financialImpact = $type === 'financial' ? fake()->randomFloat(2, -10000, 50000) : null;

                $update = ProjectUpdate::create([
                    'project_proposal_id' => $project->id,
                    'author_id' => $author->id,
                    'title' => $this->generateTitle($type, $project->title),
                    'content' => $this->generateContent($type, $project),
                    'update_type' => $type,
                    'progress_percentage' => $progressPercentage,
                    'actual_power_installed_kw' => $currentProduction,
                    'budget_spent' => $financialImpact,
                    'technical_metrics' => json_encode($this->generateMetrics($type, $project)),
                    'images' => json_encode($this->generateImages($type)),
                    'completed_milestones' => $isMilestone ? json_encode([$this->generateMilestoneDescription($type)]) : null,
                    'visibility' => $visibility,
                    'is_urgent' => $priority === 'urgent',
                    'upcoming_milestones' => json_encode($this->generateNextSteps($type)),
                    'published_at' => fake()->dateTimeBetween('-6 months', 'now'),
                ]);

                $createdCount++;
            }
        }

        $this->command->info("✅ Creadas {$createdCount} actualizaciones de proyectos");
        $this->showStatistics();
    }

    private function generateTitle(string $type, string $projectTitle): string
    {
        return match($type) {
            'general' => fake()->randomElement([
                "Avances significativos en {$projectTitle}",
                "Actualización general - {$projectTitle}",
                "Nuevo progreso en {$projectTitle}"
            ]),
            'milestone' => fake()->randomElement([
                "¡Hito importante alcanzado en {$projectTitle}!",
                "Nueva fase completada - {$projectTitle}",
                "Objetivo cumplido en {$projectTitle}"
            ]),
            'financial' => fake()->randomElement([
                "Actualización financiera - {$projectTitle}",
                "Nuevos datos de rentabilidad en {$projectTitle}",
                "Reporte económico - {$projectTitle}"
            ]),
            'technical' => fake()->randomElement([
                "Actualización técnica - {$projectTitle}",
                "Mejoras implementadas en {$projectTitle}",
                "Optimización del sistema - {$projectTitle}"
            ]),
            'success' => fake()->randomElement([
                "¡Éxito alcanzado en {$projectTitle}!",
                "Logro importante - {$projectTitle}",
                "Meta superada en {$projectTitle}"
            ]),
            'issue' => fake()->randomElement([
                "Incidencia detectada en {$projectTitle}",
                "Problema técnico - {$projectTitle}",
                "Retraso en {$projectTitle}"
            ]),
            default => "Actualización - {$projectTitle}"
        };
    }

    private function generateContent(string $type, $project): string
    {
        return match($type) {
            'progress' => "Nos complace informar sobre los avances significativos en el proyecto {$project->title}. " .
                         "Hemos completado las fases de diseño y obtención de permisos, y actualmente estamos " .
                         "en la etapa de instalación de equipos. El progreso general del proyecto se encuentra " .
                         "en un " . fake()->randomFloat(1, 20, 90) . "% de completitud.",
                         
            'milestone' => "¡Excelentes noticias! Hemos alcanzado un hito importante en el proyecto {$project->title}. " .
                          "La instalación de los equipos principales ha sido completada exitosamente y " .
                          "hemos comenzado las pruebas de funcionamiento.",
                          
            'financial' => "Presentamos el reporte financiero actualizado del proyecto {$project->title}. " .
                           "Los costes se mantienen dentro del presupuesto establecido, con una " .
                           "desviación mínima del " . fake()->randomFloat(1, -5, 5) . "%.",
                           
            'technical' => "Actualización técnica del proyecto {$project->title}: " .
                           "Hemos implementado mejoras en el sistema de monitoreo que nos permitirán " .
                           "un control más preciso de la producción energética.",
                           
            'announcement' => "Comunicado importante sobre el proyecto {$project->title}: " .
                             "Queremos informar sobre una actualización significativa en nuestros " .
                             "procesos operativos.",
                             
            'issue' => "Información sobre una incidencia detectada en el proyecto {$project->title}: " .
                       "Durante las pruebas de funcionamiento, hemos identificado un problema " .
                       "menor en uno de los componentes del sistema.",
                       
            default => "Actualización del proyecto {$project->title}: " .
                      "Continuamos trabajando diligentemente para completar todos los aspectos " .
                      "del proyecto según lo planificado."
        };
    }

    private function generateMetrics(string $type, $project): array
    {
        $baseMetrics = [
            'views_count' => fake()->numberBetween(50, 500),
            'engagement_rate' => fake()->randomFloat(2, 0.1, 0.8),
            'response_time_hours' => fake()->numberBetween(1, 48)
        ];

        return match($type) {
            'progress' => array_merge($baseMetrics, [
                'completion_percentage' => fake()->randomFloat(2, 0, 100),
                'tasks_completed' => fake()->numberBetween(5, 50),
                'tasks_remaining' => fake()->numberBetween(1, 20),
                'estimated_completion_days' => fake()->numberBetween(1, 90)
            ]),
            'milestone' => array_merge($baseMetrics, [
                'milestone_number' => fake()->numberBetween(1, 10),
                'days_ahead_schedule' => fake()->numberBetween(-10, 30),
                'quality_score' => fake()->randomFloat(2, 0.7, 1.0)
            ]),
            'financial' => array_merge($baseMetrics, [
                'budget_variance_percentage' => fake()->randomFloat(2, -10, 10),
                'cost_savings' => fake()->randomFloat(2, 0, 50000),
                'roi_improvement' => fake()->randomFloat(2, 0, 5)
            ]),
            'technical' => array_merge($baseMetrics, [
                'efficiency_improvement' => fake()->randomFloat(2, 0, 15),
                'downtime_minutes' => fake()->numberBetween(0, 480),
                'system_reliability' => fake()->randomFloat(2, 0.85, 1.0)
            ]),
            default => $baseMetrics
        };
    }

    private function generateImages(string $type): array
    {
        if (!fake()->boolean(60)) {
            return [];
        }

        $imageCount = fake()->numberBetween(1, 3);
        $images = [];
        
        for ($i = 0; $i < $imageCount; $i++) {
            $images[] = [
                'url' => fake()->imageUrl(800, 600, 'technology'),
                'alt' => fake()->sentence(4),
                'type' => 'project_update_image',
                'uploaded_at' => fake()->dateTimeBetween('-1 month', 'now')
            ];
        }
        
        return $images;
    }

    private function generateMilestoneDescription(string $type): ?string
    {
        if (!fake()->boolean(70)) {
            return null;
        }

        return match($type) {
            'milestone' => "Hito importante: " . fake()->randomElement([
                "Completada la instalación de equipos principales",
                "Obtenidos todos los permisos necesarios",
                "Finalizada la conexión a la red eléctrica",
                "Completadas las pruebas de funcionamiento"
            ]),
            'progress' => "Progreso significativo: " . fake()->randomElement([
                "Avance del 50% en la instalación",
                "Completado el 75% de las obras civiles",
                "Finalizada la fase de diseño"
            ]),
            default => null
        };
    }

    private function generateTags(string $type): array
    {
        $baseTags = ['proyecto', 'energía', 'renovable', 'sostenibilidad'];
        
        $typeTags = match($type) {
            'progress' => ['progreso', 'avances', 'instalación'],
            'milestone' => ['hito', 'logro', 'completado'],
            'financial' => ['finanzas', 'rentabilidad', 'costes'],
            'technical' => ['técnico', 'tecnología', 'optimización'],
            'announcement' => ['anuncio', 'comunicado', 'noticias'],
            'issue' => ['incidencia', 'problema', 'resolución'],
            default => []
        };
        
        return array_merge($baseTags, $typeTags);
    }

    private function generateNextSteps(string $type): array
    {
        return match($type) {
            'progress' => [
                "Continuar con la instalación de equipos restantes",
                "Realizar pruebas de funcionamiento",
                "Preparar documentación técnica"
            ],
            'milestone' => [
                "Iniciar la siguiente fase del proyecto",
                "Documentar lecciones aprendidas",
                "Comunicar el éxito a todos los stakeholders"
            ],
            'financial' => [
                "Revisar proyecciones financieras",
                "Optimizar estructura de costes",
                "Evaluar oportunidades de financiación adicional"
            ],
            'technical' => [
                "Implementar mejoras identificadas",
                "Realizar pruebas adicionales",
                "Documentar cambios técnicos"
            ],
            'announcement' => [
                "Comunicar cambios a todos los stakeholders",
                "Actualizar documentación del proyecto",
                "Coordinar con socios estratégicos"
            ],
            'issue' => [
                "Resolver la incidencia detectada",
                "Implementar medidas preventivas",
                "Actualizar procedimientos de trabajo"
            ],
            default => [
                "Continuar con las actividades planificadas",
                "Monitorear progreso del proyecto"
            ]
        };
    }

    private function showStatistics(): void
    {
        $total = ProjectUpdate::count();
        $published = ProjectUpdate::where('published_at', '<=', now())->count();
        $milestones = ProjectUpdate::where('update_type', 'milestone')->count();
        
        $byType = ProjectUpdate::selectRaw('update_type, COUNT(*) as count')
            ->groupBy('update_type')
            ->pluck('count', 'update_type');
        
        $byUrgency = ProjectUpdate::selectRaw('is_urgent, COUNT(*) as count')
            ->groupBy('is_urgent')
            ->pluck('count', 'is_urgent');

        $avgProgress = ProjectUpdate::whereNotNull('progress_percentage')->avg('progress_percentage');

        $this->command->info("\n📊 Estadísticas de actualizaciones:");
        $this->command->info("   • Total de actualizaciones: {$total}");
        $this->command->info("   • Actualizaciones publicadas: {$published}");
        $this->command->info("   • Hitos alcanzados: {$milestones}");
        $this->command->info("   • Progreso promedio: " . round($avgProgress, 1) . "%");

        $this->command->info("\n📝 Por tipo:");
        foreach ($byType as $type => $count) {
            $typeLabel = match($type) {
                'general' => 'General',
                'milestone' => 'Hito',
                'financial' => 'Financiero',
                'technical' => 'Técnico',
                'success' => 'Éxito',
                'issue' => 'Incidencia',
                default => ucfirst($type)
            };
            $this->command->info("   • {$typeLabel}: {$count}");
        }

        $this->command->info("\n🎯 Por urgencia:");
        foreach ($byUrgency as $urgent => $count) {
            $urgencyLabel = $urgent ? 'Urgente' : 'Normal';
            $this->command->info("   • {$urgencyLabel}: {$count}");
        }
    }
}
