<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\EnergyCertificate;
use App\Models\User;
use App\Models\ZoneClimate;
use Carbon\Carbon;

class EnergyCertificateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🏠 Sembrando certificados energéticos de edificios...');

        // Obtener usuarios y zonas climáticas disponibles
        $users = User::take(10)->get();
        $zoneClimates = ZoneClimate::take(15)->get();

        if ($users->isEmpty() || $zoneClimates->isEmpty()) {
            $this->command->error('❌ No hay usuarios o zonas climáticas disponibles para crear EnergyCertificate');
            return;
        }

        $energyCertificates = [
            // ===== EDIFICIOS RESIDENCIALES =====
            [
                'building_type' => 'residential',
                'energy_rating' => 'A',
                'annual_energy_consumption_kwh' => 2500.00,
                'annual_emissions_kg_co2e' => 125.00,
            ],
            [
                'building_type' => 'residential',
                'energy_rating' => 'A+',
                'annual_energy_consumption_kwh' => 1800.00,
                'annual_emissions_kg_co2e' => 90.00,
            ],
            [
                'building_type' => 'residential',
                'energy_rating' => 'B',
                'annual_energy_consumption_kwh' => 4500.00,
                'annual_emissions_kg_co2e' => 225.00,
            ],
            [
                'building_type' => 'residential',
                'energy_rating' => 'C',
                'annual_energy_consumption_kwh' => 6500.00,
                'annual_emissions_kg_co2e' => 325.00,
            ],
            [
                'building_type' => 'residential',
                'energy_rating' => 'D',
                'annual_energy_consumption_kwh' => 8500.00,
                'annual_emissions_kg_co2e' => 425.00,
            ],

            // ===== EDIFICIOS DE OFICINAS =====
            [
                'building_type' => 'office',
                'energy_rating' => 'A',
                'annual_energy_consumption_kwh' => 15000.00,
                'annual_emissions_kg_co2e' => 750.00,
            ],
            [
                'building_type' => 'office',
                'energy_rating' => 'B',
                'annual_energy_consumption_kwh' => 25000.00,
                'annual_emissions_kg_co2e' => 1250.00,
            ],
            [
                'building_type' => 'office',
                'energy_rating' => 'C',
                'annual_energy_consumption_kwh' => 35000.00,
                'annual_emissions_kg_co2e' => 1750.00,
            ],

            // ===== EDIFICIOS COMERCIALES =====
            [
                'building_type' => 'commercial',
                'energy_rating' => 'A',
                'annual_energy_consumption_kwh' => 20000.00,
                'annual_emissions_kg_co2e' => 1000.00,
            ],
            [
                'building_type' => 'commercial',
                'energy_rating' => 'B',
                'annual_energy_consumption_kwh' => 30000.00,
                'annual_emissions_kg_co2e' => 1500.00,
            ],
            [
                'building_type' => 'commercial',
                'energy_rating' => 'C',
                'annual_energy_consumption_kwh' => 45000.00,
                'annual_emissions_kg_co2e' => 2250.00,
            ],

            // ===== EDIFICIOS INDUSTRIALES =====
            [
                'building_type' => 'industrial',
                'energy_rating' => 'A',
                'annual_energy_consumption_kwh' => 50000.00,
                'annual_emissions_kg_co2e' => 2500.00,
            ],
            [
                'building_type' => 'industrial',
                'energy_rating' => 'B',
                'annual_energy_consumption_kwh' => 75000.00,
                'annual_emissions_kg_co2e' => 3750.00,
            ],
            [
                'building_type' => 'industrial',
                'energy_rating' => 'C',
                'annual_energy_consumption_kwh' => 100000.00,
                'annual_emissions_kg_co2e' => 5000.00,
            ],

            // ===== EDIFICIOS PÚBLICOS =====
            [
                'building_type' => 'public',
                'energy_rating' => 'A',
                'annual_energy_consumption_kwh' => 12000.00,
                'annual_emissions_kg_co2e' => 600.00,
            ],
            [
                'building_type' => 'public',
                'energy_rating' => 'B',
                'annual_energy_consumption_kwh' => 18000.00,
                'annual_emissions_kg_co2e' => 900.00,
            ],
            [
                'building_type' => 'public',
                'energy_rating' => 'C',
                'annual_energy_consumption_kwh' => 25000.00,
                'annual_emissions_kg_co2e' => 1250.00,
            ],

            // ===== EDIFICIOS EDUCATIVOS =====
            [
                'building_type' => 'educational',
                'energy_rating' => 'A',
                'annual_energy_consumption_kwh' => 15000.00,
                'annual_emissions_kg_co2e' => 750.00,
            ],
            [
                'building_type' => 'educational',
                'energy_rating' => 'B',
                'annual_energy_consumption_kwh' => 22000.00,
                'annual_emissions_kg_co2e' => 1100.00,
            ],
            [
                'building_type' => 'educational',
                'energy_rating' => 'C',
                'annual_energy_consumption_kwh' => 32000.00,
                'annual_emissions_kg_co2e' => 1600.00,
            ],

            // ===== EDIFICIOS SANITARIOS =====
            [
                'building_type' => 'healthcare',
                'energy_rating' => 'A',
                'annual_energy_consumption_kwh' => 30000.00,
                'annual_emissions_kg_co2e' => 1500.00,
            ],
            [
                'building_type' => 'healthcare',
                'energy_rating' => 'B',
                'annual_energy_consumption_kwh' => 45000.00,
                'annual_emissions_kg_co2e' => 2250.00,
            ],
            [
                'building_type' => 'healthcare',
                'energy_rating' => 'C',
                'annual_energy_consumption_kwh' => 60000.00,
                'annual_emissions_kg_co2e' => 3000.00,
            ],

            // ===== EDIFICIOS HOTELEROS =====
            [
                'building_type' => 'hotel',
                'energy_rating' => 'A',
                'annual_energy_consumption_kwh' => 25000.00,
                'annual_emissions_kg_co2e' => 1250.00,
            ],
            [
                'building_type' => 'hotel',
                'energy_rating' => 'B',
                'annual_energy_consumption_kwh' => 35000.00,
                'annual_emissions_kg_co2e' => 1750.00,
            ],
            [
                'building_type' => 'hotel',
                'energy_rating' => 'C',
                'annual_energy_consumption_kwh' => 50000.00,
                'annual_emissions_kg_co2e' => 2500.00,
            ],

            // ===== EDIFICIOS DEPORTIVOS =====
            [
                'building_type' => 'sports',
                'energy_rating' => 'A',
                'annual_energy_consumption_kwh' => 20000.00,
                'annual_emissions_kg_co2e' => 1000.00,
            ],
            [
                'building_type' => 'sports',
                'energy_rating' => 'B',
                'annual_energy_consumption_kwh' => 30000.00,
                'annual_emissions_kg_co2e' => 1500.00,
            ],
            [
                'building_type' => 'sports',
                'energy_rating' => 'C',
                'annual_energy_consumption_kwh' => 45000.00,
                'annual_emissions_kg_co2e' => 2250.00,
            ],

            // ===== EDIFICIOS CULTURALES =====
            [
                'building_type' => 'cultural',
                'energy_rating' => 'A',
                'annual_energy_consumption_kwh' => 15000.00,
                'annual_emissions_kg_co2e' => 750.00,
            ],
            [
                'building_type' => 'cultural',
                'energy_rating' => 'B',
                'annual_energy_consumption_kwh' => 22000.00,
                'annual_emissions_kg_co2e' => 1100.00,
            ],
            [
                'building_type' => 'cultural',
                'energy_rating' => 'C',
                'annual_energy_consumption_kwh' => 32000.00,
                'annual_emissions_kg_co2e' => 1600.00,
            ],

            // ===== EDIFICIOS RELIGIOSOS =====
            [
                'building_type' => 'religious',
                'energy_rating' => 'B',
                'annual_energy_consumption_kwh' => 8000.00,
                'annual_emissions_kg_co2e' => 400.00,
            ],
            [
                'building_type' => 'religious',
                'energy_rating' => 'C',
                'annual_energy_consumption_kwh' => 12000.00,
                'annual_emissions_kg_co2e' => 600.00,
            ],
            [
                'building_type' => 'religious',
                'energy_rating' => 'D',
                'annual_energy_consumption_kwh' => 18000.00,
                'annual_emissions_kg_co2e' => 900.00,
            ],

            // ===== EDIFICIOS MIXTOS =====
            [
                'building_type' => 'mixed',
                'energy_rating' => 'A',
                'annual_energy_consumption_kwh' => 35000.00,
                'annual_emissions_kg_co2e' => 1750.00,
            ],
            [
                'building_type' => 'mixed',
                'energy_rating' => 'B',
                'annual_energy_consumption_kwh' => 50000.00,
                'annual_emissions_kg_co2e' => 2500.00,
            ],
            [
                'building_type' => 'mixed',
                'energy_rating' => 'C',
                'annual_energy_consumption_kwh' => 70000.00,
                'annual_emissions_kg_co2e' => 3500.00,
            ],
        ];

        $createdCount = 0;
        $updatedCount = 0;

        foreach ($energyCertificates as $index => $certificateData) {
            // Asignar usuario aleatorio
            $certificateData['user_id'] = $users->random()->id;
            
            // Asignar zona climática aleatoria
            $certificateData['zone_climate_id'] = $zoneClimates->random()->id;
            
            // Los certificados se crean con la fecha actual

            $certificate = EnergyCertificate::updateOrCreate(
                [
                    'user_id' => $certificateData['user_id'],
                    'building_type' => $certificateData['building_type'],
                    'energy_rating' => $certificateData['energy_rating'],
                ],
                $certificateData
            );

            if ($certificate->wasRecentlyCreated) {
                $createdCount++;
            } else {
                $updatedCount++;
            }
        }

        // Mostrar estadísticas
        $this->command->info("✅ Certificados creados: {$createdCount}");
        $this->command->info("🔄 Certificados actualizados: {$updatedCount}");
        $this->command->info("📊 Total de certificados: " . EnergyCertificate::count());

        // Mostrar resumen por tipo de edificio
        $this->command->info("\n📋 Resumen por tipo de edificio:");
        $buildingTypes = EnergyCertificate::all()->groupBy('building_type');
        foreach ($buildingTypes as $type => $certificates) {
            $avgConsumption = $certificates->avg('annual_energy_consumption_kwh');
            $avgEmissions = $certificates->avg('annual_emissions_kg_co2e');
            $this->command->info("  {$type}: {$certificates->count()} certificados, consumo medio: " . number_format($avgConsumption, 0) . " kWh/año, emisiones medias: " . number_format($avgEmissions, 0) . " kg CO2e/año");
        }

        // Mostrar resumen por calificación energética
        $this->command->info("\n🏆 Resumen por calificación energética:");
        $ratings = EnergyCertificate::all()->groupBy('energy_rating');
        foreach ($ratings as $rating => $certificates) {
            $avgConsumption = $certificates->avg('annual_energy_consumption_kwh');
            $avgEmissions = $certificates->avg('annual_emissions_kg_co2e');
            $this->command->info("  {$rating}: {$certificates->count()} certificados, consumo medio: " . number_format($avgConsumption, 0) . " kWh/año, emisiones medias: " . number_format($avgEmissions, 0) . " kg CO2e/año");
        }

        // Mostrar algunos certificados destacados
        $this->command->info("\n🔬 Certificados destacados:");
        $featuredCertificates = EnergyCertificate::where('energy_rating', 'A')->orWhere('energy_rating', 'A+')->take(3)->get();
        foreach ($featuredCertificates as $certificate) {
            $this->command->info("  🏠 {$certificate->building_type} - Calificación: {$certificate->energy_rating}");
            $this->command->info("     📊 Consumo: " . number_format($certificate->annual_energy_consumption_kwh, 0) . " kWh/año");
            $this->command->info("     🌱 Emisiones: " . number_format($certificate->annual_emissions_kg_co2e, 0) . " kg CO2e/año");
            $this->command->info("     ---");
        }

        // Mostrar estadísticas de eficiencia
        $this->command->info("\n📈 Estadísticas de eficiencia energética:");
        $totalCertificates = EnergyCertificate::count();
        $highEfficiency = EnergyCertificate::whereIn('energy_rating', ['A', 'A+'])->count();
        $mediumEfficiency = EnergyCertificate::whereIn('energy_rating', ['B', 'C'])->count();
        $lowEfficiency = EnergyCertificate::whereIn('energy_rating', ['D', 'E', 'F', 'G'])->count();
        
        $this->command->info("  🟢 Alta eficiencia (A/A+): {$highEfficiency} certificados (" . round(($highEfficiency/$totalCertificates)*100, 1) . "%)");
        $this->command->info("  🟡 Eficiencia media (B/C): {$mediumEfficiency} certificados (" . round(($mediumEfficiency/$totalCertificates)*100, 1) . "%)");
        $this->command->info("  🔴 Baja eficiencia (D-G): {$lowEfficiency} certificados (" . round(($lowEfficiency/$totalCertificates)*100, 1) . "%)");

        $this->command->info("\n🎯 Seeder de EnergyCertificate completado exitosamente!");
    }
}
