<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Person;
use App\Models\Country;
use App\Models\Language;
use App\Models\Profession;

class PersonSeeder extends Seeder
{
    /**
     * Ejecutar el seeder para personas españolas famosas.
     */
    public function run(): void
    {
        $this->command->info('Creando personas españolas famosas...');

        // Buscar datos de referencia
        $spain = Country::where('name', 'España')->orWhere('name', 'Spain')->first();
        $spanish = Language::where('language', 'Spanish')->orWhere('iso_639_1', 'es')->first();
        
        if (!$spain) {
            $spain = Country::factory()->create(['name' => 'España', 'code' => 'ES']);
        }
        
        if (!$spanish) {
            $spanish = Language::factory()->create([
                'language' => 'Spanish',
                'slug' => 'spanish',
                'native_name' => 'Español',
                'iso_639_1' => 'es',
                'iso_639_2' => 'spa'
            ]);
        }

        // Crear personas famosas españolas
        $famousPeople = $this->getFamousSpanishPeople();
        $createdCount = 0;

        foreach ($famousPeople as $personData) {
            $person = Person::firstOrCreate(
                ['slug' => \Str::slug($personData['name'])],
                [
                'name' => $personData['name'],
                'birth_name' => $personData['birth_name'] ?? null,
                'slug' => \Str::slug($personData['name']),
                'birth_date' => $personData['birth_date'] ?? null,
                'death_date' => $personData['death_date'] ?? null,
                'birth_place' => $personData['birth_place'] ?? null,
                'nationality_id' => $spain->id,
                'language_id' => $spanish->id,
                'gender' => $personData['gender'] ?? null,
                'notable_for' => $personData['notable_for'],
                'occupation_summary' => $personData['occupation'],
                'is_influencer' => $personData['is_influencer'] ?? false,
                'short_bio' => $personData['short_bio'],
                'long_bio' => $personData['long_bio'] ?? null,
                'source_url' => $personData['source_url'] ?? null,
                ]
            );
            
            if ($person->wasRecentlyCreated) {
                $createdCount++;
            }
        }

        $this->command->info("✅ Creadas {$createdCount} personas famosas españolas");

        // Crear personas adicionales con factory
        $additionalPeople = Person::factory()
            ->count(50)
            ->create([
                'nationality_id' => $spain->id,
                'language_id' => $spanish->id,
            ]);

        $this->command->info("✅ Creadas {$additionalPeople->count()} personas adicionales");

        // Mostrar estadísticas
        $this->showStatistics();
    }

    /**
     * Datos de personas famosas españolas.
     */
    private function getFamousSpanishPeople(): array
    {
        return [
            // Artistas y músicos
            [
                'name' => 'Pablo Picasso',
                'birth_name' => 'Pablo Diego José Francisco de Paula Juan Nepomuceno María de los Remedios Cipriano de la Santísima Trinidad Ruiz y Picasso',
                'birth_date' => '1881-10-25',
                'death_date' => '1973-04-08',
                'birth_place' => 'Málaga, Andalucía',
                'gender' => 'male',
                'notable_for' => 'Pintor y escultor, cofundador del cubismo',
                'occupation' => 'Artista, Pintor, Escultor',
                'is_influencer' => true,
                'short_bio' => 'Pintor español, uno de los mayores artistas del siglo XX y cofundador del movimiento cubista.',
                'source_url' => 'https://es.wikipedia.org/wiki/Pablo_Picasso',
            ],
            [
                'name' => 'Salvador Dalí',
                'birth_name' => 'Salvador Domingo Felipe Jacinto Dalí i Domènech',
                'birth_date' => '1904-05-11',
                'death_date' => '1989-01-23',
                'birth_place' => 'Figueres, Cataluña',
                'gender' => 'male',
                'notable_for' => 'Pintor surrealista',
                'occupation' => 'Artista, Pintor',
                'is_influencer' => true,
                'short_bio' => 'Pintor surrealista español conocido por sus imágenes oníricas y su excentricidad.',
            ],
            [
                'name' => 'Paco de Lucía',
                'birth_name' => 'Francisco Sánchez Gómez',
                'birth_date' => '1947-12-21',
                'death_date' => '2014-02-25',
                'birth_place' => 'Algeciras, Andalucía',
                'gender' => 'male',
                'notable_for' => 'Guitarrista flamenco',
                'occupation' => 'Músico, Guitarrista',
                'is_influencer' => true,
                'short_bio' => 'Guitarrista flamenco español, considerado uno de los mejores de todos los tiempos.',
            ],
            [
                'name' => 'Rosalía',
                'birth_name' => 'Rosalía Vila Tobella',
                'birth_date' => '1992-09-25',
                'birth_place' => 'San Esteban Sasroviras, Cataluña',
                'gender' => 'female',
                'notable_for' => 'Cantante de flamenco fusión',
                'occupation' => 'Cantante, Compositora',
                'is_influencer' => true,
                'short_bio' => 'Cantante española que fusiona flamenco con géneros contemporáneos.',
            ],

            // Actores y directores
            [
                'name' => 'Pedro Almodóvar',
                'birth_name' => 'Pedro Almodóvar Caballero',
                'birth_date' => '1949-09-25',
                'birth_place' => 'Calzada de Calatrava, Castilla-La Mancha',
                'gender' => 'male',
                'notable_for' => 'Director de cine',
                'occupation' => 'Director, Guionista, Productor',
                'is_influencer' => true,
                'short_bio' => 'Director de cine español, ganador de dos Premios Óscar.',
            ],
            [
                'name' => 'Penélope Cruz',
                'birth_name' => 'Penélope Cruz Sánchez',
                'birth_date' => '1974-04-28',
                'birth_place' => 'Alcobendas, Madrid',
                'gender' => 'female',
                'notable_for' => 'Actriz ganadora del Óscar',
                'occupation' => 'Actriz',
                'is_influencer' => true,
                'short_bio' => 'Actriz española ganadora del Premio Óscar, una de las actrices más reconocidas internacionalmente.',
            ],
            [
                'name' => 'Javier Bardem',
                'birth_name' => 'Javier Ángel Encinas Bardem',
                'birth_date' => '1969-03-01',
                'birth_place' => 'Las Palmas de Gran Canaria, Canarias',
                'gender' => 'male',
                'notable_for' => 'Actor ganador del Óscar',
                'occupation' => 'Actor',
                'is_influencer' => true,
                'short_bio' => 'Actor español ganador del Premio Óscar por "No Country for Old Men".',
            ],

            // Deportistas
            [
                'name' => 'Rafael Nadal',
                'birth_name' => 'Rafael Nadal Parera',
                'birth_date' => '1986-06-03',
                'birth_place' => 'Manacor, Islas Baleares',
                'gender' => 'male',
                'notable_for' => 'Tenista profesional',
                'occupation' => 'Deportista, Tenista',
                'is_influencer' => true,
                'short_bio' => 'Tenista español, considerado uno de los mejores de todos los tiempos.',
            ],
            [
                'name' => 'Pau Gasol',
                'birth_name' => 'Pau Gasol Sáez',
                'birth_date' => '1980-07-06',
                'birth_place' => 'Barcelona, Cataluña',
                'gender' => 'male',
                'notable_for' => 'Jugador de baloncesto',
                'occupation' => 'Deportista, Baloncestista',
                'is_influencer' => true,
                'short_bio' => 'Baloncestista español, dos veces campeón de la NBA.',
            ],

            // Escritores y pensadores
            [
                'name' => 'Miguel de Cervantes',
                'birth_name' => 'Miguel de Cervantes Saavedra',
                'birth_date' => '1547-09-29',
                'death_date' => '1616-04-22',
                'birth_place' => 'Alcalá de Henares, Madrid',
                'gender' => 'male',
                'notable_for' => 'Escritor, autor del Quijote',
                'occupation' => 'Escritor, Novelista',
                'is_influencer' => true,
                'short_bio' => 'Escritor español, autor de "Don Quijote de la Mancha", considerada la primera novela moderna.',
            ],
            [
                'name' => 'Federico García Lorca',
                'birth_name' => 'Federico del Sagrado Corazón de Jesús García Lorca',
                'birth_date' => '1898-06-05',
                'death_date' => '1936-08-19',
                'birth_place' => 'Fuente Vaqueros, Andalucía',
                'gender' => 'male',
                'notable_for' => 'Poeta y dramaturgo',
                'occupation' => 'Poeta, Dramaturgo',
                'is_influencer' => true,
                'short_bio' => 'Poeta y dramaturgo español, una de las figuras más importantes de la literatura española.',
            ],

            // Científicos y innovadores
            [
                'name' => 'Santiago Ramón y Cajal',
                'birth_name' => 'Santiago Ramón y Cajal',
                'birth_date' => '1852-05-01',
                'death_date' => '1934-10-17',
                'birth_place' => 'Petilla de Aragón, Navarra',
                'gender' => 'male',
                'notable_for' => 'Médico y científico, Premio Nobel',
                'occupation' => 'Médico, Científico, Investigador',
                'is_influencer' => true,
                'short_bio' => 'Médico y científico español, Premio Nobel de Fisiología o Medicina en 1906.',
            ],
        ];
    }

    /**
     * Mostrar estadísticas de las personas creadas.
     */
    private function showStatistics(): void
    {
        $stats = [
            'Total personas' => Person::count(),
            'Personas famosas' => Person::where('is_influencer', true)->count(),
            'Hombres' => Person::where('gender', 'male')->count(),
            'Mujeres' => Person::where('gender', 'female')->count(),
            'Personas vivas' => Person::whereNull('death_date')->count(),
            'Personas fallecidas' => Person::whereNotNull('death_date')->count(),
            'Con biografía corta' => Person::whereNotNull('short_bio')->count(),
            'Con biografía larga' => Person::whereNotNull('long_bio')->count(),
        ];

        $this->command->info("\n📊 Estadísticas de personas:");
        foreach ($stats as $type => $count) {
            $this->command->info("   {$type}: {$count}");
        }

        // Mostrar algunas profesiones
        $occupations = Person::whereNotNull('occupation_summary')
                            ->selectRaw('occupation_summary, COUNT(*) as count')
                            ->groupBy('occupation_summary')
                            ->orderBy('count', 'desc')
                            ->limit(5)
                            ->get();

        if ($occupations->isNotEmpty()) {
            $this->command->info("\n🎭 Principales ocupaciones:");
            foreach ($occupations as $occupation) {
                $this->command->info("   {$occupation->occupation_summary}: {$occupation->count}");
            }
        }
    }
}
