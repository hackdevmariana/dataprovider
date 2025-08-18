<?php

namespace Database\Seeders;

use App\Models\NewsArticle;
use App\Models\MediaOutlet;
use App\Models\Person;
use App\Models\Language;
use Illuminate\Database\Seeder;

/**
 * Seeder para artículos de noticias con contenido realista.
 */
class NewsArticleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear artículos específicos de sostenibilidad
        $this->createSustainabilityArticles();
        
        // Crear artículos generales usando factory
        NewsArticle::factory(50)->create();
        
        // Crear artículos destacados
        NewsArticle::factory(8)->featured()->create();
        
        // Crear noticias de última hora
        NewsArticle::factory(5)->breaking()->create();
        
        // Crear artículos de sostenibilidad adicionales
        NewsArticle::factory(15)->sustainability()->create();
        
        // Crear artículos populares
        NewsArticle::factory(10)->popular()->create();
        
        // Asignar relaciones a los artículos
        $this->assignRelations();
        
        echo "✅ Creados " . NewsArticle::count() . " artículos de noticias\n";
    }

    /**
     * Crear artículos específicos sobre sostenibilidad.
     */
    private function createSustainabilityArticles(): void
    {
        $elPais = MediaOutlet::where('slug', 'el-pais')->first();
        $energiasRenovables = MediaOutlet::where('slug', 'energias-renovables')->first();
        $ecoticias = MediaOutlet::where('slug', 'ecoticias')->first();
        $spanish = Language::where('language', 'Español')->first();

        $sustainabilityArticles = [
            [
                'title' => 'España alcanza el 50% de energía renovable en 2024',
                'slug' => 'espana-alcanza-50-por-ciento-energia-renovable-2024',
                'summary' => 'España logra un hito histórico al generar el 50% de su electricidad con fuentes renovables, superando los objetivos europeos.',
                'excerpt' => 'El país ha superado todas las expectativas en la transición energética, consolidándose como líder europeo en energías limpias.',
                'content' => '<p>España ha alcanzado un hito histórico al generar el 50% de su electricidad a partir de fuentes renovables durante 2024, superando ampliamente los objetivos establecidos por la Unión Europea para esta década.</p><p>Según datos del operador del sistema eléctrico Red Eléctrica de España (REE), la energía eólica ha sido la principal protagonista de este logro, aportando el 23% del total, seguida de la energía solar fotovoltaica con un 15%, y la hidráulica con un 12%.</p><p>"Este es un momento histórico para España y para la lucha contra el cambio climático", declaró Teresa Ribera, Ministra para la Transición Ecológica. "Hemos demostrado que la transición energética no solo es posible, sino que es rentable y genera empleo".</p><p>El sector de las energías renovables ha creado más de 100.000 empleos directos en los últimos cinco años, convirtiendo a España en un referente mundial en la fabricación de componentes para aerogeneradores y paneles solares.</p><p>Los expertos señalan que este logro ha sido posible gracias a las favorables condiciones climáticas del país, las inversiones en infraestructuras y un marco regulatorio que ha incentivado el desarrollo de proyectos renovables.</p>',
                'category' => 'energía',
                'topic_focus' => 'energías renovables',
                'article_type' => 'noticia',
                'status' => 'published',
                'visibility' => 'public',
                'is_outstanding' => true,
                'is_verified' => true,
                'is_breaking_news' => false,
                'is_evergreen' => false,
                'published_at' => now()->subDays(2),
                'views_count' => 45000,
                'shares_count' => 1200,
                'comments_count' => 89,
                'reading_time_minutes' => 4,
                'word_count' => 580,
                'sentiment_score' => 0.8,
                'sentiment_label' => 'positivo',
                'sustainability_topics' => json_encode(['energías renovables', 'transición energética', 'cambio climático']),
                'environmental_impact_score' => 9,
                'related_co2_data' => json_encode([
                    'emission_reduction' => '25.000 toneladas CO2/año evitadas',
                    'equivalent_trees' => '1.200.000 árboles plantados',
                    'equivalent_cars' => '15.000 coches menos en circulación'
                ]),
                'keywords' => json_encode(['energías renovables', 'España', 'sostenibilidad', 'electricidad', 'medio ambiente']),
                'entities' => json_encode([
                    'organizations' => ['Red Eléctrica de España', 'MITECO', 'Unión Europea'],
                    'locations' => ['España', 'Europa'],
                    'people' => ['Teresa Ribera']
                ]),
                'geo_scope' => 'nacional',
                'seo_title' => 'España lidera Europa en energías renovables alcanzando el 50% en 2024',
                'seo_description' => 'España supera objetivos europeos generando el 50% de electricidad con renovables. Eólica y solar lideran la transición energética.',
                'media_outlet_id' => $energiasRenovables?->id,
                'language_id' => $spanish?->id,
            ],
            [
                'title' => 'Madrid implementa la mayor zona de bajas emisiones de Europa',
                'slug' => 'madrid-implementa-mayor-zona-bajas-emisiones-europa',
                'summary' => 'La capital española pone en marcha Madrid 360, la zona de bajas emisiones más extensa de Europa, para reducir la contaminación.',
                'excerpt' => 'La medida afectará a más de 3 millones de habitantes y se espera que reduzca las emisiones de CO2 en un 40% para 2030.',
                'content' => '<p>Madrid ha puesto en marcha Madrid 360, la zona de bajas emisiones más extensa de Europa, que abarca 472 kilómetros cuadrados y afecta a más de 3 millones de habitantes de la región metropolitana.</p><p>La medida, que entró en vigor el 1 de enero, prohíbe la circulación de vehículos sin distintivo ambiental y restringe el acceso de los más contaminantes durante los episodios de alta polución.</p><p>"Madrid se convierte en pionera europea en la lucha contra la contaminación urbana", declaró José Luis Martínez-Almeida, alcalde de Madrid. "Esta medida no solo mejorará la calidad del aire, sino que incentivará el uso del transporte público y la movilidad sostenible".</p><p>Según estudios del Ayuntamiento, se espera que la medida reduzca las emisiones de dióxido de nitrógeno en un 23% y las de CO2 en un 40% para 2030. Además, se prevé una disminución del 15% en los niveles de ruido.</p><p>Para facilitar la transición, el consistorio ha ampliado la flota de autobuses eléctricos y ha instalado 200 nuevos puntos de recarga para vehículos eléctricos en la última década.</p>',
                'category' => 'medio ambiente',
                'topic_focus' => 'movilidad sostenible',
                'article_type' => 'noticia',
                'status' => 'published',
                'visibility' => 'public',
                'is_outstanding' => false,
                'is_verified' => true,
                'is_breaking_news' => false,
                'is_evergreen' => false,
                'published_at' => now()->subDays(5),
                'views_count' => 32000,
                'shares_count' => 890,
                'comments_count' => 156,
                'reading_time_minutes' => 3,
                'word_count' => 420,
                'sentiment_score' => 0.6,
                'sentiment_label' => 'positivo',
                'sustainability_topics' => json_encode(['movilidad sostenible', 'contaminación urbana', 'transporte público']),
                'environmental_impact_score' => 8,
                'related_co2_data' => json_encode([
                    'emission_reduction' => '180.000 toneladas CO2/año',
                    'equivalent_trees' => '8.500.000 árboles plantados',
                    'air_quality_improvement' => '23% reducción NO2'
                ]),
                'keywords' => json_encode(['Madrid', 'zona bajas emisiones', 'movilidad sostenible', 'contaminación', 'transporte']),
                'entities' => json_encode([
                    'organizations' => ['Ayuntamiento de Madrid', 'EMT Madrid'],
                    'locations' => ['Madrid', 'España', 'Europa'],
                    'people' => ['José Luis Martínez-Almeida']
                ]),
                'geo_scope' => 'local',
                'latitude' => 40.4168,
                'longitude' => -3.7038,
                'media_outlet_id' => $elPais?->id,
                'language_id' => $spanish?->id,
            ],
            [
                'title' => 'Descubren nueva especie de lince en los Pirineos',
                'slug' => 'descubren-nueva-especie-lince-pirineos',
                'summary' => 'Un equipo internacional de científicos identifica una nueva especie de lince en los Pirineos, considerada clave para la biodiversidad europea.',
                'excerpt' => 'El descubrimiento representa un hito para la conservación de la fauna pirenaica y podría cambiar las estrategias de protección del ecosistema.',
                'content' => '<p>Un equipo internacional de científicos ha identificado una nueva especie de lince en los Pirineos, denominada Lynx pyrenaicus, que podría ser clave para entender la evolución de los félidos europeos y mejorar las estrategias de conservación.</p><p>El descubrimiento, publicado en la revista Nature Ecology & Evolution, es resultado de cinco años de investigación genética y seguimiento por GPS de más de 50 ejemplares en ambas vertientes de la cordillera.</p><p>"Este hallazgo cambia completamente nuestra comprensión de la biodiversidad pirenaica", explica la Dra. María Fernández, investigadora principal del CSIC. "El Lynx pyrenaicus presenta adaptaciones únicas al clima de montaña y podría ser un indicador crucial del cambio climático".</p><p>La nueva especie se caracteriza por su pelaje más denso y extremidades más cortas que el lince euroasiático, adaptaciones que le permiten moverse eficientemente por terrenos nevados y rocosos.</p><p>Los científicos estiman que existen entre 150 y 200 ejemplares en libertad, lo que la convierte en una especie vulnerable que requiere medidas de protección inmediatas.</p>',
                'category' => 'biodiversidad',
                'topic_focus' => 'conservación',
                'article_type' => 'noticia',
                'status' => 'published',
                'visibility' => 'public',
                'is_outstanding' => true,
                'is_verified' => true,
                'is_breaking_news' => true,
                'is_evergreen' => false,
                'published_at' => now()->subHours(6),
                'views_count' => 28000,
                'shares_count' => 650,
                'comments_count' => 94,
                'reading_time_minutes' => 3,
                'word_count' => 380,
                'sentiment_score' => 0.7,
                'sentiment_label' => 'positivo',
                'sustainability_topics' => json_encode(['biodiversidad', 'conservación', 'especies endémicas', 'cambio climático']),
                'environmental_impact_score' => 8,
                'keywords' => json_encode(['lince', 'Pirineos', 'biodiversidad', 'nueva especie', 'conservación']),
                'entities' => json_encode([
                    'organizations' => ['CSIC', 'Nature Ecology & Evolution'],
                    'locations' => ['Pirineos', 'España', 'Francia'],
                    'people' => ['María Fernández']
                ]),
                'geo_scope' => 'regional',
                'media_outlet_id' => $ecoticias?->id,
                'language_id' => $spanish?->id,
            ],
        ];

        foreach ($sustainabilityArticles as $article) {
            NewsArticle::create($article);
            echo "✅ Creado artículo: {$article['title']}\n";
        }
    }

    /**
     * Asignar relaciones a artículos existentes.
     */
    private function assignRelations(): void
    {
        $mediaOutlets = MediaOutlet::all();
        $authors = Person::limit(20)->get();
        
        NewsArticle::whereNull('media_outlet_id')->chunk(10, function ($articles) use ($mediaOutlets, $authors) {
            foreach ($articles as $article) {
                $article->update([
                    'media_outlet_id' => $mediaOutlets->random()->id,
                    'author_id' => $authors->isNotEmpty() ? $authors->random()->id : null,
                ]);
            }
        });
        
        echo "✅ Asignadas relaciones a artículos\n";
    }
}