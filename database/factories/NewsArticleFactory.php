<?php

namespace Database\Factories;

use App\Models\NewsArticle;
use App\Models\Person;
use App\Models\MediaOutlet;
use App\Models\Image;
use App\Models\Municipality;
use App\Models\Language;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Factory para generar artículos de noticias realistas.
 */
class NewsArticleFactory extends Factory
{
    protected $model = NewsArticle::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        $categories = [
            'política', 'economía', 'sociedad', 'medio ambiente', 'tecnología',
            'cultura', 'deportes', 'internacional', 'nacional', 'local',
            'sostenibilidad', 'energía', 'cambio climático', 'biodiversidad'
        ];

        $topicFocus = [
            'política nacional', 'economía verde', 'energías renovables',
            'cambio climático', 'biodiversidad', 'contaminación',
            'tecnología sostenible', 'movilidad sostenible', 'agricultura ecológica',
            'reciclaje', 'economía circular', 'conservación', 'urbanismo sostenible'
        ];

        $articleTypes = ['noticia', 'reportaje', 'entrevista', 'opinión', 'análisis', 'crónica'];
        $statuses = ['draft', 'published', 'archived'];
        $visibilities = ['public', 'premium', 'subscribers'];

        $title = $this->generateRealisticTitle();
        $slug = \Str::slug($title);
        $content = $this->generateRealisticContent($title);
        $wordCount = str_word_count(strip_tags($content));

        return [
            'title' => $title,
            'slug' => $slug,
            'summary' => $this->generateSummary($content),
            'excerpt' => $this->generateExcerpt($content),
            'content' => $content,
            'original_title' => fake()->boolean(30) ? fake()->sentence(6) : null,
            
            // Metadata del artículo
            'category' => fake()->randomElement($categories),
            'topic_focus' => fake()->randomElement($topicFocus),
            'article_type' => fake()->randomElement($articleTypes),
            'language_id' => Language::where('language', 'Español')->first()?->id ?? null,
            
            // Estado y visibilidad
            'status' => fake()->randomElement($statuses),
            'visibility' => fake()->randomElement($visibilities),
            'is_outstanding' => fake()->boolean(15),
            'is_verified' => fake()->boolean(85),
            'is_scraped' => fake()->boolean(40),
            'is_translated' => fake()->boolean(10),
            'is_breaking_news' => fake()->boolean(5),
            'is_evergreen' => fake()->boolean(20),
            
            // Fechas
            'published_at' => fake()->dateTimeBetween('-6 months', 'now'),
            'featured_start' => fake()->boolean(20) ? fake()->dateTimeBetween('-1 month', '+1 week') : null,
            'featured_end' => fake()->boolean(20) ? fake()->dateTimeBetween('+1 week', '+1 month') : null,
            'scraped_at' => fake()->boolean(40) ? fake()->dateTimeBetween('-1 month', 'now') : null,
            'last_engagement_at' => fake()->dateTimeBetween('-1 week', 'now'),
            
            // Métricas de engagement
            'views_count' => fake()->numberBetween(50, 50000),
            'shares_count' => fake()->numberBetween(0, 500),
            'comments_count' => fake()->numberBetween(0, 200),
            
            // Análisis de contenido
            'reading_time_minutes' => max(1, intval($wordCount / 200)),
            'word_count' => $wordCount,
            'sentiment_score' => fake()->randomFloat(2, -1, 1),
            'sentiment_label' => fake()->randomElement(['positivo', 'neutral', 'negativo']),
            
            // Sostenibilidad y medio ambiente
            'sustainability_topics' => json_encode($this->generateSustainabilityTopics()),
            'environmental_impact_score' => fake()->numberBetween(1, 10),
            'related_co2_data' => json_encode($this->generateCO2Data()),
            
            // Metadatos estructurados
            'keywords' => json_encode($this->generateKeywords()),
            'entities' => json_encode($this->generateEntities()),
            
            // Geolocalización
            'geo_scope' => fake()->randomElement(['local', 'regional', 'nacional', 'internacional']),
            'latitude' => fake()->boolean(60) ? fake()->latitude(35.0, 44.0) : null, // España
            'longitude' => fake()->boolean(60) ? fake()->longitude(-10.0, 5.0) : null, // España
            
            // SEO y redes sociales
            'seo_title' => $title . ' | ' . fake()->company(),
            'seo_description' => fake()->sentence(20),
            'social_media_meta' => json_encode([
                'facebook_title' => $title,
                'twitter_title' => $title,
                'og_image' => fake()->imageUrl(1200, 630),
            ]),
            
            // Relaciones (se asignarán en el seeder)
            'author_id' => null,
            'media_outlet_id' => null,
            'image_id' => null,
            'municipality_id' => fake()->boolean(60) ? Municipality::inRandomOrder()->first()?->id : null,
        ];
    }

    /**
     * Generar título realista.
     */
    private function generateRealisticTitle(): string
    {
        $templates = [
            'España aprueba nueva ley de {tema} que {accion} {impacto}',
            '{Ciudad} se convierte en la primera ciudad en {logro} {sostenibilidad}',
            'El {sector} español {verbo} un {porcentaje}% en {tema}',
            'Nuevo estudio revela {dato} sobre {tema} en {region}',
            '{Empresa} invierte {cantidad} millones en {tecnologia} sostenible',
            'La {institucion} advierte sobre {riesgo} del {problema}',
            '{Experto} propone {solucion} para combatir {problema}',
            'Madrid implementa {medida} para reducir {problema} en un {porcentaje}%',
        ];

        $replacements = [
            'tema' => ['energías renovables', 'cambio climático', 'biodiversidad', 'reciclaje', 'movilidad sostenible'],
            'accion' => ['impulsa', 'promueve', 'regula', 'incentiva', 'protege'],
            'impacto' => ['significativamente', 'en todo el territorio', 'hasta 2030', 'de forma inmediata'],
            'Ciudad' => ['Barcelona', 'Madrid', 'Valencia', 'Sevilla', 'Bilbao', 'Zaragoza'],
            'logro' => ['eliminar', 'reducir', 'implementar', 'conseguir'],
            'sostenibilidad' => ['cero emisiones', 'energía 100% renovable', 'residuo cero', 'transporte limpio'],
            'sector' => ['sector energético', 'sector agrícola', 'sector turístico', 'sector industrial'],
            'verbo' => ['crece', 'reduce', 'aumenta', 'mejora', 'transforma'],
            'porcentaje' => ['15', '25', '30', '40', '50'],
            'dato' => ['datos alarmantes', 'cifras esperanzadoras', 'tendencias preocupantes', 'avances significativos'],
            'region' => ['España', 'Cataluña', 'Andalucía', 'la Comunidad de Madrid', 'el País Vasco'],
            'Empresa' => ['Iberdrola', 'Endesa', 'Repsol', 'Naturgy', 'Acciona'],
            'cantidad' => ['100', '250', '500', '750', '1.000'],
            'tecnologia' => ['solar', 'eólica', 'hidrógeno verde', 'almacenamiento'],
            'institucion' => ['AEMET', 'MITECO', 'CSIC', 'Greenpeace', 'WWF'],
            'riesgo' => ['los efectos', 'las consecuencias', 'el impacto', 'los peligros'],
            'problema' => ['cambio climático', 'contaminación', 'sequía', 'pérdida de biodiversidad'],
            'Experto' => ['Científico del CSIC', 'Experto en sostenibilidad', 'Investigador', 'Especialista'],
            'solucion' => ['plan integral', 'estrategia innovadora', 'medidas urgentes', 'proyecto piloto'],
            'medida' => ['zona de bajas emisiones', 'sistema de reciclaje', 'red de transporte sostenible'],
        ];

        $template = fake()->randomElement($templates);
        
        foreach ($replacements as $key => $options) {
            $template = str_replace('{' . $key . '}', fake()->randomElement($options), $template);
        }

        return $template;
    }

    /**
     * Generar contenido realista.
     */
    private function generateRealisticContent(string $title): string
    {
        $paragraphs = [];
        
        // Párrafo de introducción
        $paragraphs[] = fake()->paragraph(4) . ' Esta medida forma parte de la estrategia nacional de sostenibilidad y tiene como objetivo reducir las emisiones de CO2 en un ' . fake()->numberBetween(20, 50) . '% para el año 2030.';
        
        // Párrafos de desarrollo
        for ($i = 0; $i < fake()->numberBetween(3, 8); $i++) {
            $paragraphs[] = fake()->paragraph(fake()->numberBetween(3, 6));
        }
        
        // Párrafo con datos
        $paragraphs[] = 'Según datos del Ministerio para la Transición Ecológica, ' . fake()->sentence() . ' Las cifras muestran que ' . fake()->sentence() . ' lo que representa un avance significativo hacia los objetivos climáticos europeos.';
        
        // Párrafo de expertos
        $expert = fake()->name();
        $paragraphs[] = '"' . fake()->sentence() . '", explica ' . $expert . ', experto en sostenibilidad del CSIC. "' . fake()->sentence() . ' Es fundamental que ' . fake()->sentence() . '"';
        
        // Párrafo de conclusión
        $paragraphs[] = fake()->paragraph(3) . ' La implementación de estas medidas comenzará el próximo mes y se espera que los primeros resultados sean visibles en ' . fake()->numberBetween(6, 18) . ' meses.';
        
        return '<p>' . implode('</p><p>', $paragraphs) . '</p>';
    }

    /**
     * Generar resumen del artículo.
     */
    private function generateSummary(string $content): string
    {
        $sentences = explode('.', strip_tags($content));
        $summary = array_slice($sentences, 0, 3);
        return implode('. ', $summary) . '.';
    }

    /**
     * Generar extracto del artículo.
     */
    private function generateExcerpt(string $content): string
    {
        $text = strip_tags($content);
        return substr($text, 0, 200) . '...';
    }

    /**
     * Generar temas de sostenibilidad.
     */
    private function generateSustainabilityTopics(): array
    {
        $topics = [
            'energías renovables', 'cambio climático', 'biodiversidad', 'reciclaje',
            'economía circular', 'movilidad sostenible', 'agricultura ecológica',
            'conservación', 'contaminación', 'eficiencia energética', 'agua',
            'bosques', 'océanos', 'especies protegidas', 'tecnología verde'
        ];
        
        return fake()->randomElements($topics, fake()->numberBetween(1, 5));
    }

    /**
     * Generar datos de CO2.
     */
    private function generateCO2Data(): array
    {
        return [
            'emission_reduction' => fake()->numberBetween(100, 10000) . ' toneladas CO2/año',
            'equivalent_trees' => fake()->numberBetween(50, 5000) . ' árboles plantados',
            'equivalent_cars' => fake()->numberBetween(20, 2000) . ' coches menos en circulación',
        ];
    }

    /**
     * Generar palabras clave.
     */
    private function generateKeywords(): array
    {
        $keywords = [
            'sostenibilidad', 'medio ambiente', 'energía renovable', 'cambio climático',
            'biodiversidad', 'reciclaje', 'economía verde', 'innovación', 'tecnología',
            'España', 'Europa', 'política ambiental', 'conservación', 'futuro sostenible'
        ];
        
        return fake()->randomElements($keywords, fake()->numberBetween(3, 8));
    }

    /**
     * Generar entidades.
     */
    private function generateEntities(): array
    {
        return [
            'organizations' => fake()->randomElements([
                'MITECO', 'Greenpeace España', 'WWF España', 'SEO/BirdLife',
                'Ecologistas en Acción', 'CSIC', 'IDAE'
            ], fake()->numberBetween(1, 3)),
            'locations' => fake()->randomElements([
                'Madrid', 'Barcelona', 'Valencia', 'España', 'Andalucía',
                'Cataluña', 'País Vasco', 'Comunidad Valenciana'
            ], fake()->numberBetween(1, 3)),
            'people' => [fake()->name(), fake()->name()],
        ];
    }

    /**
     * Artículo destacado.
     */
    public function featured(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_outstanding' => true,
            'featured_start' => fake()->dateTimeBetween('-1 week', 'now'),
            'featured_end' => fake()->dateTimeBetween('now', '+1 month'),
            'views_count' => fake()->numberBetween(10000, 100000),
            'shares_count' => fake()->numberBetween(100, 1000),
        ]);
    }

    /**
     * Noticia de última hora.
     */
    public function breaking(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_breaking_news' => true,
            'published_at' => fake()->dateTimeBetween('-24 hours', 'now'),
            'views_count' => fake()->numberBetween(5000, 50000),
            'shares_count' => fake()->numberBetween(50, 500),
        ]);
    }

    /**
     * Artículo de sostenibilidad.
     */
    public function sustainability(): static
    {
        return $this->state(fn (array $attributes) => [
            'category' => fake()->randomElement(['medio ambiente', 'sostenibilidad', 'energía', 'cambio climático']),
            'environmental_impact_score' => fake()->numberBetween(7, 10),
            'sustainability_topics' => json_encode([
                'energías renovables', 'economía circular', 'biodiversidad'
            ]),
        ]);
    }

    /**
     * Artículo popular.
     */
    public function popular(): static
    {
        return $this->state(fn (array $attributes) => [
            'views_count' => fake()->numberBetween(20000, 200000),
            'shares_count' => fake()->numberBetween(200, 2000),
            'comments_count' => fake()->numberBetween(50, 500),
        ]);
    }
}