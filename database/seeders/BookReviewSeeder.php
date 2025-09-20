<?php

namespace Database\Seeders;

use App\Models\BookReview;
use App\Models\Book;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BookReviewSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Creando reseñas de libros...');

        // Verificar que existen libros
        $books = Book::limit(10)->get();
        if ($books->isEmpty()) {
            $this->command->warn('No hay libros disponibles. Creando libros de prueba...');
            for ($i = 1; $i <= 5; $i++) {
                Book::create([
                    'title' => 'Libro de Prueba ' . $i,
                    'author' => 'Autor ' . $i,
                    'isbn' => '978-84-1234-' . str_pad($i, 3, '0', STR_PAD_LEFT),
                    'publication_year' => 2020 + $i,
                ]);
            }
            $books = Book::limit(10)->get();
        }

        $reviewTitles = [
            'Una obra maestra de la literatura contemporánea',
            'Lectura imprescindible para todos los amantes de los libros',
            'Una historia que te atrapa desde la primera página',
            'Excelente narrativa con personajes muy bien desarrollados',
            'Un libro que no puedes dejar de leer',
            'Una decepción total, no cumple las expectativas',
            'Historia interesante pero con algunos altibajos',
            'Muy recomendable para pasar un buen rato',
            'Una lectura entretenida sin ser extraordinaria',
            'Libro decente para pasar el tiempo',
            'Una historia conmovedora y bien escrita',
            'Narrativa pobre y argumento inconsistente',
            'Tiene momentos brillantes pero también partes flojas',
            'Una historia con potencial pero mal ejecutada',
            'Algunos aspectos buenos pero otros decepcionantes',
            'Libro irregular con altibajos constantes',
            'Ideas interesantes pero desarrollo inconsistente',
            'Una obra que supera todas las expectativas',
            'Lectura obligatoria para cualquier biblioteca',
            'Un clásico moderno que perdurará en el tiempo'
        ];

        $prosOptions = [
            ['Excelente narrativa', 'Personajes bien desarrollados', 'Trama interesante'],
            ['Muy entretenido', 'Buen ritmo', 'Final satisfactorio'],
            ['Historia original', 'Bien escrito', 'Recomendable'],
            ['Adictivo', 'Personajes complejos', 'Muy bien estructurado'],
            ['Estilo único', 'Temas profundos', 'Muy emotivo'],
            ['Buen diálogo', 'Ambientación perfecta', 'Suspense constante'],
            ['Personajes memorables', 'Trama original', 'Muy bien documentado'],
            ['Estilo elegante', 'Temas universales', 'Muy reflexivo'],
            ['Buen humor', 'Personajes entrañables', 'Muy divertido'],
            ['Trama compleja', 'Muy inteligente', 'Satisfactorio']
        ];

        $consOptions = [
            ['Algunas partes lentas', 'Final predecible'],
            ['Personajes poco desarrollados', 'Trama confusa'],
            ['Demasiado largo', 'Ritmo irregular'],
            ['Final abrupto', 'Algunos diálogos forzados'],
            ['Personajes estereotipados', 'Trama repetitiva'],
            ['Estilo monótono', 'Falta de originalidad'],
            ['Personajes poco creíbles', 'Trama inconsistente'],
            ['Demasiado descriptivo', 'Poco diálogo'],
            ['Personajes planos', 'Trama simplista'],
            ['Estilo confuso', 'Estructura desordenada']
        ];
        
        $reviewTemplates = [
            'positive' => [
                'Una obra maestra que no puedes dejar de leer.',
                'Excelente narrativa y personajes muy bien desarrollados.',
                'Un libro que te atrapa desde la primera página.',
                'Muy recomendable para todos los amantes de la literatura.',
                'Una historia conmovedora y bien escrita.',
            ],
            'neutral' => [
                'Un libro interesante con algunos puntos destacables.',
                'Buena historia aunque con algunos altibajos.',
                'Lectura entretenida sin ser extraordinaria.',
                'Un libro decente para pasar el tiempo.',
                'Historia aceptable con personajes bien definidos.',
            ],
            'negative' => [
                'Una decepción total, no cumple las expectativas.',
                'Historia confusa y personajes poco desarrollados.',
                'No recomendaría este libro a nadie.',
                'Una pérdida de tiempo, muy aburrido.',
                'Narrativa pobre y argumento inconsistente.',
            ],
            'mixed' => [
                'Tiene momentos brillantes pero también partes flojas.',
                'Una historia con potencial pero mal ejecutada.',
                'Algunos aspectos buenos pero otros decepcionantes.',
                'Libro irregular con altibajos constantes.',
                'Ideas interesantes pero desarrollo inconsistente.',
            ]
        ];

        $tags = [
            ['aventura', 'fantasía', 'magia'],
            ['romance', 'drama', 'histórico'],
            ['ciencia ficción', 'futuro', 'tecnología'],
            ['misterio', 'suspense', 'crimen'],
            ['biografía', 'memorias', 'realidad'],
            ['filosofía', 'reflexión', 'profundo'],
            ['humor', 'comedia', 'divertido'],
            ['terror', 'horror', 'escalofriante'],
        ];

        $count = 0;
        foreach ($books as $book) {
            // Crear entre 2-6 reseñas por libro
            $reviewsCount = rand(2, 6);
            
            for ($i = 0; $i < $reviewsCount; $i++) {
                $rating = fake()->randomFloat(1, 1.0, 5.0);
                $sentiment = $this->getSentimentFromRating($rating);
                $reviewText = fake()->randomElement($reviewTemplates[$sentiment]);
                
                // Añadir más contenido a la reseña
                $reviewText .= ' ' . fake()->paragraph(2);
                
                $userId = fake()->numberBetween(1, 10);
                $reviewTitle = fake()->randomElement($reviewTitles);
                
                // Generar pros y cons más realistas
                $pros = fake()->optional(0.8)->randomElement($prosOptions);
                $cons = fake()->optional(0.4)->randomElement($consOptions);
                
                // Ajustar pros y cons según la calificación
                if ($rating >= 4.0) {
                    $pros = fake()->randomElement($prosOptions);
                    $cons = fake()->optional(0.2)->randomElement($consOptions); // Menos contras para calificaciones altas
                } elseif ($rating <= 2.0) {
                    $pros = fake()->optional(0.3)->randomElement($prosOptions); // Menos pros para calificaciones bajas
                    $cons = fake()->randomElement($consOptions);
                }
                
                $review = BookReview::updateOrCreate(
                    [
                        'book_id' => $book->id,
                        'user_id' => $userId,
                    ],
                    [
                        'rating' => $rating,
                        'review_text' => $reviewText,
                        'title' => $reviewTitle,
                        'is_verified_purchase' => fake()->boolean(70), // 70% verificadas
                        'is_helpful' => fake()->boolean(60), // 60% útiles
                        'helpful_votes' => fake()->numberBetween(0, 100),
                        'not_helpful_votes' => fake()->numberBetween(0, 30),
                        'pros' => $pros ? json_encode($pros) : null,
                        'cons' => $cons ? json_encode($cons) : null,
                        'is_public' => fake()->boolean(85), // 85% públicas
                    ]
                );

                $count++;
            }
        }

        $this->command->info("✅ Creadas {$count} reseñas de libros");
        $this->showStatistics();
    }

    /**
     * Determinar el sentimiento basado en la calificación.
     */
    private function getSentimentFromRating(float $rating): string
    {
        if ($rating >= 4.0) {
            return 'positive';
        } elseif ($rating >= 3.0) {
            return 'neutral';
        } elseif ($rating >= 2.0) {
            return 'mixed';
        } else {
            return 'negative';
        }
    }

    /**
     * Mostrar estadísticas de las reseñas creadas.
     */
    private function showStatistics(): void
    {
        $stats = [
            'Total reseñas' => BookReview::count(),
            'Reseñas verificadas' => BookReview::where('is_verified_purchase', true)->count(),
            'Reseñas útiles' => BookReview::where('is_helpful', true)->count(),
            'Reseñas públicas' => BookReview::where('is_public', true)->count(),
            'Con pros' => BookReview::whereNotNull('pros')->count(),
            'Con contras' => BookReview::whereNotNull('cons')->count(),
            'Con votos útiles' => BookReview::where('helpful_votes', '>', 0)->count(),
            'Con votos no útiles' => BookReview::where('not_helpful_votes', '>', 0)->count(),
        ];

        $this->command->info("\n📊 Estadísticas de reseñas:");
        foreach ($stats as $type => $count) {
            $this->command->info("   {$type}: {$count}");
        }

        // Calificaciones promedio por libro
        $avgByBook = BookReview::selectRaw('book_id, AVG(rating) as avg_rating, COUNT(*) as count')
                               ->groupBy('book_id')
                               ->orderBy('avg_rating', 'desc')
                               ->limit(5)
                               ->get();

        if ($avgByBook->isNotEmpty()) {
            $this->command->info("\n⭐ Calificaciones promedio por libro:");
            foreach ($avgByBook as $book) {
                $bookTitle = Book::find($book->book_id);
                $title = $bookTitle ? $bookTitle->title : "Libro {$book->book_id}";
                $this->command->info("   {$title}: " . number_format($book->avg_rating, 1) . " ({$book->count} reseñas)");
            }
        }

        // Reseñas más útiles
        $helpfulReviews = BookReview::where('helpful_votes', '>', 0)
                                   ->orderByRaw('(helpful_votes / (helpful_votes + not_helpful_votes)) DESC')
                                   ->limit(3)
                                   ->get();

        if ($helpfulReviews->isNotEmpty()) {
            $this->command->info("\n👍 Reseñas más útiles:");
            foreach ($helpfulReviews as $review) {
                $totalVotes = $review->helpful_votes + $review->not_helpful_votes;
                $percentage = round(($review->helpful_votes / $totalVotes) * 100, 1);
                $this->command->info("   {$review->title}: {$percentage}% útil ({$review->helpful_votes}/{$totalVotes})");
            }
        }

        // Estadísticas de calificaciones
        $ratingStats = BookReview::selectRaw('rating, COUNT(*) as count')
                                ->groupBy('rating')
                                ->orderBy('rating', 'desc')
                                ->get();

        if ($ratingStats->isNotEmpty()) {
            $this->command->info("\n⭐ Distribución de calificaciones:");
            foreach ($ratingStats as $stat) {
                $stars = str_repeat('⭐', (int)$stat->rating);
                $this->command->info("   {$stars} {$stat->rating}/5: {$stat->count} reseñas");
            }
        }

        // Estadísticas de pros y cons
        $withPros = BookReview::whereNotNull('pros')->count();
        $withCons = BookReview::whereNotNull('cons')->count();
        $this->command->info("\n📝 Contenido adicional:");
        $this->command->info("   Con pros: {$withPros} reseñas");
        $this->command->info("   Con contras: {$withCons} reseñas");

        // Estadísticas por libro
        $bookStats = BookReview::selectRaw('book_id, COUNT(*) as reviews_count, AVG(rating) as avg_rating')
                               ->groupBy('book_id')
                               ->orderBy('reviews_count', 'desc')
                               ->limit(3)
                               ->get();

        if ($bookStats->isNotEmpty()) {
            $this->command->info("\n📚 Libros más reseñados:");
            foreach ($bookStats as $stat) {
                $book = Book::find($stat->book_id);
                $bookTitle = $book ? $book->title : "Libro {$stat->book_id}";
                $avgRating = number_format($stat->avg_rating, 1);
                $this->command->info("   {$bookTitle}: {$stat->reviews_count} reseñas, {$avgRating}/5 promedio");
            }
        }
    }
}