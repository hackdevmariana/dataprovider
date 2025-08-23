<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\CarbonSavingLog;
use App\Models\User;
use App\Models\Cooperative;
use Carbon\Carbon;

class CarbonSavingLogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🌱 Sembrando logs de ahorro de carbono...');

        // Obtener usuarios y cooperativas disponibles
        $users = User::take(5)->get();
        $cooperatives = Cooperative::take(3)->get();

        if ($users->isEmpty()) {
            $this->command->error('❌ No se encontraron usuarios. Ejecuta primero el UserSeeder.');
            return;
        }

        if ($cooperatives->isEmpty()) {
            $this->command->error('❌ No se encontraron cooperativas. Ejecuta primero el CooperativeSeeder.');
            return;
        }

        // Datos de ejemplo para diferentes tipos de logs
        $sampleLogs = [
            // Logs de paneles solares residenciales
            [
                'user_id' => $users->random()->id,
                'cooperative_id' => null,
                'kw_installed' => 5.0,
                'production_kwh' => 4200,
                'co2_saved_kg' => 1155,
                'date_range_start' => '2025-01-01',
                'date_range_end' => '2025-12-31',
                'estimation_source' => 'sensor',
                'carbon_saving_method' => 'solar_panel',
                'created_by_system' => false,
                'metadata' => [
                    'panel_type' => 'Monocristalino',
                    'orientation' => 'Sur',
                    'inclination' => '30°',
                    'shading_factor' => 0.95,
                ],
            ],
            [
                'user_id' => $users->random()->id,
                'cooperative_id' => null,
                'kw_installed' => 3.5,
                'production_kwh' => 3150,
                'co2_saved_kg' => 866.25,
                'date_range_start' => '2025-01-01',
                'date_range_end' => '2025-12-31',
                'estimation_source' => 'calculation',
                'carbon_saving_method' => 'solar_panel',
                'created_by_system' => true,
                'metadata' => [
                    'panel_type' => 'Policristalino',
                    'orientation' => 'Suroeste',
                    'inclination' => '25°',
                    'shading_factor' => 0.90,
                ],
            ],

            // Logs de cooperativas solares
            [
                'user_id' => $users->random()->id,
                'cooperative_id' => $cooperatives->random()->id,
                'kw_installed' => 25.0,
                'production_kwh' => 22500,
                'co2_saved_kg' => 6187.5,
                'date_range_start' => '2025-01-01',
                'date_range_end' => '2025-12-31',
                'estimation_source' => 'sensor',
                'carbon_saving_method' => 'solar_panel',
                'created_by_system' => false,
                'metadata' => [
                    'installation_type' => 'Comercial',
                    'monitoring_system' => 'SMA Sunny Portal',
                    'maintenance_schedule' => 'Trimestral',
                ],
            ],
            [
                'user_id' => $users->random()->id,
                'cooperative_id' => $cooperatives->random()->id,
                'kw_installed' => 50.0,
                'production_kwh' => 45000,
                'co2_saved_kg' => 12375,
                'date_range_start' => '2025-01-01',
                'date_range_end' => '2025-12-31',
                'estimation_source' => 'api',
                'carbon_saving_method' => 'solar_panel',
                'created_by_system' => true,
                'metadata' => [
                    'installation_type' => 'Industrial',
                    'monitoring_system' => 'Fronius Solar.web',
                    'maintenance_schedule' => 'Mensual',
                ],
            ],

            // Logs de eficiencia energética
            [
                'user_id' => $users->random()->id,
                'cooperative_id' => null,
                'kw_installed' => 0,
                'production_kwh' => 0,
                'co2_saved_kg' => 150,
                'date_range_start' => '2025-01-01',
                'date_range_end' => '2025-12-31',
                'estimation_source' => 'manual',
                'carbon_saving_method' => 'energy_efficiency',
                'created_by_system' => false,
                'metadata' => [
                    'efficiency_measures' => ['LED Lighting', 'Smart Thermostat'],
                    'energy_savings_kwh' => 545,
                    'cost_savings_eur' => 87.20,
                ],
            ],
            [
                'user_id' => $users->random()->id,
                'cooperative_id' => null,
                'kw_installed' => 0,
                'production_kwh' => 0,
                'co2_saved_kg' => 75,
                'date_range_start' => '2025-01-01',
                'date_range_end' => '2025-12-31',
                'estimation_source' => 'calculation',
                'carbon_saving_method' => 'insulation',
                'created_by_system' => true,
                'metadata' => [
                    'insulation_type' => 'Fachada ventilada',
                    'r_value' => 3.5,
                    'energy_savings_kwh' => 273,
                    'cost_savings_eur' => 43.68,
                ],
            ],

            // Logs de transporte sostenible
            [
                'user_id' => $users->random()->id,
                'cooperative_id' => null,
                'kw_installed' => 0,
                'production_kwh' => 0,
                'co2_saved_kg' => 200,
                'date_range_start' => '2025-01-01',
                'date_range_end' => '2025-12-31',
                'estimation_source' => 'manual',
                'carbon_saving_method' => 'public_transport',
                'created_by_system' => false,
                'metadata' => [
                    'transport_mode' => 'Metro y autobús',
                    'km_saved' => 2500,
                    'fuel_savings_l' => 150,
                    'cost_savings_eur' => 225,
                ],
            ],
            [
                'user_id' => $users->random()->id,
                'cooperative_id' => null,
                'kw_installed' => 0,
                'production_kwh' => 0,
                'co2_saved_kg' => 300,
                'date_range_start' => '2025-01-01',
                'date_range_end' => '2025-12-31',
                'estimation_source' => 'calculation',
                'carbon_saving_method' => 'electric_vehicle',
                'created_by_system' => true,
                'metadata' => [
                    'vehicle_type' => 'Coche eléctrico',
                    'km_electric' => 8000,
                    'km_ice_equivalent' => 8000,
                    'energy_consumption_kwh' => 1200,
                ],
            ],

            // Logs de plantación de árboles
            [
                'user_id' => $users->random()->id,
                'cooperative_id' => $cooperatives->random()->id,
                'kw_installed' => 0,
                'production_kwh' => 0,
                'co2_saved_kg' => 500,
                'date_range_start' => '2025-01-01',
                'date_range_end' => '2025-12-31',
                'estimation_source' => 'estimate',
                'carbon_saving_method' => 'tree_planting',
                'created_by_system' => false,
                'metadata' => [
                    'trees_planted' => 25,
                    'tree_species' => ['Pino', 'Encina', 'Alcornoque'],
                    'area_hectares' => 0.5,
                    'expected_co2_kg_per_year' => 500,
                ],
            ],

            // Logs de reciclaje
            [
                'user_id' => $users->random()->id,
                'cooperative_id' => null,
                'kw_installed' => 0,
                'production_kwh' => 0,
                'co2_saved_kg' => 50,
                'date_range_start' => '2025-01-01',
                'date_range_end' => '2025-12-31',
                'estimation_source' => 'manual',
                'carbon_saving_method' => 'recycling',
                'created_by_system' => false,
                'metadata' => [
                    'materials_recycled' => ['Papel', 'Plástico', 'Vidrio', 'Metal'],
                    'weight_kg' => 120,
                    'recycling_frequency' => 'Semanal',
                    'local_facility' => 'Punto limpio municipal',
                ],
            ],

            // Logs de turbinas eólicas
            [
                'user_id' => $users->random()->id,
                'cooperative_id' => $cooperatives->random()->id,
                'kw_installed' => 100.0,
                'production_kwh' => 180000,
                'co2_saved_kg' => 49500,
                'date_range_start' => '2025-01-01',
                'date_range_end' => '2025-12-31',
                'estimation_source' => 'sensor',
                'carbon_saving_method' => 'wind_turbine',
                'created_by_system' => false,
                'metadata' => [
                    'turbine_model' => 'Vestas V100',
                    'hub_height' => 80,
                    'rotor_diameter' => 100,
                    'wind_class' => 'IIA',
                    'capacity_factor' => 0.205,
                ],
            ],

            // Logs de iluminación LED
            [
                'user_id' => $users->random()->id,
                'cooperative_id' => null,
                'kw_installed' => 0,
                'production_kwh' => 0,
                'co2_saved_kg' => 100,
                'date_range_start' => '2025-01-01',
                'date_range_end' => '2025-12-31',
                'estimation_source' => 'calculation',
                'carbon_saving_method' => 'led_lighting',
                'created_by_system' => true,
                'metadata' => [
                    'bulbs_replaced' => 20,
                    'old_wattage' => 60,
                    'new_wattage' => 9,
                    'daily_hours' => 4,
                    'energy_savings_kwh' => 364,
                ],
            ],

            // Logs de termostatos inteligentes
            [
                'user_id' => $users->random()->id,
                'cooperative_id' => null,
                'kw_installed' => 0,
                'production_kwh' => 0,
                'co2_saved_kg' => 125,
                'date_range_start' => '2025-01-01',
                'date_range_end' => '2025-12-31',
                'estimation_source' => 'sensor',
                'carbon_saving_method' => 'smart_thermostat',
                'created_by_system' => false,
                'metadata' => [
                    'thermostat_model' => 'Nest Learning Thermostat',
                    'temperature_reduction' => 2.5,
                    'heating_hours_saved' => 450,
                    'energy_savings_kwh' => 455,
                    'cost_savings_eur' => 72.80,
                ],
            ],

            // Logs de períodos diferentes
            [
                'user_id' => $users->random()->id,
                'cooperative_id' => null,
                'kw_installed' => 10.0,
                'production_kwh' => 900,
                'co2_saved_kg' => 247.5,
                'date_range_start' => '2025-01-01',
                'date_range_end' => '2025-01-31',
                'estimation_source' => 'sensor',
                'carbon_saving_method' => 'solar_panel',
                'created_by_system' => false,
                'metadata' => [
                    'monthly_performance' => 'Enero 2025',
                    'snow_clearing_days' => 3,
                    'cleaning_frequency' => 'Semanal',
                ],
            ],
            [
                'user_id' => $users->random()->id,
                'cooperative_id' => null,
                'kw_installed' => 2.5,
                'production_kwh' => 60,
                'co2_saved_kg' => 16.5,
                'date_range_start' => '2025-01-15',
                'date_range_end' => '2025-01-15',
                'estimation_source' => 'sensor',
                'carbon_saving_method' => 'solar_panel',
                'created_by_system' => false,
                'metadata' => [
                    'daily_performance' => '15 de Enero 2025',
                    'weather_conditions' => 'Soleado',
                    'peak_hours' => 6,
                ],
            ],
        ];

        $createdCount = 0;
        $updatedCount = 0;

        foreach ($sampleLogs as $logData) {
            // Crear o actualizar el log
            $log = CarbonSavingLog::updateOrCreate(
                [
                    'user_id' => $logData['user_id'],
                    'date_range_start' => $logData['date_range_start'],
                    'carbon_saving_method' => $logData['carbon_saving_method'],
                ],
                $logData
            );

            if ($log->wasRecentlyCreated) {
                $createdCount++;
            } else {
                $updatedCount++;
            }
        }

        // Mostrar estadísticas
        $this->command->info("✅ Logs creados: {$createdCount}");
        $this->command->info("🔄 Logs actualizados: {$updatedCount}");
        $this->command->info("📊 Total de logs: " . CarbonSavingLog::count());

        // Mostrar algunos ejemplos de cálculos
        $this->command->info("\n🔬 Ejemplos de logs creados:");
        $sampleLog = CarbonSavingLog::first();
        if ($sampleLog) {
            $this->command->info("📋 Log ID: {$sampleLog->id}");
            $this->command->info("👤 Usuario: " . ($sampleLog->user?->name ?? 'N/A'));
            $this->command->info("⚡ Potencia: {$sampleLog->getFormattedPower()}");
            $this->command->info("🏭 Producción: {$sampleLog->getFormattedProduction()}");
            $this->command->info("🌱 CO2 Ahorrado: {$sampleLog->getFormattedCarbonSavings()}");
            $this->command->info("📅 Período: {$sampleLog->getDateRangeLabel()}");
            $this->command->info("🔧 Método: {$sampleLog->getCarbonSavingMethodLabel()}");
            $this->command->info("📊 Fuente: {$sampleLog->getEstimationSourceLabel()}");
            $this->command->info("📍 Regional: {$sampleLog->getRegionalInfo()}");
        }

        $this->command->info("\n🎯 Seeder de CarbonSavingLog completado exitosamente!");
    }
}
