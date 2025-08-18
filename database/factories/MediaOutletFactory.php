<?php

namespace Database\Factories;

use App\Models\MediaOutlet;
use App\Models\Municipality;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Factory para generar medios de comunicación españoles realistas.
 */
class MediaOutletFactory extends Factory
{
    protected $model = MediaOutlet::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        $mediaData = $this->getSpanishMediaData();
        $selectedMedia = fake()->randomElement($mediaData);
        
        return [
            'name' => $selectedMedia['name'],
            'slug' => \Str::slug($selectedMedia['name']),
            'description' => $selectedMedia['description'],
            'type' => $selectedMedia['type'],
            'website' => $selectedMedia['website'],
            'rss_feed' => $selectedMedia['rss_feed'] ?? null,
            
            // Ubicación y cobertura
            'headquarters_location' => $selectedMedia['headquarters'],
            'coverage_scope' => $selectedMedia['coverage_scope'],
            'languages' => json_encode(['es']),
            
            // Información corporativa
            'founding_year' => $selectedMedia['founding_year'],
            'owner_company' => $selectedMedia['owner_company'] ?? null,
            'political_leaning' => $selectedMedia['political_leaning'] ?? 'centro',
            'specializations' => json_encode($selectedMedia['specializations']),
            'circulation' => $selectedMedia['circulation'] ?? null,
            'circulation_type' => fake()->optional(0.7)->randomElement(['impreso', 'digital', 'mixto', 'audiencia']),
            
            // Categorización
            'media_category' => fake()->randomElement(['diario', 'revista', 'digital', 'agencia', 'television', 'radio', 'blog']),
            
            // Características del medio
            'is_digital_native' => $selectedMedia['is_digital_native'],
            'is_verified' => fake()->boolean(80),
            'is_active' => fake()->boolean(95),
            'covers_sustainability' => $selectedMedia['covers_sustainability'],
            
            // Métricas de calidad
            'credibility_score' => fake()->randomFloat(2, 6.0, 9.5),
            'influence_score' => fake()->randomFloat(2, 5.0, 9.8),
            'sustainability_focus' => $selectedMedia['covers_sustainability'] ? fake()->randomFloat(2, 6.0, 9.0) : fake()->randomFloat(2, 1.0, 5.0),
            
            // Métricas de audiencia
            'articles_count' => fake()->numberBetween(100, 50000),
            'monthly_pageviews' => $selectedMedia['monthly_pageviews'],
            'social_media_followers' => $selectedMedia['social_media_followers'],
            'social_media_handles' => json_encode($selectedMedia['social_media_handles']),
            
            // Contacto y prensa
            'contact_email' => $selectedMedia['contact_email'],
            'press_contact_name' => $selectedMedia['press_contact_name'] ?? null,
            'press_contact_email' => $selectedMedia['press_contact_email'] ?? null,
            'press_contact_phone' => $selectedMedia['press_contact_phone'] ?? null,
            
            // Información adicional
            'editorial_team' => json_encode($selectedMedia['editorial_team'] ?? []),
            'content_licensing' => $selectedMedia['content_licensing'] ?? 'todos los derechos reservados',
            'allows_reprints' => fake()->boolean(30),
            'api_access' => json_encode($selectedMedia['api_access'] ?? []),
            
            // Fechas
            'last_scraped_at' => fake()->dateTimeBetween('-1 week', 'now'),
            'verified_at' => fake()->boolean(80) ? fake()->dateTimeBetween('-1 year', 'now') : null,
            
            // Relaciones
            'municipality_id' => Municipality::where('name', $selectedMedia['headquarters'])->first()?->id 
                ?? Municipality::inRandomOrder()->first()?->id,
        ];
    }

    /**
     * Datos de medios españoles reales.
     */
    private function getSpanishMediaData(): array
    {
        return [
            [
                'name' => 'El País',
                'description' => 'Diario español de información general. Líder en España.',
                'type' => 'newspaper',
                'website' => 'https://elpais.com',
                'rss_feed' => 'https://feeds.elpais.com/mrss-s/pages/ep/site/elpais.com/portada',
                'headquarters' => 'Madrid',
                'coverage_scope' => 'nacional',
                'founding_year' => 1976,
                'owner_company' => 'Grupo PRISA',
                'political_leaning' => 'centro-izquierda',
                'specializations' => ['política', 'internacional', 'economía'],
                'media_category' => 'diario',
                'is_digital_native' => false,
                'covers_sustainability' => true,
                'circulation' => 200000,
                'circulation_type' => 'impreso',
                'monthly_pageviews' => 45000000,
                'social_media_followers' => 8500000,
                'social_media_handles' => ['twitter' => '@el_pais', 'facebook' => 'elpais'],
                'contact_email' => 'redaccion@elpais.es',
                'press_contact_name' => 'Departamento de Comunicación',
                'press_contact_email' => 'comunicacion@elpais.es',
                'editorial_team' => ['Pepa Bueno', 'Javier Moreno'],
                'content_licensing' => 'Creative Commons para algunos contenidos'
            ],
            [
                'name' => 'El Mundo',
                'description' => 'Diario español con enfoque en investigación.',
                'type' => 'newspaper',
                'website' => 'https://elmundo.es',
                'headquarters' => 'Madrid',
                'coverage_scope' => 'nacional',
                'founding_year' => 1989,
                'owner_company' => 'Unidad Editorial',
                'political_leaning' => 'centro-derecha',
                'specializations' => ['política', 'investigación', 'economía'],
                'media_category' => 'diario',
                'is_digital_native' => false,
                'covers_sustainability' => true,
                'circulation' => 150000,
                'circulation_type' => 'impreso',
                'monthly_pageviews' => 35000000,
                'social_media_followers' => 6200000,
                'social_media_handles' => ['twitter' => '@elmundoes'],
                'contact_email' => 'redaccion@elmundo.es'
            ],
            [
                'name' => 'elDiario.es',
                'description' => 'Medio digital independiente financiado por lectores.',
                'type' => 'blog',
                'website' => 'https://eldiario.es',
                'headquarters' => 'Madrid',
                'coverage_scope' => 'nacional',
                'founding_year' => 2012,
                'political_leaning' => 'izquierda',
                'specializations' => ['política social', 'derechos humanos', 'medio ambiente'],
                'media_category' => 'diario',
                'is_digital_native' => true,
                'covers_sustainability' => true,
                'monthly_pageviews' => 28000000,
                'social_media_followers' => 3200000,
                'social_media_handles' => ['twitter' => '@eldiarioes'],
                'contact_email' => 'redaccion@eldiario.es'
            ],
            [
                'name' => 'Energías Renovables',
                'description' => 'Revista especializada en energías renovables.',
                'type' => 'magazine',
                'website' => 'https://energias-renovables.com',
                'headquarters' => 'Madrid',
                'coverage_scope' => 'nacional',
                'founding_year' => 1999,
                'specializations' => ['energías renovables', 'sostenibilidad'],
                'media_category' => 'revista',
                'is_digital_native' => false,
                'covers_sustainability' => true,
                'monthly_pageviews' => 800000,
                'social_media_followers' => 150000,
                'social_media_handles' => ['twitter' => '@energiasrenovab'],
                'contact_email' => 'redaccion@energias-renovables.com'
            ],
            [
                'name' => 'La Vanguardia',
                'description' => 'Diario catalán con proyección nacional.',
                'type' => 'newspaper',
                'website' => 'https://lavanguardia.com',
                'headquarters' => 'Barcelona',
                'coverage_scope' => 'regional',
                'founding_year' => 1881,
                'owner_company' => 'Grupo Godó',
                'specializations' => ['Cataluña', 'política', 'cultura'],
                'media_category' => 'diario',
                'is_digital_native' => false,
                'covers_sustainability' => true,
                'monthly_pageviews' => 22000000,
                'social_media_followers' => 5100000,
                'social_media_handles' => ['twitter' => '@LaVanguardia'],
                'contact_email' => 'redaccion@lavanguardia.es'
            ]
        ];
    }

    public function verified(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_verified' => true,
            'verified_at' => fake()->dateTimeBetween('-2 years', 'now'),
            'credibility_score' => fake()->randomFloat(2, 8.0, 9.5),
        ]);
    }

    public function sustainabilityFocused(): static
    {
        return $this->state(fn (array $attributes) => [
            'covers_sustainability' => true,
            'sustainability_focus' => fake()->randomFloat(2, 7.0, 9.5),
        ]);
    }
}