<?php

namespace Database\Seeders;

use App\Models\CarbonCalculation;
use App\Models\CarbonEquivalence;
use App\Models\User;
use Illuminate\Database\Seeder;
use Carbon\Carbon;
use Illuminate\Support\Str;

class CarbonCalculationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener equivalencias y usuarios disponibles
        $equivalences = CarbonEquivalence::all();
        $users = User::all();
        
        if ($equivalences->isEmpty()) {
            $this->command->warn('No hay equivalencias de carbono en la base de datos. No se pueden crear cálculos.');
            return;
        }
        
        if ($users->isEmpty()) {
            $this->command->warn('No hay usuarios en la base de datos. No se pueden crear cálculos.');
            return;
        }

        // Contextos de cálculo realistas
        $contexts = [
            'energy' => [
                'Consumo eléctrico mensual del hogar',
                'Uso de aire acondicionado en verano',
                'Calefacción eléctrica en invierno',
                'Carga de vehículo eléctrico',
                'Uso de electrodomésticos',
                'Iluminación LED vs tradicional',
                'Instalación de paneles solares',
                'Consumo de gas natural para cocina',
            ],
            'transport' => [
                'Viaje diario al trabajo',
                'Viaje de vacaciones en avión',
                'Transporte de mercancías',
                'Uso de transporte público',
                'Desplazamiento en coche eléctrico',
                'Viaje en tren de alta velocidad',
                'Logística de última milla',
                'Transporte de pasajeros',
            ],
            'food' => [
                'Consumo semanal de carne',
                'Dieta vegetariana vs omnívora',
                'Producción de alimentos locales',
                'Transporte de alimentos',
                'Consumo de productos lácteos',
                'Agricultura sostenible',
                'Pesca y acuicultura',
                'Producción de cereales',
            ],
            'other' => [
                'Construcción de vivienda',
                'Mantenimiento de edificios',
                'Producción industrial',
                'Gestión de residuos',
                'Agricultura intensiva',
                'Deforestación',
                'Reforestación',
                'Tecnologías limpias',
            ],
        ];

        // Parámetros adicionales por categoría
        $parametersByCategory = [
            'energy' => [
                'efficiency_rating' => ['A+++', 'A++', 'A+', 'A', 'B', 'C', 'D', 'E', 'F', 'G'],
                'time_of_use' => ['peak', 'off_peak', 'valley'],
                'renewable_percentage' => [0, 25, 50, 75, 100],
                'building_type' => ['residential', 'commercial', 'industrial'],
            ],
            'transport' => [
                'vehicle_age' => [1, 2, 3, 5, 8, 10, 15, 20],
                'fuel_efficiency' => ['low', 'medium', 'high', 'hybrid', 'electric'],
                'occupancy_rate' => [0.25, 0.5, 0.75, 1.0],
                'road_conditions' => ['urban', 'highway', 'mountain', 'city_center'],
            ],
            'food' => [
                'production_method' => ['conventional', 'organic', 'biodynamic', 'permaculture'],
                'transport_distance' => [0, 50, 100, 500, 1000, 5000],
                'storage_method' => ['fresh', 'frozen', 'canned', 'dried'],
                'seasonality' => ['in_season', 'out_of_season', 'greenhouse'],
            ],
            'other' => [
                'material_type' => ['concrete', 'steel', 'wood', 'bamboo', 'recycled'],
                'construction_method' => ['traditional', 'prefabricated', 'modular'],
                'energy_source' => ['fossil', 'renewable', 'mixed'],
                'waste_management' => ['landfill', 'recycling', 'composting', 'incineration'],
            ],
        ];

        $createdCalculations = [];
        $calculationCount = 0;

        // Crear cálculos para usuarios registrados
        foreach ($users as $user) {
            // Cada usuario tendrá entre 5 y 15 cálculos
            $userCalculationCount = rand(5, 15);
            
            for ($i = 0; $i < $userCalculationCount; $i++) {
                $equivalence = $equivalences->random();
                $category = $equivalence->category;
                
                // Generar cantidad realista según la unidad
                $quantity = $this->generateRealisticQuantity($equivalence->unit);
                
                // Calcular CO2 usando el método del modelo
                $co2Result = $equivalence->calculateCO2($quantity);
                
                // Seleccionar contexto apropiado
                $context = $contexts[$category][array_rand($contexts[$category])];
                
                // Generar parámetros adicionales
                $parameters = $this->generateParameters($category, $parametersByCategory);
                
                // Generar fecha realista (últimos 6 meses)
                $createdAt = Carbon::now()->subDays(rand(0, 180));
                
                $calculation = CarbonCalculation::create([
                    'user_id' => $user->id,
                    'carbon_equivalence_id' => $equivalence->id,
                    'quantity' => $quantity,
                    'co2_result' => $co2Result,
                    'context' => $context,
                    'parameters' => $parameters,
                    'session_id' => null,
                    'created_at' => $createdAt,
                    'updated_at' => $createdAt,
                ]);
                
                $createdCalculations[] = [
                    'id' => $calculation->id,
                    'user' => $user->name ?? $user->email,
                    'equivalence' => $equivalence->name,
                    'category' => ucfirst($equivalence->category),
                    'quantity' => $quantity . ' ' . $equivalence->unit,
                    'co2_result' => round($co2Result, 3) . ' kg CO2',
                    'impact_level' => $calculation->impact_level,
                    'context' => $context,
                ];
                
                $calculationCount++;
            }
        }

        // Crear cálculos anónimos (sin usuario, solo con session_id)
        $anonymousCalculationCount = rand(20, 40);
        
        for ($i = 0; $i < $anonymousCalculationCount; $i++) {
            $equivalence = $equivalences->random();
            $category = $equivalence->category;
            
            $quantity = $this->generateRealisticQuantity($equivalence->unit);
            $co2Result = $equivalence->calculateCO2($quantity);
            $context = $contexts[$category][array_rand($contexts[$category])];
            $parameters = $this->generateParameters($category, $parametersByCategory);
            $createdAt = Carbon::now()->subDays(rand(0, 90));
            
            $calculation = CarbonCalculation::create([
                'user_id' => null,
                'carbon_equivalence_id' => $equivalence->id,
                'quantity' => $quantity,
                'co2_result' => $co2Result,
                'context' => $context,
                'parameters' => $parameters,
                'session_id' => 'anon_' . Str::random(10),
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ]);
            
            $createdCalculations[] = [
                'id' => $calculation->id,
                'user' => 'Anónimo',
                'equivalence' => $equivalence->name,
                'category' => ucfirst($equivalence->category),
                'quantity' => $quantity . ' ' . $equivalence->unit,
                'co2_result' => round($co2Result, 3) . ' kg CO2',
                'impact_level' => $calculation->impact_level,
                'context' => $context,
            ];
            
            $calculationCount++;
        }

        $this->command->info("Se han creado {$calculationCount} cálculos de carbono.");
        
        // Mostrar tabla con los cálculos creados
        $displayData = array_slice($createdCalculations, 0, 20); // Mostrar solo los primeros 20
        $this->command->table(
            ['ID', 'Usuario', 'Equivalencia', 'Categoría', 'Cantidad', 'CO2 Resultado', 'Nivel Impacto', 'Contexto'],
            $displayData
        );
        
        if (count($createdCalculations) > 20) {
            $this->command->info("... y " . (count($createdCalculations) - 20) . " cálculos más.");
        }

        // Estadísticas
        $totalCalculations = CarbonCalculation::count();
        $userCalculations = CarbonCalculation::whereNotNull('user_id')->count();
        $anonymousCalculations = CarbonCalculation::whereNull('user_id')->count();
        
        // Estadísticas por nivel de impacto
        $impactStats = CarbonCalculation::selectRaw('
            CASE 
                WHEN co2_result < 1 THEN "bajo"
                WHEN co2_result < 5 THEN "medio"
                WHEN co2_result < 10 THEN "alto"
                ELSE "muy_alto"
            END as impact_level,
            COUNT(*) as count
        ')
        ->groupBy('impact_level')
        ->pluck('count', 'impact_level')
        ->toArray();
        
        // Estadísticas por categoría
        $categoryStats = CarbonCalculation::selectRaw('carbon_equivalences.category, COUNT(*) as count')
            ->join('carbon_equivalences', 'carbon_calculations.carbon_equivalence_id', '=', 'carbon_equivalences.id')
            ->groupBy('carbon_equivalences.category')
            ->pluck('count', 'category')
            ->toArray();
        
        // Total de CO2 calculado
        $totalCO2 = CarbonCalculation::sum('co2_result');
        $averageCO2 = CarbonCalculation::avg('co2_result');
        
        $this->command->newLine();
        $this->command->info("📊 Estadísticas:");
        $this->command->info("   • Total de cálculos: {$totalCalculations}");
        $this->command->info("   • Cálculos de usuarios: {$userCalculations}");
        $this->command->info("   • Cálculos anónimos: {$anonymousCalculations}");
        $this->command->info("   • Total CO2 calculado: " . round($totalCO2, 3) . " kg");
        $this->command->info("   • Promedio por cálculo: " . round($averageCO2, 3) . " kg CO2");
        
        $this->command->newLine();
        $this->command->info("🌍 Por nivel de impacto:");
        foreach ($impactStats as $level => $count) {
            $levelLabel = match($level) {
                'bajo' => 'Bajo (<1kg)',
                'medio' => 'Medio (1-5kg)',
                'alto' => 'Alto (5-10kg)',
                'muy_alto' => 'Muy alto (>10kg)',
                default => ucfirst($level)
            };
            $this->command->info("   • {$levelLabel}: {$count}");
        }
        
        $this->command->newLine();
        $this->command->info("🏷️ Por categoría:");
        foreach ($categoryStats as $category => $count) {
            $categoryLabel = match($category) {
                'energy' => 'Energía',
                'transport' => 'Transporte',
                'food' => 'Alimentación',
                'other' => 'Otros',
                default => ucfirst($category)
            };
            $this->command->info("   • {$categoryLabel}: {$count}");
        }
        
        // Recomendaciones de compensación
        $treesNeeded = ceil($totalCO2 / 22); // 1 árbol absorbe ~22kg CO2/año
        $this->command->newLine();
        $this->command->info("🌳 Recomendaciones de compensación:");
        $this->command->info("   • Árboles necesarios: {$treesNeeded}");
        $this->command->info("   • Costo estimado: " . ($treesNeeded * 2) . "€");
        $this->command->info("   • Equivalente en energía solar: " . round($totalCO2 / 0.04, 1) . " kWh");
        
        $this->command->newLine();
        $this->command->info("✅ Seeder de CarbonCalculation completado exitosamente.");
    }

    /**
     * Generar cantidad realista según la unidad
     */
    private function generateRealisticQuantity(string $unit): float
    {
        return match($unit) {
            'kWh' => rand(1, 1000) / 10, // 0.1 a 100 kWh
            'm³' => rand(1, 100) / 10, // 0.1 a 10 m³
            'km' => rand(1, 1000), // 1 a 1000 km
            'kg' => rand(1, 50) / 10, // 0.1 a 5 kg
            'litro' => rand(1, 20) / 10, // 0.1 a 2 litros
            default => rand(1, 100) / 10, // 0.1 a 10 unidades
        };
    }

    /**
     * Generar parámetros adicionales según la categoría
     */
    private function generateParameters(string $category, array $parametersByCategory): array
    {
        $parameters = [];
        
        if (isset($parametersByCategory[$category])) {
            foreach ($parametersByCategory[$category] as $key => $possibleValues) {
                $parameters[$key] = $possibleValues[array_rand($possibleValues)];
            }
        }
        
        // Parámetros comunes
        $parameters['calculation_timestamp'] = now()->toISOString();
        $parameters['data_quality'] = ['high', 'medium', 'low'][array_rand([0, 1, 2])];
        $parameters['uncertainty_factor'] = rand(5, 20) / 100; // 5% a 20%
        
        return $parameters;
    }
}
