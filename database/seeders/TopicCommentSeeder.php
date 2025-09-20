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
                    'is_pinned' => fake()->boolean(5),
                    'upvotes_count' => fake()->numberBetween(0, 50),
                    'downvotes_count' => fake()->numberBetween(0, 10),
                    'score' => fake()->numberBetween(-5, 45),
                    'replies_count' => 0,
                    'quality_score' => fake()->randomFloat(2, 60, 100),
                    'status' => fake()->randomElement(['published', 'published', 'published', 'pending']),
                    'last_activity_at' => fake()->dateTimeBetween('-10 days', 'now'),
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
                            'is_pinned' => false,
                            'upvotes_count' => fake()->numberBetween(0, 25),
                            'downvotes_count' => fake()->numberBetween(0, 5),
                            'score' => fake()->numberBetween(-2, 20),
                            'replies_count' => 0,
                            'quality_score' => fake()->randomFloat(2, 50, 95),
                            'status' => 'published',
                            'last_activity_at' => fake()->dateTimeBetween('-8 days', 'now'),
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