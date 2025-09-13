<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CarbonCalculation;
use App\Models\User;
use App\Models\CarbonEquivalence;
use Illuminate\Support\Str;

class CarbonCalculationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🌱 Sembrando cálculos de huella de carbono...');

        // Verificar que existan usuarios y equivalencias
        $users = User::take(20)->get();
        $equivalences = CarbonEquivalence::take(30)->get();

        if ($users->isEmpty()) {
            $this->command->error('❌ No hay usuarios disponibles. Ejecuta UserSeeder primero.');
            return;
        }

        if ($equivalences->isEmpty()) {
            $this->command->error('❌ No hay equivalencias de carbono disponibles. Ejecuta CarbonEquivalenceSeeder primero.');
            return;
        }

        $this->command->info("👥 Usuarios disponibles: {$users->count()}");
        $this->command->info("⚖️ Equivalencias disponibles: {$equivalences->count()}");

        $createdCount = 0;
        $contexts = [
            'personal_transport',
            'home_energy',
            'food_consumption',
            'shopping',
            'travel',
            'work_commute',
            'household_appliances',
            'heating_cooling',
            'water_usage',
            'waste_management',
        ];

        $parameters = [
            'distance_km' => [1, 100],
            'duration_hours' => [0.5, 8],
            'quantity_kg' => [0.1, 50],
            'area_m2' => [10, 200],
            'occupants' => [1, 6],
            'frequency_per_month' => [1, 30],
        ];

        // Crear cálculos para usuarios registrados
        foreach ($users as $user) {
            $calculationsCount = rand(3, 8);
            
            for ($i = 0; $i < $calculationsCount; $i++) {
                $equivalence = $equivalences->random();
                $context = $contexts[array_rand($contexts)];
                $quantity = $this->generateRealisticQuantity($equivalence, $context);
                
                // Calcular CO2 usando el método del modelo
                $co2Result = $equivalence->calculateCO2($quantity);
                
                $calculationData = [
                    'user_id' => $user->id,
                    'carbon_equivalence_id' => $equivalence->id,
                    'quantity' => $quantity,
                    'co2_result' => $co2Result,
                    'context' => $context,
                    'parameters' => $this->generateParameters($context, $parameters),
                ];

                CarbonCalculation::create($calculationData);
                $createdCount++;
            }
        }

        // Crear cálculos anónimos (con session_id)
        for ($i = 0; $i < 50; $i++) {
            $equivalence = $equivalences->random();
            $context = $contexts[array_rand($contexts)];
            $quantity = $this->generateRealisticQuantity($equivalence, $context);
            $co2Result = $equivalence->calculateCO2($quantity);
            
            $calculationData = [
                'user_id' => null,
                'carbon_equivalence_id' => $equivalence->id,
                'quantity' => $quantity,
                'co2_result' => $co2Result,
                'context' => $context,
                'parameters' => $this->generateParameters($context, $parameters),
                'session_id' => 'session_' . Str::random(10),
            ];

            CarbonCalculation::create($calculationData);
            $createdCount++;
        }

        $this->command->info("✅ Cálculos creados: {$createdCount}");
        $this->command->info("📊 Total de cálculos: " . CarbonCalculation::count());

        // Mostrar estadísticas
        $this->showStatistics();
    }

    /**
     * Generar cantidad realista según la equivalencia y contexto.
     */
    private function generateRealisticQuantity(CarbonEquivalence $equivalence, string $context): float
    {
        $category = $equivalence->category;
        $unit = $equivalence->unit;

        return match($category) {
            'energy' => match($unit) {
                'kWh' => rand(50, 2000),
                'm³' => rand(1, 50),
                'kg' => rand(1, 100),
                default => rand(1, 100),
            },
            'transport' => match($unit) {
                'km' => rand(1, 500),
                default => rand(1, 100),
            },
            'food' => match($unit) {
                'kg' => rand(0.1, 10),
                'litro' => rand(0.5, 20),
                default => rand(0.1, 5),
            },
            'construction' => match($unit) {
                'kg' => rand(10, 1000),
                default => rand(1, 100),
            },
            'waste' => match($unit) {
                'kg' => rand(1, 50),
                default => rand(1, 20),
            },
            'agriculture' => match($unit) {
                'kg' => rand(1, 100),
                'm³' => rand(1, 20),
                default => rand(1, 50),
            },
            default => rand(1, 100),
        };
    }

    /**
     * Generar parámetros adicionales según el contexto.
     */
    private function generateParameters(string $context, array $parameterRanges): array
    {
        $parameters = [];

        switch ($context) {
            case 'personal_transport':
                $parameters['distance_km'] = rand($parameterRanges['distance_km'][0], $parameterRanges['distance_km'][1]);
                $parameters['vehicle_type'] = ['coche', 'moto', 'autobús', 'tren'][array_rand(['coche', 'moto', 'autobús', 'tren'])];
                break;

            case 'home_energy':
                $parameters['area_m2'] = rand($parameterRanges['area_m2'][0], $parameterRanges['area_m2'][1]);
                $parameters['occupants'] = rand($parameterRanges['occupants'][0], $parameterRanges['occupants'][1]);
                $parameters['heating_type'] = ['gas', 'electricidad', 'gasóleo'][array_rand(['gas', 'electricidad', 'gasóleo'])];
                break;

            case 'food_consumption':
                $parameters['quantity_kg'] = rand($parameterRanges['quantity_kg'][0], $parameterRanges['quantity_kg'][1]);
                $parameters['origin'] = ['local', 'nacional', 'internacional'][array_rand(['local', 'nacional', 'internacional'])];
                break;

            case 'travel':
                $parameters['distance_km'] = rand($parameterRanges['distance_km'][0], $parameterRanges['distance_km'][1]);
                $parameters['transport_mode'] = ['avión', 'tren', 'coche', 'autobús'][array_rand(['avión', 'tren', 'coche', 'autobús'])];
                break;

            case 'work_commute':
                $parameters['distance_km'] = rand(1, 50);
                $parameters['frequency_per_month'] = rand($parameterRanges['frequency_per_month'][0], $parameterRanges['frequency_per_month'][1]);
                $parameters['transport_mode'] = ['coche', 'moto', 'autobús', 'bicicleta'][array_rand(['coche', 'moto', 'autobús', 'bicicleta'])];
                break;

            default:
                $parameters['quantity'] = rand(1, 10);
                $parameters['frequency'] = rand(1, 30);
                break;
        }

        return $parameters;
    }

    /**
     * Mostrar estadísticas de los cálculos creados.
     */
    private function showStatistics(): void
    {
        $this->command->info("\n📊 Estadísticas de cálculos de carbono:");
        
        $totalCalculations = CarbonCalculation::count();
        $userCalculations = CarbonCalculation::whereNotNull('user_id')->count();
        $anonymousCalculations = CarbonCalculation::whereNull('user_id')->count();
        
        $this->command->info("   Total cálculos: {$totalCalculations}");
        $this->command->info("   Cálculos de usuarios: {$userCalculations}");
        $this->command->info("   Cálculos anónimos: {$anonymousCalculations}");
        
        // Estadísticas por contexto
        $contextStats = CarbonCalculation::selectRaw('context, COUNT(*) as count')
            ->groupBy('context')
            ->orderBy('count', 'desc')
            ->get();
            
        $this->command->info("\n🎯 Cálculos por contexto:");
        foreach ($contextStats as $stat) {
            $this->command->info("   {$stat->context}: {$stat->count} cálculos");
        }
        
        // Estadísticas por nivel de impacto
        $impactStats = CarbonCalculation::get()->groupBy('impact_level');
        $this->command->info("\n🌱 Cálculos por nivel de impacto:");
        foreach ($impactStats as $level => $calculations) {
            $this->command->info("   {$level}: {$calculations->count()} cálculos");
        }
        
        // Ejemplos de cálculos
        $this->command->info("\n🔬 Ejemplos de cálculos creados:");
        $sampleCalculations = CarbonCalculation::with(['user', 'carbonEquivalence'])->take(3)->get();
        
        foreach ($sampleCalculations as $calculation) {
            $userName = $calculation->user ? $calculation->user->name : 'Usuario anónimo';
            $equivalenceName = $calculation->carbonEquivalence->name;
            
            $this->command->info("   👤 {$userName}");
            $this->command->info("      📊 {$equivalenceName}");
            $this->command->info("      📏 Cantidad: {$calculation->quantity} {$calculation->carbonEquivalence->unit}");
            $this->command->info("      🌱 CO2: {$calculation->co2_result} kg");
            $this->command->info("      🎯 Contexto: {$calculation->context}");
            $this->command->info("      📈 Impacto: {$calculation->impact_level}");
            $this->command->info("      ---");
        }
        
        $this->command->info("\n🎯 Seeder de CarbonCalculation completado exitosamente!");
    }
}