<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PointOfInterest;
use App\Models\Municipality;

class PointOfInterestSeeder extends Seeder
{
    /**
     * Ejecutar el seeder para puntos de interés españoles.
     */
    public function run(): void
    {
        $this->command->info('Creando puntos de interés españoles...');

        // Buscar municipios existentes
        $municipalities = Municipality::all();
        if ($municipalities->isEmpty()) {
            $this->command->warn('No hay municipios. Creando algunos básicos...');
            $municipalities = $this->createBasicMunicipalities();
        }

        // Crear puntos de interés famosos españoles
        $famousPOIs = $this->getFamousSpanishPOIs();
        $createdCount = 0;

        foreach ($famousPOIs as $poiData) {
            $municipality = $municipalities->where('name', $poiData['municipality'])->first() 
                         ?? $municipalities->random();

            $poi = PointOfInterest::firstOrCreate(
                ['slug' => \Str::slug($poiData['name'])],
                [
                    'name' => $poiData['name'],
                    'slug' => \Str::slug($poiData['name']),
                    'address' => $poiData['address'],
                    'type' => $poiData['type'],
                    'latitude' => $poiData['latitude'],
                    'longitude' => $poiData['longitude'],
                    'municipality_id' => $municipality->id,
                    'source' => 'manual',
                    'description' => $poiData['description'],
                    'is_cultural_center' => $poiData['is_cultural_center'] ?? false,
                    'is_energy_installation' => $poiData['is_energy_installation'] ?? false,
                    'is_cooperative_office' => $poiData['is_cooperative_office'] ?? false,
                    'opening_hours' => $poiData['opening_hours'] ?? null,
                ]
            );
            
            if ($poi->wasRecentlyCreated) {
                $createdCount++;
            }
        }

        $this->command->info("✅ Creados {$createdCount} puntos de interés famosos");

        // Crear puntos de interés energéticos
        $energyPOIs = $this->createEnergyPOIs($municipalities);
        $this->command->info("✅ Creados {$energyPOIs} puntos de interés energéticos");

        // Crear oficinas de cooperativas
        $cooperativePOIs = $this->createCooperativeOffices($municipalities);
        $this->command->info("✅ Creadas {$cooperativePOIs} oficinas de cooperativas");

        // Mostrar estadísticas
        $this->showStatistics();
    }

    /**
     * Puntos de interés famosos españoles.
     */
    private function getFamousSpanishPOIs(): array
    {
        return [
            // Madrid
            [
                'name' => 'Museo del Prado',
                'address' => 'Calle de Ruiz de Alarcón, 23, 28014 Madrid',
                'type' => 'museum',
                'latitude' => 40.4138,
                'longitude' => -3.6921,
                'municipality' => 'Madrid',
                'description' => 'Uno de los museos más importantes del mundo con obras de Velázquez, Goya y El Greco.',
                'is_cultural_center' => true,
                'opening_hours' => ['lunes-sabado' => '10:00-20:00', 'domingo' => '10:00-19:00'],
            ],
            [
                'name' => 'Palacio Real de Madrid',
                'address' => 'Calle de Bailén, s/n, 28071 Madrid',
                'type' => 'monument',
                'latitude' => 40.4179,
                'longitude' => -3.7146,
                'municipality' => 'Madrid',
                'description' => 'Residencia oficial de la Familia Real Española, usado para ceremonias de Estado.',
                'is_cultural_center' => true,
                'opening_hours' => ['lunes-domingo' => '10:00-18:00'],
            ],
            [
                'name' => 'Parque del Retiro',
                'address' => 'Plaza de la Independencia, 7, 28001 Madrid',
                'type' => 'park',
                'latitude' => 40.4153,
                'longitude' => -3.6844,
                'municipality' => 'Madrid',
                'description' => 'Parque histórico y artístico de Madrid, Patrimonio de la Humanidad.',
                'is_cultural_center' => false,
                'opening_hours' => ['lunes-domingo' => '06:00-24:00'],
            ],

            // Barcelona
            [
                'name' => 'Sagrada Familia',
                'address' => 'Carrer de Mallorca, 401, 08013 Barcelona',
                'type' => 'monument',
                'latitude' => 41.4036,
                'longitude' => 2.1744,
                'municipality' => 'Barcelona',
                'description' => 'Basílica diseñada por Antoni Gaudí, símbolo de Barcelona.',
                'is_cultural_center' => true,
                'opening_hours' => ['lunes-domingo' => '09:00-18:00'],
            ],
            [
                'name' => 'Park Güell',
                'address' => 'Carrer d\'Olot, s/n, 08024 Barcelona',
                'type' => 'park',
                'latitude' => 41.4145,
                'longitude' => 2.1527,
                'municipality' => 'Barcelona',
                'description' => 'Parque público con jardines y elementos arquitectónicos de Gaudí.',
                'is_cultural_center' => true,
                'opening_hours' => ['lunes-domingo' => '08:00-18:00'],
            ],
            [
                'name' => 'Casa Batlló',
                'address' => 'Passeig de Gràcia, 43, 08007 Barcelona',
                'type' => 'museum',
                'latitude' => 41.3916,
                'longitude' => 2.1649,
                'municipality' => 'Barcelona',
                'description' => 'Casa modernista diseñada por Antoni Gaudí.',
                'is_cultural_center' => true,
                'opening_hours' => ['lunes-domingo' => '09:00-20:00'],
            ],

            // Sevilla
            [
                'name' => 'Catedral de Sevilla',
                'address' => 'Avenida de la Constitución, s/n, 41004 Sevilla',
                'type' => 'monument',
                'latitude' => 37.3858,
                'longitude' => -5.9933,
                'municipality' => 'Sevilla',
                'description' => 'Catedral gótica más grande del mundo, Patrimonio de la Humanidad.',
                'is_cultural_center' => true,
                'opening_hours' => ['lunes-sabado' => '11:00-17:00', 'domingo' => '14:30-18:00'],
            ],
            [
                'name' => 'Real Alcázar de Sevilla',
                'address' => 'Patio de Banderas, s/n, 41004 Sevilla',
                'type' => 'monument',
                'latitude' => 37.3830,
                'longitude' => -5.9931,
                'municipality' => 'Sevilla',
                'description' => 'Complejo palaciego fortificado, ejemplo del arte mudéjar.',
                'is_cultural_center' => true,
                'opening_hours' => ['lunes-domingo' => '09:30-17:00'],
            ],

            // Valencia
            [
                'name' => 'Ciudad de las Artes y las Ciencias',
                'address' => 'Av. del Professor López Piñero, 7, 46013 Valencia',
                'type' => 'other',
                'latitude' => 39.4547,
                'longitude' => -0.3492,
                'municipality' => 'Valencia',
                'description' => 'Complejo arquitectónico, cultural y de entretenimiento.',
                'is_cultural_center' => true,
                'opening_hours' => ['lunes-domingo' => '10:00-18:00'],
            ],

            // Granada
            [
                'name' => 'Alhambra',
                'address' => 'Calle Real de la Alhambra, s/n, 18009 Granada',
                'type' => 'monument',
                'latitude' => 37.1760,
                'longitude' => -3.5881,
                'municipality' => 'Granada',
                'description' => 'Conjunto palaciego nazarí, Patrimonio de la Humanidad.',
                'is_cultural_center' => true,
                'opening_hours' => ['lunes-domingo' => '08:30-18:00'],
            ],

            // Bilbao
            [
                'name' => 'Museo Guggenheim Bilbao',
                'address' => 'Abandoibarra Etorb., 2, 48009 Bilbao',
                'type' => 'museum',
                'latitude' => 43.2687,
                'longitude' => -2.9340,
                'municipality' => 'Bilbao',
                'description' => 'Museo de arte contemporáneo diseñado por Frank Gehry.',
                'is_cultural_center' => true,
                'opening_hours' => ['martes-domingo' => '10:00-20:00'],
            ],

            // Santiago de Compostela
            [
                'name' => 'Catedral de Santiago de Compostela',
                'address' => 'Praza do Obradoiro, s/n, 15704 Santiago de Compostela',
                'type' => 'monument',
                'latitude' => 42.8805,
                'longitude' => -8.5456,
                'municipality' => 'Santiago de Compostela',
                'description' => 'Destino final del Camino de Santiago, Patrimonio de la Humanidad.',
                'is_cultural_center' => true,
                'opening_hours' => ['lunes-domingo' => '07:00-20:30'],
            ],
        ];
    }

    /**
     * Crear puntos de interés energéticos.
     */
    private function createEnergyPOIs($municipalities): int
    {
        $energyPOIs = [
            [
                'name' => 'Parque Solar Fotovoltaico Mula',
                'type' => 'other',
                'description' => 'Una de las plantas solares más grandes de España.',
                'is_energy_installation' => true,
            ],
            [
                'name' => 'Parque Eólico Cabo de Trafalgar',
                'type' => 'other',
                'description' => 'Parque eólico marino en la costa andaluza.',
                'is_energy_installation' => true,
            ],
            [
                'name' => 'Central Hidroeléctrica Aldeadávila',
                'type' => 'other',
                'description' => 'Central hidroeléctrica en el río Duero.',
                'is_energy_installation' => true,
            ],
            [
                'name' => 'Planta Termosolar Gemasolar',
                'type' => 'other',
                'description' => 'Primera planta comercial termosolar con tecnología de torre.',
                'is_energy_installation' => true,
            ],
        ];

        $count = 0;
        foreach ($energyPOIs as $poiData) {
            $municipality = $municipalities->random();
            
            PointOfInterest::firstOrCreate(
                ['slug' => \Str::slug($poiData['name'])],
                [
                    'name' => $poiData['name'],
                    'slug' => \Str::slug($poiData['name']),
                    'address' => 'España',
                    'type' => $poiData['type'],
                    'latitude' => fake()->randomFloat(6, 36.0, 43.0),
                    'longitude' => fake()->randomFloat(6, -9.0, 3.0),
                    'municipality_id' => $municipality->id,
                    'source' => 'manual',
                    'description' => $poiData['description'],
                    'is_cultural_center' => false,
                    'is_energy_installation' => $poiData['is_energy_installation'],
                    'is_cooperative_office' => false,
                ]
            );
            $count++;
        }

        return $count;
    }

    /**
     * Crear oficinas de cooperativas energéticas.
     */
    private function createCooperativeOffices($municipalities): int
    {
        $cooperativeOffices = [
            [
                'name' => 'Som Energia - Oficina Barcelona',
                'description' => 'Oficina principal de Som Energia en Barcelona.',
            ],
            [
                'name' => 'Goiener - Oficina Pamplona',
                'description' => 'Sede de la cooperativa energética Goiener.',
            ],
            [
                'name' => 'Zencer - Oficina Madrid',
                'description' => 'Oficina de la cooperativa Zencer en Madrid.',
            ],
            [
                'name' => 'EnergÉtica - Oficina Valencia',
                'description' => 'Cooperativa energética valenciana.',
            ],
        ];

        $count = 0;
        foreach ($cooperativeOffices as $officeData) {
            $municipality = $municipalities->random();
            
            PointOfInterest::firstOrCreate(
                ['slug' => \Str::slug($officeData['name'])],
                [
                    'name' => $officeData['name'],
                    'slug' => \Str::slug($officeData['name']),
                    'address' => 'España',
                    'type' => 'other',
                    'latitude' => fake()->randomFloat(6, 36.0, 43.0),
                    'longitude' => fake()->randomFloat(6, -9.0, 3.0),
                    'municipality_id' => $municipality->id,
                    'source' => 'manual',
                    'description' => $officeData['description'],
                    'is_cultural_center' => false,
                    'is_energy_installation' => false,
                    'is_cooperative_office' => true,
                    'opening_hours' => ['lunes-viernes' => '09:00-17:00'],
                ]
            );
            $count++;
        }

        return $count;
    }

    /**
     * Crear municipios básicos si no existen.
     */
    private function createBasicMunicipalities()
    {
        $basicMunicipalities = [
            ['name' => 'Madrid', 'slug' => 'madrid'],
            ['name' => 'Barcelona', 'slug' => 'barcelona'],
            ['name' => 'Sevilla', 'slug' => 'sevilla'],
            ['name' => 'Valencia', 'slug' => 'valencia'],
            ['name' => 'Granada', 'slug' => 'granada'],
            ['name' => 'Bilbao', 'slug' => 'bilbao'],
            ['name' => 'Santiago de Compostela', 'slug' => 'santiago-de-compostela'],
        ];

        $municipalities = collect();
        foreach ($basicMunicipalities as $municipalityData) {
            $municipality = Municipality::create($municipalityData);
            $municipalities->push($municipality);
        }

        return $municipalities;
    }

    /**
     * Mostrar estadísticas de los puntos de interés creados.
     */
    private function showStatistics(): void
    {
        $stats = [
            'Total puntos de interés' => PointOfInterest::count(),
            'Centros culturales' => PointOfInterest::where('is_cultural_center', true)->count(),
            'Instalaciones energéticas' => PointOfInterest::where('is_energy_installation', true)->count(),
            'Oficinas cooperativas' => PointOfInterest::where('is_cooperative_office', true)->count(),
            'Museos' => PointOfInterest::where('type', 'museum')->count(),
            'Monumentos' => PointOfInterest::where('type', 'monument')->count(),
            'Parques' => PointOfInterest::where('type', 'park')->count(),
            'Otros' => PointOfInterest::where('type', 'other')->count(),
        ];

        $this->command->info("\n📊 Estadísticas de puntos de interés:");
        foreach ($stats as $type => $count) {
            $this->command->info("   {$type}: {$count}");
        }

        // Tipos más comunes
        $types = PointOfInterest::selectRaw('type, COUNT(*) as count')
                               ->groupBy('type')
                               ->orderBy('count', 'desc')
                               ->limit(5)
                               ->get();

        if ($types->isNotEmpty()) {
            $this->command->info("\n📍 Tipos más comunes:");
            foreach ($types as $type) {
                $this->command->info("   {$type->type}: {$type->count}");
            }
        }

        // Distribución por funcionalidad
        $this->command->info("\n🎯 Distribución funcional:");
        $this->command->info("   🎭 Culturales: " . PointOfInterest::where('is_cultural_center', true)->count());
        $this->command->info("   ⚡ Energéticas: " . PointOfInterest::where('is_energy_installation', true)->count());
        $this->command->info("   🤝 Cooperativas: " . PointOfInterest::where('is_cooperative_office', true)->count());
    }
}
