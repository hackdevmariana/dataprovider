<?php

namespace Database\Seeders;

use App\Models\TopicComment;
use App\Models\TopicPost;
use App\Models\User;
use Illuminate\Database\Seeder;

class TopicCommentSeeder extends Seeder
{
    public function run(): void
    {
        $topicPosts = TopicPost::take(5)->get();
        $users = User::take(10)->get();
        
        if ($topicPosts->isEmpty() || $users->isEmpty()) {
            $this->command->warn('No hay posts de temas o usuarios disponibles.');
            return;
        }

        $this->createComments($topicPosts, $users);
    }

    private function createComments($topicPosts, $users): void
    {
        foreach ($topicPosts as $post) {
            // Crear comentarios raíz
            for ($i = 0; $i < fake()->numberBetween(3, 6); $i++) {
                $user = $users->random();
                $comment = TopicComment::create([
                    'topic_post_id' => $post->id,
                    'user_id' => $user->id,
                    'parent_id' => null,
                    'body' => $this->generateCommentBody(),
                    'excerpt' => fake()->sentence(),
                    'depth' => 0,
                    'thread_path' => null,
                    'sort_order' => $i,
                    'children_count' => 0,
                    'comment_type' => fake()->randomElement(['comment', 'answer', 'solution']),
                    'is_best_answer' => fake()->boolean(10),
                    'is_author_reply' => $user->id === $post->user_id,
                    'is_moderator_reply' => fake()->boolean(5),
                    'is_pinned' => fake()->boolean(5),
                    'is_highlighted' => fake()->boolean(8),
                    'is_edited' => fake()->boolean(15),
                    'is_deleted' => false,
                    'upvotes_count' => fake()->numberBetween(0, 50),
                    'downvotes_count' => fake()->numberBetween(0, 10),
                    'score' => fake()->numberBetween(-5, 45),
                    'replies_count' => 0,
                    'likes_count' => fake()->numberBetween(0, 25),
                    'reports_count' => fake()->numberBetween(0, 3),
                    'helpful_votes' => fake()->numberBetween(0, 20),
                    'not_helpful_votes' => fake()->numberBetween(0, 5),
                    'quality_score' => fake()->randomFloat(2, 60, 100),
                    'helpfulness_score' => fake()->randomFloat(2, 0, 100),
                    'relevance_score' => fake()->randomFloat(2, 70, 100),
                    'read_time_seconds' => fake()->numberBetween(15, 120),
                    'engagement_rate' => fake()->randomFloat(2, 2, 15),
                    'images' => fake()->boolean(20) ? [fake()->imageUrl()] : null,
                    'attachments' => fake()->boolean(10) ? [['name' => fake()->word() . '.pdf', 'url' => fake()->url()]] : null,
                    'links' => fake()->boolean(30) ? [fake()->url()] : null,
                    'code_snippets' => fake()->boolean(15) ? [['language' => 'php', 'code' => fake()->sentence()]] : null,
                    'status' => fake()->randomElement(['published', 'published', 'published', 'pending']),
                    'moderation_flags' => fake()->boolean(5) ? ['spam' => false] : null,
                    'moderation_notes' => fake()->optional(0.1)->sentence(),
                    'moderated_by' => fake()->optional(0.1)->randomElement($users->pluck('id')->toArray()),
                    'moderated_at' => fake()->optional(0.1)->dateTimeBetween('-30 days', 'now'),
                    'last_edited_at' => fake()->optional(0.15)->dateTimeBetween('-20 days', 'now'),
                    'last_edited_by' => fake()->optional(0.15)->randomElement($users->pluck('id')->toArray()),
                    'edit_reason' => fake()->optional(0.1)->sentence(),
                    'edit_count' => fake()->numberBetween(0, 3),
                    'edit_history' => fake()->boolean(10) ? [['edited_at' => fake()->dateTime()->format('Y-m-d H:i:s'), 'changes' => 'Corrección']] : null,
                    'mentioned_users' => fake()->boolean(25) ? fake()->randomElements($users->pluck('id')->toArray(), fake()->numberBetween(1, 2)) : null,
                    'tags' => fake()->boolean(40) ? fake()->randomElements(['energía', 'sostenibilidad', 'tecnología'], fake()->numberBetween(1, 2)) : null,
                    'language' => fake()->randomElement(['es', 'en']),
                    'quote_text' => fake()->optional(0.2)->sentence(),
                    'quoted_comment_id' => null,
                    'context_data' => fake()->boolean(30) ? ['device_type' => 'desktop'] : null,
                    'notify_parent_author' => true,
                    'notify_post_author' => true,
                    'notify_followers' => fake()->boolean(80),
                    'last_activity_at' => fake()->dateTimeBetween('-10 days', 'now'),
                    'views_count' => fake()->numberBetween(5, 200),
                    'unique_views_count' => fake()->numberBetween(3, 150),
                    'ranking_score' => fake()->randomFloat(2, 0, 100),
                    'controversy_score' => fake()->randomFloat(2, 0, 50),
                    'hot_until' => fake()->optional(0.3)->dateTimeBetween('now', '+7 days'),
                    'source' => fake()->randomElement(['web', 'mobile_app', 'api']),
                    'user_agent' => fake()->userAgent(),
                    'creation_metadata' => ['ip_address' => fake()->ipv4(), 'session_id' => fake()->uuid()],
                    'author_reputation_at_time' => fake()->randomFloat(2, 0, 1000),
                    'root_comment_id' => null,
                    'thread_participants' => fake()->boolean(60) ? fake()->randomElements($users->pluck('id')->toArray(), fake()->numberBetween(2, 4)) : null,
                    'breaks_thread' => fake()->boolean(5),
                    'thread_last_activity' => fake()->dateTimeBetween('-5 days', 'now'),
                    'collapsed_by_default' => fake()->boolean(10),
                    'show_score' => true,
                    'allow_replies' => true,
                    'max_reply_depth' => fake()->optional(0.2)->numberBetween(3, 10),
                ]);

                $comment->update(['thread_path' => (string)$comment->id]);

                // Crear respuestas a algunos comentarios
                if (fake()->boolean(60)) {
                    for ($j = 0; $j < fake()->numberBetween(1, 3); $j++) {
                        $replyUser = $users->random();
                        TopicComment::create([
                            'topic_post_id' => $post->id,
                            'user_id' => $replyUser->id,
                            'parent_id' => $comment->id,
                            'body' => $this->generateCommentBody(),
                            'excerpt' => fake()->sentence(),
                            'depth' => 1,
                            'thread_path' => $comment->thread_path . '/' . ($j + 1),
                            'sort_order' => $j,
                            'children_count' => 0,
                            'comment_type' => fake()->randomElement(['comment', 'answer', 'clarification']),
                            'is_best_answer' => false,
                            'is_author_reply' => $replyUser->id === $post->user_id,
                            'is_moderator_reply' => fake()->boolean(3),
                            'is_pinned' => false,
                            'is_highlighted' => fake()->boolean(5),
                            'is_edited' => fake()->boolean(10),
                            'is_deleted' => false,
                            'upvotes_count' => fake()->numberBetween(0, 25),
                            'downvotes_count' => fake()->numberBetween(0, 5),
                            'score' => fake()->numberBetween(-2, 20),
                            'replies_count' => 0,
                            'likes_count' => fake()->numberBetween(0, 15),
                            'reports_count' => fake()->numberBetween(0, 2),
                            'helpful_votes' => fake()->numberBetween(0, 10),
                            'not_helpful_votes' => fake()->numberBetween(0, 3),
                            'quality_score' => fake()->randomFloat(2, 50, 95),
                            'helpfulness_score' => fake()->randomFloat(2, 0, 90),
                            'relevance_score' => fake()->randomFloat(2, 60, 95),
                            'read_time_seconds' => fake()->numberBetween(10, 90),
                            'engagement_rate' => fake()->randomFloat(2, 1, 12),
                            'images' => null,
                            'attachments' => null,
                            'links' => fake()->boolean(20) ? [fake()->url()] : null,
                            'code_snippets' => null,
                            'status' => 'published',
                            'moderation_flags' => null,
                            'moderation_notes' => null,
                            'moderated_by' => null,
                            'moderated_at' => null,
                            'last_edited_at' => fake()->optional(0.1)->dateTimeBetween('-15 days', 'now'),
                            'last_edited_by' => fake()->optional(0.1)->randomElement($users->pluck('id')->toArray()),
                            'edit_reason' => fake()->optional(0.05)->sentence(),
                            'edit_count' => fake()->numberBetween(0, 2),
                            'edit_history' => null,
                            'mentioned_users' => fake()->boolean(20) ? fake()->randomElements($users->pluck('id')->toArray(), 1) : null,
                            'tags' => fake()->boolean(30) ? fake()->randomElements(['energía', 'sostenibilidad'], 1) : null,
                            'language' => fake()->randomElement(['es', 'en']),
                            'quote_text' => fake()->optional(0.3)->sentence(),
                            'quoted_comment_id' => null,
                            'context_data' => null,
                            'notify_parent_author' => true,
                            'notify_post_author' => fake()->boolean(70),
                            'notify_followers' => fake()->boolean(60),
                            'last_activity_at' => fake()->dateTimeBetween('-8 days', 'now'),
                            'views_count' => fake()->numberBetween(3, 100),
                            'unique_views_count' => fake()->numberBetween(2, 80),
                            'ranking_score' => fake()->randomFloat(2, 0, 80),
                            'controversy_score' => fake()->randomFloat(2, 0, 30),
                            'hot_until' => fake()->optional(0.2)->dateTimeBetween('now', '+5 days'),
                            'source' => fake()->randomElement(['web', 'mobile_app']),
                            'user_agent' => fake()->userAgent(),
                            'creation_metadata' => ['ip_address' => fake()->ipv4()],
                            'author_reputation_at_time' => fake()->randomFloat(2, 0, 800),
                            'root_comment_id' => $comment->id,
                            'thread_participants' => null,
                            'breaks_thread' => fake()->boolean(3),
                            'thread_last_activity' => fake()->dateTimeBetween('-3 days', 'now'),
                            'collapsed_by_default' => fake()->boolean(15),
                            'show_score' => true,
                            'allow_replies' => true,
                            'max_reply_depth' => null,
                        ]);

                        $comment->increment('children_count');
                        $comment->increment('replies_count');
                    }
                }
            }
        }
    }

    private function generateCommentBody(): string
    {
        $templates = [
            "Excelente punto sobre {topic}. Creo que también deberíamos considerar {aspect}.",
            "Tengo una pregunta sobre {topic}. ¿Alguien ha probado {solution}?",
            "Muy interesante. En mi experiencia con {topic}, he encontrado que {insight}.",
            "Gracias por compartir esto. Me parece muy útil para {use_case}.",
            "¿Podrías explicar más sobre {concept}? No estoy seguro de entender completamente.",
            "Estoy de acuerdo contigo en que {opinion}. Además, creo que {additional_point}.",
            "He estado trabajando en {project} y este enfoque me parece {assessment}.",
            "¿Has considerado {alternative}? Podría ser una buena opción para {scenario}.",
            "Muy buena información. ¿Tienes alguna referencia sobre {topic}?",
            "Interesante perspectiva. ¿Cómo se relaciona esto con {related_concept}?",
        ];

        $topic = fake()->randomElement(['energía renovable', 'sostenibilidad', 'tecnología', 'innovación', 'eficiencia energética']);
        $aspect = fake()->randomElement(['el impacto ambiental', 'los costos a largo plazo', 'la implementación práctica']);
        $solution = fake()->randomElement(['paneles solares', 'aerogeneradores', 'baterías de litio']);
        $insight = fake()->randomElement(['funciona mejor de lo esperado', 'requiere más mantenimiento', 'es más costoso inicialmente']);
        $use_case = fake()->randomElement(['proyectos residenciales', 'aplicaciones industriales', 'sistemas comunitarios']);
        $concept = fake()->randomElement(['el funcionamiento técnico', 'los costos asociados', 'la implementación práctica']);
        $opinion = fake()->randomElement(['es fundamental para el futuro', 'requiere más investigación', 'debería ser más accesible']);
        $additional_point = fake()->randomElement(['también debemos considerar el impacto ambiental', 'la regulación juega un papel importante']);
        $project = fake()->randomElement(['un sistema solar', 'una instalación eólica', 'un proyecto de eficiencia']);
        $assessment = fake()->randomElement(['muy prometedor', 'desafiante pero factible', 'necesita más desarrollo']);
        $alternative = fake()->randomElement(['otras tecnologías', 'un enfoque híbrido', 'soluciones más simples']);
        $scenario = fake()->randomElement(['tu situación específica', 'aplicaciones a gran escala', 'proyectos piloto']);
        $related_concept = fake()->randomElement(['cambio climático', 'economía circular', 'transición energética']);

        $template = fake()->randomElement($templates);
        
        return str_replace(
            ['{topic}', '{aspect}', '{solution}', '{insight}', '{use_case}', '{concept}', '{opinion}', '{additional_point}', '{project}', '{assessment}', '{alternative}', '{scenario}', '{related_concept}'],
            [$topic, $aspect, $solution, $insight, $use_case, $concept, $opinion, $additional_point, $project, $assessment, $alternative, $scenario, $related_concept],
            $template
        );
    }
}