<?php

namespace Database\Seeders;

use App\Models\NewsArticle;
use App\Models\MediaOutlet;
use App\Models\Person;
use App\Models\Municipality;
use App\Models\Language;
use App\Models\Image;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Carbon\Carbon;

class NewsArticleSeeder extends Seeder
{
    public function run(): void
    {
        $mediaOutlets = MediaOutlet::all();
        $people = Person::all();
        $municipalities = Municipality::all();
        $languages = Language::all();
        
        if ($mediaOutlets->isEmpty()) {
            $this->command->warn('No hay medios de comunicación disponibles. No se pueden crear artículos de noticias.');
            return;
        }

        $newsArticles = [
            // NOTICIAS DE ENERGÍA RENOVABLE
            [
                'title' => 'España alcanza el 50% de energía renovable en 2024',
                'category' => 'energia',
                'topic_focus' => 'energia_renovable',
                'article_type' => 'noticia',
                'summary' => 'El país supera las expectativas en generación de energía limpia, liderando la transición energética en Europa.',
                'content' => 'España ha logrado un hito histórico al alcanzar el 50% de su generación eléctrica a partir de fuentes renovables durante el primer semestre de 2024. Este logro representa un avance significativo hacia los objetivos climáticos establecidos por la Unión Europea...',
                'is_outstanding' => true,
                'is_verified' => true,
                'is_breaking_news' => false,
                'status' => 'published',
                'visibility' => 'public',
                'geo_scope' => 'nacional',
                'sustainability_topics' => ['energia_renovable', 'cambio_climatico'],
                'environmental_impact_score' => 9.5,
            ],
            [
                'title' => 'Nueva planta solar en Andalucía generará energía para 50.000 hogares',
                'category' => 'energia',
                'topic_focus' => 'energia_solar',
                'article_type' => 'reportaje',
                'summary' => 'La instalación fotovoltaica más grande de la región comenzará operaciones en 2025.',
                'content' => 'Andalucía se consolida como líder en energía solar con la construcción de una nueva planta fotovoltaica que generará 200 MW de potencia...',
                'is_outstanding' => true,
                'is_verified' => true,
                'is_breaking_news' => false,
                'status' => 'published',
                'visibility' => 'public',
                'geo_scope' => 'regional',
                'sustainability_topics' => ['energia_renovable', 'energia_solar'],
                'environmental_impact_score' => 9.0,
            ],
            [
                'title' => 'Eólica marina: España apuesta por el mar como fuente de energía',
                'category' => 'energia',
                'topic_focus' => 'energia_eolica',
                'article_type' => 'analisis',
                'summary' => 'El desarrollo de parques eólicos marinos podría triplicar la capacidad eólica del país.',
                'content' => 'La energía eólica marina representa una oportunidad única para España de aprovechar su extensa costa y vientos constantes...',
                'is_outstanding' => false,
                'is_verified' => true,
                'is_breaking_news' => false,
                'status' => 'published',
                'visibility' => 'public',
                'geo_scope' => 'nacional',
                'sustainability_topics' => ['energia_renovable', 'energia_eolica'],
                'environmental_impact_score' => 8.5,
            ],

            // NOTICIAS DE SOSTENIBILIDAD
            [
                'title' => 'Madrid implementa sistema de recogida de residuos inteligente',
                'category' => 'sostenibilidad',
                'topic_focus' => 'economia_circular',
                'article_type' => 'noticia',
                'summary' => 'La capital española estrena contenedores inteligentes para mejorar el reciclaje.',
                'content' => 'Madrid da un paso adelante en la gestión sostenible de residuos con la implementación de un sistema inteligente de recogida...',
                'is_outstanding' => false,
                'is_verified' => true,
                'is_breaking_news' => false,
                'status' => 'published',
                'visibility' => 'public',
                'geo_scope' => 'local',
                'sustainability_topics' => ['economia_circular', 'reciclaje'],
                'environmental_impact_score' => 7.5,
            ],
            [
                'title' => 'Barcelona: 100% transporte público eléctrico para 2030',
                'category' => 'sostenibilidad',
                'topic_focus' => 'transporte_sostenible',
                'article_type' => 'reportaje',
                'summary' => 'La ciudad condal se compromete a eliminar las emisiones del transporte público.',
                'content' => 'Barcelona ha anunciado un ambicioso plan para convertir toda su flota de transporte público en eléctrica...',
                'is_outstanding' => true,
                'is_verified' => true,
                'is_breaking_news' => false,
                'status' => 'published',
                'visibility' => 'public',
                'geo_scope' => 'local',
                'sustainability_topics' => ['transporte_sostenible', 'cambio_climatico'],
                'environmental_impact_score' => 8.0,
            ],

            // NOTICIAS DE MEDIO AMBIENTE
            [
                'title' => 'Restauración del río Ebro: proyecto pionero en Europa',
                'category' => 'medio_ambiente',
                'topic_focus' => 'biodiversidad',
                'article_type' => 'entrevista',
                'summary' => 'Entrevista con expertos sobre la rehabilitación del ecosistema fluvial.',
                'content' => 'El proyecto de restauración del río Ebro representa uno de los esfuerzos más ambiciosos de rehabilitación fluvial en Europa...',
                'is_outstanding' => false,
                'is_verified' => true,
                'is_breaking_news' => false,
                'status' => 'published',
                'visibility' => 'public',
                'geo_scope' => 'regional',
                'sustainability_topics' => ['biodiversidad', 'ecosistema'],
                'environmental_impact_score' => 9.0,
            ],
            [
                'title' => 'Alerta por sequía: embalses al 30% de capacidad',
                'category' => 'medio_ambiente',
                'topic_focus' => 'cambio_climatico',
                'article_type' => 'noticia',
                'summary' => 'La falta de lluvias pone en riesgo el suministro de agua en varias regiones.',
                'content' => 'Los embalses españoles se encuentran al 30% de su capacidad, un nivel crítico que preocupa a las autoridades...',
                'is_outstanding' => true,
                'is_verified' => true,
                'is_breaking_news' => true,
                'status' => 'published',
                'visibility' => 'public',
                'geo_scope' => 'nacional',
                'sustainability_topics' => ['cambio_climatico', 'sequia'],
                'environmental_impact_score' => 8.5,
            ],

            // NOTICIAS DE TECNOLOGÍA SOSTENIBLE
            [
                'title' => 'Startup española desarrolla baterías de grafeno para vehículos eléctricos',
                'category' => 'tecnologia',
                'topic_focus' => 'energia_renovable',
                'article_type' => 'reportaje',
                'summary' => 'Innovación en almacenamiento energético que podría revolucionar la movilidad eléctrica.',
                'content' => 'Una startup española ha desarrollado una tecnología revolucionaria de baterías basada en grafeno...',
                'is_outstanding' => false,
                'is_verified' => true,
                'is_breaking_news' => false,
                'status' => 'published',
                'visibility' => 'public',
                'geo_scope' => 'nacional',
                'sustainability_topics' => ['energia_renovable', 'transporte_sostenible'],
                'environmental_impact_score' => 7.0,
            ],

            // NOTICIAS DE ECONOMÍA SOSTENIBLE
            [
                'title' => 'Fondos de inversión verdes crecen 150% en España',
                'category' => 'economia',
                'topic_focus' => 'finanzas_sostenibles',
                'article_type' => 'analisis',
                'summary' => 'El mercado de inversiones sostenibles experimenta un auge sin precedentes.',
                'content' => 'Los fondos de inversión con criterios ESG (Environmental, Social, Governance) han experimentado un crecimiento del 150%...',
                'is_outstanding' => false,
                'is_verified' => true,
                'is_breaking_news' => false,
                'status' => 'published',
                'visibility' => 'public',
                'geo_scope' => 'nacional',
                'sustainability_topics' => ['finanzas_sostenibles'],
                'environmental_impact_score' => 6.0,
            ],

            // NOTICIAS DE POLÍTICA AMBIENTAL
            [
                'title' => 'Nueva ley de cambio climático: objetivos más ambiciosos para 2030',
                'category' => 'politica',
                'topic_focus' => 'cambio_climatico',
                'article_type' => 'noticia',
                'summary' => 'El gobierno aprueba medidas más estrictas para reducir emisiones de CO2.',
                'content' => 'El Consejo de Ministros ha aprobado una nueva ley de cambio climático que establece objetivos más ambiciosos...',
                'is_outstanding' => true,
                'is_verified' => true,
                'is_breaking_news' => true,
                'status' => 'published',
                'visibility' => 'public',
                'geo_scope' => 'nacional',
                'sustainability_topics' => ['cambio_climatico', 'politica_ambiental'],
                'environmental_impact_score' => 9.0,
            ],

            // NOTICIAS LOCALES
            [
                'title' => 'Valencia instala 500 puntos de recarga para vehículos eléctricos',
                'category' => 'sostenibilidad',
                'topic_focus' => 'transporte_sostenible',
                'article_type' => 'noticia',
                'summary' => 'La ciudad del Turia se convierte en referente de movilidad eléctrica.',
                'content' => 'Valencia ha completado la instalación de 500 puntos de recarga para vehículos eléctricos...',
                'is_outstanding' => false,
                'is_verified' => true,
                'is_breaking_news' => false,
                'status' => 'published',
                'visibility' => 'public',
                'geo_scope' => 'local',
                'sustainability_topics' => ['transporte_sostenible'],
                'environmental_impact_score' => 7.5,
            ],

            // NOTICIAS INTERNACIONALES
            [
                'title' => 'COP29: España lidera coalición europea para financiación climática',
                'category' => 'politica',
                'topic_focus' => 'cambio_climatico',
                'article_type' => 'reportaje',
                'summary' => 'El país asume un papel protagonista en la próxima cumbre climática.',
                'content' => 'España ha asumido el liderazgo de una coalición europea que buscará aumentar la financiación climática...',
                'is_outstanding' => true,
                'is_verified' => true,
                'is_breaking_news' => false,
                'status' => 'published',
                'visibility' => 'public',
                'geo_scope' => 'internacional',
                'sustainability_topics' => ['cambio_climatico', 'politica_ambiental'],
                'environmental_impact_score' => 8.5,
            ],

            // NOTICIAS DE INVESTIGACIÓN
            [
                'title' => 'CSIC descubre nueva especie de algas para biocombustibles',
                'category' => 'tecnologia',
                'topic_focus' => 'energia_renovable',
                'article_type' => 'reportaje',
                'summary' => 'Investigadores españoles desarrollan biocombustibles de tercera generación.',
                'content' => 'Científicos del CSIC han descubierto una nueva especie de algas que podría revolucionar la producción de biocombustibles...',
                'is_outstanding' => false,
                'is_verified' => true,
                'is_breaking_news' => false,
                'status' => 'published',
                'visibility' => 'public',
                'geo_scope' => 'nacional',
                'sustainability_topics' => ['energia_renovable', 'biocombustibles'],
                'environmental_impact_score' => 8.0,
            ],

            // NOTICIAS DE AGRICULTURA SOSTENIBLE
            [
                'title' => 'Agricultores de La Rioja adoptan técnicas de permacultura',
                'category' => 'sostenibilidad',
                'topic_focus' => 'agricultura_sostenible',
                'article_type' => 'entrevista',
                'summary' => 'La región pionera en agricultura regenerativa y sostenible.',
                'content' => 'Los agricultores de La Rioja están adoptando técnicas de permacultura y agricultura regenerativa...',
                'is_outstanding' => false,
                'is_verified' => true,
                'is_breaking_news' => false,
                'status' => 'published',
                'visibility' => 'public',
                'geo_scope' => 'regional',
                'sustainability_topics' => ['agricultura_sostenible', 'permacultura'],
                'environmental_impact_score' => 7.0,
            ],

            // NOTICIAS DE CONSTRUCCIÓN SOSTENIBLE
            [
                'title' => 'Edificio de madera más alto de España se construye en Bilbao',
                'category' => 'sostenibilidad',
                'topic_focus' => 'construccion_sostenible',
                'article_type' => 'reportaje',
                'summary' => 'La capital vizcaína apuesta por la construcción sostenible con materiales renovables.',
                'content' => 'Bilbao se convierte en pionera de la construcción sostenible con la edificación del edificio de madera más alto de España...',
                'is_outstanding' => true,
                'is_verified' => true,
                'is_breaking_news' => false,
                'status' => 'published',
                'visibility' => 'public',
                'geo_scope' => 'local',
                'sustainability_topics' => ['construccion_sostenible', 'materiales_renovables'],
                'environmental_impact_score' => 8.0,
            ],

            // NOTICIAS DE TURISMO SOSTENIBLE
            [
                'title' => 'Islas Canarias: 100% energía renovable para 2030',
                'category' => 'turismo',
                'topic_focus' => 'energia_renovable',
                'article_type' => 'noticia',
                'summary' => 'El archipiélago se compromete a ser autosuficiente energéticamente.',
                'content' => 'Las Islas Canarias han anunciado un plan ambicioso para alcanzar el 100% de energía renovable...',
                'is_outstanding' => false,
                'is_verified' => true,
                'is_breaking_news' => false,
                'status' => 'published',
                'visibility' => 'public',
                'geo_scope' => 'regional',
                'sustainability_topics' => ['energia_renovable', 'autosuficiencia'],
                'environmental_impact_score' => 9.0,
            ],

            // NOTICIAS DE EDUCACIÓN AMBIENTAL
            [
                'title' => 'Programa escolar de concienciación climática llega a 100.000 estudiantes',
                'category' => 'educacion',
                'topic_focus' => 'cambio_climatico',
                'article_type' => 'reportaje',
                'summary' => 'Iniciativa educativa para formar a las nuevas generaciones en sostenibilidad.',
                'content' => 'Un programa pionero de concienciación climática ha llegado a más de 100.000 estudiantes en toda España...',
                'is_outstanding' => false,
                'is_verified' => true,
                'is_breaking_news' => false,
                'status' => 'published',
                'visibility' => 'public',
                'geo_scope' => 'nacional',
                'sustainability_topics' => ['cambio_climatico', 'educacion_ambiental'],
                'environmental_impact_score' => 7.5,
            ],

            // NOTICIAS DE SALUD AMBIENTAL
            [
                'title' => 'Estudio: Mejora de la calidad del aire reduce enfermedades respiratorias',
                'category' => 'salud',
                'topic_focus' => 'calidad_aire',
                'article_type' => 'analisis',
                'summary' => 'Investigación confirma beneficios de las políticas ambientales en la salud pública.',
                'content' => 'Un estudio realizado en varias ciudades españolas confirma que la mejora de la calidad del aire...',
                'is_outstanding' => false,
                'is_verified' => true,
                'is_breaking_news' => false,
                'status' => 'published',
                'visibility' => 'public',
                'geo_scope' => 'nacional',
                'sustainability_topics' => ['calidad_aire', 'salud_publica'],
                'environmental_impact_score' => 8.5,
            ],

            // NOTICIAS DE INNOVACIÓN SOCIAL
            [
                'title' => 'Cooperativa energética de Málaga: energía 100% renovable y local',
                'category' => 'sostenibilidad',
                'topic_focus' => 'energia_renovable',
                'article_type' => 'entrevista',
                'summary' => 'Modelo cooperativo que democratiza la energía renovable.',
                'content' => 'Una cooperativa energética en Málaga está demostrando que es posible generar energía 100% renovable...',
                'is_outstanding' => true,
                'is_verified' => true,
                'is_breaking_news' => false,
                'status' => 'published',
                'visibility' => 'public',
                'geo_scope' => 'local',
                'sustainability_topics' => ['energia_renovable', 'cooperativismo'],
                'environmental_impact_score' => 8.5,
            ],
        ];

        $createdArticles = [];
        $articleCount = 0;

        foreach ($newsArticles as $articleData) {
            $mediaOutlet = $mediaOutlets->random();
            $author = $people->random();
            $municipality = $municipalities->random();
            $language = $languages->random();
            
            $publishedAt = Carbon::now()->subDays(rand(1, 365));
            $featuredStart = $articleData['is_outstanding'] ? $publishedAt->copy()->addDays(rand(1, 30)) : null;
            $featuredEnd = $featuredStart ? $featuredStart->copy()->addDays(rand(30, 90)) : null;
            
            $wordCount = rand(800, 2500);
            $readingTime = ceil($wordCount / 250);
            
            $viewsCount = rand(100, 10000);
            $sharesCount = rand(10, 500);
            $commentsCount = rand(0, 100);
            
            $sentimentScore = (rand(-100, 100) / 100);
            $sentimentLabel = $sentimentScore >= 0.3 ? 'positivo' : ($sentimentScore <= -0.3 ? 'negativo' : 'neutral');
            
            $article = NewsArticle::create([
                'title' => $articleData['title'],
                'slug' => Str::slug($articleData['title']),
                'summary' => $articleData['summary'],
                'content' => $this->generateContent($articleData['content'], $wordCount),
                'excerpt' => $articleData['summary'],
                'source_url' => 'https://example.com/noticia-' . Str::random(8),
                'original_title' => null,
                'published_at' => $publishedAt,
                'featured_start' => $featuredStart,
                'featured_end' => $featuredEnd,
                'media_outlet_id' => $mediaOutlet->id,
                'author_id' => $author->id,
                'municipality_id' => $municipality->id,
                'language_id' => $language->id,
                'image_id' => null,
                'category' => $articleData['category'],
                'topic_focus' => $articleData['topic_focus'],
                'article_type' => $articleData['article_type'],
                'is_outstanding' => $articleData['is_outstanding'],
                'is_verified' => $articleData['is_verified'],
                'is_scraped' => true,
                'is_translated' => false,
                'is_breaking_news' => $articleData['is_breaking_news'],
                'is_evergreen' => $articleData['article_type'] === 'analisis',
                'visibility' => $articleData['visibility'],
                'status' => $articleData['status'],
                'views_count' => $viewsCount,
                'shares_count' => $sharesCount,
                'comments_count' => $commentsCount,
                'reading_time_minutes' => $readingTime,
                'word_count' => $wordCount,
                'sentiment_score' => $sentimentScore,
                'sentiment_label' => $sentimentLabel,
                'keywords' => $this->generateKeywords($articleData['title'], $articleData['summary']),
                'entities' => $this->generateEntities($articleData['title'], $articleData['summary']),
                'sustainability_topics' => $articleData['sustainability_topics'],
                'environmental_impact_score' => $articleData['environmental_impact_score'],
                'related_co2_data' => $this->generateCO2Data($articleData['category']),
                'geo_scope' => $articleData['geo_scope'],
                'latitude' => $municipality->latitude ?? null,
                'longitude' => $municipality->longitude ?? null,
                'seo_title' => $articleData['title'],
                'seo_description' => $articleData['summary'],
                'social_media_meta' => [
                    'twitter_card' => 'summary_large_image',
                    'og_type' => 'article',
                    'og_image' => null,
                ],
                'scraped_at' => $publishedAt->copy()->subHours(rand(1, 24)),
                'last_engagement_at' => $publishedAt->copy()->addDays(rand(1, 30)),
            ]);

            $createdArticles[] = [
                'id' => $article->id,
                'title' => $article->title,
                'category' => $article->category,
                'media_outlet' => $mediaOutlet->name,
                'author' => $author->name,
                'municipality' => $municipality->name,
                'status' => $article->status,
                'views' => $article->views_count,
                'published' => $article->published_at->format('d/m/Y'),
                'impact_score' => $article->environmental_impact_score,
            ];
            
            $articleCount++;
        }

        $this->command->info("Se han creado {$articleCount} artículos de noticias.");
        $this->command->table(
            ['ID', 'Título', 'Categoría', 'Medio', 'Autor', 'Municipio', 'Estado', 'Vistas', 'Publicado', 'Impacto'],
            array_slice($createdArticles, 0, 15)
        );
        
        if (count($createdArticles) > 15) {
            $this->command->info("... y " . (count($createdArticles) - 15) . " artículos más.");
        }

        $this->showStatistics($createdArticles);
        $this->command->info('✅ Seeder de NewsArticle completado exitosamente.');
    }

    private function generateContent(string $baseContent, int $wordCount): string
    {
        $paragraphs = [
            'La transición hacia un modelo energético más sostenible requiere el compromiso de todos los sectores de la sociedad.',
            'Los expertos coinciden en que la implementación de tecnologías renovables es fundamental para alcanzar los objetivos climáticos.',
            'La inversión en infraestructura verde no solo beneficia al medio ambiente, sino que también genera empleo y desarrollo económico.',
            'La colaboración entre el sector público y privado es esencial para acelerar la adopción de soluciones sostenibles.',
            'La educación y concienciación ciudadana juegan un papel crucial en la transición energética.',
            'Los avances tecnológicos están haciendo que las energías renovables sean cada vez más competitivas.',
            'La descentralización de la generación energética empodera a las comunidades locales.',
            'La eficiencia energética es tan importante como la generación de energía renovable.',
            'La biodiversidad y la conservación de ecosistemas son fundamentales para el equilibrio planetario.',
            'La economía circular representa un cambio de paradigma en la gestión de recursos.',
        ];

        $content = $baseContent;
        $currentWordCount = str_word_count(strip_tags($content));

        while ($currentWordCount < $wordCount) {
            $paragraph = $paragraphs[array_rand($paragraphs)];
            $content .= "\n\n" . $paragraph;
            $currentWordCount = str_word_count(strip_tags($content));
        }

        return $content;
    }

    private function generateKeywords(string $title, string $summary): array
    {
        $baseKeywords = ['sostenibilidad', 'energía', 'medio ambiente', 'cambio climático', 'renovables'];
        $titleKeywords = explode(' ', strtolower($title));
        $summaryKeywords = explode(' ', strtolower($summary));
        
        $allKeywords = array_merge($baseKeywords, $titleKeywords, $summaryKeywords);
        $filteredKeywords = array_filter($allKeywords, function($word) {
            return strlen($word) > 3 && !in_array($word, ['para', 'con', 'los', 'las', 'una', 'por', 'que', 'del', 'está', 'este', 'esta']);
        });
        
        return array_slice(array_unique($filteredKeywords), 0, 10);
    }

    private function generateEntities(string $title, string $summary): array
    {
        $entities = [];
        
        if (str_contains(strtolower($title), 'españa') || str_contains(strtolower($summary), 'españa')) {
            $entities[] = ['type' => 'country', 'name' => 'España', 'confidence' => 0.95];
        }
        
        if (str_contains(strtolower($title), 'madrid') || str_contains(strtolower($summary), 'madrid')) {
            $entities[] = ['type' => 'city', 'name' => 'Madrid', 'confidence' => 0.90];
        }
        
        if (str_contains(strtolower($title), 'barcelona') || str_contains(strtolower($summary), 'barcelona')) {
            $entities[] = ['type' => 'city', 'name' => 'Barcelona', 'confidence' => 0.90];
        }
        
        if (str_contains(strtolower($title), 'valencia') || str_contains(strtolower($summary), 'valencia')) {
            $entities[] = ['type' => 'city', 'name' => 'Valencia', 'confidence' => 0.90];
        }
        
        if (str_contains(strtolower($title), 'energía') || str_contains(strtolower($summary), 'energía')) {
            $entities[] = ['type' => 'concept', 'name' => 'Energía Renovable', 'confidence' => 0.85];
        }
        
        return $entities;
    }

    private function generateCO2Data(string $category): array
    {
        $co2Data = [
            'category_impact' => $category,
            'estimated_savings' => rand(100, 1000),
            'equivalent_trees' => rand(10, 100),
            'carbon_footprint' => rand(50, 500),
        ];
        
        if ($category === 'energia') {
            $co2Data['energy_savings_kwh'] = rand(1000, 10000);
            $co2Data['co2_reduction_kg'] = rand(500, 5000);
        }
        
        return $co2Data;
    }

    private function showStatistics(array $articles): void
    {
        $categories = collect($articles)->groupBy('category')->map->count();
        $statuses = collect($articles)->groupBy('status')->map->count();
        $outstandingCount = collect($articles)->filter(fn($a) => $a['views'] > 1000)->count();
        $avgImpact = collect($articles)->avg('impact_score');
        
        $this->command->info("\n📊 Estadísticas del Seeder:");
        $this->command->info("   • Categorías: " . $categories->map(fn($count, $cat) => "{$cat}: {$count}")->join(', '));
        $this->command->info("   • Estados: " . $statuses->map(fn($count, $status) => "{$status}: {$count}")->join(', '));
        $this->command->info("   • Artículos destacados: {$outstandingCount}");
        $this->command->info("   • Impacto ambiental promedio: " . round($avgImpact, 1) . "/10");
    }
}