<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\TopicComment;
use App\Models\TopicPost;
use App\Models\User;
use Illuminate\Support\Str;
use Carbon\Carbon;

class TopicCommentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $topicPosts = TopicPost::all();
        $users = User::all();
        
        if ($topicPosts->isEmpty() || $users->isEmpty()) {
            $this->command->warn('No hay topic posts o usuarios disponibles. Ejecuta TopicPostSeeder y UserSeeder primero.');
            return;
        }

        $comments = [];
        $commentId = 1;

        // Tipos de comentarios disponibles
        $commentTypes = [
            'general' => [
                'name' => 'Comentario General',
                'description' => 'Comentarios generales sobre el post',
                'weight' => 0.4,
            ],
            'question' => [
                'name' => 'Pregunta',
                'description' => 'Preguntas sobre el contenido del post',
                'weight' => 0.2,
            ],
            'answer' => [
                'name' => 'Respuesta',
                'description' => 'Respuestas a preguntas o comentarios',
                'weight' => 0.2,
            ],
            'suggestion' => [
                'name' => 'Sugerencia',
                'description' => 'Sugerencias y recomendaciones',
                'weight' => 0.1,
            ],
            'feedback' => [
                'name' => 'Feedback',
                'description' => 'Comentarios de feedback y valoración',
                'weight' => 0.1,
            ],
        ];

        // Crear comentarios para cada topic post
        foreach ($topicPosts as $topicPost) {
            // Número de comentarios por post (entre 2 y 8)
            $commentsPerPost = rand(2, 8);
            
            for ($i = 0; $i < $commentsPerPost; $i++) {
                $commentType = $this->getRandomCommentType($commentTypes);
                $author = $users->random();
                $createdAt = Carbon::now()->subDays(rand(1, 30));
                
                // Determinar si es un comentario padre o hijo
                $isParent = $i < 3 || rand(0, 3) === 0; // 75% de comentarios padres
                $parentId = null;
                
                if (!$isParent && !empty($comments)) {
                    // Buscar un comentario padre existente del mismo post
                    $parentComments = array_filter($comments, function($comment) use ($topicPost) {
                        return $comment['topic_post_id'] === $topicPost->id && is_null($comment['parent_id']);
                    });
                    
                    if (!empty($parentComments)) {
                        $parentComment = array_values($parentComments)[array_rand(array_values($parentComments))];
                        $parentId = $parentComment['id'];
                    }
                }
                
                $comment = [
                    'id' => $commentId++,
                    'topic_post_id' => $topicPost->id,
                    'user_id' => $author->id,
                    'parent_id' => $parentId,
                    'body' => $this->getCommentContent($commentType, $topicPost->title),
                    'excerpt' => Str::limit($this->getCommentContent($commentType, $topicPost->title), 150),
                    'depth' => $parentId ? 1 : 0,
                    'thread_path' => $parentId ? '1/' . $commentId : '1',
                    'sort_order' => $i,
                    'children_count' => 0,
                    'descendants_count' => 0,
                    'comment_type' => $this->mapCommentType($commentType),
                    'is_best_answer' => $commentType === 'answer' && rand(0, 10) === 0,
                    'is_author_reply' => false,
                    'is_moderator_reply' => false,
                    'is_pinned' => $i === 0 && rand(0, 5) === 0,
                    'is_highlighted' => false,
                    'is_edited' => false,
                    'is_deleted' => false,
                    'upvotes_count' => rand(0, 15),
                    'downvotes_count' => rand(0, 5),
                    'score' => 0,
                    'replies_count' => 0,
                    'likes_count' => rand(0, 10),
                    'reports_count' => 0,
                    'helpful_votes' => rand(0, 8),
                    'not_helpful_votes' => rand(0, 3),
                    'quality_score' => rand(80, 100),
                    'helpfulness_score' => rand(0, 100),
                    'relevance_score' => rand(80, 100),
                    'read_time_seconds' => rand(15, 120),
                    'engagement_rate' => rand(0, 100) / 100,
                    'images' => json_encode($this->getCommentImages($commentType)),
                    'attachments' => json_encode($this->getCommentAttachments($commentType)),
                    'links' => json_encode($this->getCommentLinks($commentType)),
                    'code_snippets' => null,
                    'status' => 'published',
                    'moderation_flags' => null,
                    'moderation_notes' => null,
                    'moderated_by' => null,
                    'moderated_at' => null,
                    'last_edited_at' => null,
                    'last_edited_by' => null,
                    'edit_reason' => null,
                    'edit_count' => 0,
                    'edit_history' => null,
                    'mentioned_users' => null,
                    'tags' => null,
                    'language' => 'es',
                    'quote_text' => null,
                    'quoted_comment_id' => null,
                    'context_data' => null,
                    'notify_parent_author' => true,
                    'notify_post_author' => true,
                    'notify_followers' => true,
                    'last_activity_at' => $createdAt,
                    'views_count' => rand(5, 50),
                    'unique_views_count' => rand(3, 30),
                    'ranking_score' => 0,
                    'controversy_score' => rand(0, 50),
                    'hot_until' => null,
                    'source' => 'web',
                    'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                    'creation_metadata' => null,
                    'author_reputation_at_time' => rand(50, 100),
                    'root_comment_id' => $parentId ? $parentId : null,
                    'thread_participants' => null,
                    'breaks_thread' => false,
                    'thread_last_activity' => $createdAt,
                    'collapsed_by_default' => false,
                    'show_score' => true,
                    'allow_replies' => true,
                    'max_reply_depth' => null,
                    'created_at' => $createdAt,
                    'updated_at' => $createdAt,
                ];
                
                $comments[] = $comment;
            }
        }

        // Insertar comentarios en lotes
        foreach (array_chunk($comments, 100) as $chunk) {
            TopicComment::insert($chunk);
        }

        // Actualizar el contador de respuestas para comentarios padres
        $this->updateRepliesCount($comments);

        $this->command->info('TopicCommentSeeder completado. Se crearon ' . count($comments) . ' comentarios.');
    }

    /**
     * Obtiene un tipo de comentario aleatorio basado en pesos
     */
    private function getRandomCommentType(array $types): string
    {
        $rand = mt_rand(1, 100);
        $cumulative = 0;
        
        foreach ($types as $type => $config) {
            $cumulative += $config['weight'] * 100;
            if ($rand <= $cumulative) {
                return $type;
            }
        }
        
        return array_key_first($types);
    }

    /**
     * Genera contenido de comentario basado en el tipo
     */
    private function getCommentContent(string $type, string $postTitle): string
    {
        $contents = [
            'general' => [
                'Excelente post sobre ' . $postTitle . '. Muy informativo y bien estructurado.',
                'Me ha gustado mucho este contenido sobre ' . $postTitle . '. Gracias por compartirlo.',
                'Interesante perspectiva sobre ' . $postTitle . '. Hay varios puntos que me han llamado la atención.',
                'Buena información sobre ' . $postTitle . '. Me ha ayudado a entender mejor el tema.',
                'Comparto tu punto de vista sobre ' . $postTitle . '. Es un tema muy importante.',
            ],
            'question' => [
                '¿Podrías explicar más sobre algún aspecto específico de ' . $postTitle . '?',
                'Tengo una duda: ¿cómo se relaciona esto con otros temas similares?',
                '¿Hay alguna fuente adicional donde pueda encontrar más información sobre ' . $postTitle . '?',
                '¿Podrías dar un ejemplo práctico de lo que describes en ' . $postTitle . '?',
                '¿Qué opinas sobre la evolución futura de este tema?',
            ],
            'answer' => [
                'Basándome en mi experiencia, puedo confirmar que ' . $postTitle . ' es exactamente como describes.',
                'Para complementar tu explicación sobre ' . $postTitle . ', añadiría que...',
                'Excelente pregunta. La respuesta es que ' . $postTitle . ' funciona de la siguiente manera...',
                'Según los estudios más recientes, ' . $postTitle . ' se puede abordar desde...',
                'Mi recomendación para ' . $postTitle . ' sería considerar también...',
            ],
            'suggestion' => [
                'Te sugiero que también consideres incluir información sobre...',
                'Podrías añadir algunos ejemplos prácticos para hacer ' . $postTitle . ' más comprensible.',
                'Mi sugerencia sería organizar la información de ' . $postTitle . ' de manera cronológica.',
                'Considera añadir una sección de recursos adicionales para ' . $postTitle . '.',
                'Te recomiendo incluir casos de uso reales de ' . $postTitle . '.',
            ],
            'feedback' => [
                'Muy buen trabajo con ' . $postTitle . '. La información está muy bien organizada.',
                'Este contenido sobre ' . $postTitle . ' es muy útil para principiantes.',
                'Gracias por abordar ' . $postTitle . ' de manera tan clara y concisa.',
                'El enfoque que tomas en ' . $postTitle . ' es muy acertado.',
                'Me ha gustado especialmente la forma en que explicas ' . $postTitle . '.',
            ],
        ];

        $typeContents = $contents[$type] ?? $contents['general'];
        return $typeContents[array_rand($typeContents)];
    }

    /**
     * Genera imágenes para comentarios (opcional)
     */
    private function getCommentImages(string $type): ?array
    {
        if (rand(0, 10) === 0) { // 10% de probabilidad
            $imageTypes = ['jpg', 'png', 'gif'];
            $imageType = $imageTypes[array_rand($imageTypes)];
            
            return [
                [
                    'url' => 'https://via.placeholder.com/400x300/4F46E5/FFFFFF?text=Imagen+Comentario',
                    'alt' => 'Imagen del comentario',
                    'type' => $imageType,
                    'size' => rand(100, 2000) . 'KB'
                ]
            ];
        }
        
        return null;
    }

    /**
     * Genera archivos adjuntos para comentarios (opcional)
     */
    private function getCommentAttachments(string $type): ?array
    {
        if (rand(0, 15) === 0) { // 7% de probabilidad
            $attachmentTypes = ['pdf', 'doc', 'xls', 'txt'];
            $attachmentType = $attachmentTypes[array_rand($attachmentTypes)];
            
            return [
                [
                    'name' => 'documento_' . Str::random(8) . '.' . $attachmentType,
                    'url' => 'https://via.placeholder.com/300x400/10B981/FFFFFF?text=Adjunto',
                    'type' => $attachmentType,
                    'size' => rand(50, 5000) . 'KB'
                ]
            ];
        }
        
        return null;
    }

    /**
     * Genera enlaces para comentarios (opcional)
     */
    private function getCommentLinks(string $type): ?array
    {
        if (rand(0, 8) === 0) { // 12% de probabilidad
            $linkTypes = ['article', 'video', 'resource', 'reference'];
            $linkType = $linkTypes[array_rand($linkTypes)];
            
            return [
                [
                    'url' => 'https://example.com/' . $linkType . '/' . Str::random(10),
                    'title' => 'Enlace relacionado con ' . $linkType,
                    'type' => $linkType,
                    'description' => 'Recurso adicional sobre el tema'
                ]
            ];
        }
        
        return null;
    }

    /**
     * Mapea el tipo de comentario del seeder al tipo de la base de datos
     */
    private function mapCommentType(string $seederType): string
    {
        $typeMap = [
            'general' => 'comment',
            'question' => 'comment',
            'answer' => 'answer',
            'suggestion' => 'comment',
            'feedback' => 'comment',
        ];
        
        return $typeMap[$seederType] ?? 'comment';
    }

    /**
     * Actualiza el contador de respuestas para comentarios padres
     */
    private function updateRepliesCount(array $comments): void
    {
        $parentRepliesCount = [];
        
        // Contar respuestas para cada comentario padre
        foreach ($comments as $comment) {
            if (!is_null($comment['parent_id'])) {
                if (!isset($parentRepliesCount[$comment['parent_id']])) {
                    $parentRepliesCount[$comment['parent_id']] = 0;
                }
                $parentRepliesCount[$comment['parent_id']]++;
            }
        }
        
        // Actualizar la base de datos
        foreach ($parentRepliesCount as $parentId => $count) {
            TopicComment::where('id', $parentId)->update([
                'replies_count' => $count,
                'children_count' => $count,
                'descendants_count' => $count
            ]);
        }
    }
}
