<?php

namespace Database\Seeders;

use App\Models\CarbonSavingRequest;
use App\Models\Province;
use App\Models\Municipality;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class CarbonSavingRequestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener provincias y municipios existentes
        $provinces = Province::all();
        $municipalities = Municipality::all();
        
        if ($provinces->isEmpty()) {
            $this->command->warn('No hay provincias disponibles. Ejecuta ProvinceSeeder primero.');
            return;
        }

        // Crear diferentes tipos de solicitudes de ahorro de carbono
        $this->createResidentialRequests($provinces, $municipalities);
        $this->createCommercialRequests($provinces, $municipalities);
        $this->createIndustrialRequests($provinces, $municipalities);
        $this->createAgriculturalRequests($provinces, $municipalities);
    }

    /**
     * Crear solicitudes residenciales.
     */
    private function createResidentialRequests($provinces, $municipalities): void
    {
        for ($i = 0; $i < 15; $i++) {
            $province = $provinces->random();
            $provinceMunicipalities = $municipalities->where('province_id', $province->id);
            $municipality = $provinceMunicipalities->isNotEmpty() ? $provinceMunicipalities->random() : null;
            
            CarbonSavingRequest::create([
                'installation_power_kw' => fake()->randomFloat(2, 3, 15),
                'production_kwh' => fake()->optional(0.7)->randomFloat(2, 3000, 18000),
                'province_id' => $province->id,
                'municipality_id' => $municipality?->id,
                'period' => fake()->randomElement(['annual', 'monthly']),
                'start_date' => fake()->optional(0.8)->dateTimeBetween('-1 year', 'now'),
                'end_date' => fake()->optional(0.6)->dateTimeBetween('now', '+1 year'),
                'efficiency_ratio' => fake()->optional(0.6)->randomFloat(4, 0.75, 0.95),
                'loss_factor' => fake()->optional(0.5)->randomFloat(4, 0.02, 0.08),
            ]);
        }
    }

    /**
     * Crear solicitudes comerciales.
     */
    private function createCommercialRequests($provinces, $municipalities): void
    {
        for ($i = 0; $i < 10; $i++) {
            $province = $provinces->random();
            $provinceMunicipalities = $municipalities->where('province_id', $province->id);
            $municipality = $provinceMunicipalities->isNotEmpty() ? $provinceMunicipalities->random() : null;
            
            CarbonSavingRequest::create([
                'installation_power_kw' => fake()->randomFloat(2, 20, 200),
                'production_kwh' => fake()->optional(0.8)->randomFloat(2, 25000, 250000),
                'province_id' => $province->id,
                'municipality_id' => $municipality?->id,
                'period' => fake()->randomElement(['annual', 'monthly']),
                'start_date' => fake()->optional(0.9)->dateTimeBetween('-1 year', 'now'),
                'end_date' => fake()->optional(0.7)->dateTimeBetween('now', '+2 years'),
                'efficiency_ratio' => fake()->optional(0.7)->randomFloat(4, 0.80, 0.95),
                'loss_factor' => fake()->optional(0.6)->randomFloat(4, 0.03, 0.10),
            ]);
        }
    }

    /**
     * Crear solicitudes industriales.
     */
    private function createIndustrialRequests($provinces, $municipalities): void
    {
        for ($i = 0; $i < 8; $i++) {
            $province = $provinces->random();
            $provinceMunicipalities = $municipalities->where('province_id', $province->id);
            $municipality = $provinceMunicipalities->isNotEmpty() ? $provinceMunicipalities->random() : null;
            
            CarbonSavingRequest::create([
                'installation_power_kw' => fake()->randomFloat(2, 500, 5000),
                'production_kwh' => fake()->optional(0.9)->randomFloat(2, 500000, 5000000),
                'province_id' => $province->id,
                'municipality_id' => $municipality?->id,
                'period' => 'annual',
                'start_date' => fake()->optional(0.9)->dateTimeBetween('-2 years', 'now'),
                'end_date' => fake()->optional(0.8)->dateTimeBetween('now', '+5 years'),
                'efficiency_ratio' => fake()->optional(0.8)->randomFloat(4, 0.85, 0.98),
                'loss_factor' => fake()->optional(0.7)->randomFloat(4, 0.05, 0.15),
            ]);
        }
    }

    /**
     * Crear solicitudes agrícolas.
     */
    private function createAgriculturalRequests($provinces, $municipalities): void
    {
        for ($i = 0; $i < 7; $i++) {
            $province = $provinces->random();
            $provinceMunicipalities = $municipalities->where('province_id', $province->id);
            $municipality = $provinceMunicipalities->isNotEmpty() ? $provinceMunicipalities->random() : null;
            
            CarbonSavingRequest::create([
                'installation_power_kw' => fake()->randomFloat(2, 50, 1000),
                'production_kwh' => fake()->optional(0.6)->randomFloat(2, 50000, 800000),
                'province_id' => $province->id,
                'municipality_id' => $municipality?->id,
                'period' => fake()->randomElement(['annual', 'monthly']),
                'start_date' => fake()->optional(0.7)->dateTimeBetween('-1 year', 'now'),
                'end_date' => fake()->optional(0.5)->dateTimeBetween('now', '+3 years'),
                'efficiency_ratio' => fake()->optional(0.5)->randomFloat(4, 0.70, 0.90),
                'loss_factor' => fake()->optional(0.4)->randomFloat(4, 0.04, 0.12),
            ]);
        }
    }
}