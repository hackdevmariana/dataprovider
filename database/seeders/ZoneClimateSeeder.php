<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ZoneClimate;

class ZoneClimateSeeder extends Seeder
{
    /**
     * Ejecutar el seeder para zonas climáticas españolas.
     */
    public function run(): void
    {
        $this->command->info('Creando zonas climáticas españolas para energía solar...');

        // Crear zonas climáticas españolas basadas en datos reales
        $climateZones = $this->getSpanishClimateZones();
        $createdCount = 0;

        foreach ($climateZones as $zoneData) {
            $zone = ZoneClimate::firstOrCreate(
                ['climate_zone' => $zoneData['name']],
                [
                    'climate_zone' => $zoneData['name'],
                    'description' => $zoneData['description'],
                    'average_heating_demand' => null, // No tenemos estos datos
                    'average_cooling_demand' => null, // No tenemos estos datos
                ]
            );
            
            if ($zone->wasRecentlyCreated) {
                $createdCount++;
            }
        }

        $this->command->info("✅ Creadas {$createdCount} zonas climáticas españolas");

        // Mostrar estadísticas
        $this->showStatistics();
    }

    /**
     * Zonas climáticas españolas con datos reales de producción solar.
     */
    private function getSpanishClimateZones(): array
    {
        return [
            // Zonas de alta irradiación solar
            [
                'name' => 'Andalucía Oriental',
                'avg_kwh_per_kw_year' => 1650,
                'description' => 'Zona de máxima irradiación solar en España. Incluye Almería, Granada oriental y Málaga interior. Ideal para instalaciones fotovoltaicas con rendimientos superiores a 1600 kWh/kWp año.',
            ],
            [
                'name' => 'Andalucía Occidental',
                'avg_kwh_per_kw_year' => 1580,
                'description' => 'Alta irradiación solar con ligera influencia atlántica. Incluye Sevilla, Córdoba, Cádiz y Huelva. Excelente para energía solar con rendimientos de 1500-1600 kWh/kWp año.',
            ],
            [
                'name' => 'Extremadura',
                'avg_kwh_per_kw_year' => 1520,
                'description' => 'Zona continental con alta irradiación solar. Incluye Badajoz y Cáceres. Muy favorable para instalaciones solares con rendimientos de 1450-1550 kWh/kWp año.',
            ],
            [
                'name' => 'Murcia y Alicante',
                'avg_kwh_per_kw_year' => 1600,
                'description' => 'Clima mediterráneo seco con alta irradiación. Incluye toda Murcia y sur de Alicante. Rendimientos solares entre 1550-1650 kWh/kWp año.',
            ],
            [
                'name' => 'Castilla-La Mancha Sur',
                'avg_kwh_per_kw_year' => 1480,
                'description' => 'Meseta sur con alta irradiación solar. Incluye Ciudad Real, Albacete y sur de Toledo. Rendimientos solares de 1400-1500 kWh/kWp año.',
            ],

            // Zonas de irradiación media-alta
            [
                'name' => 'Valencia y Castellón',
                'avg_kwh_per_kw_year' => 1450,
                'description' => 'Costa mediterránea con buena irradiación. Incluye Valencia y Castellón. Rendimientos solares de 1350-1450 kWh/kWp año.',
            ],
            [
                'name' => 'Cataluña Sur',
                'avg_kwh_per_kw_year' => 1420,
                'description' => 'Mediterráneo catalán con buena irradiación. Incluye Tarragona y sur de Barcelona. Rendimientos de 1350-1420 kWh/kWp año.',
            ],
            [
                'name' => 'Aragón Sur',
                'avg_kwh_per_kw_year' => 1400,
                'description' => 'Valle del Ebro con clima continental seco. Incluye Zaragoza y Teruel. Buenos rendimientos solares de 1300-1400 kWh/kWp año.',
            ],
            [
                'name' => 'Castilla y León Sur',
                'avg_kwh_per_kw_year' => 1350,
                'description' => 'Meseta norte con irradiación media-alta. Incluye Salamanca, Ávila y sur de Valladolid. Rendimientos de 1250-1350 kWh/kWp año.',
            ],
            [
                'name' => 'Madrid',
                'avg_kwh_per_kw_year' => 1420,
                'description' => 'Clima continental de la capital. Buena irradiación solar con rendimientos de 1350-1450 kWh/kWp año.',
            ],

            // Zonas de irradiación media
            [
                'name' => 'Cataluña Norte',
                'avg_kwh_per_kw_year' => 1320,
                'description' => 'Norte de Cataluña con influencia pirenaica. Incluye Girona y norte de Barcelona. Rendimientos de 1250-1350 kWh/kWp año.',
            ],
            [
                'name' => 'Aragón Norte',
                'avg_kwh_per_kw_year' => 1280,
                'description' => 'Prepirineo aragonés con menor irradiación. Incluye Huesca. Rendimientos solares de 1200-1300 kWh/kWp año.',
            ],
            [
                'name' => 'Navarra',
                'avg_kwh_per_kw_year' => 1250,
                'description' => 'Transición atlántico-mediterránea. Irradiación media con rendimientos de 1150-1280 kWh/kWp año.',
            ],
            [
                'name' => 'La Rioja',
                'avg_kwh_per_kw_year' => 1300,
                'description' => 'Valle del Ebro riojano con buena irradiación. Rendimientos solares de 1200-1350 kWh/kWp año.',
            ],
            [
                'name' => 'Castilla y León Norte',
                'avg_kwh_per_kw_year' => 1220,
                'description' => 'Meseta norte con clima continental. Incluye León, Burgos y Palencia. Rendimientos de 1150-1250 kWh/kWp año.',
            ],

            // Zonas de menor irradiación
            [
                'name' => 'País Vasco',
                'avg_kwh_per_kw_year' => 1100,
                'description' => 'Clima atlántico húmedo con menor irradiación. Rendimientos solares de 1000-1150 kWh/kWp año. Excelente para energía eólica.',
            ],
            [
                'name' => 'Cantabria',
                'avg_kwh_per_kw_year' => 1080,
                'description' => 'Costa cantábrica con alta nubosidad. Menor irradiación solar pero buen potencial eólico. Rendimientos de 1000-1120 kWh/kWp año.',
            ],
            [
                'name' => 'Asturias',
                'avg_kwh_per_kw_year' => 1050,
                'description' => 'Clima oceánico húmedo con menor irradiación solar. Rendimientos de 950-1100 kWh/kWp año. Alto potencial eólico e hidroeléctrico.',
            ],
            [
                'name' => 'Galicia',
                'avg_kwh_per_kw_year' => 1120,
                'description' => 'Atlántico gallego con irradiación variable. Rendimientos de 1000-1200 kWh/kWp año. Excelente potencial eólico.',
            ],

            // Zonas insulares
            [
                'name' => 'Canarias',
                'avg_kwh_per_kw_year' => 1750,
                'description' => 'Clima subtropical con máxima irradiación de España. Rendimientos excepcionales de 1650-1800 kWh/kWp año. Ideal para autoconsumo.',
            ],
            [
                'name' => 'Baleares',
                'avg_kwh_per_kw_year' => 1500,
                'description' => 'Mediterráneo insular con alta irradiación. Rendimientos de 1400-1550 kWh/kWp año. Excelente para autoconsumo turístico.',
            ],

            // Ciudades autónomas
            [
                'name' => 'Ceuta y Melilla',
                'avg_kwh_per_kw_year' => 1620,
                'description' => 'Norte de África con alta irradiación solar. Rendimientos de 1550-1650 kWh/kWp año.',
            ],
        ];
    }

    /**
     * Mostrar estadísticas de las zonas climáticas creadas.
     */
    private function showStatistics(): void
    {
        $stats = [
            'Total zonas climáticas' => ZoneClimate::count(),
            'Con descripción' => ZoneClimate::whereNotNull('description')->count(),
            'Zonas andaluzas' => ZoneClimate::where('climate_zone', 'LIKE', '%Andalucía%')->count(),
            'Zonas catalanas' => ZoneClimate::where('climate_zone', 'LIKE', '%Cataluña%')->count(),
            'Zonas insulares' => ZoneClimate::where('climate_zone', 'LIKE', '%Canarias%')->orWhere('climate_zone', 'LIKE', '%Baleares%')->count(),
        ];

        $this->command->info("\n📊 Estadísticas de zonas climáticas:");
        foreach ($stats as $type => $count) {
            $this->command->info("   {$type}: {$count}");
        }

        // Zonas más representativas
        $canarias = ZoneClimate::where('climate_zone', 'LIKE', '%Canarias%')->first();
        $galicia = ZoneClimate::where('climate_zone', 'LIKE', '%Galicia%')->first();

        if ($canarias && $galicia) {
            $this->command->info("\n🌞 Ejemplos representativos:");
            $this->command->info("   🏆 Zona soleada: {$canarias->climate_zone}");
            $this->command->info("   🌧️ Zona húmeda: {$galicia->climate_zone}");
        }

        // Información para KiroLux
        $totalZones = ZoneClimate::count();
        $this->command->info("\n⚡ Para KiroLux:");
        $this->command->info("   🎯 Total zonas climáticas: {$totalZones}");
        $this->command->info("   💡 Cobertura geográfica: Nacional completa");
        $this->command->info("   🌍 Aplicación: Optimización energética por zona");
    }
}
