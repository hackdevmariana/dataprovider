<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\CooperativePost;
use App\Models\Cooperative;
use App\Models\User;
use Carbon\Carbon;

class CooperativePostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Limpiar posts existentes
        CooperativePost::truncate();

        $cooperatives = Cooperative::all();
        $users = User::all();

        if ($cooperatives->isEmpty() || $users->isEmpty()) {
            $this->command->warn('No hay cooperativas o usuarios disponibles para crear posts.');
            return;
        }

        $postTypes = ['announcement', 'news', 'event', 'discussion', 'update'];
        $statuses = ['draft', 'published', 'archived'];
        $visibilities = ['public', 'members_only', 'board_only'];

        // Crear posts para cada cooperativa
        foreach ($cooperatives as $cooperative) {
            $this->createPostsForCooperative($cooperative, $users, $postTypes, $statuses, $visibilities);
        }

        $this->command->info('✅ Se han creado ' . CooperativePost::count() . ' posts de cooperativas.');
    }

    private function createPostsForCooperative($cooperative, $users, $postTypes, $statuses, $visibilities): void
    {
        // Crear entre 3-8 posts por cooperativa
        $postsCount = rand(3, 8);
        
        for ($i = 0; $i < $postsCount; $i++) {
            $postType = $postTypes[array_rand($postTypes)];
            $status = $statuses[array_rand($statuses)];
            $visibility = $visibilities[array_rand($visibilities)];
            $author = $users->random();
            
            $isPublished = $status === 'published';
            $publishedAt = $isPublished ? $this->getRandomPublishedDate() : null;
            
            $isPinned = rand(1, 10) <= 2; // 20% de probabilidad de estar fijado
            $pinnedUntil = $isPinned ? $this->getRandomPinnedUntilDate() : null;
            
            $isFeatured = rand(1, 10) <= 3; // 30% de probabilidad de estar destacado
            
            CooperativePost::create([
                'cooperative_id' => $cooperative->id,
                'author_id' => $author->id,
                'title' => $this->getPostTitle($postType, $cooperative->name),
                'content' => $this->getPostContent($postType, $cooperative->name),
                'post_type' => $postType,
                'status' => $status,
                'visibility' => $visibility,
                'attachments' => $this->getRandomAttachments($postType),
                'metadata' => $this->getRandomMetadata($postType),
                'comments_enabled' => rand(1, 10) <= 8, // 80% de probabilidad de permitir comentarios
                'is_pinned' => $isPinned,
                'is_featured' => $isFeatured,
                'views_count' => $isPublished ? rand(10, 500) : 0,
                'likes_count' => $isPublished ? rand(0, 50) : 0,
                'comments_count' => $isPublished ? rand(0, 20) : 0,
                'published_at' => $publishedAt,
                'pinned_until' => $pinnedUntil,
            ]);
        }
    }

    private function getPostTitle(string $postType, string $cooperativeName): string
    {
        $titles = [
            'announcement' => [
                'Nueva Política de Membresía en ' . $cooperativeName,
                'Cambios Importantes en la Estructura de ' . $cooperativeName,
                'Actualización de Normativas de ' . $cooperativeName,
                'Nuevo Sistema de Gestión en ' . $cooperativeName,
                'Reunión Extraordinaria de ' . $cooperativeName,
            ],
            'news' => [
                'Éxito en Proyecto de Energía Renovable de ' . $cooperativeName,
                'Nueva Colaboración Estratégica de ' . $cooperativeName,
                'Premio de Sostenibilidad para ' . $cooperativeName,
                'Expansión de Servicios en ' . $cooperativeName,
                'Resultados Financieros Positivos de ' . $cooperativeName,
            ],
            'event' => [
                'Jornada de Puertas Abiertas en ' . $cooperativeName,
                'Taller de Eficiencia Energética de ' . $cooperativeName,
                'Asamblea General Anual de ' . $cooperativeName,
                'Seminario de Tecnologías Verdes de ' . $cooperativeName,
                'Networking de Miembros de ' . $cooperativeName,
            ],
            'discussion' => [
                'Propuestas para Mejorar ' . $cooperativeName,
                'Debate sobre Futuras Inversiones de ' . $cooperativeName,
                'Opiniones sobre Nuevos Servicios de ' . $cooperativeName,
                'Discusión sobre Estrategia de ' . $cooperativeName,
                'Reflexiones sobre el Futuro de ' . $cooperativeName,
            ],
            'update' => [
                'Progreso del Proyecto Solar de ' . $cooperativeName,
                'Actualización de Infraestructura de ' . $cooperativeName,
                'Estado de las Inversiones de ' . $cooperativeName,
                'Mejoras en Servicios de ' . $cooperativeName,
                'Reporte Mensual de ' . $cooperativeName,
            ],
        ];

        return $titles[$postType][array_rand($titles[$postType])];
    }

    private function getPostContent(string $postType, string $cooperativeName): string
    {
        $contents = [
            'announcement' => [
                "Estimados miembros de {$cooperativeName},\n\n" .
                "Nos complace informarles sobre importantes cambios que afectarán positivamente a nuestra organización. " .
                "Estos ajustes están diseñados para mejorar la eficiencia operativa y fortalecer nuestra posición en el mercado.\n\n" .
                "Les invitamos a revisar la documentación adjunta y contactarnos si tienen alguna pregunta.\n\n" .
                "Saludos cordiales,\n" .
                "Equipo de Gestión de {$cooperativeName}",

                "Atención miembros de {$cooperativeName}:\n\n" .
                "Hemos implementado nuevas políticas que requieren su atención inmediata. " .
                "Estas modificaciones buscan optimizar nuestros procesos y garantizar el cumplimiento normativo.\n\n" .
                "Por favor, revisen la información detallada en los archivos adjuntos.\n\n" .
                "Gracias por su comprensión,\n" .
                "Administración de {$cooperativeName}",
            ],
            'news' => [
                "¡Excelentes noticias para {$cooperativeName}!\n\n" .
                "Hemos logrado un hito importante en nuestro proyecto de energía renovable. " .
                "Este éxito representa un paso significativo hacia nuestros objetivos de sostenibilidad.\n\n" .
                "Los resultados superan nuestras expectativas y nos posicionan como líderes en el sector.\n\n" .
                "¡Gracias a todos los que han contribuido a este logro!\n\n" .
                "Equipo de {$cooperativeName}",

                "{$cooperativeName} celebra un nuevo logro:\n\n" .
                "Hemos establecido una colaboración estratégica que ampliará significativamente nuestros servicios. " .
                "Esta alianza nos permitirá ofrecer soluciones más integrales a nuestros miembros.\n\n" .
                "Manténganse atentos a futuras actualizaciones sobre esta emocionante iniciativa.\n\n" .
                "Saludos,\n" .
                "Dirección de {$cooperativeName}",
            ],
            'event' => [
                "¡Invitación especial para miembros de {$cooperativeName}!\n\n" .
                "Les invitamos a participar en nuestro próximo evento que promete ser informativo y enriquecedor. " .
                "Será una excelente oportunidad para networking y aprendizaje.\n\n" .
                "Fecha: " . now()->addDays(rand(7, 30))->format('d/m/Y') . "\n" .
                "Lugar: Sede principal de {$cooperativeName}\n" .
                "Hora: " . rand(9, 18) . ":00\n\n" .
                "¡Esperamos contar con su presencia!\n\n" .
                "Organizadores del evento",

                "Evento especial en {$cooperativeName}:\n\n" .
                "Prepárense para una jornada llena de actividades y aprendizaje. " .
                "Este evento está diseñado para fortalecer la comunidad y compartir conocimientos.\n\n" .
                "Programa detallado disponible en los archivos adjuntos.\n\n" .
                "¡No se lo pierdan!\n\n" .
                "Equipo de Eventos de {$cooperativeName}",
            ],
            'discussion' => [
                "Hola miembros de {$cooperativeName},\n\n" .
                "Queremos abrir un espacio de discusión sobre temas importantes que afectan a nuestra organización. " .
                "Sus opiniones y sugerencias son valiosas para nuestro crecimiento.\n\n" .
                "Por favor, compartan sus pensamientos en los comentarios. " .
                "Todas las ideas serán consideradas seriamente.\n\n" .
                "¡Participen activamente en esta conversación!\n\n" .
                "Equipo de Comunicación de {$cooperativeName}",

                "Discusión abierta en {$cooperativeName}:\n\n" .
                "Hemos identificado áreas de oportunidad que requieren la participación de todos. " .
                "Esta discusión nos ayudará a tomar decisiones más informadas.\n\n" .
                "Sus perspectivas son fundamentales para nuestro desarrollo futuro.\n\n" .
                "¡Esperamos sus valiosas contribuciones!\n\n" .
                "Moderadores de {$cooperativeName}",
            ],
            'update' => [
                "Actualización de progreso en {$cooperativeName}:\n\n" .
                "Queremos mantenerlos informados sobre el estado actual de nuestros proyectos. " .
                "Hemos logrado avances significativos en varias áreas clave.\n\n" .
                "Detalles técnicos y métricas disponibles en la documentación adjunta.\n\n" .
                "¡Gracias por su continuo apoyo!\n\n" .
                "Equipo de Proyectos de {$cooperativeName}",

                "Reporte de estado de {$cooperativeName}:\n\n" .
                "Presentamos un resumen completo de nuestras actividades recientes. " .
                "Los resultados muestran un progreso constante hacia nuestros objetivos.\n\n" .
                "Incluimos gráficos y análisis detallados para su revisión.\n\n" .
                "¡Manténganse informados sobre nuestro progreso!\n\n" .
                "Departamento de Información de {$cooperativeName}",
            ],
        ];

        return $contents[$postType][array_rand($contents[$postType])];
    }

    private function getRandomAttachments(string $postType): ?array
    {
        if (rand(1, 10) <= 3) { // 30% de probabilidad de tener adjuntos
            $attachments = [];
            $attachmentTypes = ['pdf', 'doc', 'xlsx', 'jpg', 'png'];
            
            $count = rand(1, 3);
            for ($i = 0; $i < $count; $i++) {
                $type = $attachmentTypes[array_rand($attachmentTypes)];
                $attachments[] = [
                    'name' => $this->getAttachmentName($postType, $type),
                    'type' => $type,
                    'size' => rand(100, 5000) . 'KB',
                    'url' => '/storage/attachments/' . uniqid() . '.' . $type,
                ];
            }
            
            return $attachments;
        }
        
        return null;
    }

    private function getAttachmentName(string $postType, string $type): string
    {
        $names = [
            'announcement' => ['Política_Actualizada', 'Normativas_Nuevas', 'Cambios_Organizacionales'],
            'news' => ['Reporte_Completo', 'Documentación_Proyecto', 'Análisis_Resultados'],
            'event' => ['Programa_Evento', 'Agenda_Detallada', 'Materiales_Presentación'],
            'discussion' => ['Propuesta_Discusión', 'Documento_Base', 'Análisis_Preliminar'],
            'update' => ['Reporte_Progreso', 'Métricas_Actuales', 'Análisis_Estado'],
        ];

        $baseName = $names[$postType][array_rand($names[$postType])];
        return $baseName . '.' . $type;
    }

    private function getRandomMetadata(string $postType): ?array
    {
        if (rand(1, 10) <= 4) { // 40% de probabilidad de tener metadatos
            $metadata = [
                'category' => $this->getRandomCategory($postType),
                'tags' => $this->getRandomTags($postType),
                'priority' => $this->getRandomPriority(),
                'target_audience' => $this->getRandomTargetAudience(),
                'estimated_read_time' => rand(2, 8) . ' min',
            ];

            if ($postType === 'event') {
                $metadata['event_date'] = now()->addDays(rand(7, 60))->toISOString();
                $metadata['event_location'] = 'Sede principal';
                $metadata['max_participants'] = rand(20, 100);
            }

            if ($postType === 'update') {
                $metadata['progress_percentage'] = rand(25, 95);
                $metadata['milestone'] = $this->getRandomMilestone();
                $metadata['next_deadline'] = now()->addDays(rand(7, 30))->toISOString();
            }

            return $metadata;
        }
        
        return null;
    }

    private function getRandomCategory(string $postType): string
    {
        $categories = [
            'announcement' => ['organizational', 'policy', 'structural', 'operational'],
            'news' => ['achievement', 'collaboration', 'recognition', 'expansion'],
            'event' => ['workshop', 'meeting', 'seminar', 'networking'],
            'discussion' => ['strategy', 'improvement', 'investment', 'future'],
            'update' => ['progress', 'infrastructure', 'investment', 'service'],
        ];

        return $categories[$postType][array_rand($categories[$postType])];
    }

    private function getRandomTags(string $postType): array
    {
        $tagSets = [
            'announcement' => ['importante', 'cambio', 'política', 'organización', 'normativa'],
            'news' => ['éxito', 'colaboración', 'premio', 'expansión', 'resultado'],
            'event' => ['evento', 'taller', 'reunión', 'networking', 'aprendizaje'],
            'discussion' => ['debate', 'propuesta', 'opinión', 'estrategia', 'futuro'],
            'update' => ['progreso', 'estado', 'reporte', 'métrica', 'avance'],
        ];

        $tags = $tagSets[$postType];
        $selectedTags = array_rand($tags, min(rand(2, 4), count($tags)));
        
        if (!is_array($selectedTags)) {
            $selectedTags = [$selectedTags];
        }
        
        return array_map(fn($index) => $tags[$index], $selectedTags);
    }

    private function getRandomPriority(): string
    {
        $priorities = ['low', 'medium', 'high'];
        return $priorities[array_rand($priorities)];
    }

    private function getRandomTargetAudience(): string
    {
        $audiences = ['all_members', 'board_members', 'active_members', 'new_members'];
        return $audiences[array_rand($audiences)];
    }

    private function getRandomMilestone(): string
    {
        $milestones = [
            'Fase 1 Completada',
            'Infraestructura Básica',
            'Pruebas de Sistema',
            'Implementación Parcial',
            'Validación de Usuarios',
            'Optimización de Procesos',
        ];
        
        return $milestones[array_rand($milestones)];
    }

    private function getRandomPublishedDate(): Carbon
    {
        // Posts publicados en los últimos 6 meses
        return now()->subDays(rand(1, 180));
    }

    private function getRandomPinnedUntilDate(): Carbon
    {
        // Fijado hasta máximo 30 días en el futuro
        return now()->addDays(rand(1, 30));
    }
}
