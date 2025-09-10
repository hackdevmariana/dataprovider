<?php

namespace Database\Seeders;

use App\Models\SyncLog;
use App\Models\DataSource;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class SyncLogSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // Verificar que existan data sources
        if (DataSource::count() === 0) {
            $this->command->warn('No hay data sources. Creando algunos data sources de ejemplo...');
            $this->createDataSources();
        }

        $dataSources = DataSource::all();

        $this->command->info('Creando logs de sincronización...');

        // Logs básicos (40% de los logs)
        SyncLog::factory(40)
            ->create([
                'data_source_id' => fn() => $dataSources->random()->id,
            ]);

        // Logs exitosos (60% de los logs)
        SyncLog::factory(60)
            ->success()
            ->create([
                'data_source_id' => fn() => $dataSources->random()->id,
            ]);

        // Logs fallidos (40% de los logs)
        SyncLog::factory(40)
            ->failed()
            ->create([
                'data_source_id' => fn() => $dataSources->random()->id,
            ]);

        // Logs recientes (30% de los logs)
        SyncLog::factory(30)
            ->recent()
            ->create([
                'data_source_id' => fn() => $dataSources->random()->id,
            ]);

        // Logs antiguos (20% de los logs)
        SyncLog::factory(20)
            ->old()
            ->create([
                'data_source_id' => fn() => $dataSources->random()->id,
            ]);

        // Logs con muchos elementos (15% de los logs)
        SyncLog::factory(15)
            ->withManyItems()
            ->create([
                'data_source_id' => fn() => $dataSources->random()->id,
            ]);

        // Logs con pocos elementos (10% de los logs)
        SyncLog::factory(10)
            ->withFewItems()
            ->create([
                'data_source_id' => fn() => $dataSources->random()->id,
            ]);

        // Logs de larga duración (8% de los logs)
        SyncLog::factory(8)
            ->longRunning()
            ->create([
                'data_source_id' => fn() => $dataSources->random()->id,
            ]);

        // Logs rápidos (12% de los logs)
        SyncLog::factory(12)
            ->quickSync()
            ->create([
                'data_source_id' => fn() => $dataSources->random()->id,
            ]);

        // Logs en progreso (5% de los logs)
        SyncLog::factory(5)
            ->inProgress()
            ->create([
                'data_source_id' => fn() => $dataSources->random()->id,
            ]);

        // Crear logs específicos para data sources conocidos
        $this->createSpecificLogs($dataSources);

        $this->command->info('✅ Logs de sincronización creados exitosamente!');
    }

    private function createDataSources(): void
    {
        $dataSources = [
            [
                'name' => 'API de Precios de Energía',
                'url' => 'https://api.energia.es/precios',
                'type' => 'api',
                'license' => 'CC-BY',
            ],
            [
                'name' => 'Base de Datos de Cooperativas',
                'url' => 'https://cooperativas.energia.es/api',
                'type' => 'api',
                'license' => 'ODbL',
            ],
            [
                'name' => 'Feed de Noticias Energéticas',
                'url' => 'https://noticias.energia.es/feed',
                'type' => 'scrap',
                'license' => 'CC-BY',
            ],
            [
                'name' => 'API de Pronósticos',
                'url' => 'https://pronosticos.energia.es/api',
                'type' => 'api',
                'license' => 'CC-BY',
            ],
            [
                'name' => 'Sistema de Alertas',
                'url' => 'https://alertas.energia.es/api',
                'type' => 'api',
                'license' => 'CC-BY',
            ],
        ];

        foreach ($dataSources as $dataSource) {
            DataSource::create($dataSource);
        }
    }

    private function createSpecificLogs($dataSources): void
    {
        // Logs para data source específico
        $dataSource1 = $dataSources->first();
        if ($dataSource1) {
            SyncLog::create([
                'data_source_id' => $dataSource1->id,
                'status' => 'success',
                'started_at' => now()->subHours(2),
                'finished_at' => now()->subHours(1),
                'processed_items_count' => 1500,
            ]);

            SyncLog::create([
                'data_source_id' => $dataSource1->id,
                'status' => 'failed',
                'started_at' => now()->subHours(4),
                'finished_at' => now()->subHours(3),
                'processed_items_count' => 25,
            ]);

            SyncLog::create([
                'data_source_id' => $dataSource1->id,
                'status' => 'success',
                'started_at' => now()->subDays(1),
                'finished_at' => now()->subDays(1)->addHours(1),
                'processed_items_count' => 800,
            ]);
        }

        // Logs para otro data source
        $dataSource2 = $dataSources->skip(1)->first();
        if ($dataSource2) {
            SyncLog::create([
                'data_source_id' => $dataSource2->id,
                'status' => 'success',
                'started_at' => now()->subMinutes(30),
                'finished_at' => now()->subMinutes(15),
                'processed_items_count' => 300,
            ]);

            SyncLog::create([
                'data_source_id' => $dataSource2->id,
                'status' => 'success',
                'started_at' => now()->subHours(6),
                'finished_at' => now()->subHours(5),
                'processed_items_count' => 1200,
            ]);

            SyncLog::create([
                'data_source_id' => $dataSource2->id,
                'status' => 'failed',
                'started_at' => now()->subHours(8),
                'finished_at' => now()->subHours(7),
                'processed_items_count' => 0,
            ]);
        }

        // Logs para otro data source
        $dataSource3 = $dataSources->skip(2)->first();
        if ($dataSource3) {
            SyncLog::create([
                'data_source_id' => $dataSource3->id,
                'status' => 'success',
                'started_at' => now()->subDays(2),
                'finished_at' => now()->subDays(2)->addMinutes(45),
                'processed_items_count' => 600,
            ]);

            SyncLog::create([
                'data_source_id' => $dataSource3->id,
                'status' => 'success',
                'started_at' => now()->subDays(3),
                'finished_at' => now()->subDays(3)->addHours(2),
                'processed_items_count' => 2500,
            ]);

            SyncLog::create([
                'data_source_id' => $dataSource3->id,
                'status' => 'failed',
                'started_at' => now()->subDays(4),
                'finished_at' => now()->subDays(4)->addMinutes(30),
                'processed_items_count' => 15,
            ]);
        }

        // Logs para otro data source
        $dataSource4 = $dataSources->skip(3)->first();
        if ($dataSource4) {
            SyncLog::create([
                'data_source_id' => $dataSource4->id,
                'status' => 'success',
                'started_at' => now()->subDays(5),
                'finished_at' => now()->subDays(5)->addMinutes(20),
                'processed_items_count' => 400,
            ]);

            SyncLog::create([
                'data_source_id' => $dataSource4->id,
                'status' => 'success',
                'started_at' => now()->subDays(6),
                'finished_at' => now()->subDays(6)->addHours(1),
                'processed_items_count' => 900,
            ]);

            SyncLog::create([
                'data_source_id' => $dataSource4->id,
                'status' => 'failed',
                'started_at' => now()->subDays(7),
                'finished_at' => null,
                'processed_items_count' => 0,
            ]);
        }

        // Logs para otro data source
        $dataSource5 = $dataSources->skip(4)->first();
        if ($dataSource5) {
            SyncLog::create([
                'data_source_id' => $dataSource5->id,
                'status' => 'success',
                'started_at' => now()->subDays(8),
                'finished_at' => now()->subDays(8)->addMinutes(35),
                'processed_items_count' => 750,
            ]);

            SyncLog::create([
                'data_source_id' => $dataSource5->id,
                'status' => 'success',
                'started_at' => now()->subDays(9),
                'finished_at' => now()->subDays(9)->addHours(1),
                'processed_items_count' => 1800,
            ]);

            SyncLog::create([
                'data_source_id' => $dataSource5->id,
                'status' => 'failed',
                'started_at' => now()->subDays(10),
                'finished_at' => now()->subDays(10)->addMinutes(15),
                'processed_items_count' => 5,
            ]);
        }

        // Logs en progreso
        if ($dataSource1) {
            SyncLog::create([
                'data_source_id' => $dataSource1->id,
                'status' => 'success',
                'started_at' => now()->subMinutes(10),
                'finished_at' => null,
                'processed_items_count' => 0,
            ]);
        }

        if ($dataSource2) {
            SyncLog::create([
                'data_source_id' => $dataSource2->id,
                'status' => 'success',
                'started_at' => now()->subMinutes(5),
                'finished_at' => null,
                'processed_items_count' => 0,
            ]);
        }

        if ($dataSource3) {
            SyncLog::create([
                'data_source_id' => $dataSource3->id,
                'status' => 'success',
                'started_at' => now()->subMinutes(15),
                'finished_at' => null,
                'processed_items_count' => 0,
            ]);
        }
    }
}
