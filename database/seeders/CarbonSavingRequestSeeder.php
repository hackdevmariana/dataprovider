<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CarbonSavingRequest;
use App\Models\Province;
use App\Models\Municipality;

class CarbonSavingRequestSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🌱 Sembrando solicitudes de ahorro de carbono...');

        $provinces = Province::take(10)->get();
        $municipalities = Municipality::take(20)->get();

        if ($provinces->isEmpty()) {
            $this->command->error('❌ No hay provincias disponibles. Ejecuta ProvinceSeeder primero.');
            return;
        }

        $createdCount = 0;
        $periods = ['annual', 'monthly', 'daily'];

        // Crear solicitudes con provincia
        foreach ($provinces as $province) {
            $requestCount = rand(2, 5);
            
            for ($i = 0; $i < $requestCount; $i++) {
                $powerKw = rand(5, 100);
                $period = $periods[array_rand($periods)];
                
                $requestData = [
                    'installation_power_kw' => $powerKw,
                    'production_kwh' => $powerKw * rand(800, 1500),
                    'province_id' => $province->id,
                    'municipality_id' => null,
                    'period' => $period,
                    'start_date' => now()->subDays(rand(1, 365)),
                    'end_date' => now()->addDays(rand(1, 365)),
                    'efficiency_ratio' => rand(80, 95) / 100,
                    'loss_factor' => rand(5, 15) / 100,
                ];

                CarbonSavingRequest::create($requestData);
                $createdCount++;
            }
        }

        // Crear solicitudes con municipio
        foreach ($municipalities as $municipality) {
            $powerKw = rand(3, 50);
            $period = $periods[array_rand($periods)];
            
            $requestData = [
                'installation_power_kw' => $powerKw,
                'production_kwh' => $powerKw * rand(800, 1500),
                'province_id' => $municipality->province_id,
                'municipality_id' => $municipality->id,
                'period' => $period,
                'start_date' => now()->subDays(rand(1, 365)),
                'end_date' => now()->addDays(rand(1, 365)),
                'efficiency_ratio' => rand(80, 95) / 100,
                'loss_factor' => rand(5, 15) / 100,
            ];

            CarbonSavingRequest::create($requestData);
            $createdCount++;
        }

        $this->command->info("✅ Solicitudes de ahorro creadas: {$createdCount}");
        $this->command->info("📊 Total de solicitudes: " . CarbonSavingRequest::count());
        $this->command->info("🎯 Seeder de CarbonSavingRequest completado exitosamente!");
    }
}
