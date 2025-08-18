<?php

namespace Database\Seeders;

use App\Models\UserGeneratedContent;
use App\Models\NewsArticle;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Seeder para contenido generado por usuarios.
 */
class UserGeneratedContentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear contenido específico para artículos de sostenibilidad
        $this->createSustainabilityComments();
        
        // Crear contenido general usando factory
        UserGeneratedContent::factory(80)->create();
        
        // Crear contenido publicado
        UserGeneratedContent::factory(40)->published()->create();
        
        // Crear contenido destacado
        UserGeneratedContent::factory(8)->featured()->create();
        
        // Crear contenido popular
        UserGeneratedContent::factory(15)->popular()->create();
        
        // Crear algunos casos de spam para testing
        UserGeneratedContent::factory(5)->spam()->create();
        
        // Asignar relaciones
        $this->assignRelations();
        
        echo "✅ Creados " . UserGeneratedContent::count() . " elementos de contenido de usuarios\n";
    }

    /**
     * Crear comentarios específicos para artículos de sostenibilidad.
     */
    private function createSustainabilityComments(): void
    {
        $sustainabilityArticles = NewsArticle::whereIn('category', ['energía', 'medio ambiente', 'sostenibilidad'])
                                            ->orWhere('covers_sustainability', true)
                                            ->limit(10)
                                            ->get();

        $users = User::limit(20)->get();

        $specificComments = [
            [
                'type' => 'comment',
                'content' => 'Excelente noticia para España. Por fin vemos resultados concretos de las inversiones en energías renovables. Como ingeniero del sector, puedo confirmar que los datos son muy prometedores y que vamos por buen camino hacia la descarbonización.',
                'title' => null,
                'user_name' => 'Carlos Ingeniero',
                'user_email' => 'carlos.ing@email.com',
                'status' => 'published',
                'visibility' => 'public',
                'is_verified' => false,
                'likes_count' => 45,
                'dislikes_count' => 2,
                'sentiment_score' => 0.8,
                'sentiment_label' => 'positivo',
                'auto_tags' => json_encode(['positivo', 'energías_renovables', 'experto']),
                'published_at' => now()->subDays(1),
            ],
            [
                'type' => 'suggestion',
                'content' => 'Me parece genial la iniciativa, pero creo que falta más información sobre cómo los ciudadanos podemos contribuir. Sugiero incluir una sección con consejos prácticos para el ahorro energético en casa.',
                'title' => 'Sugerencia para más contenido práctico',
                'user_name' => 'Ana Sostenible',
                'user_email' => 'ana.verde@email.com',
                'rating' => 4,
                'status' => 'published',
                'visibility' => 'public',
                'is_verified' => false,
                'likes_count' => 23,
                'dislikes_count' => 1,
                'sentiment_score' => 0.6,
                'sentiment_label' => 'positivo',
                'auto_tags' => json_encode(['sugerencia', 'mejora', 'ciudadanos']),
                'published_at' => now()->subDays(2),
            ],
            [
                'type' => 'question',
                'content' => '¿Podrían explicar cómo afecta esto a las facturas de la luz? Me gustaría entender si estos avances se traducen en ahorros reales para las familias.',
                'title' => 'Pregunta sobre impacto económico',
                'user_name' => 'Miguel Familia',
                'user_email' => 'miguel.familia@email.com',
                'status' => 'published',
                'visibility' => 'public',
                'is_verified' => false,
                'likes_count' => 67,
                'dislikes_count' => 0,
                'replies_count' => 3,
                'sentiment_score' => 0.1,
                'sentiment_label' => 'neutral',
                'auto_tags' => json_encode(['pregunta', 'economía_familiar', 'facturas']),
                'published_at' => now()->subDays(1),
            ],
            [
                'type' => 'compliment',
                'content' => 'Fantástico trabajo periodístico. Me encanta cómo explican temas complejos de forma clara y accesible. Este tipo de información es fundamental para que la ciudadanía entienda la importancia de la transición energética.',
                'title' => null,
                'user_name' => 'Laura Profesora',
                'user_email' => 'laura.edu@email.com',
                'status' => 'published',
                'visibility' => 'public',
                'is_verified' => true,
                'likes_count' => 34,
                'dislikes_count' => 0,
                'sentiment_score' => 0.9,
                'sentiment_label' => 'positivo',
                'auto_tags' => json_encode(['elogio', 'calidad_periodistica', 'educativo']),
                'published_at' => now()->subHours(12),
            ],
            [
                'type' => 'comment',
                'content' => 'En mi empresa hemos instalado paneles solares este año y hemos reducido el consumo de la red en un 60%. Confirmo que la tecnología funciona y es rentable. Animo a más empresas a apostar por las renovables.',
                'title' => null,
                'user_name' => 'Roberto Empresario',
                'user_email' => 'roberto.empresa@email.com',
                'status' => 'published',
                'visibility' => 'public',
                'is_verified' => false,
                'likes_count' => 89,
                'dislikes_count' => 3,
                'sentiment_score' => 0.7,
                'sentiment_label' => 'positivo',
                'auto_tags' => json_encode(['experiencia_personal', 'empresa', 'solar']),
                'location_name' => 'Sevilla',
                'latitude' => 37.3886,
                'longitude' => -5.9823,
                'published_at' => now()->subDays(1),
            ],
            [
                'type' => 'complaint',
                'content' => 'Está bien la noticia, pero me parece que se olvidan de mencionar los costes. Las renovables requieren inversiones enormes y alguien tiene que pagarlas. Falta más análisis económico en profundidad.',
                'title' => 'Falta análisis de costes',
                'user_name' => 'David Crítico',
                'user_email' => 'david.critico@email.com',
                'status' => 'published',
                'visibility' => 'public',
                'is_verified' => false,
                'likes_count' => 12,
                'dislikes_count' => 8,
                'replies_count' => 5,
                'sentiment_score' => -0.3,
                'sentiment_label' => 'negativo',
                'auto_tags' => json_encode(['critica', 'análisis_económico', 'costes']),
                'published_at' => now()->subDays(2),
            ],
        ];

        foreach ($specificComments as $index => $comment) {
            if ($sustainabilityArticles->isNotEmpty()) {
                $comment['related_type'] = 'App\Models\NewsArticle';
                $comment['related_id'] = $sustainabilityArticles->random()->id;
                $comment['user_id'] = $users->isNotEmpty() ? $users->random()->id : null;
                $comment['is_anonymous'] = !$comment['user_id'];
                $comment['language'] = 'es';
                $comment['user_ip'] = fake()->ipv4();
                $comment['user_agent'] = fake()->userAgent();
                
                UserGeneratedContent::create($comment);
                echo "✅ Creado comentario: " . substr($comment['content'], 0, 50) . "...\n";
            }
        }
    }

    /**
     * Asignar relaciones a contenido existente.
     */
    private function assignRelations(): void
    {
        $newsArticles = NewsArticle::limit(50)->get();
        $users = User::limit(30)->get();
        
        UserGeneratedContent::whereNull('related_id')->chunk(10, function ($content) use ($newsArticles, $users) {
            foreach ($content as $item) {
                $relatedTypes = [
                    'App\Models\NewsArticle' => $newsArticles->isNotEmpty() ? $newsArticles->random()->id : 1,
                ];
                
                $randomType = array_rand($relatedTypes);
                
                $item->update([
                    'related_type' => $randomType,
                    'related_id' => $relatedTypes[$randomType],
                    'user_id' => $users->isNotEmpty() && fake()->boolean(70) ? $users->random()->id : null,
                ]);
                
                // Si no tiene user_id, es contenido anónimo
                if (!$item->user_id) {
                    $item->update(['is_anonymous' => true]);
                }
            }
        });
        
        echo "✅ Asignadas relaciones a contenido de usuarios\n";
    }
}