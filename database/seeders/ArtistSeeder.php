<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Artist;
use App\Models\Person;
use App\Models\Language;

class ArtistSeeder extends Seeder
{
    /**
     * Ejecutar el seeder para artistas españoles.
     */
    public function run(): void
    {
        $this->command->info('Creando artistas españoles famosos...');

        // Buscar idioma español
        $spanish = Language::where('language', 'Spanish')->orWhere('iso_639_1', 'es')->first();
        if (!$spanish) {
            $spanish = Language::factory()->create([
                'language' => 'Spanish',
                'slug' => 'spanish',
                'native_name' => 'Español',
                'iso_639_1' => 'es',
                'iso_639_2' => 'spa'
            ]);
        }

        // Crear artistas famosos españoles
        $famousArtists = $this->getFamousSpanishArtists();
        $createdCount = 0;

        foreach ($famousArtists as $artistData) {
            // Buscar o crear la persona asociada
            $person = Person::where('name', $artistData['person_name'])->first();
            if (!$person) {
                $person = Person::factory()->create([
                    'name' => $artistData['person_name'],
                    'slug' => \Str::slug($artistData['person_name']),
                    'birth_date' => $artistData['birth_date'] ?? null,
                    'notable_for' => $artistData['notable_for'] ?? $artistData['genre'],
                    'is_influencer' => true,
                ]);
            }

            $artist = Artist::create([
                'name' => $artistData['name'],
                'slug' => \Str::slug($artistData['name']),
                'stage_name' => $artistData['stage_name'] ?? $artistData['name'],
                'description' => $artistData['description'],
                'birth_date' => $artistData['birth_date'] ?? null,
                'genre' => $artistData['genre'],
                'person_id' => $person->id,
                'active_years_start' => $artistData['active_years_start'] ?? null,
                'active_years_end' => $artistData['active_years_end'] ?? null,
                'bio' => $artistData['bio'],
                'social_links' => $artistData['social_links'] ?? null,
                'language_id' => $spanish->id,
            ]);
            
            $createdCount++;
        }

        $this->command->info("✅ Creados {$createdCount} artistas famosos españoles");

        // Crear artistas adicionales con factory
        $additionalArtists = Artist::factory()
            ->count(30)
            ->create([
                'language_id' => $spanish->id,
            ]);

        $this->command->info("✅ Creados {$additionalArtists->count()} artistas adicionales");

        // Mostrar estadísticas
        $this->showStatistics();
    }

    /**
     * Datos de artistas famosos españoles.
     */
    private function getFamousSpanishArtists(): array
    {
        return [
            // Músicos
            [
                'name' => 'Paco de Lucía',
                'person_name' => 'Paco de Lucía',
                'stage_name' => 'Paco de Lucía',
                'birth_date' => '1947-12-21',
                'genre' => 'Flamenco',
                'description' => 'Guitarrista flamenco virtuoso, revolucionó el flamenco tradicional.',
                'bio' => 'Francisco Sánchez Gómez, conocido como Paco de Lucía, fue un guitarrista español considerado uno de los mejores de todos los tiempos.',
                'active_years_start' => 1963,
                'active_years_end' => 2014,
                'notable_for' => 'Guitarrista flamenco',
            ],
            [
                'name' => 'Rosalía',
                'person_name' => 'Rosalía Vila Tobella',
                'stage_name' => 'Rosalía',
                'birth_date' => '1992-09-25',
                'genre' => 'Flamenco fusión',
                'description' => 'Cantante que fusiona flamenco con géneros contemporáneos.',
                'bio' => 'Rosalía Vila Tobella es una cantante española que ha revolucionado el flamenco fusionándolo con géneros urbanos.',
                'active_years_start' => 2017,
                'social_links' => [
                    'instagram' => '@rosalia.vt',
                    'twitter' => '@rosaliavt',
                    'spotify' => 'rosalia'
                ],
            ],
            [
                'name' => 'Alejandro Sanz',
                'person_name' => 'Alejandro Sanz',
                'stage_name' => 'Alejandro Sanz',
                'birth_date' => '1968-12-18',
                'genre' => 'Pop Latino',
                'description' => 'Cantautor español, uno de los artistas latinos más exitosos.',
                'bio' => 'Alejandro Sánchez Pizarro es un cantautor español ganador de múltiples Grammy Latino.',
                'active_years_start' => 1989,
            ],
            [
                'name' => 'Manu Chao',
                'person_name' => 'Manu Chao',
                'stage_name' => 'Manu Chao',
                'birth_date' => '1961-06-21',
                'genre' => 'Rock alternativo',
                'description' => 'Músico franco-español, líder de Mano Negra.',
                'bio' => 'José-Manuel Thomas Arthur Chao es un músico franco-español conocido por su estilo multicultural.',
                'active_years_start' => 1987,
            ],

            // Cantaores flamencos
            [
                'name' => 'Camarón de la Isla',
                'person_name' => 'José Monge Cruz',
                'stage_name' => 'Camarón de la Isla',
                'birth_date' => '1950-12-05',
                'genre' => 'Flamenco',
                'description' => 'Cantaor flamenco, considerado el mejor de todos los tiempos.',
                'bio' => 'José Monge Cruz, conocido como Camarón de la Isla, revolucionó el cante flamenco.',
                'active_years_start' => 1969,
                'active_years_end' => 1992,
            ],
            [
                'name' => 'Enrique Morente',
                'person_name' => 'Enrique Morente',
                'stage_name' => 'Enrique Morente',
                'birth_date' => '1942-12-25',
                'genre' => 'Flamenco',
                'description' => 'Cantaor flamenco innovador y experimentador.',
                'bio' => 'Enrique Morente Cotelo fue un cantaor flamenco que experimentó con diferentes estilos musicales.',
                'active_years_start' => 1967,
                'active_years_end' => 2010,
            ],

            // Rock y pop
            [
                'name' => 'Miguel Bosé',
                'person_name' => 'Miguel Bosé',
                'stage_name' => 'Miguel Bosé',
                'birth_date' => '1956-04-03',
                'genre' => 'Pop',
                'description' => 'Cantante y actor español-italiano.',
                'bio' => 'Luis Miguel González Bosé es un cantante y actor español con gran éxito en Latinoamérica.',
                'active_years_start' => 1977,
            ],
            [
                'name' => 'Mecano',
                'person_name' => 'Mecano',
                'stage_name' => 'Mecano',
                'genre' => 'Pop',
                'description' => 'Grupo de pop español de los años 80 y 90.',
                'bio' => 'Mecano fue un grupo español de pop formado por Ana Torroja, Nacho Cano y José María Cano.',
                'active_years_start' => 1981,
                'active_years_end' => 1998,
            ],

            // Artistas contemporáneos
            [
                'name' => 'Jesse & Joy',
                'person_name' => 'Jesse & Joy',
                'stage_name' => 'Jesse & Joy',
                'genre' => 'Pop Latino',
                'description' => 'Dúo mexicano con gran éxito en España.',
                'bio' => 'Jesse Eduardo y Tirzah Joy Huerta son un dúo musical mexicano popular en España.',
                'active_years_start' => 2005,
            ],
            [
                'name' => 'C. Tangana',
                'person_name' => 'Antón Álvarez Alfaro',
                'stage_name' => 'C. Tangana',
                'birth_date' => '1990-07-16',
                'genre' => 'Hip hop',
                'description' => 'Rapero y cantante español.',
                'bio' => 'Antón Álvarez Alfaro, conocido como C. Tangana, es un rapero y cantante español.',
                'active_years_start' => 2006,
                'social_links' => [
                    'instagram' => '@c.tangana',
                    'twitter' => '@ctangana'
                ],
            ],

            // Artistas visuales como músicos
            [
                'name' => 'Ojos de Brujo',
                'person_name' => 'Ojos de Brujo',
                'stage_name' => 'Ojos de Brujo',
                'genre' => 'Flamenco fusión',
                'description' => 'Grupo barcelonés que fusiona flamenco con otros géneros.',
                'bio' => 'Ojos de Brujo es un grupo musical español que combina flamenco con hip hop, rock y música electrónica.',
                'active_years_start' => 1996,
            ],
        ];
    }

    /**
     * Mostrar estadísticas de los artistas creados.
     */
    private function showStatistics(): void
    {
        $stats = [
            'Total artistas' => Artist::count(),
            'Por género - Flamenco' => Artist::where('genre', 'LIKE', '%flamenco%')->count(),
            'Por género - Pop' => Artist::where('genre', 'LIKE', '%pop%')->count(),
            'Por género - Rock' => Artist::where('genre', 'LIKE', '%rock%')->count(),
            'Artistas activos (sin fecha fin)' => Artist::whereNull('active_years_end')->count(),
            'Con biografía' => Artist::whereNotNull('bio')->count(),
            'Con redes sociales' => Artist::whereNotNull('social_links')->count(),
        ];

        $this->command->info("\n📊 Estadísticas de artistas:");
        foreach ($stats as $type => $count) {
            $this->command->info("   {$type}: {$count}");
        }

        // Mostrar géneros más populares
        $genres = Artist::whereNotNull('genre')
                        ->selectRaw('genre, COUNT(*) as count')
                        ->groupBy('genre')
                        ->orderBy('count', 'desc')
                        ->limit(5)
                        ->get();

        if ($genres->isNotEmpty()) {
            $this->command->info("\n🎵 Géneros más populares:");
            foreach ($genres as $genre) {
                $this->command->info("   {$genre->genre}: {$genre->count}");
            }
        }

        // Artistas por década de inicio
        $decades = Artist::whereNotNull('active_years_start')
                         ->selectRaw('FLOOR(active_years_start/10)*10 as decade, COUNT(*) as count')
                         ->groupBy('decade')
                         ->orderBy('decade', 'desc')
                         ->get();

        if ($decades->isNotEmpty()) {
            $this->command->info("\n📅 Artistas por década de inicio:");
            foreach ($decades as $decade) {
                $this->command->info("   Años {$decade->decade}s: {$decade->count}");
            }
        }
    }
}
