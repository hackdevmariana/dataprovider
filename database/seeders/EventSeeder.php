<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Event;
use App\Models\EventType;
use App\Models\Venue;
use App\Models\Artist;

class EventSeeder extends Seeder
{
    /**
     * Ejecutar el seeder para eventos españoles.
     */
    public function run(): void
    {
        $this->command->info('Creando eventos culturales españoles...');

        // Verificar que existan tipos de evento
        $eventTypes = EventType::all();
        if ($eventTypes->isEmpty()) {
            $this->command->warn('No hay tipos de evento. Creando algunos básicos...');
            $eventTypes = collect([
                EventType::factory()->create(['name' => 'Concierto', 'slug' => 'concierto']),
                EventType::factory()->create(['name' => 'Teatro', 'slug' => 'teatro']),
                EventType::factory()->create(['name' => 'Exposición', 'slug' => 'exposicion']),
                EventType::factory()->create(['name' => 'Festival', 'slug' => 'festival']),
                EventType::factory()->create(['name' => 'Conferencia', 'slug' => 'conferencia']),
            ]);
        }

        // Verificar que existan venues
        $venues = Venue::all();
        if ($venues->isEmpty()) {
            $this->command->warn('No hay venues. Creando algunos...');
            $venues = $this->createBasicVenues();
        }

        // Crear eventos famosos españoles
        $famousEvents = $this->getFamousSpanishEvents();
        $createdCount = 0;

        foreach ($famousEvents as $eventData) {
            // Crear fecha y hora combinadas
            $startDate = $eventData['start_date'] ?? fake()->dateTimeBetween('now', '+6 months')->format('Y-m-d');
            $startTime = $eventData['start_time'] ?? '20:00:00';
            $startDatetime = $startDate . ' ' . $startTime;
            
            $endDatetime = null;
            if (isset($eventData['end_date'])) {
                $endTime = $eventData['end_time'] ?? '23:59:59';
                $endDatetime = $eventData['end_date'] . ' ' . $endTime;
            }

            $event = Event::firstOrCreate(
                ['slug' => \Str::slug($eventData['name'])],
                [
                'title' => $eventData['name'],
                'slug' => \Str::slug($eventData['name']),
                'description' => $eventData['description'],
                'start_datetime' => $startDatetime,
                'end_datetime' => $endDatetime,
                'event_type_id' => $eventTypes->where('name', $eventData['event_type'])->first()?->id ?? $eventTypes->random()->id,
                'venue_id' => $venues->random()->id,
                'price' => $eventData['price_min'] ?? null,
                'is_free' => $eventData['is_free'] ?? false,
                'audience_size_estimate' => $eventData['capacity'] ?? fake()->numberBetween(100, 5000),
                'source_url' => $eventData['website'] ?? null,
                ]
            );
            
            if ($event->wasRecentlyCreated) {
                $createdCount++;
            }
        }

        $this->command->info("✅ Creados {$createdCount} eventos famosos españoles");

        // Crear eventos adicionales con factory (comentado por problemas de duplicados)
        // $additionalEvents = Event::factory()
        //     ->count(50)
        //     ->create();
        // $this->command->info("✅ Creados {$additionalEvents->count()} eventos adicionales");
        
        $this->command->info("⚠️ Eventos adicionales omitidos (factory con problemas)");

        // Asociar algunos artistas a eventos si existen
        $artists = Artist::all();
        if ($artists->isNotEmpty()) {
            $eventsWithArtists = Event::inRandomOrder()->limit(20)->get();
            foreach ($eventsWithArtists as $event) {
                $event->artists()->attach(
                    $artists->random(fake()->numberBetween(1, 3))->pluck('id'),
                    ['role' => fake()->randomElement(['headliner', 'support', 'guest'])]
                );
            }
            $this->command->info("✅ Asociados artistas a 20 eventos");
        }

        // Mostrar estadísticas
        $this->showStatistics();
    }

    /**
     * Datos de eventos famosos españoles.
     */
    private function getFamousSpanishEvents(): array
    {
        return [
            // Festivales de música
            [
                'name' => 'Festival Internacional de Benicàssim',
                'description' => 'Uno de los festivales de música más importantes de España.',
                'event_type' => 'Festival',
                'start_date' => '2025-07-17',
                'end_date' => '2025-07-20',
                'start_time' => '18:00:00',
                'price_min' => 180.00,
                'price_max' => 250.00,
                'capacity' => 50000,
                'organizer_name' => 'FIB',
                'website' => 'https://www.fiberfib.com',
                'language' => 'es',
            ],
            [
                'name' => 'Primavera Sound Barcelona',
                'description' => 'Festival de música independiente y alternativa.',
                'event_type' => 'Festival',
                'start_date' => '2025-06-05',
                'end_date' => '2025-06-08',
                'price_min' => 220.00,
                'price_max' => 350.00,
                'capacity' => 55000,
                'organizer_name' => 'Primavera Sound',
                'website' => 'https://www.primaverasound.com',
            ],
            [
                'name' => 'Festival de Flamenco de Jerez',
                'description' => 'Festival internacional dedicado al flamenco.',
                'event_type' => 'Festival',
                'start_date' => '2025-02-28',
                'end_date' => '2025-03-15',
                'price_min' => 25.00,
                'price_max' => 80.00,
                'capacity' => 2000,
                'organizer_name' => 'Ayuntamiento de Jerez',
                'language' => 'es',
            ],

            // Teatro y artes escénicas
            [
                'name' => 'Festival Internacional de Teatro Clásico de Almagro',
                'description' => 'Festival dedicado al teatro clásico español.',
                'event_type' => 'Teatro',
                'start_date' => '2025-07-04',
                'end_date' => '2025-07-27',
                'price_min' => 15.00,
                'price_max' => 45.00,
                'capacity' => 800,
                'organizer_name' => 'Compañía Nacional de Teatro Clásico',
                'language' => 'es',
            ],
            [
                'name' => 'La Casa de Bernarda Alba - Teatro Español',
                'description' => 'Representación de la obra clásica de García Lorca.',
                'event_type' => 'Teatro',
                'start_date' => '2025-04-15',
                'end_date' => '2025-05-30',
                'start_time' => '20:30:00',
                'price_min' => 20.00,
                'price_max' => 55.00,
                'capacity' => 450,
                'organizer_name' => 'Teatro Español',
                'language' => 'es',
            ],

            // Exposiciones
            [
                'name' => 'Picasso y el Arte Contemporáneo',
                'description' => 'Exposición sobre la influencia de Picasso en el arte actual.',
                'event_type' => 'Exposición',
                'start_date' => '2025-03-01',
                'end_date' => '2025-06-15',
                'start_time' => '10:00:00',
                'end_time' => '18:00:00',
                'price_min' => 12.00,
                'price_max' => 18.00,
                'organizer_name' => 'Museo Reina Sofía',
                'language' => 'es',
            ],
            [
                'name' => 'Dalí, Ser y Parecer',
                'description' => 'Retrospectiva completa de Salvador Dalí.',
                'event_type' => 'Exposición',
                'start_date' => '2025-02-10',
                'end_date' => '2025-07-20',
                'price_min' => 10.00,
                'price_max' => 15.00,
                'organizer_name' => 'Fundación Dalí',
                'language' => 'es',
            ],

            // Conciertos
            [
                'name' => 'Concierto de Año Nuevo - Orquesta Nacional',
                'description' => 'Concierto tradicional de Año Nuevo.',
                'event_type' => 'Concierto',
                'start_date' => '2025-01-01',
                'start_time' => '12:00:00',
                'price_min' => 35.00,
                'price_max' => 120.00,
                'capacity' => 1800,
                'organizer_name' => 'Orquesta Nacional de España',
                'language' => 'es',
            ],
            [
                'name' => 'Rosalía - Motomami World Tour',
                'description' => 'Gira mundial de Rosalía presentando su álbum Motomami.',
                'event_type' => 'Concierto',
                'start_date' => '2025-05-20',
                'start_time' => '21:00:00',
                'price_min' => 45.00,
                'price_max' => 150.00,
                'capacity' => 15000,
                'organizer_name' => 'Live Nation',
                'social_media_links' => [
                    'instagram' => '@rosalia.vt',
                    'twitter' => '@rosaliavt'
                ],
            ],

            // Eventos culturales especiales
            [
                'name' => 'Noche en Blanco Madrid',
                'description' => 'Noche cultural con museos y espacios abiertos gratuitamente.',
                'event_type' => 'Festival',
                'start_date' => '2025-09-21',
                'start_time' => '20:00:00',
                'end_time' => '06:00:00',
                'is_free' => true,
                'capacity' => 100000,
                'organizer_name' => 'Ayuntamiento de Madrid',
                'language' => 'es',
            ],
            [
                'name' => 'Feria del Libro de Madrid',
                'description' => 'Feria anual del libro en el Parque del Retiro.',
                'event_type' => 'Festival',
                'start_date' => '2025-05-30',
                'end_date' => '2025-06-15',
                'start_time' => '11:00:00',
                'end_time' => '21:00:00',
                'is_free' => true,
                'capacity' => 20000,
                'organizer_name' => 'Gremio de Editores de Madrid',
                'language' => 'es',
            ],
        ];
    }

    /**
     * Crear venues básicos sin usar factory.
     */
    private function createBasicVenues()
    {
        $basicVenues = [
            ['name' => 'Teatro Real Madrid', 'slug' => 'teatro-real-madrid', 'capacity' => 1748],
            ['name' => 'Palau de la Música Catalana', 'slug' => 'palau-musica-catalana', 'capacity' => 2146],
            ['name' => 'Kursaal San Sebastián', 'slug' => 'kursaal-san-sebastian', 'capacity' => 1800],
            ['name' => 'Palacio de Festivales Santander', 'slug' => 'palacio-festivales-santander', 'capacity' => 1700],
            ['name' => 'Teatro de la Zarzuela', 'slug' => 'teatro-zarzuela', 'capacity' => 1245],
            ['name' => 'Gran Teatro del Liceo', 'slug' => 'gran-teatro-liceo', 'capacity' => 2292],
            ['name' => 'Auditorio Nacional Madrid', 'slug' => 'auditorio-nacional-madrid', 'capacity' => 2293],
            ['name' => 'Palau de Congressos Barcelona', 'slug' => 'palau-congressos-barcelona', 'capacity' => 3000],
            ['name' => 'Teatro Español Madrid', 'slug' => 'teatro-espanol-madrid', 'capacity' => 450],
            ['name' => 'Centro Cultural Conde Duque', 'slug' => 'centro-cultural-conde-duque', 'capacity' => 800],
        ];

        // Buscar un municipio existente o crear uno genérico
        $municipality = \App\Models\Municipality::first();
        if (!$municipality) {
            $municipality = \App\Models\Municipality::create([
                'name' => 'Madrid',
                'slug' => 'madrid',
            ]);
        }

        $venues = collect();
        foreach ($basicVenues as $venueData) {
            $venue = Venue::create([
                'name' => $venueData['name'],
                'slug' => $venueData['slug'],
                'capacity' => $venueData['capacity'],
                'address' => 'España',
                'municipality_id' => $municipality->id,
                'latitude' => 40.4168, // Madrid coordinates
                'longitude' => -3.7038,
                'venue_type' => 'auditorium',
                'venue_status' => 'active',
                'is_verified' => true,
            ]);
            $venues->push($venue);
        }

        return $venues;
    }

    /**
     * Mostrar estadísticas de los eventos creados.
     */
    private function showStatistics(): void
    {
        $stats = [
            'Total eventos' => Event::count(),
            'Eventos gratuitos' => Event::where('is_free', true)->count(),
            'Eventos de pago' => Event::where('is_free', false)->count(),
            'Con venue asignado' => Event::whereNotNull('venue_id')->count(),
            'Con precio definido' => Event::whereNotNull('price')->count(),
            'Con tipo de evento' => Event::whereNotNull('event_type_id')->count(),
            'Con fecha de fin' => Event::whereNotNull('end_datetime')->count(),
            'Con URL fuente' => Event::whereNotNull('source_url')->count(),
        ];

        $this->command->info("\n📊 Estadísticas de eventos:");
        foreach ($stats as $type => $count) {
            $this->command->info("   {$type}: {$count}");
        }

        // Estadísticas de precios
        $avgPrice = Event::where('is_free', false)->whereNotNull('price')->avg('price');
        $avgCapacity = Event::whereNotNull('audience_size_estimate')->avg('audience_size_estimate');

        if ($avgPrice) {
            $this->command->info("   Precio promedio: " . round($avgPrice, 2) . " €");
        }
        if ($avgCapacity) {
            $this->command->info("   Capacidad promedio: " . round($avgCapacity, 0) . " personas");
        }

        // Eventos por tipo
        $eventTypes = Event::join('event_types', 'events.event_type_id', '=', 'event_types.id')
                           ->selectRaw('event_types.name, COUNT(*) as count')
                           ->groupBy('event_types.name')
                           ->orderBy('count', 'desc')
                           ->get();

        if ($eventTypes->isNotEmpty()) {
            $this->command->info("\n🎭 Eventos por tipo:");
            foreach ($eventTypes as $type) {
                $this->command->info("   {$type->name}: {$type->count}");
            }
        }

        // Eventos con artistas
        $eventsWithArtists = Event::has('artists')->count();
        if ($eventsWithArtists > 0) {
            $this->command->info("\n🎤 Eventos con artistas asociados: {$eventsWithArtists}");
        }
    }
}
