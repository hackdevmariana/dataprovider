<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Region;
use App\Models\Province;
use App\Models\AutonomousCommunity;
use App\Models\Country;
use App\Models\Timezone;
use Illuminate\Support\Str;

class RegionSeeder extends Seeder
{
    public function run()
    {
        $spain = Country::where('slug', 'espana')->first();
        if (!$spain) {
            $this->command->error('No se encontró el país España. Ejecuta primero el CountrySeeder.');
            return;
        }

        $aragon = AutonomousCommunity::where('slug', 'aragon')->first();
        if (!$aragon) {
            $this->command->error('No se encontró la comunidad autónoma de Aragón. Ejecuta primero el AutonomousCommunitySeeder.');
            return;
        }

        $madridTz = Timezone::where('name', 'Europe/Madrid')->first();
        if (!$madridTz) {
            $this->command->error('No se encontró el timezone Europe/Madrid. Ejecuta primero el TimezoneSeeder.');
            return;
        }

        // Obtener las provincias de Aragón
        $huesca = Province::where('slug', 'huesca')->first();
        $teruel = Province::where('slug', 'teruel')->first();
        $zaragoza = Province::where('slug', 'zaragoza')->first();

        if (!$huesca || !$teruel || !$zaragoza) {
            $this->command->error('No se encontraron las provincias de Aragón. Ejecuta primero el ProvinceSeeder.');
            return;
        }

        // Comarcas de Aragón organizadas por provincia
        $regions = [
            // PROVINCIA DE HUESCA
            [
                'name' => 'Alto Gállego',
                'province' => $huesca,
                'latitude' => 42.5167,
                'longitude' => -0.4500,
                'area_km2' => 1359.8,
                'altitude_m' => 1200
            ],
            [
                'name' => 'Bajo Cinca',
                'province' => $huesca,
                'latitude' => 41.6833,
                'longitude' => 0.1833,
                'area_km2' => 1397.3,
                'altitude_m' => 200
            ],
            [
                'name' => 'Cinca Medio',
                'province' => $huesca,
                'latitude' => 41.9000,
                'longitude' => 0.1500,
                'area_km2' => 576.7,
                'altitude_m' => 300
            ],
            [
                'name' => 'Hoya de Huesca',
                'province' => $huesca,
                'latitude' => 42.1400,
                'longitude' => -0.4080,
                'area_km2' => 1515.7,
                'altitude_m' => 500
            ],
            [
                'name' => 'Jacetania',
                'province' => $huesca,
                'latitude' => 42.5667,
                'longitude' => -0.5500,
                'area_km2' => 1857.9,
                'altitude_m' => 1000
            ],
            [
                'name' => 'La Litera',
                'province' => $huesca,
                'latitude' => 41.8833,
                'longitude' => 0.3000,
                'area_km2' => 733.9,
                'altitude_m' => 250
            ],
            [
                'name' => 'Monegros',
                'province' => $huesca,
                'latitude' => 41.8000,
                'longitude' => -0.2000,
                'area_km2' => 2764.4,
                'altitude_m' => 400
            ],
            [
                'name' => 'Ribagorza',
                'province' => $huesca,
                'latitude' => 42.4500,
                'longitude' => 0.4000,
                'area_km2' => 2459.8,
                'altitude_m' => 800
            ],
            [
                'name' => 'Sobrarbe',
                'province' => $huesca,
                'latitude' => 42.5167,
                'longitude' => 0.1167,
                'area_km2' => 2202.7,
                'altitude_m' => 1000
            ],
            [
                'name' => 'Somontano de Barbastro',
                'province' => $huesca,
                'latitude' => 42.0333,
                'longitude' => 0.1167,
                'area_km2' => 1167.5,
                'altitude_m' => 400
            ],

            // PROVINCIA DE TERUEL
            [
                'name' => 'Andorra-Sierra de Arcos',
                'province' => $teruel,
                'latitude' => 40.9667,
                'longitude' => -0.4333,
                'area_km2' => 675.1,
                'altitude_m' => 700
            ],
            [
                'name' => 'Bajo Aragón',
                'province' => $teruel,
                'latitude' => 40.8333,
                'longitude' => -0.4000,
                'area_km2' => 1304.2,
                'altitude_m' => 500
            ],
            [
                'name' => 'Bajo Martín',
                'province' => $teruel,
                'latitude' => 41.1000,
                'longitude' => -0.5000,
                'area_km2' => 795.3,
                'altitude_m' => 400
            ],
            [
                'name' => 'Campo de Belchite',
                'province' => $teruel,
                'latitude' => 41.3000,
                'longitude' => -0.7500,
                'area_km2' => 1047.4,
                'altitude_m' => 400
            ],
            [
                'name' => 'Campo de Daroca (Teruel)',
                'province' => $teruel,
                'latitude' => 41.1167,
                'longitude' => -1.4167,
                'area_km2' => 1111.6,
                'altitude_m' => 800
            ],
            [
                'name' => 'Cuencas Mineras',
                'province' => $teruel,
                'latitude' => 40.8000,
                'longitude' => -0.7000,
                'area_km2' => 1408.3,
                'altitude_m' => 600
            ],
            [
                'name' => 'Gúdar-Javalambre',
                'province' => $teruel,
                'latitude' => 40.4500,
                'longitude' => -0.7500,
                'area_km2' => 2358.7,
                'altitude_m' => 1200
            ],
            [
                'name' => 'Maestrazgo',
                'province' => $teruel,
                'latitude' => 40.8333,
                'longitude' => -0.4000,
                'area_km2' => 1204.3,
                'altitude_m' => 800
            ],
            [
                'name' => 'Matarraña',
                'province' => $teruel,
                'latitude' => 40.8333,
                'longitude' => 0.1500,
                'area_km2' => 933.0,
                'altitude_m' => 400
            ],
            [
                'name' => 'Sierra de Albarracín',
                'province' => $teruel,
                'latitude' => 40.4000,
                'longitude' => -1.4500,
                'area_km2' => 1414.0,
                'altitude_m' => 1200
            ],
            [
                'name' => 'Comunidad de Teruel',
                'province' => $teruel,
                'latitude' => 40.3440,
                'longitude' => -1.1060,
                'area_km2' => 276.8,
                'altitude_m' => 900
            ],

            // PROVINCIA DE ZARAGOZA
            [
                'name' => 'Aranda',
                'province' => $zaragoza,
                'latitude' => 41.6000,
                'longitude' => -1.5000,
                'area_km2' => 1108.6,
                'altitude_m' => 800
            ],
            [
                'name' => 'Bajo Aragón-Caspe',
                'province' => $zaragoza,
                'latitude' => 41.2333,
                'longitude' => -0.0333,
                'area_km2' => 993.3,
                'altitude_m' => 200
            ],
            [
                'name' => 'Campo de Borja',
                'province' => $zaragoza,
                'latitude' => 41.8333,
                'longitude' => -1.5333,
                'area_km2' => 305.5,
                'altitude_m' => 400
            ],
            [
                'name' => 'Campo de Cariñena',
                'province' => $zaragoza,
                'latitude' => 41.3333,
                'longitude' => -1.2167,
                'area_km2' => 772.0,
                'altitude_m' => 600
            ],
            [
                'name' => 'Campo de Daroca (Zaragoza)',
                'province' => $zaragoza,
                'latitude' => 41.1167,
                'longitude' => -1.4167,
                'area_km2' => 1111.6,
                'altitude_m' => 800
            ],
            [
                'name' => 'Cinco Villas',
                'province' => $zaragoza,
                'latitude' => 42.1333,
                'longitude' => -1.1000,
                'area_km2' => 3052.8,
                'altitude_m' => 500
            ],
            [
                'name' => 'Comunidad de Calatayud',
                'province' => $zaragoza,
                'latitude' => 41.3500,
                'longitude' => -1.6333,
                'area_km2' => 2518.6,
                'altitude_m' => 700
            ],
            [
                'name' => 'Ribera Alta del Ebro',
                'province' => $zaragoza,
                'latitude' => 41.6500,
                'longitude' => -0.8833,
                'area_km2' => 416.9,
                'altitude_m' => 200
            ],
            [
                'name' => 'Ribera Baja del Ebro',
                'province' => $zaragoza,
                'latitude' => 41.3333,
                'longitude' => -0.2000,
                'area_km2' => 989.8,
                'altitude_m' => 150
            ],
            [
                'name' => 'Tarazona y el Moncayo',
                'province' => $zaragoza,
                'latitude' => 41.9000,
                'longitude' => -1.7167,
                'area_km2' => 452.4,
                'altitude_m' => 600
            ],
            [
                'name' => 'Valdejalón',
                'province' => $zaragoza,
                'latitude' => 41.5000,
                'longitude' => -1.3000,
                'area_km2' => 933.3,
                'altitude_m' => 400
            ],
            [
                'name' => 'Zaragoza',
                'province' => $zaragoza,
                'latitude' => 41.6480,
                'longitude' => -0.8890,
                'area_km2' => 2288.0,
                'altitude_m' => 200
            ]
        ];

        $created = 0;
        $updated = 0;

        foreach ($regions as $regionData) {
            $slug = Str::slug($regionData['name']);
            
            $region = Region::updateOrCreate(
                [
                    'slug' => $slug,
                    'province_id' => $regionData['province']->id
                ],
                [
                    'name' => $regionData['name'],
                    'slug' => $slug,
                    'province_id' => $regionData['province']->id,
                    'autonomous_community_id' => $aragon->id,
                    'country_id' => $spain->id,
                    'latitude' => $regionData['latitude'],
                    'longitude' => $regionData['longitude'],
                    'area_km2' => $regionData['area_km2'],
                    'altitude_m' => $regionData['altitude_m'],
                    'timezone_id' => $madridTz->id
                ]
            );

            if ($region->wasRecentlyCreated) {
                $created++;
                $this->command->info("✅ Comarca creada: {$regionData['name']} ({$regionData['province']->name})");
            } else {
                $updated++;
                $this->command->info("🔄 Comarca actualizada: {$regionData['name']} ({$regionData['province']->name})");
            }
        }

        $this->command->info("🎯 Resumen del seeder:");
        $this->command->info("   - Comarcas creadas: {$created}");
        $this->command->info("   - Comarcas actualizadas: {$updated}");
        $this->command->info("   - Total de comarcas de Aragón: " . ($created + $updated));
        
        // Mostrar estadísticas por provincia
        $this->command->info("📊 Distribución por provincia:");
        $this->command->info("   - Huesca: " . Region::where('province_id', $huesca->id)->count() . " comarcas");
        $this->command->info("   - Teruel: " . Region::where('province_id', $teruel->id)->count() . " comarcas");
        $this->command->info("   - Zaragoza: " . Region::where('province_id', $zaragoza->id)->count() . " comarcas");
    }
}
