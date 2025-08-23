<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\CarbonSavingRequest;
use App\Models\Province;
use App\Models\Municipality;
use App\Models\User;
use Illuminate\Support\Str;

class CarbonSavingRequestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🌱 Sembrando solicitudes de ahorro de carbono...');

        // Obtener provincias y municipios disponibles
        $provinces = Province::take(5)->get();
        $municipalities = Municipality::take(10)->get();
        $users = User::take(3)->get();

        if ($provinces->isEmpty()) {
            $this->command->error('❌ No se encontraron provincias. Ejecuta primero el ProvinceSeeder.');
            return;
        }

        if ($municipalities->isEmpty()) {
            $this->command->error('❌ No se encontraron municipios. Ejecuta primero el MunicipalitySeeder.');
            return;
        }

        if ($users->isEmpty()) {
            $this->command->error('❌ No se encontraron usuarios. Ejecuta primero el UserSeeder.');
            return;
        }

        // Datos de ejemplo para diferentes tipos de instalaciones
        $sampleRequests = [
            // Instalaciones residenciales
            [
                'installation_power_kw' => 5.0,
                'production_kwh' => null, // Se calculará automáticamente
                'province_id' => $provinces->random()->id,
                'municipality_id' => $municipalities->random()->id,
                'period' => 'annual',
                'start_date' => '2025-01-01',
                'end_date' => '2025-12-31',
                'efficiency_ratio' => 0.85,
                'loss_factor' => 0.05,
            ],
            [
                'installation_power_kw' => 3.5,
                'production_kwh' => 4200,
                'province_id' => $provinces->random()->id,
                'municipality_id' => $municipalities->random()->id,
                'period' => 'annual',
                'start_date' => '2025-01-01',
                'end_date' => '2025-12-31',
                'efficiency_ratio' => 0.90,
                'loss_factor' => 0.03,
            ],
            [
                'installation_power_kw' => 10.0,
                'production_kwh' => null,
                'province_id' => $provinces->random()->id,
                'municipality_id' => $municipalities->random()->id,
                'period' => 'annual',
                'start_date' => '2025-01-01',
                'end_date' => '2025-12-31',
                'efficiency_ratio' => 0.88,
                'loss_factor' => 0.07,
            ],

            // Instalaciones comerciales
            [
                'installation_power_kw' => 25.0,
                'production_kwh' => 30000,
                'province_id' => $provinces->random()->id,
                'municipality_id' => $municipalities->random()->id,
                'period' => 'annual',
                'start_date' => '2025-01-01',
                'end_date' => '2025-12-31',
                'efficiency_ratio' => 0.92,
                'loss_factor' => 0.04,
            ],
            [
                'installation_power_kw' => 50.0,
                'production_kwh' => null,
                'province_id' => $provinces->random()->id,
                'municipality_id' => $municipalities->random()->id,
                'period' => 'annual',
                'start_date' => '2025-01-01',
                'end_date' => '2025-12-31',
                'efficiency_ratio' => 0.89,
                'loss_factor' => 0.06,
            ],

            // Instalaciones industriales
            [
                'installation_power_kw' => 100.0,
                'production_kwh' => 120000,
                'province_id' => $provinces->random()->id,
                'municipality_id' => $municipalities->random()->id,
                'period' => 'annual',
                'start_date' => '2025-01-01',
                'end_date' => '2025-12-31',
                'efficiency_ratio' => 0.94,
                'loss_factor' => 0.08,
            ],
            [
                'installation_power_kw' => 200.0,
                'production_kwh' => null,
                'province_id' => $provinces->random()->id,
                'municipality_id' => $municipalities->random()->id,
                'period' => 'annual',
                'start_date' => '2025-01-01',
                'end_date' => '2025-12-31',
                'efficiency_ratio' => 0.91,
                'loss_factor' => 0.10,
            ],

            // Instalaciones con períodos diferentes
            [
                'installation_power_kw' => 15.0,
                'production_kwh' => null,
                'province_id' => $provinces->random()->id,
                'municipality_id' => $municipalities->random()->id,
                'period' => 'monthly',
                'start_date' => '2025-01-01',
                'end_date' => '2025-01-31',
                'efficiency_ratio' => 0.87,
                'loss_factor' => 0.05,
            ],
            [
                'installation_power_kw' => 7.5,
                'production_kwh' => null,
                'province_id' => $provinces->random()->id,
                'municipality_id' => $municipalities->random()->id,
                'period' => 'daily',
                'start_date' => '2025-01-15',
                'end_date' => '2025-01-15',
                'efficiency_ratio' => 0.86,
                'loss_factor' => 0.04,
            ],

            // Instalaciones sin ubicación regional (para comparación)
            [
                'installation_power_kw' => 12.0,
                'production_kwh' => 14400,
                'province_id' => null,
                'municipality_id' => null,
                'period' => 'annual',
                'start_date' => '2025-01-01',
                'end_date' => '2025-12-31',
                'efficiency_ratio' => 0.88,
                'loss_factor' => 0.06,
            ],
            [
                'installation_power_kw' => 30.0,
                'production_kwh' => null,
                'province_id' => null,
                'municipality_id' => null,
                'period' => 'annual',
                'start_date' => '2025-01-01',
                'end_date' => '2025-12-31',
                'efficiency_ratio' => 0.90,
                'loss_factor' => 0.05,
            ],

            // Instalaciones con diferentes ratios de eficiencia
            [
                'installation_power_kw' => 20.0,
                'production_kwh' => null,
                'province_id' => $provinces->random()->id,
                'municipality_id' => $municipalities->random()->id,
                'period' => 'annual',
                'start_date' => '2025-01-01',
                'end_date' => '2025-12-31',
                'efficiency_ratio' => 0.95, // Alta eficiencia
                'loss_factor' => 0.02, // Bajas pérdidas
            ],
            [
                'installation_power_kw' => 8.0,
                'production_kwh' => null,
                'province_id' => $provinces->random()->id,
                'municipality_id' => $municipalities->random()->id,
                'period' => 'annual',
                'start_date' => '2025-01-01',
                'end_date' => '2025-12-31',
                'efficiency_ratio' => 0.75, // Baja eficiencia
                'loss_factor' => 0.15, // Altas pérdidas
            ],
        ];

        $createdCount = 0;
        $updatedCount = 0;

        foreach ($sampleRequests as $requestData) {
            // Crear o actualizar la solicitud
            $request = CarbonSavingRequest::updateOrCreate(
                [
                    'installation_power_kw' => $requestData['installation_power_kw'],
                    'period' => $requestData['period'],
                    'start_date' => $requestData['start_date'],
                ],
                $requestData
            );

            if ($request->wasRecentlyCreated) {
                $createdCount++;
            } else {
                $updatedCount++;
            }
        }

        // Mostrar estadísticas
        $this->command->info("✅ Solicitudes creadas: {$createdCount}");
        $this->command->info("🔄 Solicitudes actualizadas: {$updatedCount}");
        $this->command->info("📊 Total de solicitudes: " . CarbonSavingRequest::count());

        // Mostrar algunos ejemplos de cálculos
        $this->command->info("\n🔬 Ejemplos de cálculos:");
        $sampleRequest = CarbonSavingRequest::first();
        if ($sampleRequest) {
            $this->command->info("📋 Solicitud ID: {$sampleRequest->id}");
            $this->command->info("⚡ Potencia: {$sampleRequest->getFormattedPower()}");
            $this->command->info("📅 Período: {$sampleRequest->getPeriodLabel()}");
            $this->command->info("🏭 Producción estimada: {$sampleRequest->getFormattedProduction()}");
            $this->command->info("🌱 Ahorro de CO2: {$sampleRequest->getFormattedCarbonSavings()}");
            $this->command->info("📍 Ubicación: {$sampleRequest->getRegionalInfo()}");
        }

        $this->command->info("\n🎯 Seeder de CarbonSavingRequest completado exitosamente!");
    }
}
