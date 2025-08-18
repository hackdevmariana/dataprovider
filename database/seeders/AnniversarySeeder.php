<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Anniversary;

class AnniversarySeeder extends Seeder
{
    /**
     * Ejecutar el seeder para efemérides españolas.
     */
    public function run(): void
    {
        $this->command->info('Creando efemérides y aniversarios españoles...');

        // Crear efemérides principales
        $anniversaries = $this->getSpanishAnniversaries();
        $createdCount = 0;

        foreach ($anniversaries as $anniversaryData) {
            // Parsear la fecha MM-DD
            $dateParts = explode('-', $anniversaryData['date']);
            $month = (int)$dateParts[0];
            $day = (int)$dateParts[1];
            
            $anniversary = Anniversary::firstOrCreate(
                [
                    'title' => $anniversaryData['title'],
                    'month' => $month,
                    'day' => $day,
                ],
                [
                    'title' => $anniversaryData['title'],
                    'slug' => \Str::slug($anniversaryData['title']),
                    'description' => $anniversaryData['description'],
                    'month' => $month,
                    'day' => $day,
                    'year' => $anniversaryData['year'] ?? null,
                ]
            );
            
            if ($anniversary->wasRecentlyCreated) {
                $createdCount++;
            }
        }

        $this->command->info("✅ Creadas {$createdCount} efemérides principales");

        // Crear efemérides adicionales con factory
        $additionalAnniversaries = Anniversary::factory()->count(40)->create();
        $this->command->info("✅ Creadas {$additionalAnniversaries->count()} efemérides adicionales");

        // Mostrar estadísticas
        $this->showStatistics();
    }

    /**
     * Efemérides españolas principales.
     */
    private function getSpanishAnniversaries(): array
    {
        return [
            // Fechas históricas
            [
                'title' => 'Día de la Constitución Española',
                'description' => 'Conmemoración de la aprobación de la Constitución Española de 1978.',
                'date' => '12-06',
                'year' => 1978,
                'category' => 'Historia',
                'importance_level' => 'high',
                'is_recurring' => true,
            ],
            [
                'title' => 'Fiesta Nacional de España',
                'description' => 'Día Nacional de España, conmemora el descubrimiento de América.',
                'date' => '10-12',
                'year' => null, // 1492 está fuera del rango del tipo YEAR
                'category' => 'Historia',
                'importance_level' => 'high',
                'is_recurring' => true,
            ],
            [
                'title' => 'Día de la Comunidad de Madrid',
                'description' => 'Conmemora el levantamiento del 2 de mayo de 1808 contra las tropas francesas.',
                'date' => '05-02',
                'year' => null, // 1808 está fuera del rango del tipo YEAR
                'category' => 'Historia',
                'importance_level' => 'medium',
                'is_recurring' => true,
            ],

            // Literatura
            [
                'title' => 'Día del Libro',
                'description' => 'Día Mundial del Libro y del Derecho de Autor, coincide con la muerte de Cervantes.',
                'date' => '04-23',
                'year' => null, // 1616 fuera del rango YEAR
                'category' => 'Literatura',
                'importance_level' => 'high',
                'is_recurring' => true,
            ],
            [
                'title' => 'Nacimiento de Federico García Lorca',
                'description' => 'Natalicio del gran poeta y dramaturgo español.',
                'date' => '06-05',
                'year' => null, // 1898 fuera del rango YEAR
                'category' => 'Literatura',
                'importance_level' => 'high',
                'is_recurring' => true,
            ],
            [
                'title' => 'Muerte de Miguel de Cervantes',
                'description' => 'Fallecimiento del autor de Don Quijote de la Mancha.',
                'date' => '04-22',
                'year' => null, // 1616 fuera del rango YEAR
                'category' => 'Literatura',
                'importance_level' => 'high',
                'is_recurring' => true,
            ],

            // Arte
            [
                'title' => 'Nacimiento de Pablo Picasso',
                'description' => 'Natalicio del pintor malagueño, cofundador del cubismo.',
                'date' => '10-25',
                'year' => null, // 1881 fuera del rango YEAR
                'category' => 'Arte',
                'importance_level' => 'high',
                'is_recurring' => true,
            ],
            [
                'title' => 'Nacimiento de Salvador Dalí',
                'description' => 'Natalicio del pintor surrealista catalán.',
                'date' => '05-11',
                'year' => 1904,
                'category' => 'Arte',
                'importance_level' => 'high',
                'is_recurring' => true,
            ],
            [
                'title' => 'Inauguración del Museo del Prado',
                'description' => 'Apertura del Museo Nacional del Prado en Madrid.',
                'date' => '11-19',
                'year' => null, // 1819 fuera del rango YEAR
                'category' => 'Arte',
                'importance_level' => 'high',
                'is_recurring' => true,
            ],

            // Música
            [
                'title' => 'Nacimiento de Paco de Lucía',
                'description' => 'Natalicio del guitarrista flamenco más influyente.',
                'date' => '12-21',
                'year' => 1947,
                'category' => 'Música',
                'importance_level' => 'high',
                'is_recurring' => true,
            ],
            [
                'title' => 'Festival de Eurovisión en España',
                'description' => 'España organizó el Festival de Eurovisión en Barcelona.',
                'date' => '05-06',
                'year' => 1969,
                'category' => 'Música',
                'importance_level' => 'medium',
                'is_recurring' => false,
            ],

            // Cine
            [
                'title' => 'Primeros Premios Goya',
                'description' => 'Primera edición de los Premios Goya del cine español.',
                'date' => '03-17',
                'year' => 1987,
                'category' => 'Cine',
                'importance_level' => 'medium',
                'is_recurring' => true,
            ],
            [
                'title' => 'Nacimiento de Luis Buñuel',
                'description' => 'Natalicio del director de cine aragonés.',
                'date' => '02-22',
                'year' => null, // 1900 fuera del rango YEAR
                'category' => 'Cine',
                'importance_level' => 'high',
                'is_recurring' => true,
            ],

            // Ciencia
            [
                'title' => 'Premio Nobel de Santiago Ramón y Cajal',
                'description' => 'Santiago Ramón y Cajal recibe el Premio Nobel de Medicina.',
                'date' => '12-10',
                'year' => 1906,
                'category' => 'Ciencia',
                'importance_level' => 'high',
                'is_recurring' => true,
            ],
            [
                'title' => 'Nacimiento de Santiago Ramón y Cajal',
                'description' => 'Natalicio del médico e investigador español, padre de la neurociencia.',
                'date' => '05-01',
                'year' => null, // 1852 fuera del rango YEAR
                'category' => 'Ciencia',
                'importance_level' => 'high',
                'is_recurring' => true,
            ],

            // Deportes
            [
                'title' => 'España campeona del mundo de fútbol',
                'description' => 'La selección española gana su primer Mundial de Fútbol en Sudáfrica.',
                'date' => '07-11',
                'year' => 2010,
                'category' => 'Deportes',
                'importance_level' => 'high',
                'is_recurring' => true,
            ],
            [
                'title' => 'Juegos Olímpicos de Barcelona',
                'description' => 'Inauguración de los Juegos Olímpicos de Barcelona 1992.',
                'date' => '07-25',
                'year' => 1992,
                'category' => 'Deportes',
                'importance_level' => 'high',
                'is_recurring' => true,
            ],

            // Tradiciones
            [
                'title' => 'San Fermín - Encierro de Pamplona',
                'description' => 'Inicio de las fiestas de San Fermín en Pamplona.',
                'date' => '07-07',
                'category' => 'Tradición',
                'importance_level' => 'medium',
                'is_recurring' => true,
            ],
            [
                'title' => 'La Tomatina de Buñol',
                'description' => 'Festival de la Tomatina en Buñol, Valencia.',
                'date' => '08-30',
                'category' => 'Tradición',
                'importance_level' => 'medium',
                'is_recurring' => true,
            ],
            [
                'title' => 'Día de Santiago Apóstol',
                'description' => 'Festividad del patrón de España.',
                'date' => '07-25',
                'category' => 'Tradición',
                'importance_level' => 'medium',
                'is_recurring' => true,
            ],
        ];
    }

    /**
     * Mostrar estadísticas de las efemérides creadas.
     */
    private function showStatistics(): void
    {
        $stats = [
            'Total efemérides' => Anniversary::count(),
            'Con año específico' => Anniversary::whereNotNull('year')->count(),
            'Sin año específico' => Anniversary::whereNull('year')->count(),
            'Efemérides de enero' => Anniversary::where('month', 1)->count(),
            'Efemérides de mayo' => Anniversary::where('month', 5)->count(),
            'Efemérides de julio' => Anniversary::where('month', 7)->count(),
            'Efemérides de octubre' => Anniversary::where('month', 10)->count(),
            'Efemérides de diciembre' => Anniversary::where('month', 12)->count(),
        ];

        $this->command->info("\n📊 Estadísticas de efemérides:");
        foreach ($stats as $type => $count) {
            $this->command->info("   {$type}: {$count}");
        }

        // Efemérides por mes
        $months = Anniversary::selectRaw('month, COUNT(*) as count')
                            ->groupBy('month')
                            ->orderBy('count', 'desc')
                            ->limit(5)
                            ->get();

        if ($months->isNotEmpty()) {
            $monthNames = [
                1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
                5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
                9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
            ];
            
            $this->command->info("\n📅 Meses con más efemérides:");
            foreach ($months as $monthData) {
                $monthName = $monthNames[$monthData->month] ?? "Mes {$monthData->month}";
                $this->command->info("   {$monthName}: {$monthData->count}");
            }
        }

        // Próximas efemérides (este mes)
        $currentMonth = now()->month;
        $thisMonth = Anniversary::where('month', $currentMonth)
                               ->orderBy('day')
                               ->limit(5)
                               ->get();

        if ($thisMonth->isNotEmpty()) {
            $this->command->info("\n🗓️ Efemérides de este mes:");
            foreach ($thisMonth as $anniversary) {
                $this->command->info("   {$anniversary->day}: {$anniversary->title}");
            }
        }
    }
}
