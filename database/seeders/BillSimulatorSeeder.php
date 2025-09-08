<?php

namespace Database\Seeders;

use App\Models\BillSimulator;
use App\Models\User;
use Illuminate\Database\Seeder;

class BillSimulatorSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();

        if ($users->isEmpty()) {
            $this->command->info('No hay usuarios disponibles. Ejecuta UserSeeder primero.');
            return;
        }

        $simulations = [
            [
                'user_id' => $users->random()->id,
                'energy_type' => 'electricity',
                'zone' => 'peninsula',
                'monthly_consumption' => 350.00,
                'consumption_unit' => 'kWh',
                'contract_type' => 'fixed',
                'power_contracted' => 4.4,
                'tariff_details' => json_encode([
                    'tariff_name' => 'Tarifa 2.0TD',
                    'power_price' => 0.1234,
                    'energy_price' => 0.0892,
                    'fixed_charges' => 8.50
                ]),
                'estimated_monthly_bill' => 45.80,
                'estimated_annual_bill' => 549.60,
                'breakdown' => json_encode([
                    'energy_cost' => 31.22,
                    'power_cost' => 5.43,
                    'fixed_charges' => 8.50,
                    'taxes' => 0.65
                ]),
                'simulation_date' => now()->subDays(1),
                'assumptions' => json_encode([
                    'price_increase' => 0.03,
                    'consumption_variation' => 0.05,
                    'tariff_stability' => 'stable'
                ]),
            ],
            [
                'user_id' => $users->random()->id,
                'energy_type' => 'electricity',
                'zone' => 'peninsula',
                'monthly_consumption' => 450.00,
                'consumption_unit' => 'kWh',
                'contract_type' => 'variable',
                'power_contracted' => 5.5,
                'tariff_details' => json_encode([
                    'tariff_name' => 'Tarifa 2.0TD',
                    'power_price' => 0.1345,
                    'energy_price' => 0.0956,
                    'fixed_charges' => 10.20
                ]),
                'estimated_monthly_bill' => 58.90,
                'estimated_annual_bill' => 706.80,
                'breakdown' => json_encode([
                    'energy_cost' => 43.02,
                    'power_cost' => 7.40,
                    'fixed_charges' => 10.20,
                    'taxes' => 1.28
                ]),
                'simulation_date' => now()->subDays(2),
                'assumptions' => json_encode([
                    'price_increase' => 0.04,
                    'consumption_variation' => 0.08,
                    'tariff_stability' => 'variable'
                ]),
            ],
            [
                'user_id' => $users->random()->id,
                'energy_type' => 'gas',
                'zone' => 'peninsula',
                'monthly_consumption' => 120.00,
                'consumption_unit' => 'm³',
                'contract_type' => 'fixed',
                'power_contracted' => null,
                'tariff_details' => json_encode([
                    'tariff_name' => 'Tarifa 3.1',
                    'energy_price' => 0.0456,
                    'fixed_charges' => 12.50,
                    'distribution_cost' => 0.0234
                ]),
                'estimated_monthly_bill' => 18.75,
                'estimated_annual_bill' => 225.00,
                'breakdown' => json_encode([
                    'energy_cost' => 5.47,
                    'distribution_cost' => 2.81,
                    'fixed_charges' => 12.50,
                    'taxes' => 0.97
                ]),
                'simulation_date' => now()->subDays(3),
                'assumptions' => json_encode([
                    'price_increase' => 0.02,
                    'consumption_variation' => 0.03,
                    'tariff_stability' => 'stable'
                ]),
            ],
            [
                'user_id' => $users->random()->id,
                'energy_type' => 'electricity',
                'zone' => 'canary_islands',
                'monthly_consumption' => 280.00,
                'consumption_unit' => 'kWh',
                'contract_type' => 'fixed',
                'power_contracted' => 3.3,
                'tariff_details' => json_encode([
                    'tariff_name' => 'Tarifa 2.0TD',
                    'power_price' => 0.1456,
                    'energy_price' => 0.1023,
                    'fixed_charges' => 7.80
                ]),
                'estimated_monthly_bill' => 38.45,
                'estimated_annual_bill' => 461.40,
                'breakdown' => json_encode([
                    'energy_cost' => 28.64,
                    'power_cost' => 4.80,
                    'fixed_charges' => 7.80,
                    'taxes' => 0.81
                ]),
                'simulation_date' => now()->subDays(4),
                'assumptions' => json_encode([
                    'price_increase' => 0.025,
                    'consumption_variation' => 0.04,
                    'tariff_stability' => 'stable'
                ]),
            ],
            [
                'user_id' => $users->random()->id,
                'energy_type' => 'electricity',
                'zone' => 'peninsula',
                'monthly_consumption' => 600.00,
                'consumption_unit' => 'kWh',
                'contract_type' => 'variable',
                'power_contracted' => 6.6,
                'tariff_details' => json_encode([
                    'tariff_name' => 'Tarifa 2.0TD',
                    'power_price' => 0.1567,
                    'energy_price' => 0.1089,
                    'fixed_charges' => 12.80
                ]),
                'estimated_monthly_bill' => 78.20,
                'estimated_annual_bill' => 938.40,
                'breakdown' => json_encode([
                    'energy_cost' => 65.34,
                    'power_cost' => 10.35,
                    'fixed_charges' => 12.80,
                    'taxes' => 1.71
                ]),
                'simulation_date' => now()->subDays(5),
                'assumptions' => json_encode([
                    'price_increase' => 0.035,
                    'consumption_variation' => 0.06,
                    'tariff_stability' => 'variable'
                ]),
            ],
            [
                'user_id' => $users->random()->id,
                'energy_type' => 'gas',
                'zone' => 'peninsula',
                'monthly_consumption' => 200.00,
                'consumption_unit' => 'm³',
                'contract_type' => 'variable',
                'power_contracted' => null,
                'tariff_details' => json_encode([
                    'tariff_name' => 'Tarifa 3.1',
                    'energy_price' => 0.0523,
                    'fixed_charges' => 15.20,
                    'distribution_cost' => 0.0267
                ]),
                'estimated_monthly_bill' => 28.90,
                'estimated_annual_bill' => 346.80,
                'breakdown' => json_encode([
                    'energy_cost' => 10.46,
                    'distribution_cost' => 5.34,
                    'fixed_charges' => 15.20,
                    'taxes' => 1.90
                ]),
                'simulation_date' => now()->subDays(6),
                'assumptions' => json_encode([
                    'price_increase' => 0.03,
                    'consumption_variation' => 0.05,
                    'tariff_stability' => 'variable'
                ]),
            ],
            [
                'user_id' => $users->random()->id,
                'energy_type' => 'electricity',
                'zone' => 'balearic_islands',
                'monthly_consumption' => 320.00,
                'consumption_unit' => 'kWh',
                'contract_type' => 'fixed',
                'power_contracted' => 4.4,
                'tariff_details' => json_encode([
                    'tariff_name' => 'Tarifa 2.0TD',
                    'power_price' => 0.1678,
                    'energy_price' => 0.1156,
                    'fixed_charges' => 9.50
                ]),
                'estimated_monthly_bill' => 48.75,
                'estimated_annual_bill' => 585.00,
                'breakdown' => json_encode([
                    'energy_cost' => 36.99,
                    'power_cost' => 7.38,
                    'fixed_charges' => 9.50,
                    'taxes' => 1.88
                ]),
                'simulation_date' => now()->subDays(7),
                'assumptions' => json_encode([
                    'price_increase' => 0.04,
                    'consumption_variation' => 0.07,
                    'tariff_stability' => 'stable'
                ]),
            ],
            [
                'user_id' => $users->random()->id,
                'energy_type' => 'electricity',
                'zone' => 'peninsula',
                'monthly_consumption' => 150.00,
                'consumption_unit' => 'kWh',
                'contract_type' => 'fixed',
                'power_contracted' => 2.2,
                'tariff_details' => json_encode([
                    'tariff_name' => 'Tarifa 2.0TD',
                    'power_price' => 0.1123,
                    'energy_price' => 0.0789,
                    'fixed_charges' => 6.20
                ]),
                'estimated_monthly_bill' => 22.15,
                'estimated_annual_bill' => 265.80,
                'breakdown' => json_encode([
                    'energy_cost' => 11.84,
                    'power_cost' => 2.47,
                    'fixed_charges' => 6.20,
                    'taxes' => 0.64
                ]),
                'simulation_date' => now()->subDays(8),
                'assumptions' => json_encode([
                    'price_increase' => 0.02,
                    'consumption_variation' => 0.03,
                    'tariff_stability' => 'stable'
                ]),
            ],
            [
                'user_id' => $users->random()->id,
                'energy_type' => 'gas',
                'zone' => 'peninsula',
                'monthly_consumption' => 80.00,
                'consumption_unit' => 'm³',
                'contract_type' => 'fixed',
                'power_contracted' => null,
                'tariff_details' => json_encode([
                    'tariff_name' => 'Tarifa 3.1',
                    'energy_price' => 0.0434,
                    'fixed_charges' => 10.80,
                    'distribution_cost' => 0.0212
                ]),
                'estimated_monthly_bill' => 15.20,
                'estimated_annual_bill' => 182.40,
                'breakdown' => json_encode([
                    'energy_cost' => 3.47,
                    'distribution_cost' => 1.70,
                    'fixed_charges' => 10.80,
                    'taxes' => 0.23
                ]),
                'simulation_date' => now()->subDays(9),
                'assumptions' => json_encode([
                    'price_increase' => 0.015,
                    'consumption_variation' => 0.02,
                    'tariff_stability' => 'stable'
                ]),
            ],
            [
                'user_id' => $users->random()->id,
                'energy_type' => 'electricity',
                'zone' => 'peninsula',
                'monthly_consumption' => 500.00,
                'consumption_unit' => 'kWh',
                'contract_type' => 'variable',
                'power_contracted' => 6.6,
                'tariff_details' => json_encode([
                    'tariff_name' => 'Tarifa 2.0TD',
                    'power_price' => 0.1489,
                    'energy_price' => 0.1034,
                    'fixed_charges' => 11.50
                ]),
                'estimated_monthly_bill' => 65.80,
                'estimated_annual_bill' => 789.60,
                'breakdown' => json_encode([
                    'energy_cost' => 51.70,
                    'power_cost' => 9.83,
                    'fixed_charges' => 11.50,
                    'taxes' => 1.77
                ]),
                'simulation_date' => now()->subDays(10),
                'assumptions' => json_encode([
                    'price_increase' => 0.03,
                    'consumption_variation' => 0.05,
                    'tariff_stability' => 'variable'
                ]),
            ],
        ];

        foreach ($simulations as $simulation) {
            BillSimulator::create($simulation);
        }

        $this->command->info('✅ Creadas ' . count($simulations) . ' simulaciones de facturas');
    }
}
