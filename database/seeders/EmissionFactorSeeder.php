<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\EmissionFactor;

class EmissionFactorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🌱 Sembrando factores de emisión...');

        $emissionFactors = [
            // ===== ENERGÍA ELÉCTRICA =====
            [
                'activity' => 'Electricidad - Red Nacional (España)',
                'factor_kg_co2e_per_unit' => 0.2500,
                'unit' => 'kWh',
            ],
            [
                'activity' => 'Electricidad - Solar Fotovoltaica',
                'factor_kg_co2e_per_unit' => 0.0450,
                'unit' => 'kWh',
            ],
            [
                'activity' => 'Electricidad - Eólica',
                'factor_kg_co2e_per_unit' => 0.0110,
                'unit' => 'kWh',
            ],
            [
                'activity' => 'Electricidad - Hidroeléctrica',
                'factor_kg_co2e_per_unit' => 0.0240,
                'unit' => 'kWh',
            ],
            [
                'activity' => 'Electricidad - Nuclear',
                'factor_kg_co2e_per_unit' => 0.0120,
                'unit' => 'kWh',
            ],
            [
                'activity' => 'Electricidad - Biomasa',
                'factor_kg_co2e_per_unit' => 0.2300,
                'unit' => 'kWh',
            ],
            [
                'activity' => 'Electricidad - Gas Natural',
                'factor_kg_co2e_per_unit' => 0.4900,
                'unit' => 'kWh',
            ],
            [
                'activity' => 'Electricidad - Carbón',
                'factor_kg_co2e_per_unit' => 0.8200,
                'unit' => 'kWh',
            ],

            // ===== COMBUSTIBLES FÓSILES =====
            [
                'activity' => 'Gasolina - Consumo directo',
                'factor_kg_co2e_per_unit' => 2.3100,
                'unit' => 'litro',
            ],
            [
                'activity' => 'Gasóleo - Consumo directo',
                'factor_kg_co2e_per_unit' => 2.6800,
                'unit' => 'litro',
            ],
            [
                'activity' => 'Gas Natural - Consumo directo',
                'factor_kg_co2e_per_unit' => 2.1625,
                'unit' => 'm³',
            ],
            [
                'activity' => 'Propano - Consumo directo',
                'factor_kg_co2e_per_unit' => 2.9830,
                'unit' => 'kg',
            ],
            [
                'activity' => 'Butano - Consumo directo',
                'factor_kg_co2e_per_unit' => 3.0300,
                'unit' => 'kg',
            ],
            [
                'activity' => 'Carbón - Consumo directo',
                'factor_kg_co2e_per_unit' => 2.4200,
                'unit' => 'kg',
            ],

            // ===== TRANSPORTE =====
            [
                'activity' => 'Coche - Gasolina',
                'factor_kg_co2e_per_unit' => 0.1700,
                'unit' => 'km',
            ],
            [
                'activity' => 'Coche - Diésel',
                'factor_kg_co2e_per_unit' => 0.1800,
                'unit' => 'km',
            ],
            [
                'activity' => 'Coche - Híbrido',
                'factor_kg_co2e_per_unit' => 0.1200,
                'unit' => 'km',
            ],
            [
                'activity' => 'Coche - Eléctrico (Red Española)',
                'factor_kg_co2e_per_unit' => 0.0425,
                'unit' => 'km',
            ],
            [
                'activity' => 'Autobús Urbano',
                'factor_kg_co2e_per_unit' => 0.0890,
                'unit' => 'km',
            ],
            [
                'activity' => 'Tren - Cercanías',
                'factor_kg_co2e_per_unit' => 0.0310,
                'unit' => 'km',
            ],
            [
                'activity' => 'Tren - Media Distancia',
                'factor_kg_co2e_per_unit' => 0.0280,
                'unit' => 'km',
            ],
            [
                'activity' => 'Tren - Alta Velocidad',
                'factor_kg_co2e_per_unit' => 0.0250,
                'unit' => 'km',
            ],
            [
                'activity' => 'Metro',
                'factor_kg_co2e_per_unit' => 0.0270,
                'unit' => 'km',
            ],
            [
                'activity' => 'Tranvía',
                'factor_kg_co2e_per_unit' => 0.0250,
                'unit' => 'km',
            ],
            [
                'activity' => 'Avión - Corta Distancia',
                'factor_kg_co2e_per_unit' => 0.2550,
                'unit' => 'km',
            ],
            [
                'activity' => 'Avión - Media Distancia',
                'factor_kg_co2e_per_unit' => 0.1980,
                'unit' => 'km',
            ],
            [
                'activity' => 'Avión - Larga Distancia',
                'factor_kg_co2e_per_unit' => 0.1390,
                'unit' => 'km',
            ],
            [
                'activity' => 'Barco - Ferry',
                'factor_kg_co2e_per_unit' => 0.1150,
                'unit' => 'km',
            ],

            // ===== AGUA =====
            [
                'activity' => 'Agua Potable - Consumo',
                'factor_kg_co2e_per_unit' => 0.2980,
                'unit' => 'm³',
            ],
            [
                'activity' => 'Agua Residual - Tratamiento',
                'factor_kg_co2e_per_unit' => 0.3500,
                'unit' => 'm³',
            ],

            // ===== RESIDUOS =====
            [
                'activity' => 'Residuos Sólidos Urbanos - Vertedero',
                'factor_kg_co2e_per_unit' => 0.5000,
                'unit' => 'kg',
            ],
            [
                'activity' => 'Residuos Sólidos Urbanos - Incineración',
                'factor_kg_co2e_per_unit' => 0.3000,
                'unit' => 'kg',
            ],
            [
                'activity' => 'Residuos Sólidos Urbanos - Compostaje',
                'factor_kg_co2e_per_unit' => 0.1000,
                'unit' => 'kg',
            ],
            [
                'activity' => 'Residuos Sólidos Urbanos - Reciclaje',
                'factor_kg_co2e_per_unit' => 0.0500,
                'unit' => 'kg',
            ],

            // ===== ALIMENTACIÓN =====
            [
                'activity' => 'Carne de Vaca',
                'factor_kg_co2e_per_unit' => 13.3000,
                'unit' => 'kg',
            ],
            [
                'activity' => 'Carne de Cordero',
                'factor_kg_co2e_per_unit' => 39.2000,
                'unit' => 'kg',
            ],
            [
                'activity' => 'Carne de Cerdo',
                'factor_kg_co2e_per_unit' => 7.2000,
                'unit' => 'kg',
            ],
            [
                'activity' => 'Carne de Pollo',
                'factor_kg_co2e_per_unit' => 6.9000,
                'unit' => 'kg',
            ],
            [
                'activity' => 'Pescado - Salmón',
                'factor_kg_co2e_per_unit' => 11.9000,
                'unit' => 'kg',
            ],
            [
                'activity' => 'Pescado - Atún',
                'factor_kg_co2e_per_unit' => 6.1000,
                'unit' => 'kg',
            ],
            [
                'activity' => 'Huevos',
                'factor_kg_co2e_per_unit' => 4.8000,
                'unit' => 'kg',
            ],
            [
                'activity' => 'Leche',
                'factor_kg_co2e_per_unit' => 1.4000,
                'unit' => 'kg',
            ],
            [
                'activity' => 'Queso',
                'factor_kg_co2e_per_unit' => 13.5000,
                'unit' => 'kg',
            ],
            [
                'activity' => 'Arroz',
                'factor_kg_co2e_per_unit' => 2.7000,
                'unit' => 'kg',
            ],
            [
                'activity' => 'Trigo',
                'factor_kg_co2e_per_unit' => 1.4000,
                'unit' => 'kg',
            ],
            [
                'activity' => 'Maíz',
                'factor_kg_co2e_per_unit' => 1.0000,
                'unit' => 'kg',
            ],
            [
                'activity' => 'Patatas',
                'factor_kg_co2e_per_unit' => 0.2000,
                'unit' => 'kg',
            ],
            [
                'activity' => 'Tomates',
                'factor_kg_co2e_per_unit' => 1.4000,
                'unit' => 'kg',
            ],
            [
                'activity' => 'Manzanas',
                'factor_kg_co2e_per_unit' => 0.3000,
                'unit' => 'kg',
            ],
            [
                'activity' => 'Plátanos',
                'factor_kg_co2e_per_unit' => 0.7000,
                'unit' => 'kg',
            ],
            [
                'activity' => 'Naranjas',
                'factor_kg_co2e_per_unit' => 0.3000,
                'unit' => 'kg',
            ],
            [
                'activity' => 'Uvas',
                'factor_kg_co2e_per_unit' => 0.3000,
                'unit' => 'kg',
            ],
            [
                'activity' => 'Aceite de Oliva',
                'factor_kg_co2e_per_unit' => 6.3000,
                'unit' => 'kg',
            ],
            [
                'activity' => 'Vino',
                'factor_kg_co2e_per_unit' => 1.4000,
                'unit' => 'litro',
            ],
            [
                'activity' => 'Cerveza',
                'factor_kg_co2e_per_unit' => 0.6000,
                'unit' => 'litro',
            ],
            [
                'activity' => 'Café',
                'factor_kg_co2e_per_unit' => 28.5000,
                'unit' => 'kg',
            ],
            [
                'activity' => 'Té',
                'factor_kg_co2e_per_unit' => 8.9000,
                'unit' => 'kg',
            ],
            [
                'activity' => 'Chocolate',
                'factor_kg_co2e_per_unit' => 2.3000,
                'unit' => 'kg',
            ],

            // ===== MATERIALES Y CONSTRUCCIÓN =====
            [
                'activity' => 'Cemento',
                'factor_kg_co2e_per_unit' => 0.8300,
                'unit' => 'kg',
            ],
            [
                'activity' => 'Acero',
                'factor_kg_co2e_per_unit' => 1.8500,
                'unit' => 'kg',
            ],
            [
                'activity' => 'Aluminio',
                'factor_kg_co2e_per_unit' => 8.1400,
                'unit' => 'kg',
            ],
            [
                'activity' => 'Vidrio',
                'factor_kg_co2e_per_unit' => 0.8500,
                'unit' => 'kg',
            ],
            [
                'activity' => 'Plástico - PET',
                'factor_kg_co2e_per_unit' => 2.1500,
                'unit' => 'kg',
            ],
            [
                'activity' => 'Plástico - HDPE',
                'factor_kg_co2e_per_unit' => 1.6000,
                'unit' => 'kg',
            ],
            [
                'activity' => 'Madera - Seca',
                'factor_kg_co2e_per_unit' => 0.1100,
                'unit' => 'kg',
            ],
            [
                'activity' => 'Madera - Húmeda',
                'factor_kg_co2e_per_unit' => 0.2200,
                'unit' => 'kg',
            ],
            [
                'activity' => 'Papel - Reciclado',
                'factor_kg_co2e_per_unit' => 0.7000,
                'unit' => 'kg',
            ],
            [
                'activity' => 'Papel - Virgen',
                'factor_kg_co2e_per_unit' => 1.2000,
                'unit' => 'kg',
            ],
            [
                'activity' => 'Cartón',
                'factor_kg_co2e_per_unit' => 0.9000,
                'unit' => 'kg',
            ],

            // ===== SERVICIOS =====
            [
                'activity' => 'Hotel - Por noche',
                'factor_kg_co2e_per_unit' => 15.0000,
                'unit' => 'noche',
            ],
            [
                'activity' => 'Restaurante - Por comida',
                'factor_kg_co2e_per_unit' => 2.5000,
                'unit' => 'comida',
            ],
            [
                'activity' => 'Lavadora - Por ciclo',
                'factor_kg_co2e_per_unit' => 0.6000,
                'unit' => 'ciclo',
            ],
            [
                'activity' => 'Secadora - Por ciclo',
                'factor_kg_co2e_per_unit' => 2.4000,
                'unit' => 'ciclo',
            ],
            [
                'activity' => 'Lavavajillas - Por ciclo',
                'factor_kg_co2e_per_unit' => 0.8000,
                'unit' => 'ciclo',
            ],
            [
                'activity' => 'Televisión - Por hora',
                'factor_kg_co2e_per_unit' => 0.0500,
                'unit' => 'hora',
            ],
            [
                'activity' => 'Ordenador - Por hora',
                'factor_kg_co2e_per_unit' => 0.0800,
                'unit' => 'hora',
            ],
            [
                'activity' => 'Smartphone - Por año',
                'factor_kg_co2e_per_unit' => 55.0000,
                'unit' => 'año',
            ],
            [
                'activity' => 'Internet - Por GB',
                'factor_kg_co2e_per_unit' => 0.0000,
                'unit' => 'GB',
            ],
        ];

        $createdCount = 0;
        $updatedCount = 0;

        foreach ($emissionFactors as $factorData) {
            $factor = EmissionFactor::updateOrCreate(
                [
                    'activity' => $factorData['activity'],
                ],
                $factorData
            );

            if ($factor->wasRecentlyCreated) {
                $createdCount++;
            } else {
                $updatedCount++;
            }
        }

        // Mostrar estadísticas
        $this->command->info("✅ Factores creados: {$createdCount}");
        $this->command->info("🔄 Factores actualizados: {$updatedCount}");
        $this->command->info("📊 Total de factores: " . EmissionFactor::count());

        // Mostrar resumen por categorías
        $this->command->info("\n📋 Resumen por categorías:");
        $categories = EmissionFactor::all()->groupBy('activity_category');
        
        foreach ($categories as $category => $factors) {
            $this->command->info("  {$category}: {$factors->count()} factores");
        }

        // Mostrar algunos ejemplos
        $this->command->info("\n🔬 Ejemplos de factores creados:");
        $sampleFactors = EmissionFactor::take(5)->get();
        foreach ($sampleFactors as $factor) {
            $this->command->info("  📋 {$factor->activity}");
            $this->command->info("     💰 {$factor->formatted_factor}");
            $this->command->info("     🏷️  {$factor->activity_category}");
        }

        $this->command->info("\n🎯 Seeder de EmissionFactor completado exitosamente!");
    }
}
