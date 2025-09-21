<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CarbonCalculation;
use App\Models\CarbonEquivalence;
use App\Models\User;

class CarbonCalculationsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener equivalencias de carbono existentes
        $equivalences = CarbonEquivalence::all();
        
        if ($equivalences->isEmpty()) {
            $this->command->warn('No hay equivalencias de carbono disponibles. Ejecuta CarbonEquivalencesSeeder primero.');
            return;
        }

        // Obtener usuarios existentes
        $users = User::all();
        
        if ($users->isEmpty()) {
            $this->command->warn('No hay usuarios disponibles. Ejecuta UsersSeeder primero.');
            return;
        }

        $calculations = [
            // Cálculos de transporte
            [
                'user_id' => $users->random()->id,
                'carbon_equivalence_id' => $equivalences->where('name', 'like', '%coche%')->first()?->id ?? $equivalences->random()->id,
                'quantity' => 50.5,
                'co2_result' => 12.6,
                'context' => 'Viaje de trabajo Madrid-Barcelona',
                'parameters' => [
                    'distance_km' => 505,
                    'fuel_type' => 'gasolina',
                    'passengers' => 1,
                    'vehicle_type' => 'turismo'
                ],
                'session_id' => null,
            ],
            [
                'user_id' => $users->random()->id,
                'carbon_equivalence_id' => $equivalences->where('name', 'like', '%avión%')->first()?->id ?? $equivalences->random()->id,
                'quantity' => 1,
                'co2_result' => 285.0,
                'context' => 'Vuelo Madrid-Londres',
                'parameters' => [
                    'distance_km' => 1250,
                    'flight_type' => 'domestic',
                    'class' => 'economy',
                    'airline' => 'Iberia'
                ],
                'session_id' => null,
            ],
            [
                'user_id' => null,
                'carbon_equivalence_id' => $equivalences->where('name', 'like', '%metro%')->first()?->id ?? $equivalences->random()->id,
                'quantity' => 20,
                'co2_result' => 0.8,
                'context' => 'Desplazamiento urbano diario',
                'parameters' => [
                    'distance_km' => 20,
                    'transport_type' => 'public',
                    'frequency' => 'daily'
                ],
                'session_id' => 'session_' . uniqid(),
            ],

            // Cálculos de energía
            [
                'user_id' => $users->random()->id,
                'carbon_equivalence_id' => $equivalences->where('name', 'like', '%electricidad%')->first()?->id ?? $equivalences->random()->id,
                'quantity' => 350,
                'co2_result' => 96.25,
                'context' => 'Consumo eléctrico mensual hogar',
                'parameters' => [
                    'kwh_consumed' => 350,
                    'tariff_type' => 'domestic',
                    'region' => 'madrid',
                    'period' => 'monthly'
                ],
                'session_id' => null,
            ],
            [
                'user_id' => $users->random()->id,
                'carbon_equivalence_id' => $equivalences->where('name', 'like', '%gas%')->first()?->id ?? $equivalences->random()->id,
                'quantity' => 120,
                'co2_result' => 24.0,
                'context' => 'Consumo de gas natural para calefacción',
                'parameters' => [
                    'm3_consumed' => 120,
                    'usage_type' => 'heating',
                    'efficiency_rating' => 'A',
                    'period' => 'monthly'
                ],
                'session_id' => null,
            ],

            // Cálculos de alimentación
            [
                'user_id' => $users->random()->id,
                'carbon_equivalence_id' => $equivalences->where('name', 'like', '%carne%')->first()?->id ?? $equivalences->random()->id,
                'quantity' => 2.5,
                'co2_result' => 6.25,
                'context' => 'Consumo semanal de carne',
                'parameters' => [
                    'meat_type' => 'beef',
                    'weight_kg' => 2.5,
                    'origin' => 'spain',
                    'period' => 'weekly'
                ],
                'session_id' => null,
            ],
            [
                'user_id' => null,
                'carbon_equivalence_id' => $equivalences->where('name', 'like', '%vegetal%')->first()?->id ?? $equivalences->random()->id,
                'quantity' => 5.0,
                'co2_result' => 0.5,
                'context' => 'Consumo de verduras locales',
                'parameters' => [
                    'vegetable_type' => 'mixed',
                    'weight_kg' => 5.0,
                    'origin' => 'local',
                    'seasonal' => true
                ],
                'session_id' => 'session_' . uniqid(),
            ],

            // Cálculos de productos
            [
                'user_id' => $users->random()->id,
                'carbon_equivalence_id' => $equivalences->where('name', 'like', '%ropa%')->first()?->id ?? $equivalences->random()->id,
                'quantity' => 3,
                'co2_result' => 15.0,
                'context' => 'Compra de ropa nueva',
                'parameters' => [
                    'item_type' => 'clothing',
                    'quantity' => 3,
                    'material' => 'cotton',
                    'brand' => 'fast_fashion'
                ],
                'session_id' => null,
            ],
            [
                'user_id' => $users->random()->id,
                'carbon_equivalence_id' => $equivalences->where('name', 'like', '%plástico%')->first()?->id ?? $equivalences->random()->id,
                'quantity' => 0.5,
                'co2_result' => 1.25,
                'context' => 'Uso de productos plásticos',
                'parameters' => [
                    'plastic_type' => 'single_use',
                    'weight_kg' => 0.5,
                    'recyclable' => false,
                    'disposal_method' => 'landfill'
                ],
                'session_id' => null,
            ],

            // Cálculos de servicios digitales
            [
                'user_id' => $users->random()->id,
                'carbon_equivalence_id' => $equivalences->where('name', 'like', '%internet%')->first()?->id ?? $equivalences->random()->id,
                'quantity' => 100,
                'co2_result' => 0.5,
                'context' => 'Uso de internet y streaming',
                'parameters' => [
                    'data_gb' => 100,
                    'service_type' => 'streaming',
                    'device_type' => 'smartphone',
                    'period' => 'monthly'
                ],
                'session_id' => null,
            ],
            [
                'user_id' => null,
                'carbon_equivalence_id' => $equivalences->where('name', 'like', '%email%')->first()?->id ?? $equivalences->random()->id,
                'quantity' => 50,
                'co2_result' => 0.025,
                'context' => 'Envío de emails',
                'parameters' => [
                    'email_count' => 50,
                    'attachment_size_mb' => 5,
                    'recipients' => 1,
                    'period' => 'daily'
                ],
                'session_id' => 'session_' . uniqid(),
            ],

            // Cálculos de eventos y actividades
            [
                'user_id' => $users->random()->id,
                'carbon_equivalence_id' => $equivalences->where('name', 'like', '%evento%')->first()?->id ?? $equivalences->random()->id,
                'quantity' => 1,
                'co2_result' => 2.5,
                'context' => 'Asistencia a conferencia sobre sostenibilidad',
                'parameters' => [
                    'event_type' => 'conference',
                    'attendees' => 200,
                    'duration_hours' => 8,
                    'venue_type' => 'convention_center'
                ],
                'session_id' => null,
            ],
            [
                'user_id' => $users->random()->id,
                'carbon_equivalence_id' => $equivalences->where('name', 'like', '%deporte%')->first()?->id ?? $equivalences->random()->id,
                'quantity' => 1,
                'co2_result' => 0.1,
                'context' => 'Actividad deportiva al aire libre',
                'parameters' => [
                    'sport_type' => 'running',
                    'duration_minutes' => 60,
                    'location' => 'park',
                    'equipment_needed' => false
                ],
                'session_id' => null,
            ],

            // Cálculos de construcción y hogar
            [
                'user_id' => $users->random()->id,
                'carbon_equivalence_id' => $equivalences->where('name', 'like', '%cemento%')->first()?->id ?? $equivalences->random()->id,
                'quantity' => 100,
                'co2_result' => 50.0,
                'context' => 'Construcción de vivienda',
                'parameters' => [
                    'material_type' => 'cement',
                    'weight_kg' => 100,
                    'construction_type' => 'residential',
                    'certification' => 'LEED'
                ],
                'session_id' => null,
            ],
            [
                'user_id' => null,
                'carbon_equivalence_id' => $equivalences->where('name', 'like', '%madera%')->first()?->id ?? $equivalences->random()->id,
                'quantity' => 25,
                'co2_result' => 2.5,
                'context' => 'Uso de madera certificada',
                'parameters' => [
                    'wood_type' => 'certified',
                    'weight_kg' => 25,
                    'source' => 'sustainable',
                    'certification' => 'FSC'
                ],
                'session_id' => 'session_' . uniqid(),
            ],

            // Cálculos de agua y residuos
            [
                'user_id' => $users->random()->id,
                'carbon_equivalence_id' => $equivalences->where('name', 'like', '%agua%')->first()?->id ?? $equivalences->random()->id,
                'quantity' => 150,
                'co2_result' => 0.15,
                'context' => 'Consumo de agua potable',
                'parameters' => [
                    'water_liters' => 150,
                    'treatment_type' => 'potable',
                    'source' => 'municipal',
                    'period' => 'daily'
                ],
                'session_id' => null,
            ],
            [
                'user_id' => $users->random()->id,
                'carbon_equivalence_id' => $equivalences->where('name', 'like', '%residuo%')->first()?->id ?? $equivalences->random()->id,
                'quantity' => 1.2,
                'co2_result' => 0.6,
                'context' => 'Generación de residuos domésticos',
                'parameters' => [
                    'waste_kg' => 1.2,
                    'waste_type' => 'mixed',
                    'disposal_method' => 'landfill',
                    'recycling_rate' => 0.3
                ],
                'session_id' => null,
            ],
        ];

        foreach ($calculations as $calculation) {
            CarbonCalculation::create($calculation);
        }

        $this->command->info('Se han creado ' . count($calculations) . ' cálculos de carbono.');
    }
}

