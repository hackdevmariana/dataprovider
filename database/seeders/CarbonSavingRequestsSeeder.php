<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CarbonSavingRequest;
use App\Models\Province;
use App\Models\Municipality;
use Carbon\Carbon;

class CarbonSavingRequestsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener provincias existentes
        $provinces = Province::all();
        
        if ($provinces->isEmpty()) {
            $this->command->warn('No hay provincias disponibles. Ejecuta ProvincesSeeder primero.');
            return;
        }

        // Obtener municipios existentes
        $municipalities = Municipality::all();

        $requests = [
            // Solicitudes de ahorro solar residencial
            [
                'installation_power_kw' => 5.0,
                'production_kwh' => 7500.0,
                'province_id' => $provinces->where('name', 'Madrid')->first()?->id ?? $provinces->random()->id,
                'municipality_id' => $municipalities->isNotEmpty() ? $municipalities->random()->id : null,
                'period' => 'annual',
                'start_date' => Carbon::now()->startOfYear(),
                'end_date' => Carbon::now()->endOfYear(),
                'efficiency_ratio' => 0.85,
                'loss_factor' => 0.05,
            ],
            [
                'installation_power_kw' => 3.0,
                'production_kwh' => 4500.0,
                'province_id' => $provinces->where('name', 'Barcelona')->first()?->id ?? $provinces->random()->id,
                'municipality_id' => $municipalities->isNotEmpty() ? $municipalities->random()->id : null,
                'period' => 'annual',
                'start_date' => Carbon::now()->startOfYear(),
                'end_date' => Carbon::now()->endOfYear(),
                'efficiency_ratio' => 0.82,
                'loss_factor' => 0.08,
            ],
            [
                'installation_power_kw' => 7.5,
                'production_kwh' => 11250.0,
                'province_id' => $provinces->where('name', 'Valencia')->first()?->id ?? $provinces->random()->id,
                'municipality_id' => $municipalities->isNotEmpty() ? $municipalities->random()->id : null,
                'period' => 'annual',
                'start_date' => Carbon::now()->startOfYear(),
                'end_date' => Carbon::now()->endOfYear(),
                'efficiency_ratio' => 0.88,
                'loss_factor' => 0.04,
            ],

            // Solicitudes de ahorro solar comercial
            [
                'installation_power_kw' => 50.0,
                'production_kwh' => 75000.0,
                'province_id' => $provinces->where('name', 'Madrid')->first()?->id ?? $provinces->random()->id,
                'municipality_id' => $municipalities->isNotEmpty() ? $municipalities->random()->id : null,
                'period' => 'annual',
                'start_date' => Carbon::now()->startOfYear(),
                'end_date' => Carbon::now()->endOfYear(),
                'efficiency_ratio' => 0.90,
                'loss_factor' => 0.03,
            ],
            [
                'installation_power_kw' => 100.0,
                'production_kwh' => 150000.0,
                'province_id' => $provinces->where('name', 'Sevilla')->first()?->id ?? $provinces->random()->id,
                'municipality_id' => $municipalities->isNotEmpty() ? $municipalities->random()->id : null,
                'period' => 'annual',
                'start_date' => Carbon::now()->startOfYear(),
                'end_date' => Carbon::now()->endOfYear(),
                'efficiency_ratio' => 0.92,
                'loss_factor' => 0.02,
            ],
            [
                'installation_power_kw' => 25.0,
                'production_kwh' => 37500.0,
                'province_id' => $provinces->where('name', 'Bilbao')->first()?->id ?? $provinces->random()->id,
                'municipality_id' => $municipalities->isNotEmpty() ? $municipalities->random()->id : null,
                'period' => 'annual',
                'start_date' => Carbon::now()->startOfYear(),
                'end_date' => Carbon::now()->endOfYear(),
                'efficiency_ratio' => 0.87,
                'loss_factor' => 0.06,
            ],

            // Solicitudes de ahorro eólico
            [
                'installation_power_kw' => 2000.0,
                'production_kwh' => 6000000.0,
                'province_id' => $provinces->where('name', 'A Coruña')->first()?->id ?? $provinces->random()->id,
                'municipality_id' => $municipalities->isNotEmpty() ? $municipalities->random()->id : null,
                'period' => 'annual',
                'start_date' => Carbon::now()->startOfYear(),
                'end_date' => Carbon::now()->endOfYear(),
                'efficiency_ratio' => 0.95,
                'loss_factor' => 0.02,
            ],
            [
                'installation_power_kw' => 500.0,
                'production_kwh' => 1500000.0,
                'province_id' => $provinces->where('name', 'Lugo')->first()?->id ?? $provinces->random()->id,
                'municipality_id' => $municipalities->isNotEmpty() ? $municipalities->random()->id : null,
                'period' => 'annual',
                'start_date' => Carbon::now()->startOfYear(),
                'end_date' => Carbon::now()->endOfYear(),
                'efficiency_ratio' => 0.93,
                'loss_factor' => 0.03,
            ],

            // Solicitudes de ahorro hidroeléctrico
            [
                'installation_power_kw' => 500.0,
                'production_kwh' => 2000000.0,
                'province_id' => $provinces->where('name', 'Asturias')->first()?->id ?? $provinces->random()->id,
                'municipality_id' => $municipalities->isNotEmpty() ? $municipalities->random()->id : null,
                'period' => 'annual',
                'start_date' => Carbon::now()->startOfYear(),
                'end_date' => Carbon::now()->endOfYear(),
                'efficiency_ratio' => 0.90,
                'loss_factor' => 0.05,
            ],
            [
                'installation_power_kw' => 250.0,
                'production_kwh' => 1000000.0,
                'province_id' => $provinces->where('name', 'Cantabria')->first()?->id ?? $provinces->random()->id,
                'municipality_id' => $municipalities->isNotEmpty() ? $municipalities->random()->id : null,
                'period' => 'annual',
                'start_date' => Carbon::now()->startOfYear(),
                'end_date' => Carbon::now()->endOfYear(),
                'efficiency_ratio' => 0.88,
                'loss_factor' => 0.07,
            ],

            // Solicitudes de ahorro biomasa
            [
                'installation_power_kw' => 1000.0,
                'production_kwh' => 8000000.0,
                'province_id' => $provinces->where('name', 'León')->first()?->id ?? $provinces->random()->id,
                'municipality_id' => $municipalities->isNotEmpty() ? $municipalities->random()->id : null,
                'period' => 'annual',
                'start_date' => Carbon::now()->startOfYear(),
                'end_date' => Carbon::now()->endOfYear(),
                'efficiency_ratio' => 0.85,
                'loss_factor' => 0.08,
            ],
            [
                'installation_power_kw' => 750.0,
                'production_kwh' => 6000000.0,
                'province_id' => $provinces->where('name', 'Palencia')->first()?->id ?? $provinces->random()->id,
                'municipality_id' => $municipalities->isNotEmpty() ? $municipalities->random()->id : null,
                'period' => 'annual',
                'start_date' => Carbon::now()->startOfYear(),
                'end_date' => Carbon::now()->endOfYear(),
                'efficiency_ratio' => 0.83,
                'loss_factor' => 0.10,
            ],

            // Solicitudes mensuales
            [
                'installation_power_kw' => 10.0,
                'production_kwh' => 1250.0,
                'province_id' => $provinces->where('name', 'Madrid')->first()?->id ?? $provinces->random()->id,
                'municipality_id' => $municipalities->isNotEmpty() ? $municipalities->random()->id : null,
                'period' => 'monthly',
                'start_date' => Carbon::now()->startOfMonth(),
                'end_date' => Carbon::now()->endOfMonth(),
                'efficiency_ratio' => 0.86,
                'loss_factor' => 0.05,
            ],
            [
                'installation_power_kw' => 15.0,
                'production_kwh' => 1875.0,
                'province_id' => $provinces->where('name', 'Barcelona')->first()?->id ?? $provinces->random()->id,
                'municipality_id' => $municipalities->isNotEmpty() ? $municipalities->random()->id : null,
                'period' => 'monthly',
                'start_date' => Carbon::now()->startOfMonth(),
                'end_date' => Carbon::now()->endOfMonth(),
                'efficiency_ratio' => 0.89,
                'loss_factor' => 0.04,
            ],

            // Solicitudes diarias
            [
                'installation_power_kw' => 2.0,
                'production_kwh' => 48.0,
                'province_id' => $provinces->where('name', 'Valencia')->first()?->id ?? $provinces->random()->id,
                'municipality_id' => $municipalities->isNotEmpty() ? $municipalities->random()->id : null,
                'period' => 'daily',
                'start_date' => Carbon::now()->startOfDay(),
                'end_date' => Carbon::now()->endOfDay(),
                'efficiency_ratio' => 0.84,
                'loss_factor' => 0.06,
            ],
            [
                'installation_power_kw' => 1.5,
                'production_kwh' => 36.0,
                'province_id' => $provinces->where('name', 'Sevilla')->first()?->id ?? $provinces->random()->id,
                'municipality_id' => $municipalities->isNotEmpty() ? $municipalities->random()->id : null,
                'period' => 'daily',
                'start_date' => Carbon::now()->startOfDay(),
                'end_date' => Carbon::now()->endOfDay(),
                'efficiency_ratio' => 0.87,
                'loss_factor' => 0.05,
            ],

            // Solicitudes con diferentes eficiencias
            [
                'installation_power_kw' => 8.0,
                'production_kwh' => 12000.0,
                'province_id' => $provinces->where('name', 'Murcia')->first()?->id ?? $provinces->random()->id,
                'municipality_id' => $municipalities->isNotEmpty() ? $municipalities->random()->id : null,
                'period' => 'annual',
                'start_date' => Carbon::now()->startOfYear(),
                'end_date' => Carbon::now()->endOfYear(),
                'efficiency_ratio' => 0.95,
                'loss_factor' => 0.02,
            ],
            [
                'installation_power_kw' => 12.0,
                'production_kwh' => 15000.0,
                'province_id' => $provinces->where('name', 'Málaga')->first()?->id ?? $provinces->random()->id,
                'municipality_id' => $municipalities->isNotEmpty() ? $municipalities->random()->id : null,
                'period' => 'annual',
                'start_date' => Carbon::now()->startOfYear(),
                'end_date' => Carbon::now()->endOfYear(),
                'efficiency_ratio' => 0.80,
                'loss_factor' => 0.12,
            ],

            // Solicitudes sin datos de producción (solo potencia)
            [
                'installation_power_kw' => 6.0,
                'production_kwh' => null,
                'province_id' => $provinces->where('name', 'Zaragoza')->first()?->id ?? $provinces->random()->id,
                'municipality_id' => $municipalities->isNotEmpty() ? $municipalities->random()->id : null,
                'period' => 'annual',
                'start_date' => Carbon::now()->startOfYear(),
                'end_date' => Carbon::now()->endOfYear(),
                'efficiency_ratio' => 0.88,
                'loss_factor' => 0.06,
            ],
            [
                'installation_power_kw' => 20.0,
                'production_kwh' => null,
                'province_id' => $provinces->where('name', 'Toledo')->first()?->id ?? $provinces->random()->id,
                'municipality_id' => $municipalities->isNotEmpty() ? $municipalities->random()->id : null,
                'period' => 'annual',
                'start_date' => Carbon::now()->startOfYear(),
                'end_date' => Carbon::now()->endOfYear(),
                'efficiency_ratio' => 0.91,
                'loss_factor' => 0.04,
            ],

            // Solicitudes sin factores de eficiencia
            [
                'installation_power_kw' => 4.0,
                'production_kwh' => 6000.0,
                'province_id' => $provinces->where('name', 'Córdoba')->first()?->id ?? $provinces->random()->id,
                'municipality_id' => $municipalities->isNotEmpty() ? $municipalities->random()->id : null,
                'period' => 'annual',
                'start_date' => Carbon::now()->startOfYear(),
                'end_date' => Carbon::now()->endOfYear(),
                'efficiency_ratio' => null,
                'loss_factor' => null,
            ],
            [
                'installation_power_kw' => 9.0,
                'production_kwh' => 13500.0,
                'province_id' => $provinces->where('name', 'Granada')->first()?->id ?? $provinces->random()->id,
                'municipality_id' => $municipalities->isNotEmpty() ? $municipalities->random()->id : null,
                'period' => 'annual',
                'start_date' => Carbon::now()->startOfYear(),
                'end_date' => Carbon::now()->endOfYear(),
                'efficiency_ratio' => null,
                'loss_factor' => null,
            ],

            // Solicitudes sin ubicación regional
            [
                'installation_power_kw' => 30.0,
                'production_kwh' => 45000.0,
                'province_id' => null,
                'municipality_id' => null,
                'period' => 'annual',
                'start_date' => Carbon::now()->startOfYear(),
                'end_date' => Carbon::now()->endOfYear(),
                'efficiency_ratio' => 0.89,
                'loss_factor' => 0.05,
            ],
            [
                'installation_power_kw' => 40.0,
                'production_kwh' => 60000.0,
                'province_id' => null,
                'municipality_id' => null,
                'period' => 'annual',
                'start_date' => Carbon::now()->startOfYear(),
                'end_date' => Carbon::now()->endOfYear(),
                'efficiency_ratio' => 0.92,
                'loss_factor' => 0.03,
            ],
        ];

        foreach ($requests as $request) {
            CarbonSavingRequest::create($request);
        }

        $this->command->info('Se han creado ' . count($requests) . ' solicitudes de ahorro de carbono.');
    }
}
