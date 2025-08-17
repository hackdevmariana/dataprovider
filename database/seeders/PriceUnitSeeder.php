<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\PriceUnit;

class PriceUnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $priceUnits = [
            // Unidades energéticas principales
            [
                'name' => 'Euro por megavatio hora',
                'short_name' => '€/MWh',
                'unit_code' => 'EUR_MWH',
                'conversion_factor_to_kwh' => 0.001, // 1 MWh = 1000 kWh
            ],
            [
                'name' => 'Euro por kilovatio hora',
                'short_name' => '€/kWh',
                'unit_code' => 'EUR_KWH',
                'conversion_factor_to_kwh' => 1.0, // Base unit
            ],
            [
                'name' => 'Céntimos de euro por kilovatio hora',
                'short_name' => 'c€/kWh',
                'unit_code' => 'CENT_EUR_KWH',
                'conversion_factor_to_kwh' => 100.0, // 1 €/kWh = 100 c€/kWh
            ],
            [
                'name' => 'Euro por watio hora',
                'short_name' => '€/Wh',
                'unit_code' => 'EUR_WH',
                'conversion_factor_to_kwh' => 1000.0, // 1 kWh = 1000 Wh
            ],
            
            // Unidades internacionales
            [
                'name' => 'Dólar estadounidense por megavatio hora',
                'short_name' => '$/MWh',
                'unit_code' => 'USD_MWH',
                'conversion_factor_to_kwh' => 0.001,
            ],
            [
                'name' => 'Dólar estadounidense por kilovatio hora',
                'short_name' => '$/kWh',
                'unit_code' => 'USD_KWH',
                'conversion_factor_to_kwh' => 1.0,
            ],
            [
                'name' => 'Libra esterlina por megavatio hora',
                'short_name' => '£/MWh',
                'unit_code' => 'GBP_MWH',
                'conversion_factor_to_kwh' => 0.001,
            ],
            [
                'name' => 'Libra esterlina por kilovatio hora',
                'short_name' => '£/kWh',
                'unit_code' => 'GBP_KWH',
                'conversion_factor_to_kwh' => 1.0,
            ],
            
            // Unidades especiales para trading energético
            [
                'name' => 'Euro por teravatio hora',
                'short_name' => '€/TWh',
                'unit_code' => 'EUR_TWH',
                'conversion_factor_to_kwh' => 0.000001, // 1 TWh = 1,000,000 MWh
            ],
            [
                'name' => 'Euro por gigavatio hora',
                'short_name' => '€/GWh',
                'unit_code' => 'EUR_GWH',
                'conversion_factor_to_kwh' => 0.000001, // 1 GWh = 1,000 MWh
            ],
            
            // Para combustibles (referencia)
            [
                'name' => 'Euro por metro cúbico de gas',
                'short_name' => '€/m³',
                'unit_code' => 'EUR_M3_GAS',
                'conversion_factor_to_kwh' => null, // Conversión compleja, depende del poder calorífico
            ],
            [
                'name' => 'Euro por litro de combustible',
                'short_name' => '€/L',
                'unit_code' => 'EUR_L_FUEL',
                'conversion_factor_to_kwh' => null,
            ],
            
            // Para metales preciosos (energía vs metales)
            [
                'name' => 'Euro por gramo de oro',
                'short_name' => '€/g Au',
                'unit_code' => 'EUR_G_GOLD',
                'conversion_factor_to_kwh' => null,
            ],
            [
                'name' => 'Euro por gramo de plata',
                'short_name' => '€/g Ag',
                'unit_code' => 'EUR_G_SILVER',
                'conversion_factor_to_kwh' => null,
            ],
        ];

        foreach ($priceUnits as $priceUnit) {
            PriceUnit::firstOrCreate(
                ['unit_code' => $priceUnit['unit_code']],
                $priceUnit
            );
        }

        $this->command->info('PriceUnit seeder completed: ' . count($priceUnits) . ' price units created/updated.');
    }
}