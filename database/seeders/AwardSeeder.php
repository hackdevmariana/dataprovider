<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Award;
use App\Models\AwardWinner;
use App\Models\Person;

class AwardSeeder extends Seeder
{
    /**
     * Ejecutar el seeder para premios españoles.
     */
    public function run(): void
    {
        $this->command->info('Creando premios y reconocimientos españoles...');

        // Crear premios principales
        $awards = $this->getSpanishAwards();
        $createdCount = 0;

        foreach ($awards as $awardData) {
            $award = Award::firstOrCreate(
                ['slug' => \Str::slug($awardData['name'])],
                [
                    'name' => $awardData['name'],
                    'slug' => \Str::slug($awardData['name']),
                    'description' => $awardData['description'],
                    'awarded_by' => $awardData['awarded_by'],
                    'first_year_awarded' => $awardData['first_year_awarded'],
                    'category' => $awardData['category'],
                ]
            );
            
            if ($award->wasRecentlyCreated) {
                $createdCount++;
            }
        }

        $this->command->info("✅ Creados {$createdCount} premios principales");

        // Crear premios adicionales con factory
        $additionalAwards = Award::factory()->count(15)->create();
        $this->command->info("✅ Creados {$additionalAwards->count()} premios adicionales");

        // Crear ganadores de premios si hay personas
        $people = Person::all();
        if ($people->isNotEmpty()) {
            $allAwards = Award::all();
            $winnersCreated = 0;

            foreach ($allAwards->take(10) as $award) {
                $winnersCount = fake()->numberBetween(1, 5);
                for ($i = 0; $i < $winnersCount; $i++) {
                    AwardWinner::firstOrCreate([
                        'award_id' => $award->id,
                        'person_id' => $people->random()->id,
                        'year' => fake()->numberBetween($award->first_year_awarded ?? 2000, 2024),
                    ], [
                        'classification' => fake()->randomElement(['winner', 'finalist']),
                    ]);
                    $winnersCreated++;
                }
            }

            $this->command->info("✅ Creados {$winnersCreated} ganadores de premios");
        }

        // Mostrar estadísticas
        $this->showStatistics();
    }

    /**
     * Premios españoles principales.
     */
    private function getSpanishAwards(): array
    {
        return [
            // Cine
            [
                'name' => 'Premios Goya',
                'description' => 'Premios anuales del cine español otorgados por la Academia de las Artes y las Ciencias Cinematográficas.',
                'awarded_by' => 'Academia de las Artes y las Ciencias Cinematográficas',
                'first_year_awarded' => 1987,
                'category' => 'Cine',
            ],
            [
                'name' => 'Festival de Cine de San Sebastián - Concha de Oro',
                'description' => 'Premio principal del Festival Internacional de Cine de San Sebastián.',
                'awarded_by' => 'Festival de Cine de San Sebastián',
                'first_year_awarded' => 1957,
                'category' => 'Cine',
            ],

            // Literatura
            [
                'name' => 'Premio Cervantes',
                'description' => 'Premio más prestigioso de las letras hispanas, otorgado anualmente.',
                'awarded_by' => 'Ministerio de Cultura de España',
                'first_year_awarded' => 1976,
                'category' => 'Literatura',
            ],
            [
                'name' => 'Premio Planeta',
                'description' => 'Uno de los premios literarios más importantes en lengua española.',
                'awarded_by' => 'Editorial Planeta',
                'first_year_awarded' => 1952,
                'category' => 'Literatura',
            ],
            [
                'name' => 'Premio Nacional de Literatura',
                'description' => 'Premio otorgado por el Ministerio de Cultura a obras literarias destacadas.',
                'awarded_by' => 'Ministerio de Cultura de España',
                'first_year_awarded' => 1924,
                'category' => 'Literatura',
            ],

            // Música
            [
                'name' => 'Premios de la Música',
                'description' => 'Premios anuales de la industria musical española.',
                'awarded_by' => 'Sociedad General de Autores y Editores',
                'first_year_awarded' => 1997,
                'category' => 'Música',
            ],
            [
                'name' => 'Premio Nacional de Música',
                'description' => 'Reconocimiento a trayectorias musicales destacadas.',
                'awarded_by' => 'Ministerio de Cultura de España',
                'first_year_awarded' => 1983,
                'category' => 'Música',
            ],

            // Arte
            [
                'name' => 'Premio Nacional de Artes Plásticas',
                'description' => 'Reconocimiento a artistas visuales españoles.',
                'awarded_by' => 'Ministerio de Cultura de España',
                'first_year_awarded' => 1980,
                'category' => 'Arte',
            ],
            [
                'name' => 'Premio Velázquez',
                'description' => 'Premio internacional de artes plásticas.',
                'awarded_by' => 'Ministerio de Cultura de España',
                'first_year_awarded' => 2002,
                'category' => 'Arte',
            ],

            // Teatro
            [
                'name' => 'Premios Max',
                'description' => 'Premios de las artes escénicas españolas.',
                'awarded_by' => 'Fundación SGAE',
                'first_year_awarded' => 1998,
                'category' => 'Teatro',
            ],
            [
                'name' => 'Premio Nacional de Teatro',
                'description' => 'Reconocimiento a profesionales del teatro.',
                'awarded_by' => 'Ministerio de Cultura de España',
                'first_year_awarded' => 1932,
                'category' => 'Teatro',
            ],

            // Ciencia
            [
                'name' => 'Premio Príncipe de Asturias',
                'description' => 'Premios internacionales en diversas categorías.',
                'awarded_by' => 'Fundación Princesa de Asturias',
                'first_year_awarded' => 1981,
                'category' => 'Ciencia',
            ],
        ];
    }

    /**
     * Mostrar estadísticas de los premios creados.
     */
    private function showStatistics(): void
    {
        $stats = [
            'Total premios' => Award::count(),
            'Ganadores de premios' => AwardWinner::count(),
            'Premios de Cine' => Award::where('category', 'Cine')->count(),
            'Premios de Literatura' => Award::where('category', 'Literatura')->count(),
            'Premios de Música' => Award::where('category', 'Música')->count(),
            'Premios de Arte' => Award::where('category', 'Arte')->count(),
            'Premios de Teatro' => Award::where('category', 'Teatro')->count(),
        ];

        $this->command->info("\n📊 Estadísticas de premios:");
        foreach ($stats as $type => $count) {
            $this->command->info("   {$type}: {$count}");
        }

        // Premios más antiguos
        $oldestAwards = Award::whereNotNull('first_year_awarded')
                           ->orderBy('first_year_awarded')
                           ->limit(3)
                           ->get();

        if ($oldestAwards->isNotEmpty()) {
            $this->command->info("\n🏆 Premios más antiguos:");
            foreach ($oldestAwards as $award) {
                $this->command->info("   {$award->name} ({$award->first_year_awarded})");
            }
        }

        // Categorías más premiadas
        $categories = Award::selectRaw('category, COUNT(*) as count')
                          ->groupBy('category')
                          ->orderBy('count', 'desc')
                          ->get();

        if ($categories->isNotEmpty()) {
            $this->command->info("\n🎭 Categorías más premiadas:");
            foreach ($categories as $category) {
                $this->command->info("   {$category->category}: {$category->count}");
            }
        }
    }
}
