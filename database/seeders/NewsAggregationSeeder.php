<?php

namespace Database\Seeders;

use App\Models\NewsAggregation;
use App\Models\NewsSource;
use App\Models\NewsArticle;
use Illuminate\Database\Seeder;

class NewsAggregationSeeder extends Seeder
{
    public function run(): void
    {
        $newsSources = NewsSource::all();
        $newsArticles = NewsArticle::all();

        if ($newsSources->isEmpty()) {
            $this->command->info('No hay fuentes de noticias disponibles. Ejecuta NewsSourceSeeder primero.');
            return;
        }

        if ($newsArticles->isEmpty()) {
            $this->command->info('No hay artículos de noticias disponibles. Ejecuta NewsArticleSeeder primero.');
            return;
        }

        $aggregations = [
            [
                'source_id' => $newsSources->random()->id,
                'article_id' => $newsArticles->random()->id,
                'aggregated_at' => now()->subHours(2),
                'processing_status' => 'completed',
                'duplicate_check' => false,
                'quality_score' => 0.95,
                'processing_metadata' => json_encode([
                    'processing_time' => '2.3s',
                    'language_detected' => 'es',
                    'sentiment_score' => 0.3,
                    'key_topics' => ['energías renovables', 'sostenibilidad'],
                    'word_count' => 450,
                    'reading_time' => '3 minutos',
                    'complexity_score' => 0.7
                ]),
                'processed_at' => now()->subHours(1),
                'processing_notes' => 'Artículo procesado exitosamente, alta calidad de contenido sobre energías renovables',
            ],
            [
                'source_id' => $newsSources->random()->id,
                'article_id' => $newsArticles->random()->id,
                'aggregated_at' => now()->subHours(4),
                'processing_status' => 'completed',
                'duplicate_check' => false,
                'quality_score' => 0.87,
                'processing_metadata' => json_encode([
                    'processing_time' => '1.8s',
                    'language_detected' => 'es',
                    'sentiment_score' => 0.1,
                    'key_topics' => ['mercado energético', 'precios'],
                    'word_count' => 320,
                    'reading_time' => '2 minutos',
                    'complexity_score' => 0.6
                ]),
                'processed_at' => now()->subHours(3),
                'processing_notes' => 'Artículo procesado, contenido relevante para el mercado energético español',
            ],
            [
                'source_id' => $newsSources->random()->id,
                'article_id' => $newsArticles->random()->id,
                'aggregated_at' => now()->subHours(6),
                'processing_status' => 'completed',
                'duplicate_check' => false,
                'quality_score' => 0.92,
                'processing_metadata' => json_encode([
                    'processing_time' => '2.1s',
                    'language_detected' => 'es',
                    'sentiment_score' => 0.6,
                    'key_topics' => ['tecnología verde', 'innovación'],
                    'word_count' => 580,
                    'reading_time' => '4 minutos',
                    'complexity_score' => 0.8
                ]),
                'processed_at' => now()->subHours(5),
                'processing_notes' => 'Artículo de alta calidad sobre innovación tecnológica en el sector energético',
            ],
            [
                'source_id' => $newsSources->random()->id,
                'article_id' => $newsArticles->random()->id,
                'aggregated_at' => now()->subHours(8),
                'processing_status' => 'completed',
                'duplicate_check' => false,
                'quality_score' => 0.78,
                'processing_metadata' => json_encode([
                    'processing_time' => '1.5s',
                    'language_detected' => 'es',
                    'sentiment_score' => 0.4,
                    'key_topics' => ['Andalucía', 'energía solar'],
                    'word_count' => 280,
                    'reading_time' => '2 minutos',
                    'complexity_score' => 0.5
                ]),
                'processed_at' => now()->subHours(7),
                'processing_notes' => 'Artículo regional procesado, contenido específico sobre energía solar en Andalucía',
            ],
            [
                'source_id' => $newsSources->random()->id,
                'article_id' => $newsArticles->random()->id,
                'aggregated_at' => now()->subHours(12),
                'processing_status' => 'completed',
                'duplicate_check' => false,
                'quality_score' => 0.89,
                'processing_metadata' => json_encode([
                    'processing_time' => '2.0s',
                    'language_detected' => 'es',
                    'sentiment_score' => 0.2,
                    'key_topics' => ['políticas energéticas', 'regulación'],
                    'word_count' => 650,
                    'reading_time' => '5 minutos',
                    'complexity_score' => 0.9
                ]),
                'processed_at' => now()->subHours(11),
                'processing_notes' => 'Artículo político procesado, análisis detallado de regulaciones energéticas',
            ],
            [
                'source_id' => $newsSources->random()->id,
                'article_id' => $newsArticles->random()->id,
                'aggregated_at' => now()->subHours(1),
                'processing_status' => 'processing',
                'duplicate_check' => false,
                'quality_score' => null,
                'processing_metadata' => json_encode([
                    'processing_time' => '0.8s',
                    'language_detected' => 'es',
                    'status' => 'en proceso',
                    'current_step' => 'análisis de sentimiento'
                ]),
                'processed_at' => null,
                'processing_notes' => 'Artículo en proceso de análisis, pendiente de completar el procesamiento',
            ],
            [
                'source_id' => $newsSources->random()->id,
                'article_id' => $newsArticles->random()->id,
                'aggregated_at' => now()->subHours(24),
                'processing_status' => 'completed',
                'duplicate_check' => true,
                'quality_score' => 0.76,
                'processing_metadata' => json_encode([
                    'processing_time' => '1.9s',
                    'language_detected' => 'es',
                    'sentiment_score' => -0.3,
                    'key_topics' => ['crisis energética', 'precios'],
                    'word_count' => 420,
                    'reading_time' => '3 minutos',
                    'complexity_score' => 0.6,
                    'duplicate_check_result' => 'no_duplicate'
                ]),
                'processed_at' => now()->subHours(23),
                'processing_notes' => 'Artículo procesado, verificado como no duplicado, contenido sobre crisis energética',
            ],
            [
                'source_id' => $newsSources->random()->id,
                'article_id' => $newsArticles->random()->id,
                'aggregated_at' => now()->subHours(36),
                'processing_status' => 'completed',
                'duplicate_check' => false,
                'quality_score' => 0.94,
                'processing_metadata' => json_encode([
                    'processing_time' => '2.2s',
                    'language_detected' => 'es',
                    'sentiment_score' => 0.5,
                    'key_topics' => ['investigación', 'desarrollo'],
                    'word_count' => 720,
                    'reading_time' => '6 minutos',
                    'complexity_score' => 0.9
                ]),
                'processed_at' => now()->subHours(35),
                'processing_notes' => 'Artículo científico de alta calidad procesado, contenido sobre investigación energética',
            ],
            [
                'source_id' => $newsSources->random()->id,
                'article_id' => $newsArticles->random()->id,
                'aggregated_at' => now()->subHours(48),
                'processing_status' => 'failed',
                'duplicate_check' => false,
                'quality_score' => null,
                'processing_metadata' => json_encode([
                    'processing_time' => '0.5s',
                    'language_detected' => 'es',
                    'error' => 'timeout_error',
                    'error_message' => 'Procesamiento interrumpido por timeout'
                ]),
                'processed_at' => null,
                'processing_notes' => 'Error en el procesamiento: timeout durante el análisis de contenido',
            ],
            [
                'source_id' => $newsSources->random()->id,
                'article_id' => $newsArticles->random()->id,
                'aggregated_at' => now()->subHours(72),
                'processing_status' => 'completed',
                'duplicate_check' => true,
                'quality_score' => 0.82,
                'processing_metadata' => json_encode([
                    'processing_time' => '1.7s',
                    'language_detected' => 'es',
                    'sentiment_score' => 0.1,
                    'key_topics' => ['eficiencia energética', 'ahorro'],
                    'word_count' => 380,
                    'reading_time' => '3 minutos',
                    'complexity_score' => 0.7,
                    'duplicate_check_result' => 'no_duplicate'
                ]),
                'processed_at' => now()->subHours(71),
                'processing_notes' => 'Artículo procesado exitosamente, verificado como no duplicado, contenido sobre eficiencia energética',
            ],
        ];

        foreach ($aggregations as $aggregation) {
            NewsAggregation::firstOrCreate(
                [
                    'source_id' => $aggregation['source_id'],
                    'article_id' => $aggregation['article_id'],
                ],
                $aggregation
            );
        }

        $this->command->info('✅ Creadas ' . count($aggregations) . ' agregaciones de noticias');
    }
}