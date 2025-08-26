<?php

namespace Database\Factories;

use App\Models\UserGeneratedContent;
use App\Models\User;
use App\Models\NewsArticle;
use App\Models\MediaOutlet;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Factory para generar contenido de usuarios realista.
 */
class UserGeneratedContentFactory extends Factory
{
    protected $model = UserGeneratedContent::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        $contentTypes = ['comment', 'suggestion', 'photo'];
        $contentType = fake()->randomElement($contentTypes);
        
        $relatedTypes = [
            'App\Models\NewsArticle',
            'App\Models\MediaOutlet',
            'App\Models\Event',
            'App\Models\Festival'
        ];
        
        $content = $this->generateRealisticContent($contentType);
        
        return [
            'related_type' => fake()->randomElement($relatedTypes),
            'related_id' => fake()->numberBetween(1, 10),
            'content_type' => $contentType, // Usar 'content_type' para coincidir con la tabla
            'content' => $content,
            'title' => $this->generateTitle($contentType),
            'excerpt' => $this->generateExcerpt($content),
            'parent_id' => fake()->optional(0.3)->numberBetween(1, 10),
            'rating' => $contentType === 'suggestion' ? fake()->numberBetween(1, 5) : null,
            'metadata' => json_encode($this->generateMetadata()),
            'media_attachments' => json_encode($this->generateMediaAttachments()),
            
            // Información del usuario
            'user_id' => fake()->numberBetween(1, 3),
            'user_name' => fake()->name(),
            'user_email' => fake()->email(),
            'user_ip' => fake()->ipv4(),
            'user_agent' => fake()->userAgent(),
            'is_anonymous' => fake()->boolean(30),
            
            // Estado y moderación
            'is_verified' => fake()->boolean(20),
            'is_featured' => fake()->boolean(5),
            'is_spam' => fake()->boolean(10),
            'needs_moderation' => fake()->boolean(25),
            'status' => fake()->randomElement(['pending', 'approved', 'rejected']),
            'visibility' => fake()->randomElement(['public', 'private', 'unlisted']),
            'language' => 'es',
            
            // Métricas de engagement
            'likes_count' => fake()->numberBetween(0, 500),
            'dislikes_count' => fake()->numberBetween(0, 50),
            'replies_count' => fake()->numberBetween(0, 25),
            'reports_count' => fake()->numberBetween(0, 5),
            
            // Análisis de sentimiento
            'sentiment_score' => fake()->randomFloat(2, -1, 1),
            'sentiment_label' => fake()->randomElement(['positivo', 'neutral', 'negativo']),
            
            // Tags y moderación
            'moderation_notes' => json_encode($this->generateModerationNotes()),
            'auto_tags' => json_encode($this->generateAutoTags($contentType)),
            'moderator_id' => fake()->optional(0.3)->numberBetween(1, 3),
            
            // Geolocalización
            'location_name' => fake()->optional(0.4)->city(),
            'latitude' => fake()->optional(0.4)->latitude(35.0, 44.0), // España
            'longitude' => fake()->optional(0.4)->longitude(-10.0, 5.0), // España
            
            // Fechas
            'published_at' => fake()->optional(0.8)->dateTimeBetween('-6 months', 'now'),
            'moderated_at' => fake()->optional(0.3)->dateTimeBetween('-3 months', 'now'),
            'featured_until' => fake()->optional(0.05)->dateTimeBetween('now', '+1 month'),
        ];
    }

    /**
     * Generar contenido realista según el tipo.
     */
    private function generateRealisticContent(string $type): string
    {
        switch ($type) {
            case 'comment':
                return $this->generateComment();
            case 'suggestion':
                return $this->generateSuggestion();
            case 'question':
                return $this->generateQuestion();
            case 'complaint':
                return $this->generateComplaint();
            case 'compliment':
                return $this->generateCompliment();
            default:
                return fake()->paragraph(3);
        }
    }

    /**
     * Generar comentario realista.
     */
    private function generateComment(): string
    {
        $templates = [
            "Muy interesante el artículo. {opinion} Creo que {suggestion}",
            "Totalmente de acuerdo con {point}. {experience}",
            "Me parece que falta mencionar {missing}. {additional_info}",
            "Excelente información. {positive_feedback} {question}",
            "No estoy de acuerdo con {disagreement}. {counter_argument}",
        ];

        $replacements = [
            'opinion' => [
                'Es un tema muy actual.',
                'Hacía falta esta información.',
                'No sabía estos datos.',
                'Es preocupante la situación.',
                'Me alegra ver avances.',
            ],
            'suggestion' => [
                'deberían profundizar más en las soluciones.',
                'sería interesante ver más ejemplos.',
                'falta incluir la perspectiva ciudadana.',
                'necesitamos más datos actualizados.',
                'habría que mencionar las iniciativas locales.',
            ],
            'point' => [
                'la necesidad de más energías renovables',
                'la importancia de la educación ambiental',
                'el papel de las empresas en la sostenibilidad',
                'la urgencia del cambio climático',
                'la relevancia de las políticas verdes',
            ],
            'experience' => [
                'En mi empresa hemos implementado medidas similares.',
                'Como ciudadano, he notado estos cambios.',
                'En mi comunidad estamos trabajando en esto.',
                'He visto resultados positivos en otros países.',
                'Mi experiencia profesional confirma esto.',
            ],
            'missing' => [
                'el impacto económico',
                'las consecuencias sociales',
                'los retos tecnológicos',
                'la perspectiva regional',
                'las alternativas disponibles',
            ],
            'additional_info' => [
                'Sería útil incluir más estadísticas.',
                'Faltan ejemplos concretos.',
                'Necesitamos casos de éxito.',
                'Habría que mencionar los costes.',
                'Es importante el contexto histórico.',
            ]
        ];

        $template = fake()->randomElement($templates);
        foreach ($replacements as $key => $options) {
            $template = str_replace('{' . $key . '}', fake()->randomElement($options), $template);
        }

        return $template;
    }

    /**
     * Generar sugerencia realista.
     */
    private function generateSuggestion(): string
    {
        $suggestions = [
            "Propongo que se incluya una sección sobre soluciones prácticas que los ciudadanos podemos implementar en casa.",
            "Sería genial tener más entrevistas con expertos locales que estén trabajando en estos temas.",
            "Sugiero crear una newsletter semanal con las noticias más relevantes de sostenibilidad.",
            "Podrían añadir infografías para hacer la información más visual y comprensible.",
            "Me gustaría ver más cobertura de iniciativas de pequeñas empresas sostenibles.",
            "Propongo incluir un apartado de recursos útiles al final de cada artículo.",
            "Sería interesante tener una sección de preguntas y respuestas sobre temas ambientales.",
        ];

        return fake()->randomElement($suggestions);
    }

    /**
     * Generar pregunta realista.
     */
    private function generateQuestion(): string
    {
        $questions = [
            "¿Podrían explicar con más detalle cómo afecta esto a las pequeñas empresas?",
            "¿Qué alternativas existen para los consumidores individuales?",
            "¿Cuál es el plazo estimado para ver resultados de estas medidas?",
            "¿Hay algún ejemplo similar en otros países europeos?",
            "¿Cómo podemos los ciudadanos contribuir a esta iniciativa?",
            "¿Qué papel juegan las administraciones locales en este proceso?",
            "¿Existen ayudas públicas para implementar estas soluciones?",
            "¿Cuáles son los principales obstáculos para el cambio?",
        ];

        return fake()->randomElement($questions);
    }

    /**
     * Generar queja realista.
     */
    private function generateComplaint(): string
    {
        $complaints = [
            "Me parece que la información está incompleta. Falta mencionar los aspectos negativos de esta propuesta.",
            "El artículo es demasiado optimista. La realidad es más compleja y problemática.",
            "No se mencionan los costes reales que esto supondrá para los ciudadanos.",
            "Creo que hay un sesgo evidente hacia ciertas empresas o sectores.",
            "La información parece desactualizada. Los datos no reflejan la situación actual.",
            "Falta rigor periodístico. Se presentan opiniones como hechos.",
            "El titular es sensacionalista y no refleja el contenido real del artículo.",
        ];

        return fake()->randomElement($complaints);
    }

    /**
     * Generar elogio realista.
     */
    private function generateCompliment(): string
    {
        $compliments = [
            "Excelente artículo, muy bien documentado y con fuentes fiables. Felicidades al equipo.",
            "Me encanta la claridad con la que explican temas tan complejos. Muy didáctico.",
            "Fantástico trabajo de investigación. Se nota la profesionalidad y el rigor.",
            "Gracias por mantener informada a la ciudadanía sobre estos temas tan importantes.",
            "La calidad del periodismo de este medio es excepcional. Sigan así.",
            "Muy buena la inclusión de diferentes perspectivas y voces expertas.",
            "Artículo muy completo y equilibrado. Se agradece la objetividad.",
        ];

        return fake()->randomElement($compliments);
    }

    /**
     * Generar título según el tipo.
     */
    private function generateTitle(string $type): ?string
    {
        if (fake()->boolean(60)) {
            switch ($type) {
                case 'question':
                    return 'Pregunta sobre ' . fake()->randomElement(['sostenibilidad', 'energías renovables', 'políticas ambientales']);
                case 'suggestion':
                    return 'Sugerencia para mejorar ' . fake()->randomElement(['la cobertura', 'el contenido', 'la información']);
                case 'complaint':
                    return 'Queja sobre ' . fake()->randomElement(['el artículo', 'la información', 'el enfoque']);
                case 'compliment':
                    return 'Felicitaciones por ' . fake()->randomElement(['el artículo', 'el trabajo', 'la investigación']);
                default:
                    return null;
            }
        }
        return null;
    }

    /**
     * Generar extracto del contenido.
     */
    private function generateExcerpt(string $content): string
    {
        return substr($content, 0, 150) . '...';
    }

    /**
     * Generar metadatos.
     */
    private function generateMetadata(): array
    {
        return [
            'source' => fake()->randomElement(['web', 'mobile_app', 'newsletter']),
            'referrer' => fake()->optional(0.6)->url(),
            'utm_source' => fake()->optional(0.4)->randomElement(['google', 'facebook', 'twitter', 'direct']),
            'device_type' => fake()->randomElement(['desktop', 'mobile', 'tablet']),
        ];
    }

    /**
     * Generar adjuntos multimedia.
     */
    private function generateMediaAttachments(): array
    {
        if (fake()->boolean(20)) {
            return [
                [
                    'type' => 'image',
                    'url' => fake()->imageUrl(800, 600),
                    'alt_text' => fake()->sentence(3),
                ]
            ];
        }
        return [];
    }

    /**
     * Generar notas de moderación.
     */
    private function generateModerationNotes(): array
    {
        if (fake()->boolean(30)) {
            return [
                [
                    'date' => fake()->dateTimeBetween('-1 month', 'now')->format('Y-m-d H:i:s'),
                    'moderator' => fake()->name(),
                    'action' => fake()->randomElement(['approved', 'flagged', 'edited']),
                    'note' => fake()->sentence(8),
                ]
            ];
        }
        return [];
    }

    /**
     * Generar tags automáticos.
     */
    private function generateAutoTags(string $type): array
    {
        $baseTags = ['usuario', 'contenido_publico'];
        
        switch ($type) {
            case 'comment':
                $baseTags[] = 'comentario';
                break;
            case 'suggestion':
                $baseTags[] = 'sugerencia';
                $baseTags[] = 'mejora';
                break;
            case 'question':
                $baseTags[] = 'pregunta';
                $baseTags[] = 'consulta';
                break;
            case 'complaint':
                $baseTags[] = 'queja';
                $baseTags[] = 'critica';
                break;
            case 'compliment':
                $baseTags[] = 'elogio';
                $baseTags[] = 'positivo';
                break;
        }
        
        if (fake()->boolean(40)) {
            $baseTags[] = fake()->randomElement(['sostenibilidad', 'medio_ambiente', 'energia', 'politica']);
        }
        
        return $baseTags;
    }

    /**
     * Contenido publicado.
     */
    public function published(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'approved',
            'published_at' => fake()->dateTimeBetween('-3 months', 'now'),
            'is_spam' => false,
            'needs_moderation' => false,
        ]);
    }

    /**
     * Contenido destacado.
     */
    public function featured(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_featured' => true,
            'featured_until' => fake()->dateTimeBetween('now', '+1 month'),
            'likes_count' => fake()->numberBetween(100, 1000),
            'status' => 'approved',
        ]);
    }

    /**
     * Comentario popular.
     */
    public function popular(): static
    {
        return $this->state(fn (array $attributes) => [
            'likes_count' => fake()->numberBetween(50, 500),
            'replies_count' => fake()->numberBetween(5, 50),
            'status' => 'approved',
        ]);
    }

    /**
     * Contenido spam.
     */
    public function spam(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_spam' => true,
            'status' => 'rejected',
            'needs_moderation' => false,
            'content' => 'Contenido promocional no solicitado con enlaces sospechosos.',
        ]);
    }
}