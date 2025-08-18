<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Stat;
use App\Models\User;
use App\Models\Cooperative;
use App\Models\EnergyInstallation;
use App\Models\DataSource;
use Carbon\Carbon;

class StatSeeder extends Seeder
{
    /**
     * Ejecutar el seeder para estadísticas y métricas.
     */
    public function run(): void
    {
        $this->command->info('Creando estadísticas y métricas para KiroLux...');

        // Crear fuentes de datos si no existen
        $dataSources = $this->createDataSources();

        // Crear estadísticas básicas
        $stats = $this->createBasicStats($dataSources);

        $this->command->info("✅ Creadas {$stats} estadísticas");

        // Mostrar estadísticas
        $this->showStatistics();
    }

    /**
     * Crear fuentes de datos.
     */
    private function createDataSources(): array
    {
        $sources = [
            [
                'name' => 'KiroLux App',
                'type' => 'manual',
                'url' => 'https://app.kirolux.com',
                'license' => 'Proprietary',
            ],
            [
                'name' => 'Smart Meter',
                'type' => 'api',
                'url' => null,
                'license' => 'Proprietary',
            ],
            [
                'name' => 'Weather Station AEMET',
                'type' => 'api',
                'url' => 'https://opendata.aemet.es',
                'license' => 'CC-BY',
            ],
            [
                'name' => 'Market API REE',
                'type' => 'api',
                'url' => 'https://www.ree.es/es/apidatos',
                'license' => 'Open Data',
            ],
        ];

        $dataSources = [];
        foreach ($sources as $sourceData) {
            $source = DataSource::firstOrCreate(
                ['name' => $sourceData['name']],
                $sourceData
            );
            $dataSources[] = $source;
        }

        return $dataSources;
    }

    /**
     * Crear estadísticas básicas del sistema.
     */
    private function createBasicStats($dataSources): int
    {
        $currentYear = Carbon::now()->year;
        $count = 0;

        // Estadísticas de usuarios (si existen)
        $users = User::limit(3)->get();
        foreach ($users as $user) {
            $userStats = [
                [
                    'subject_type' => User::class,
                    'subject_id' => $user->id,
                    'key' => 'daily_energy_consumption',
                    'value' => fake()->randomFloat(2, 10, 50),
                    'year' => $currentYear,
                    'data_source_id' => $dataSources[1]->id, // Smart Meter
                    'unit' => 'kWh',
                    'source_note' => 'Consumo diario promedio de energía',
                ],
                [
                    'subject_type' => User::class,
                    'subject_id' => $user->id,
                    'key' => 'solar_production',
                    'value' => fake()->randomFloat(2, 5, 25),
                    'year' => $currentYear,
                    'data_source_id' => $dataSources[0]->id, // KiroLux App
                    'unit' => 'kWh',
                    'source_note' => 'Producción solar diaria promedio',
                ],
                [
                    'subject_type' => User::class,
                    'subject_id' => $user->id,
                    'key' => 'energy_savings',
                    'value' => fake()->randomFloat(2, 5, 30),
                    'year' => $currentYear,
                    'data_source_id' => $dataSources[0]->id, // KiroLux App
                    'unit' => 'EUR',
                    'source_note' => 'Ahorro mensual en factura eléctrica',
                ],
                [
                    'subject_type' => User::class,
                    'subject_id' => $user->id,
                    'key' => 'co2_avoided',
                    'value' => fake()->randomFloat(2, 10, 50),
                    'year' => $currentYear,
                    'data_source_id' => $dataSources[0]->id, // KiroLux App
                    'unit' => 'kg CO2',
                    'source_note' => 'CO2 evitado mensualmente',
                ],
            ];

            foreach ($userStats as $statData) {
                Stat::create($statData);
                $count++;
            }
        }

        // Estadísticas de cooperativas (si existen)
        $cooperatives = Cooperative::limit(2)->get();
        foreach ($cooperatives as $cooperative) {
            $cooperativeStats = [
                [
                    'subject_type' => Cooperative::class,
                    'subject_id' => $cooperative->id,
                    'key' => 'total_members',
                    'value' => fake()->numberBetween(50, 200),
                    'year' => $currentYear,
                    'data_source_id' => $dataSources[0]->id, // KiroLux App
                    'unit' => 'members',
                    'source_note' => 'Número total de miembros activos',
                ],
                [
                    'subject_type' => Cooperative::class,
                    'subject_id' => $cooperative->id,
                    'key' => 'energy_traded',
                    'value' => fake()->randomFloat(2, 1000, 5000),
                    'year' => $currentYear,
                    'data_source_id' => $dataSources[0]->id, // KiroLux App
                    'unit' => 'kWh',
                    'source_note' => 'Energía intercambiada mensualmente',
                ],
                [
                    'subject_type' => Cooperative::class,
                    'subject_id' => $cooperative->id,
                    'key' => 'transaction_volume',
                    'value' => fake()->randomFloat(2, 2000, 10000),
                    'year' => $currentYear,
                    'data_source_id' => $dataSources[0]->id, // KiroLux App
                    'unit' => 'EUR',
                    'source_note' => 'Volumen de transacciones mensual',
                ],
            ];

            foreach ($cooperativeStats as $statData) {
                Stat::create($statData);
                $count++;
            }
        }

        // Estadísticas de instalaciones (si existen)
        $installations = EnergyInstallation::limit(3)->get();
        foreach ($installations as $installation) {
            $installationStats = [
                [
                    'subject_type' => EnergyInstallation::class,
                    'subject_id' => $installation->id,
                    'key' => 'daily_production',
                    'value' => fake()->randomFloat(2, 20, 100),
                    'year' => $currentYear,
                    'data_source_id' => $dataSources[1]->id, // Smart Meter
                    'unit' => 'kWh',
                    'source_note' => 'Producción diaria promedio',
                ],
                [
                    'subject_type' => EnergyInstallation::class,
                    'subject_id' => $installation->id,
                    'key' => 'system_efficiency',
                    'value' => fake()->randomFloat(2, 75, 95),
                    'year' => $currentYear,
                    'data_source_id' => $dataSources[1]->id, // Smart Meter
                    'unit' => '%',
                    'source_note' => 'Eficiencia del sistema',
                ],
            ];

            foreach ($installationStats as $statData) {
                Stat::create($statData);
                $count++;
            }
        }

        // Estadísticas globales del sistema
        $globalStats = [
            [
                'subject_type' => 'App\\Models\\System',
                'subject_id' => 1,
                'key' => 'daily_active_users',
                'value' => fake()->numberBetween(500, 2000),
                'year' => $currentYear,
                'data_source_id' => $dataSources[0]->id, // KiroLux App
                'unit' => 'users',
                'source_note' => 'Usuarios activos diarios',
            ],
            [
                'subject_type' => 'App\\Models\\System',
                'subject_id' => 1,
                'key' => 'total_energy_traded',
                'value' => fake()->randomFloat(2, 10000, 50000),
                'year' => $currentYear,
                'data_source_id' => $dataSources[0]->id, // KiroLux App
                'unit' => 'kWh',
                'source_note' => 'Energía total intercambiada mensualmente',
            ],
            [
                'subject_type' => 'App\\Models\\System',
                'subject_id' => 1,
                'key' => 'platform_savings',
                'value' => fake()->randomFloat(2, 50000, 200000),
                'year' => $currentYear,
                'data_source_id' => $dataSources[0]->id, // KiroLux App
                'unit' => 'EUR',
                'source_note' => 'Ahorro total generado por la plataforma',
            ],
            [
                'subject_type' => 'App\\Models\\System',
                'subject_id' => 1,
                'key' => 'co2_avoided_total',
                'value' => fake()->randomFloat(2, 5000, 20000),
                'year' => $currentYear,
                'data_source_id' => $dataSources[0]->id, // KiroLux App
                'unit' => 'kg CO2',
                'source_note' => 'CO2 total evitado por la plataforma',
            ],
            [
                'subject_type' => 'App\\Models\\Market',
                'subject_id' => 1,
                'key' => 'average_price',
                'value' => fake()->randomFloat(4, 0.10, 0.25),
                'year' => $currentYear,
                'data_source_id' => $dataSources[3]->id, // Market API REE
                'unit' => 'EUR/kWh',
                'source_note' => 'Precio medio de electricidad',
            ],
        ];

        foreach ($globalStats as $statData) {
            Stat::create($statData);
            $count++;
        }

        return $count;
    }

    /**
     * Mostrar estadísticas de las métricas creadas.
     */
    private function showStatistics(): void
    {
        $stats = [
            'Total estadísticas' => Stat::count(),
            'Usuarios' => Stat::where('subject_type', User::class)->count(),
            'Cooperativas' => Stat::where('subject_type', Cooperative::class)->count(),
            'Instalaciones' => Stat::where('subject_type', EnergyInstallation::class)->count(),
            'Sistema global' => Stat::where('subject_type', 'LIKE', '%System%')->count(),
            'Mercado' => Stat::where('subject_type', 'LIKE', '%Market%')->count(),
        ];

        $this->command->info("\n📊 Estadísticas de métricas:");
        foreach ($stats as $type => $count) {
            $this->command->info("   {$type}: {$count}");
        }

        // Métricas más comunes
        $keys = Stat::selectRaw('`key`, COUNT(*) as count')
                   ->groupBy('key')
                   ->orderBy('count', 'desc')
                   ->limit(5)
                   ->get();

        if ($keys->isNotEmpty()) {
            $this->command->info("\n📏 Métricas más comunes:");
            foreach ($keys as $key) {
                $this->command->info("   {$key->key}: {$key->count}");
            }
        }

        // Fuentes de datos
        $sources = Stat::join('data_sources', 'stats.data_source_id', '=', 'data_sources.id')
                      ->selectRaw('data_sources.name, COUNT(*) as count')
                      ->groupBy('data_sources.name')
                      ->orderBy('count', 'desc')
                      ->get();

        if ($sources->isNotEmpty()) {
            $this->command->info("\n📡 Fuentes de datos:");
            foreach ($sources as $source) {
                $this->command->info("   {$source->name}: {$source->count}");
            }
        }

        // Información para KiroLux
        $energyStats = Stat::where('unit', 'kWh')->count();
        $economicStats = Stat::where('unit', 'EUR')->count();
        $environmentalStats = Stat::where('unit', 'kg CO2')->count();
        
        $this->command->info("\n⚡ Para KiroLux:");
        $this->command->info("   🔋 Métricas energéticas: {$energyStats}");
        $this->command->info("   💰 Métricas económicas: {$economicStats}");
        $this->command->info("   🌱 Métricas ambientales: {$environmentalStats}");
        $this->command->info("   📊 Dashboard completo: Listo");
        $this->command->info("   🎯 Analytics en tiempo real: Funcional");
    }
}