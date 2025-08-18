<?php

namespace Database\Seeders;

use App\Models\MediaOutlet;
use Illuminate\Database\Seeder;

/**
 * Seeder para medios de comunicación españoles con datos reales.
 */
class MediaOutletSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear medios españoles principales con datos específicos
        $this->createMainSpanishMedia();
        
        // Crear medios adicionales usando factory (temporalmente comentado)
        // MediaOutlet::factory(15)->create();
        
        // Crear algunos medios especializados en sostenibilidad
        // MediaOutlet::factory(5)->sustainabilityFocused()->create();
        
        // Crear medios verificados de alta credibilidad
        // MediaOutlet::factory(8)->verified()->create();
        
        echo "✅ Creados " . MediaOutlet::count() . " medios de comunicación\n";
    }

    /**
     * Crear medios españoles principales con datos específicos.
     */
    private function createMainSpanishMedia(): void
    {
        $mainMedia = [
            [
                'name' => 'El País',
                'slug' => 'el-pais',
                'description' => 'Diario español de información general. Líder en España y referente en el mundo hispanohablante con más de 40 años de historia.',
                'type' => 'newspaper',
                'website' => 'https://elpais.com',
                'rss_feed' => 'https://feeds.elpais.com/mrss-s/pages/ep/site/elpais.com/portada',
                'headquarters_location' => 'Madrid',
                'coverage_scope' => 'nacional',
                'languages' => json_encode(['es', 'ca', 'en']),
                'founding_year' => 1976,
                'owner_company' => 'Grupo PRISA',
                'political_leaning' => 'centro-izquierda',
                'specializations' => json_encode(['política', 'internacional', 'economía', 'cultura', 'deportes', 'opinión']),
                'media_category' => 'diario',
                'circulation' => 200000,
                'circulation_type' => 'impreso',
                'is_digital_native' => false,
                'is_verified' => true,
                'is_active' => true,
                'covers_sustainability' => true,
                'credibility_score' => 8.7,
                'influence_score' => 9.5,
                'sustainability_focus' => 7.8,
                'articles_count' => 45000,
                'monthly_pageviews' => 45000000,
                'social_media_followers' => 8500000,
                'social_media_handles' => json_encode([
                    'twitter' => '@el_pais',
                    'facebook' => 'elpais',
                    'instagram' => 'el_pais',
                    'youtube' => 'elpais',
                    'linkedin' => 'elpais'
                ]),
                'contact_email' => 'redaccion@elpais.es',
                'press_contact_name' => 'Departamento de Comunicación',
                'press_contact_email' => 'comunicacion@elpais.es',
                'press_contact_phone' => '+34 91 337 8200',
                'editorial_team' => json_encode([
                    'Pepa Bueno - Directora',
                    'Javier Moreno - Subdirector',
                    'Soledad Gallego-Díaz - Directora Adjunta'
                ]),
                'content_licensing' => 'Derechos reservados con licencias Creative Commons para contenido seleccionado',
                'allows_reprints' => true,
                'api_access' => json_encode(['RSS', 'API REST', 'Newsletters']),
                'verified_at' => now()->subYears(2),
            ],
            [
                'name' => 'El Mundo',
                'slug' => 'el-mundo',
                'description' => 'Diario español de información general con enfoque en investigación periodística y análisis político profundo.',
                'type' => 'newspaper',
                'website' => 'https://elmundo.es',
                'rss_feed' => 'https://e00-elmundo.uecdn.es/elmundo/rss/portada.xml',
                'headquarters_location' => 'Madrid',
                'coverage_scope' => 'nacional',
                'languages' => json_encode(['es']),
                'founding_year' => 1989,
                'owner_company' => 'Unidad Editorial',
                'political_leaning' => 'centro-derecha',
                'specializations' => json_encode(['política', 'investigación', 'economía', 'internacional', 'análisis']),
                'media_category' => 'diario',
                'circulation' => 150000,
                'circulation_type' => 'impreso',
                'is_digital_native' => false,
                'is_verified' => true,
                'is_active' => true,
                'covers_sustainability' => true,
                'credibility_score' => 8.4,
                'influence_score' => 8.9,
                'sustainability_focus' => 6.8,
                'articles_count' => 38000,
                'monthly_pageviews' => 35000000,
                'social_media_followers' => 6200000,
                'social_media_handles' => json_encode([
                    'twitter' => '@elmundoes',
                    'facebook' => 'ElMundo',
                    'instagram' => 'elmundo_es'
                ]),
                'contact_email' => 'redaccion@elmundo.es',
                'press_contact_email' => 'comunicacion@elmundo.es',
                'verified_at' => now()->subYears(3),
            ],
            [
                'name' => 'elDiario.es',
                'slug' => 'eldiario-es',
                'description' => 'Medio digital independiente financiado por sus lectores. Periodismo comprometido con la transparencia y los derechos sociales.',
                'type' => 'blog',
                'website' => 'https://eldiario.es',
                'rss_feed' => 'https://www.eldiario.es/rss/',
                'headquarters_location' => 'Madrid',
                'coverage_scope' => 'nacional',
                'languages' => json_encode(['es', 'ca', 'eu', 'gl']),
                'founding_year' => 2012,
                'political_leaning' => 'izquierda',
                'specializations' => json_encode(['política social', 'derechos humanos', 'investigación', 'medio ambiente', 'transparencia']),
                'media_category' => 'diario',
                'is_digital_native' => true,
                'is_verified' => true,
                'is_active' => true,
                'covers_sustainability' => true,
                'credibility_score' => 8.2,
                'influence_score' => 7.8,
                'sustainability_focus' => 8.5,
                'articles_count' => 32000,
                'monthly_pageviews' => 28000000,
                'social_media_followers' => 3200000,
                'social_media_handles' => json_encode([
                    'twitter' => '@eldiarioes',
                    'facebook' => 'eldiario.es',
                    'instagram' => 'eldiario.es'
                ]),
                'contact_email' => 'redaccion@eldiario.es',
                'press_contact_email' => 'comunicacion@eldiario.es',
                'verified_at' => now()->subYear(),
            ],
            [
                'name' => 'Energías Renovables',
                'slug' => 'energias-renovables',
                'description' => 'Revista líder en información especializada sobre energías renovables, eficiencia energética y sostenibilidad en España.',
                'type' => 'magazine',
                'website' => 'https://energias-renovables.com',
                'rss_feed' => 'https://energias-renovables.com/rss.xml',
                'headquarters_location' => 'Madrid',
                'coverage_scope' => 'nacional',
                'languages' => json_encode(['es']),
                'founding_year' => 1999,
                'specializations' => json_encode(['energías renovables', 'eficiencia energética', 'sostenibilidad', 'tecnología verde', 'política energética']),
                'media_category' => 'revista',
                'is_digital_native' => false,
                'is_verified' => true,
                'is_active' => true,
                'covers_sustainability' => true,
                'credibility_score' => 9.1,
                'influence_score' => 7.2,
                'sustainability_focus' => 9.8,
                'articles_count' => 8500,
                'monthly_pageviews' => 800000,
                'social_media_followers' => 150000,
                'social_media_handles' => json_encode([
                    'twitter' => '@energiasrenovab',
                    'linkedin' => 'energias-renovables',
                    'youtube' => 'energiasrenovables'
                ]),
                'contact_email' => 'redaccion@energias-renovables.com',
                'press_contact_name' => 'Departamento de Comunicación',
                'press_contact_email' => 'comunicacion@energias-renovables.com',
                'verified_at' => now()->subMonths(6),
            ],
            [
                'name' => 'La Vanguardia',
                'slug' => 'la-vanguardia',
                'description' => 'Diario catalán de información general con más de 140 años de historia y proyección nacional e internacional.',
                'type' => 'newspaper',
                'website' => 'https://lavanguardia.com',
                'rss_feed' => 'https://www.lavanguardia.com/rss/home.xml',
                'headquarters_location' => 'Barcelona',
                'coverage_scope' => 'regional',
                'languages' => json_encode(['es', 'ca']),
                'founding_year' => 1901,
                'owner_company' => 'Grupo Godó',
                'political_leaning' => 'centro',
                'specializations' => json_encode(['Cataluña', 'política', 'cultura', 'internacional', 'economía']),
                'media_category' => 'diario',
                'circulation' => 80000,
                'circulation_type' => 'impreso',
                'is_digital_native' => false,
                'is_verified' => true,
                'is_active' => true,
                'covers_sustainability' => true,
                'credibility_score' => 8.3,
                'influence_score' => 8.1,
                'sustainability_focus' => 7.2,
                'articles_count' => 35000,
                'monthly_pageviews' => 22000000,
                'social_media_followers' => 5100000,
                'social_media_handles' => json_encode([
                    'twitter' => '@LaVanguardia',
                    'facebook' => 'LaVanguardia',
                    'instagram' => 'lavanguardia'
                ]),
                'contact_email' => 'redaccion@lavanguardia.es',
                'press_contact_email' => 'comunicacion@lavanguardia.es',
                'verified_at' => now()->subYears(4),
            ],
            [
                'name' => 'Ecoticias',
                'slug' => 'ecoticias',
                'description' => 'Portal líder en noticias de medio ambiente, ecología y sostenibilidad. Información diaria sobre cambio climático y biodiversidad.',
                'type' => 'blog',
                'website' => 'https://ecoticias.com',
                'rss_feed' => 'https://ecoticias.com/rss.xml',
                'headquarters_location' => 'Barcelona',
                'coverage_scope' => 'nacional',
                'languages' => json_encode(['es', 'ca']),
                'founding_year' => 2004,
                'specializations' => json_encode(['medio ambiente', 'ecología', 'sostenibilidad', 'biodiversidad', 'cambio climático', 'energías limpias']),
                'media_category' => 'revista',
                'is_digital_native' => true,
                'is_verified' => true,
                'is_active' => true,
                'covers_sustainability' => true,
                'credibility_score' => 8.8,
                'influence_score' => 6.9,
                'sustainability_focus' => 9.9,
                'articles_count' => 12000,
                'monthly_pageviews' => 1200000,
                'social_media_followers' => 280000,
                'social_media_handles' => json_encode([
                    'twitter' => '@ecoticias',
                    'facebook' => 'ecoticias',
                    'instagram' => 'ecoticias_com'
                ]),
                'contact_email' => 'redaccion@ecoticias.com',
                'press_contact_name' => 'Equipo Editorial',
                'press_contact_email' => 'comunicacion@ecoticias.com',
                'verified_at' => now()->subMonths(8),
            ],
        ];

        foreach ($mainMedia as $media) {
            MediaOutlet::updateOrCreate(
                ['slug' => $media['slug']],
                $media
            );
            echo "✅ Creado/actualizado medio: {$media['name']}\n";
        }
    }
}