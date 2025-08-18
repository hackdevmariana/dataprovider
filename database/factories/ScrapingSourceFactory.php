<?php

namespace Database\Factories;

use App\Models\ScrapingSource;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Factory para generar fuentes de scraping realistas.
 */
class ScrapingSourceFactory extends Factory
{
    protected $model = ScrapingSource::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        $sourceData = $this->getSpanishMediaSources();
        $selectedSource = fake()->randomElement($sourceData);
        
        return [
            'name' => $selectedSource['name'],
            'url' => $selectedSource['url'],
            'type' => $selectedSource['type'],
            'source_type_description' => $selectedSource['description'],
            'frequency' => fake()->randomElement(['daily', 'weekly', 'monthly']),
            'last_scraped_at' => fake()->optional(0.8)->dateTimeBetween('-1 week', 'now'),
            'is_active' => fake()->boolean(85),
        ];
    }

    /**
     * Fuentes de scraping españolas reales.
     */
    private function getSpanishMediaSources(): array
    {
        return [
            // Medios nacionales principales
            [
                'name' => 'El País RSS',
                'url' => 'https://feeds.elpais.com/mrss-s/pages/ep/site/elpais.com/portada',
                'type' => 'blog',
                'description' => 'Feed RSS principal de El País'
            ],
            [
                'name' => 'El Mundo RSS',
                'url' => 'https://e00-elmundo.uecdn.es/elmundo/rss/portada.xml',
                'type' => 'newspaper',
                'description' => 'Feed RSS de portada de El Mundo'
            ],
            [
                'name' => 'La Vanguardia RSS',
                'url' => 'https://www.lavanguardia.com/rss/home.xml',
                'type' => 'newspaper',
                'description' => 'Feed RSS principal de La Vanguardia'
            ],
            [
                'name' => 'ABC RSS',
                'url' => 'https://www.abc.es/rss/feeds/abc_EspanaEspana.xml',
                'type' => 'newspaper',
                'description' => 'Feed RSS de ABC España'
            ],
            [
                'name' => 'elDiario.es RSS',
                'url' => 'https://www.eldiario.es/rss/',
                'type' => 'blog',
                'description' => 'Feed RSS principal de elDiario.es'
            ],

            // Medios especializados en sostenibilidad
            [
                'name' => 'Energías Renovables RSS',
                'url' => 'https://www.energias-renovables.com/rss.xml',
                'type' => 'other',
                'description' => 'Feed RSS especializado en energías renovables'
            ],
            [
                'name' => 'Ecoticias RSS',
                'url' => 'https://www.ecoticias.com/rss.xml',
                'type' => 'blog',
                'description' => 'Portal de noticias ecológicas y sostenibilidad'
            ],
            [
                'name' => 'Sostenibilidad.com RSS',
                'url' => 'https://www.sostenibilidad.com/feed/',
                'type' => 'blog',
                'description' => 'Blog especializado en sostenibilidad empresarial'
            ],
            [
                'name' => 'Compromiso Empresarial RSS',
                'url' => 'https://www.compromisoempresarial.com/rss/',
                'type' => 'other',
                'description' => 'Revista de responsabilidad social y sostenibilidad'
            ],

            // Medios regionales
            [
                'name' => 'El Periódico de Catalunya RSS',
                'url' => 'https://www.elperiodico.com/es/rss/rss_portada.xml',
                'type' => 'newspaper',
                'description' => 'Feed RSS de El Periódico de Catalunya'
            ],
            [
                'name' => 'Sur.es RSS',
                'url' => 'https://www.diariosur.es/rss/2.0/',
                'type' => 'newspaper',
                'description' => 'Diario Sur de Málaga RSS'
            ],
            [
                'name' => 'Heraldo de Aragón RSS',
                'url' => 'https://www.heraldo.es/rss/section/1/',
                'type' => 'newspaper',
                'description' => 'Feed RSS del Heraldo de Aragón'
            ],
            [
                'name' => 'Faro de Vigo RSS',
                'url' => 'https://www.farodevigo.es/rss/section/1/',
                'type' => 'newspaper',
                'description' => 'Feed RSS del Faro de Vigo'
            ],

            // Medios económicos
            [
                'name' => 'Expansión RSS',
                'url' => 'https://www.expansion.com/rss/portada.xml',
                'type' => 'newspaper',
                'description' => 'Feed RSS de Expansión - economía'
            ],
            [
                'name' => 'Cinco Días RSS',
                'url' => 'https://cincodias.elpais.com/rss/cincodias/portada.xml',
                'type' => 'newspaper',
                'description' => 'Feed RSS de Cinco Días - economía'
            ],

            // Blogs y medios digitales especializados
            [
                'name' => 'Xataka Ciencia RSS',
                'url' => 'https://www.xatakaciencia.com/rss.xml',
                'type' => 'blog',
                'description' => 'Blog de ciencia y tecnología'
            ],
            [
                'name' => 'Energía16 RSS',
                'url' => 'https://energia16.com/feed/',
                'type' => 'blog',
                'description' => 'Portal especializado en sector energético'
            ],
            [
                'name' => 'Ambientum RSS',
                'url' => 'https://www.ambientum.com/rss/',
                'type' => 'blog',
                'description' => 'Portal ambiental especializado'
            ],
            [
                'name' => 'Retema RSS',
                'url' => 'https://www.retema.es/rss.xml',
                'type' => 'other',
                'description' => 'Revista Técnica de Medio Ambiente'
            ],

            // Medios institucionales
            [
                'name' => 'MITECO Noticias RSS',
                'url' => 'https://www.miteco.gob.es/es/rss/ministerio-noticias.xml',
                'type' => 'other',
                'description' => 'Noticias del Ministerio de Transición Ecológica'
            ],
            [
                'name' => 'IDAE RSS',
                'url' => 'https://www.idae.es/rss.xml',
                'type' => 'other',
                'description' => 'Instituto para la Diversificación y Ahorro de la Energía'
            ],

            // Medios locales y autonómicos
            [
                'name' => 'Diario de Sevilla RSS',
                'url' => 'https://www.diariodesevilla.es/rss/section/1/',
                'type' => 'newspaper',
                'description' => 'Feed RSS del Diario de Sevilla'
            ],
            [
                'name' => 'La Opinión de Murcia RSS',
                'url' => 'https://www.laopiniondemurcia.es/rss/section/1/',
                'type' => 'newspaper',
                'description' => 'Feed RSS de La Opinión de Murcia'
            ],
            [
                'name' => 'Canarias7 RSS',
                'url' => 'https://www.canarias7.es/rss/section/1/',
                'type' => 'newspaper',
                'description' => 'Feed RSS de Canarias7'
            ],
        ];
    }

    /**
     * Fuente activa y con scraping reciente.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => true,
            'last_scraped_at' => fake()->dateTimeBetween('-2 days', 'now'),
            'frequency' => 'daily',
        ]);
    }

    /**
     * Fuente especializada en sostenibilidad.
     */
    public function sustainability(): static
    {
        return $this->state(fn (array $attributes) => [
            'source_type_description' => 'Fuente especializada en sostenibilidad y medio ambiente',
            'frequency' => 'daily',
            'is_active' => true,
        ]);
    }

    /**
     * Fuente de alta frecuencia.
     */
    public function highFrequency(): static
    {
        return $this->state(fn (array $attributes) => [
            'frequency' => 'daily',
            'last_scraped_at' => fake()->dateTimeBetween('-6 hours', 'now'),
            'is_active' => true,
        ]);
    }

    /**
     * Fuente inactiva o con problemas.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
            'last_scraped_at' => fake()->optional(0.3)->dateTimeBetween('-1 month', '-1 week'),
        ]);
    }
}
