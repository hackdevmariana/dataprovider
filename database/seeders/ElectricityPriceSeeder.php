<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ElectricityPrice;
use App\Models\PriceUnit;
use Carbon\Carbon;

class ElectricityPriceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Buscar la unidad de precio EUR/MWh
        $priceUnit = PriceUnit::where('unit_code', 'EUR_MWH')->first();
        
        if (!$priceUnit) {
            $this->command->error('PriceUnit EUR_MWH not found. Please run PriceUnitSeeder first.');
            return;
        }

        // Generar precios para hoy y mañana (ejemplo realista basado en datos españoles)
        $dates = [
            Carbon::today()->format('Y-m-d'),
            Carbon::tomorrow()->format('Y-m-d'),
        ];

        foreach ($dates as $date) {
            // Precios PVPC típicos españoles (simulados pero realistas)
            $pvpcPrices = [
                0 => 45.50,   // 00:00 - Valle
                1 => 42.30,   // 01:00 - Valle
                2 => 40.80,   // 02:00 - Valle
                3 => 39.50,   // 03:00 - Valle (más barato)
                4 => 41.20,   // 04:00 - Valle
                5 => 45.60,   // 05:00 - Valle
                6 => 52.30,   // 06:00 - Subida matutina
                7 => 68.90,   // 07:00 - Pico matutino
                8 => 89.40,   // 08:00 - Pico matutino
                9 => 95.60,   // 09:00 - Pico matutino
                10 => 78.50,  // 10:00 - Punta
                11 => 72.30,  // 11:00 - Punta
                12 => 68.70,  // 12:00 - Llano
                13 => 65.80,  // 13:00 - Llano
                14 => 70.20,  // 14:00 - Llano
                15 => 75.40,  // 15:00 - Llano
                16 => 82.10,  // 16:00 - Subida
                17 => 95.30,  // 17:00 - Pico vespertino
                18 => 125.60, // 18:00 - Pico máximo
                19 => 135.80, // 19:00 - Pico máximo
                20 => 128.40, // 20:00 - Pico vespertino
                21 => 98.70,  // 21:00 - Bajada
                22 => 72.50,  // 22:00 - Valle
                23 => 58.90,  // 23:00 - Valle
            ];

            // Precios spot (mercado mayorista) - ligeramente diferentes
            $spotPrices = array_map(function($price) {
                return $price * 0.85 + rand(-5, 10); // Variación del -15% + ruido
            }, $pvpcPrices);

            // Crear precios PVPC
            foreach ($pvpcPrices as $hour => $price) {
                ElectricityPrice::firstOrCreate([
                    'date' => $date,
                    'hour' => $hour,
                    'type' => 'pvpc',
                ], [
                    'price_eur_mwh' => $price,
                    'source' => 'REE',
                    'price_unit_id' => $priceUnit->id,
                    'forecast_for_tomorrow' => $date === Carbon::tomorrow()->format('Y-m-d'),
                ]);
            }

            // Crear precios spot
            foreach ($spotPrices as $hour => $price) {
                ElectricityPrice::firstOrCreate([
                    'date' => $date,
                    'hour' => $hour,
                    'type' => 'spot',
                ], [
                    'price_eur_mwh' => max(0, $price), // No permitir precios negativos para el ejemplo
                    'source' => 'OMIE',
                    'price_unit_id' => $priceUnit->id,
                    'forecast_for_tomorrow' => $date === Carbon::tomorrow()->format('Y-m-d'),
                ]);
            }

            // Crear resumen diario para cada tipo
            foreach (['pvpc', 'spot'] as $type) {
                $prices = $type === 'pvpc' ? $pvpcPrices : $spotPrices;
                
                ElectricityPrice::firstOrCreate([
                    'date' => $date,
                    'hour' => null, // Resumen diario
                    'type' => $type,
                ], [
                    'price_eur_mwh' => array_sum($prices) / count($prices), // Promedio
                    'price_min' => min($prices),
                    'price_max' => max($prices),
                    'price_avg' => array_sum($prices) / count($prices),
                    'source' => $type === 'pvpc' ? 'REE' : 'OMIE',
                    'price_unit_id' => $priceUnit->id,
                    'forecast_for_tomorrow' => $date === Carbon::tomorrow()->format('Y-m-d'),
                ]);
            }
        }

        // Crear algunos datos históricos (última semana)
        for ($i = 1; $i <= 7; $i++) {
            $historicalDate = Carbon::today()->subDays($i)->format('Y-m-d');
            
            // Solo crear resúmenes diarios para datos históricos
            foreach (['pvpc', 'spot'] as $type) {
                $basePrice = 75 + rand(-20, 30); // Precio base con variación
                
                ElectricityPrice::firstOrCreate([
                    'date' => $historicalDate,
                    'hour' => null,
                    'type' => $type,
                ], [
                    'price_eur_mwh' => $basePrice,
                    'price_min' => $basePrice * 0.6,
                    'price_max' => $basePrice * 1.8,
                    'price_avg' => $basePrice,
                    'source' => $type === 'pvpc' ? 'REE' : 'OMIE',
                    'price_unit_id' => $priceUnit->id,
                    'forecast_for_tomorrow' => false,
                ]);
            }
        }

        $this->command->info('ElectricityPrice seeder completed: ' . ElectricityPrice::count() . ' price records created/updated.');
        $this->command->info('Data includes:');
        $this->command->info('- Hourly prices for today and tomorrow (PVPC and Spot)');
        $this->command->info('- Daily summaries for the last 7 days');
        $this->command->info('- Realistic Spanish electricity market prices');
    }
}