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
                    'key_topics' => ['energías renovables', 'sostenibilidad']
                ]),
                'processed_at' => now()->subHours(1),
                'processing_notes' => 'Artículo procesado exitosamente, alta calidad de contenido',
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
                    'key_topics' => ['mercado energético', 'precios']
                ]),
                'processed_at' => now()->subHours(3),
                'processing_notes' => 'Artículo procesado, contenido relevante para el mercado',
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
                    'key_topics' => ['tecnología verde', 'innovación']
                ]),
                'processed_at' => now()->subHours(5),
                'processing_notes' => 'Artículo de alta calidad sobre innovación tecnológica',
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
                    'key_topics' => ['Andalucía', 'energía solar']
                ]),
                'processed_at' => now()->subHours(7),
                'processing_notes' => 'Artículo regional procesado, contenido específico de Andalucía',
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
                    'key_topics' => ['políticas energéticas', 'regulación']
                ]),
                'processed_at' => now()->subHours(11),
                'processing_notes' => 'Artículo político procesado, análisis de regulaciones',
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
                    'status' => 'en proceso'
                ]),
                'processed_at' => null,
                'processing_notes' => 'Artículo en proceso de análisis',
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
                    'key_topics' => ['crisis energética', 'precios']
                ]),
                'processed_at' => now()->subHours(23),
                'processing_notes' => 'Artículo procesado, verificado como no duplicado',
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
                    'key_topics' => ['investigación', 'desarrollo']
                ]),
                'processed_at' => now()->subHours(35),
                'processing_notes' => 'Artículo científico de alta calidad procesado',
            ],
        ];

        foreach ($aggregations as $aggregation) {
            NewsAggregation::create($aggregation);
        }

        $this->command->info('✅ Creadas ' . count($aggregations) . ' agregaciones de noticias');
    }
}
