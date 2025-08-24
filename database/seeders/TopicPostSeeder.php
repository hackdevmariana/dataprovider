<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\TopicPost;
use App\Models\Topic;
use App\Models\User;
use Illuminate\Support\Str;
use Carbon\Carbon;

class TopicPostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $topics = Topic::all();
        $users = User::all();
        
        if ($topics->isEmpty() || $users->isEmpty()) {
            $this->command->warn('No hay temas o usuarios disponibles. Ejecuta TopicSeeder y UserSeeder primero.');
            return;
        }

        $posts = [];

        // Tipos de posts disponibles
        $postTypes = [
            'discussion' => [
                'name' => 'Discusión',
                'description' => 'Discusiones generales sobre el tema',
                'weight' => 0.4,
            ],
            'question' => [
                'name' => 'Pregunta',
                'description' => 'Preguntas que buscan respuesta',
                'weight' => 0.2,
            ],
            'tutorial' => [
                'name' => 'Tutorial',
                'description' => 'Tutoriales y guías paso a paso',
                'weight' => 0.15,
            ],
            'news' => [
                'name' => 'Noticia',
                'description' => 'Noticias y actualizaciones',
                'weight' => 0.1,
            ],
            'showcase' => [
                'name' => 'Proyecto',
                'description' => 'Mostrar proyectos e instalaciones',
                'weight' => 0.1,
            ],
            'help' => [
                'name' => 'Ayuda',
                'description' => 'Solicitudes de ayuda',
                'weight' => 0.05,
            ],
        ];

        // Crear posts para cada tema
        foreach ($topics as $topic) {
            // Número de posts por tema (entre 3 y 8)
            $postsPerTopic = rand(3, 8);
            
            for ($i = 0; $i < $postsPerTopic; $i++) {
                $postType = $this->getRandomPostType($postTypes);
                $author = $users->random();
                $createdAt = Carbon::now()->subDays(rand(1, 90));
                
                $posts[] = [
                    'topic_id' => $topic->id,
                    'user_id' => $author->id,
                    'title' => $this->getPostTitle($postType, $topic->name),
                    'slug' => Str::slug($this->getPostTitle($postType, $topic->name)),
                    'body' => $this->getPostBody($postType, $topic->name),
                    'excerpt' => $this->getPostExcerpt($postType, $topic->name),
                    'summary' => $this->getPostSummary($postType, $topic->name),
                    'post_type' => $postType,
                    'is_pinned' => $i === 0, // El primer post de cada tema está fijado
                    'is_locked' => false,
                    'is_featured' => rand(0, 10) === 0, // 10% de probabilidad
                    'is_announcement' => $postType === 'news' && rand(0, 3) === 0,
                    'is_nsfw' => false,
                    'is_spoiler' => false,
                    'requires_approval' => false,
                    'allow_comments' => true,
                    'notify_replies' => true,
                    'images' => $this->getPostImages($postType),
                    'videos' => $this->getPostVideos($postType),
                    'attachments' => $this->getPostAttachments($postType),
                    'links' => $this->getPostLinks($postType),
                    'thumbnail_url' => $this->getThumbnailUrl($postType),
                    'views_count' => rand(10, 1000),
                    'unique_views_count' => rand(8, 800),
                    'upvotes_count' => rand(0, 50),
                    'downvotes_count' => rand(0, 10),
                    'score' => rand(-5, 45),
                    'comments_count' => rand(0, 25),
                    'shares_count' => rand(0, 15),
                    'bookmarks_count' => rand(0, 20),
                    'likes_count' => rand(0, 40),
                    'reports_count' => rand(0, 3),
                    'quality_score' => rand(70, 100),
                    'helpfulness_score' => rand(0, 100),
                    'engagement_rate' => rand(0, 25),
                    'read_time_seconds' => rand(60, 1800),
                    'completion_rate' => rand(20, 95),
                    'trending_score' => rand(0, 1000),
                    'hot_score' => rand(0, 500),
                    'relevance_score' => rand(80, 100),
                    'controversy_score' => rand(0, 30),
                    'trending_until' => rand(0, 5) === 0 ? Carbon::now()->addDays(rand(1, 7)) : null,
                    'status' => $this->getRandomStatus(),
                    'created_at' => $createdAt,
                    'updated_at' => $createdAt->copy()->addDays(rand(0, 30)),
                ];
            }
        }

        // Insertar todos los posts
        foreach ($posts as $post) {
            TopicPost::create($post);
        }

        $this->command->info('✅ Se han creado ' . count($posts) . ' posts de temas.');
        $this->command->info('📊 Distribución por tipo:');
        foreach ($postTypes as $type => $info) {
            $count = count(array_filter($posts, fn($p) => $p['post_type'] === $type));
            $this->command->info("   - {$info['name']}: {$count}");
        }
        $this->command->info('🏷️ Tipos: Discusión, Pregunta, Tutorial, Noticia, Proyecto, Ayuda');
        $this->command->info('📋 Estados: Publicado, Borrador, Pendiente, Aprobado, Rechazado, Oculto, Eliminado, Archivado, Spam');
        $this->command->info('📊 Métricas: Views, Upvotes, Comments, Engagement, Quality Score');
    }

    /**
     * Obtener tipo de post aleatorio con distribución ponderada
     */
    private function getRandomPostType(array $postTypes): string
    {
        $rand = mt_rand() / mt_getrandmax();
        $cumulative = 0;
        
        foreach ($postTypes as $type => $info) {
            $cumulative += $info['weight'];
            if ($rand <= $cumulative) {
                return $type;
            }
        }
        
        return 'discussion'; // Fallback
    }

    /**
     * Obtener título del post según tipo y tema
     */
    private function getPostTitle(string $type, string $topicName): string
    {
        $titles = match ($type) {
            'discussion' => [
                "Discusión sobre {$topicName}",
                "¿Qué opinas de {$topicName}?",
                "Conversación sobre {$topicName}",
                "Debate: {$topicName}",
                "Compartiendo experiencias en {$topicName}",
            ],
            'question' => [
                "¿Cómo implementar {$topicName}?",
                "Problema con {$topicName}",
                "¿Alguien sabe sobre {$topicName}?",
                "Duda sobre {$topicName}",
                "¿Mejor práctica para {$topicName}?",
            ],
            'tutorial' => [
                "Guía completa de {$topicName}",
                "Tutorial paso a paso: {$topicName}",
                "Cómo hacer {$topicName} desde cero",
                "Manual de {$topicName}",
                "Aprende {$topicName} fácilmente",
            ],
            'news' => [
                "Nueva actualización en {$topicName}",
                "Noticias importantes sobre {$topicName}",
                "Cambios en {$topicName}",
                "Anuncio: {$topicName}",
                "Últimas novedades de {$topicName}",
            ],
            'showcase' => [
                "Mi proyecto de {$topicName}",
                "Instalación de {$topicName} completada",
                "Resultado de mi trabajo en {$topicName}",
                "Proyecto exitoso: {$topicName}",
                "Compartiendo mi {$topicName}",
            ],
            'help' => [
                "Necesito ayuda con {$topicName}",
                "SOS: Problema en {$topicName}",
                "¿Alguien puede ayudarme con {$topicName}?",
                "Urgente: {$topicName}",
                "Ayuda para {$topicName}",
            ],
            default => "Post sobre {$topicName}",
        };

        return $titles[array_rand($titles)];
    }

    /**
     * Obtener cuerpo del post según tipo y tema
     */
    private function getPostBody(string $type, string $topicName): string
    {
        $bodies = match ($type) {
            'discussion' => [
                "Hola a todos! Me gustaría abrir una discusión sobre {$topicName}. 

He estado investigando este tema y me parece muy interesante. ¿Qué opinan ustedes? 

¿Cuáles son sus experiencias con {$topicName}? ¿Qué ventajas y desventajas ven? 

Me encantaría escuchar diferentes perspectivas y aprender de la comunidad.",
                
                "Buenos días comunidad! 

Quiero iniciar una conversación sobre {$topicName}. Es un tema que me apasiona y creo que puede ser muy útil para todos.

¿Alguien más está trabajando en esto? ¿Qué desafíos han encontrado? 

Compartamos conocimientos y experiencias para crecer juntos.",
            ],
            'question' => [
                "Hola! Tengo una pregunta sobre {$topicName}:

He estado intentando implementar esto pero me encuentro con algunos problemas. Específicamente:

1. ¿Cuál es la mejor manera de empezar?
2. ¿Qué herramientas recomiendan?
3. ¿Hay algún tutorial o guía que puedan sugerir?

Cualquier ayuda será muy apreciada. Gracias!",
                
                "Saludos! Necesito ayuda con {$topicName}:

Estoy en un proyecto donde necesito usar {$topicName} pero no estoy seguro de cómo proceder.

¿Alguien puede explicarme los conceptos básicos? ¿O conocen algún recurso donde pueda aprender más?

Muchas gracias por su tiempo.",
            ],
            'tutorial' => [
                "# Guía Completa: {$topicName}

## Introducción
En este tutorial te enseñaré todo lo que necesitas saber sobre {$topicName}.

## Requisitos Previos
- Conocimientos básicos del tema
- Herramientas necesarias
- Tiempo para practicar

## Paso 1: Preparación
Primero, necesitamos preparar nuestro entorno de trabajo...

## Paso 2: Implementación
Ahora vamos a implementar {$topicName} paso a paso...

## Conclusión
Con estos pasos ya tienes una base sólida en {$topicName}.

¿Te gustó este tutorial? ¡Déjame un comentario!",
                
                "# Tutorial: {$topicName} desde Cero

## ¿Qué es {$topicName}?
{$topicName} es una tecnología/concepto que permite...

## ¿Por qué es importante?
- Beneficio 1
- Beneficio 2
- Beneficio 3

## Implementación Práctica
Vamos a crear un ejemplo paso a paso...

## Consejos y Trucos
- Consejo 1
- Consejo 2
- Consejo 3

Espero que este tutorial te sea útil!",
            ],
            'news' => [
                "**NOTICIA IMPORTANTE**

Se ha anunciado una nueva actualización en {$topicName} que trae mejoras significativas:

## Nuevas Características
- Característica 1
- Característica 2
- Característica 3

## Cambios Técnicos
- Mejora en rendimiento
- Nuevas APIs disponibles
- Corrección de bugs

## ¿Cuándo estará disponible?
La actualización estará disponible a partir del próximo mes.

¡Mantente atento a más noticias!",
                
                "**ACTUALIZACIÓN: {$topicName}**

Hemos recibido información sobre importantes cambios en {$topicName}:

## Resumen de Cambios
- Cambio 1
- Cambio 2
- Cambio 3

## Impacto en Usuarios
Estos cambios afectarán principalmente a...

## Recomendaciones
Te recomendamos que...

Para más detalles, consulta la documentación oficial.",
            ],
            'showcase' => [
                "¡Hola comunidad! 

Quiero compartir con ustedes mi proyecto de {$topicName} que acabo de completar.

## Descripción del Proyecto
Este proyecto consistió en...

## Tecnologías Utilizadas
- Tecnología 1
- Tecnología 2
- Tecnología 3

## Resultados
Los resultados han sido excelentes:
- Resultado 1
- Resultado 2
- Resultado 3

## Aprendizajes
Durante este proyecto aprendí...

¿Les gustaría que comparta más detalles?",
                
                "**PROYECTO COMPLETADO: {$topicName}**

Después de meses de trabajo, finalmente he terminado mi proyecto de {$topicName}.

## ¿Qué es?
Es un sistema que permite...

## Características Principales
- Característica 1
- Característica 2
- Característica 3

## Desafíos Superados
- Desafío 1
- Desafío 2

## Resultados
Los resultados superaron mis expectativas.

¡Gracias a todos por su apoyo!",
            ],
            'help' => [
                "**URGENTE: Necesito ayuda con {$topicName}**

Hola a todos, estoy en una situación complicada con {$topicName} y necesito ayuda urgente.

## El Problema
He estado trabajando en {$topicName} pero me encuentro con...

## Lo que he intentado
- Solución 1 (no funcionó)
- Solución 2 (no funcionó)
- Solución 3 (no funcionó)

## Mi Pregunta
¿Alguien puede ayudarme a resolver esto? 

Cualquier sugerencia será muy bienvenida. ¡Gracias!",
                
                "**SOS: Problema en {$topicName}**

Comunidad, necesito su ayuda con un problema en {$topicName}.

## Descripción del Problema
Estoy implementando {$topicName} y me encuentro con...

## Error Específico
El error que recibo es...

## Contexto
Esto está sucediendo cuando...

## ¿Alguien puede ayudarme?
Necesito resolver esto lo antes posible. 

¡Cualquier ayuda será muy apreciada!",
            ],
            default => "Post sobre {$topicName}",
        };

        return $bodies[array_rand($bodies)];
    }

    /**
     * Obtener extracto del post
     */
    private function getPostExcerpt(string $type, string $topicName): string
    {
        return match ($type) {
            'discussion' => "Discusión abierta sobre {$topicName} - ¡Únete a la conversación!",
            'question' => "Pregunta sobre {$topicName} - ¿Puedes ayudar?",
            'tutorial' => "Tutorial completo de {$topicName} - Aprende paso a paso",
            'news' => "Noticias y actualizaciones sobre {$topicName}",
            'showcase' => "Proyecto completado de {$topicName} - ¡Mira los resultados!",
            'help' => "Solicitud de ayuda con {$topicName} - ¡Tu apoyo es necesario!",
            default => "Post sobre {$topicName}",
        };
    }

    /**
     * Obtener resumen del post
     */
    private function getPostSummary(string $type, string $topicName): string
    {
        return match ($type) {
            'discussion' => "Discusión sobre {$topicName}",
            'question' => "Pregunta: {$topicName}",
            'tutorial' => "Tutorial: {$topicName}",
            'news' => "Noticia: {$topicName}",
            'showcase' => "Proyecto: {$topicName}",
            'help' => "Ayuda: {$topicName}",
            default => "Post: {$topicName}",
        };
    }

    /**
     * Obtener imágenes del post
     */
    private function getPostImages(string $type): ?array
    {
        if (rand(0, 3) === 0) { // 25% de probabilidad
            return [
                'https://example.com/images/post1.jpg',
                'https://example.com/images/post2.jpg',
            ];
        }
        return null;
    }

    /**
     * Obtener videos del post
     */
    private function getPostVideos(string $type): ?array
    {
        if ($type === 'tutorial' && rand(0, 2) === 0) { // 33% para tutoriales
            return [
                'https://example.com/videos/tutorial1.mp4',
            ];
        }
        return null;
    }

    /**
     * Obtener archivos adjuntos
     */
    private function getPostAttachments(string $type): ?array
    {
        if (rand(0, 4) === 0) { // 20% de probabilidad
            return [
                'https://example.com/documents/guide.pdf',
                'https://example.com/documents/specs.docx',
            ];
        }
        return null;
    }

    /**
     * Obtener enlaces externos
     */
    private function getPostLinks(string $type): ?array
    {
        if (rand(0, 2) === 0) { // 33% de probabilidad
            return [
                'https://example.com/resource1',
                'https://example.com/resource2',
            ];
        }
        return null;
    }

    /**
     * Obtener URL de miniatura
     */
    private function getThumbnailUrl(string $type): ?string
    {
        if (rand(0, 2) === 0) { // 33% de probabilidad
            return 'https://example.com/thumbnails/post.jpg';
        }
        return null;
    }

    /**
     * Obtener estado aleatorio con distribución realista
     */
    private function getRandomStatus(): string
    {
        $statuses = ['published', 'published', 'published', 'published', 'published', 'draft', 'pending', 'approved'];
        return $statuses[array_rand($statuses)];
    }
}
