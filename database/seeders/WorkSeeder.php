<?php

namespace Database\Seeders;

use App\Models\Work;
use App\Models\Language;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class WorkSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Creando obras famosas...');

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

        $createdCount = 0;

        // Crear obras específicas famosas
        $famousWorks = $this->getFamousWorks();

        foreach ($famousWorks as $workData) {
            $work = Work::firstOrCreate(
                ['slug' => \Str::slug($workData['title'])],
                [
                    'title' => $workData['title'],
                    'slug' => \Str::slug($workData['title']),
                    'type' => $workData['type'],
                    'description' => $workData['description'],
                    'release_year' => $workData['release_year'],
                    'genre' => $workData['genre'],
                    'language_id' => $spanish->id,
                ]
            );

            if ($work->wasRecentlyCreated) {
                $createdCount++;
            }
        }

        $this->command->info("✅ Creadas {$createdCount} obras famosas específicas");
        $this->showStatistics();
    }

    /**
     * Datos de obras famosas españolas.
     */
    private function getFamousWorks(): array
    {
        return [
            // Pinturas famosas
            [
                'title' => 'Guernica',
                'type' => 'article',
                'description' => 'Obra maestra de Pablo Picasso que representa el bombardeo de Guernica durante la Guerra Civil Española.',
                'release_year' => 1937,
                'genre' => 'Arte',
            ],
            [
                'title' => 'Las señoritas de Avignon',
                'type' => 'article',
                'description' => 'Pintura de Pablo Picasso considerada la primera obra cubista.',
                'release_year' => 1907,
                'genre' => 'Arte',
            ],
            [
                'title' => 'La persistencia de la memoria',
                'type' => 'article',
                'description' => 'Famosa pintura surrealista de Salvador Dalí con relojes derritiéndose.',
                'release_year' => 1931,
                'genre' => 'Arte',
            ],

            // Música flamenca
            [
                'title' => 'Entre dos aguas',
                'type' => 'article',
                'description' => 'Rumba flamenca más famosa de Paco de Lucía, revolucionó el flamenco.',
                'release_year' => 1976,
                'genre' => 'Música',
            ],
            [
                'title' => 'Almoraima',
                'type' => 'article',
                'description' => 'Álbum de Paco de Lucía que revolucionó el flamenco con nuevas técnicas.',
                'release_year' => 1976,
                'genre' => 'Música',
            ],

            // Películas de Almodóvar
            [
                'title' => 'Todo sobre mi madre',
                'type' => 'movie',
                'description' => 'Película de Pedro Almodóvar ganadora del Óscar a mejor película extranjera.',
                'release_year' => 1999,
                'genre' => 'Drama',
            ],
            [
                'title' => 'Volver',
                'type' => 'movie',
                'description' => 'Película de Pedro Almodóvar protagonizada por Penélope Cruz.',
                'release_year' => 2006,
                'genre' => 'Drama',
            ],
            [
                'title' => 'No es país para viejos',
                'type' => 'movie',
                'description' => 'Película protagonizada por Javier Bardem, ganadora de varios Óscar.',
                'release_year' => 2007,
                'genre' => 'Thriller',
            ],

            // Literatura clásica
            [
                'title' => 'Don Quijote de la Mancha',
                'type' => 'book',
                'description' => 'Novela de Miguel de Cervantes, considerada la primera novela moderna.',
                'release_year' => 1905, // Año aproximado para el campo year
                'genre' => 'Literatura',
            ],
            [
                'title' => 'La casa de Bernarda Alba',
                'type' => 'theatre_play',
                'description' => 'Drama de Federico García Lorca, una de las obras más importantes del teatro español.',
                'release_year' => 1945,
                'genre' => 'Teatro',
            ],
            [
                'title' => 'Romancero gitano',
                'type' => 'book',
                'description' => 'Colección de poemas de Federico García Lorca, su obra poética más famosa.',
                'release_year' => 1928,
                'genre' => 'Poesía',
            ],

            // Ciencia
            [
                'title' => 'Textura del sistema nervioso del hombre y de los vertebrados',
                'type' => 'book',
                'description' => 'Obra fundamental de Santiago Ramón y Cajal en neurociencia, Premio Nobel 1906.',
                'release_year' => 1906,
                'genre' => 'Ciencia',
            ],
        ];
    }

    /**
     * Mostrar estadísticas de las obras creadas.
     */
    private function showStatistics(): void
    {
        $stats = [
            'Total obras' => Work::count(),
            'Libros' => Work::where('type', 'book')->count(),
            'Películas' => Work::where('type', 'movie')->count(),
            'Obras de teatro' => Work::where('type', 'theatre_play')->count(),
            'Artículos' => Work::where('type', 'article')->count(),
            'Series de TV' => Work::where('type', 'tv_show')->count(),
            'Con descripción' => Work::whereNotNull('description')->count(),
            'Con año de lanzamiento' => Work::whereNotNull('release_year')->count(),
        ];

        $this->command->info("\n📊 Estadísticas de obras:");
        foreach ($stats as $type => $count) {
            $this->command->info("   {$type}: {$count}");
        }

        // Mostrar géneros más comunes
        $genres = Work::whereNotNull('genre')
            ->selectRaw('genre, COUNT(*) as count')
            ->groupBy('genre')
            ->orderBy('count', 'desc')
            ->limit(5)
            ->get();

        if ($genres->isNotEmpty()) {
            $this->command->info("\n🎨 Géneros más comunes:");
            foreach ($genres as $genre) {
                $this->command->info("   {$genre->genre}: {$genre->count}");
            }
        }
    }
}