<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ElectricityPriceInterval;
use App\Models\ElectricityPrice;
use Carbon\Carbon;

class ElectricityPriceIntervalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🌱 Sembrando intervalos de precios de electricidad...');

        // Obtener precios de electricidad disponibles
        $electricityPrices = ElectricityPrice::take(20)->get();

        if ($electricityPrices->isEmpty()) {
            $this->command->error('❌ No se encontraron precios de electricidad. Ejecuta primero el ElectricityPriceSeeder.');
            return;
        }

        $this->command->info("📊 Encontrados {$electricityPrices->count()} precios de electricidad para crear intervalos.");

        $createdCount = 0;
        $updatedCount = 0;

        foreach ($electricityPrices as $electricityPrice) {
            // Crear intervalos de 15 minutos para cada precio
            $intervals = $this->generateIntervalsForPrice($electricityPrice);
            
            foreach ($intervals as $intervalData) {
                // Crear o actualizar el intervalo
                $interval = ElectricityPriceInterval::updateOrCreate(
                    [
                        'electricity_price_id' => $electricityPrice->id,
                        'interval_index' => $intervalData['interval_index'],
                    ],
                    $intervalData
                );

                if ($interval->wasRecentlyCreated) {
                    $createdCount++;
                } else {
                    $updatedCount++;
                }
            }
        }

        // Mostrar estadísticas
        $this->command->info("✅ Intervalos creados: {$createdCount}");
        $this->command->info("🔄 Intervalos actualizados: {$updatedCount}");
        $this->command->info("📊 Total de intervalos: " . ElectricityPriceInterval::count());

        // Mostrar algunos ejemplos de cálculos
        $this->command->info("\n🔬 Ejemplos de intervalos creados:");
        $sampleInterval = ElectricityPriceInterval::with('electricityPrice')->first();
        if ($sampleInterval) {
            $this->command->info("📋 Intervalo ID: {$sampleInterval->id}");
            $this->command->info("⏰ Hora: {$sampleInterval->start_time} - {$sampleInterval->end_time}");
            $this->command->info("💰 Precio: {$sampleInterval->price_eur_mwh} €/MWh");
            $this->command->info("📅 Fecha: " . ($sampleInterval->electricityPrice?->date ?? 'N/A'));
            $this->command->info("🕐 Hora del día: " . ($sampleInterval->electricityPrice?->hour ?? 'N/A'));
        }

        $this->command->info("\n🎯 Seeder de ElectricityPriceInterval completado exitosamente!");
    }

    /**
     * Genera intervalos de 15 minutos para un precio de electricidad
     */
    private function generateIntervalsForPrice(ElectricityPrice $electricityPrice): array
    {
        $intervals = [];
        $basePrice = $electricityPrice->price_eur_mwh;
        $hour = $electricityPrice->hour;
        
        // Generar 4 intervalos de 15 minutos por hora
        for ($i = 0; $i < 4; $i++) {
            $intervalIndex = ($hour * 4) + $i;
            $startMinute = $i * 15;
            $endMinute = ($i + 1) * 15;
            
            // Calcular precio del intervalo con variación realista
            $priceVariation = $this->calculatePriceVariation($hour, $i, $basePrice);
            $intervalPrice = $basePrice + $priceVariation;
            
            // Calcular tiempo de inicio y fin
            $startTime = sprintf('%02d:%02d:00', $hour, $startMinute);
            
            // Para el último intervalo de la hora, el fin es la siguiente hora
            if ($endMinute == 60) {
                $endTime = sprintf('%02d:00:00', ($hour + 1) % 24);
            } else {
                $endTime = sprintf('%02d:%02d:00', $hour, $endMinute);
            }
            
            $intervals[] = [
                'electricity_price_id' => $electricityPrice->id,
                'interval_index' => $intervalIndex,
                'start_time' => $startTime,
                'end_time' => $endTime,
                'price_eur_mwh' => round($intervalPrice, 4),
            ];
        }
        
        return $intervals;
    }

    /**
     * Calcula la variación de precio para un intervalo específico
     */
    private function calculatePriceVariation(int $hour, int $intervalIndex, float $basePrice): float
    {
        // Factores de variación basados en la hora del día
        $hourFactor = $this->getHourFactor($hour);
        $intervalFactor = $this->getIntervalFactor($intervalIndex);
        
        // Variación base del 5% al 15%
        $baseVariation = $basePrice * 0.10;
        
        // Aplicar factores
        $variation = $baseVariation * $hourFactor * $intervalFactor;
        
        // Agregar ruido aleatorio pequeño (±2%)
        $noise = $basePrice * (rand(-20, 20) / 1000);
        
        return $variation + $noise;
    }

    /**
     * Factor de variación basado en la hora del día
     */
    private function getHourFactor(int $hour): float
    {
        // Horas pico (mañana y tarde)
        if (in_array($hour, [8, 9, 10, 18, 19, 20, 21])) {
            return 1.2; // 20% más de variación
        }
        
        // Horas valle (madrugada)
        if (in_array($hour, [0, 1, 2, 3, 4, 5, 6])) {
            return 0.8; // 20% menos de variación
        }
        
        // Horas normales
        return 1.0;
    }

    /**
     * Factor de variación basado en el intervalo dentro de la hora
     */
    private function getIntervalFactor(int $intervalIndex): float
    {
        // Primer intervalo de la hora (más estable)
        if ($intervalIndex % 4 === 0) {
            return 0.9;
        }
        
        // Último intervalo de la hora (más variable)
        if ($intervalIndex % 4 === 3) {
            return 1.1;
        }
        
        // Intervalos intermedios
        return 1.0;
    }

    /**
     * Genera intervalos de ejemplo para diferentes escenarios
     */
    private function generateExampleIntervals(): array
    {
        return [
            // Intervalos de hora pico (mañana)
            [
                'electricity_price_id' => 1,
                'interval_index' => 32, // 8:00
                'start_time' => '08:00:00',
                'end_time' => '08:15:00',
                'price_eur_mwh' => 85.50,
            ],
            [
                'electricity_price_id' => 1,
                'interval_index' => 33, // 8:15
                'start_time' => '08:15:00',
                'end_time' => '08:30:00',
                'price_eur_mwh' => 87.25,
            ],
            [
                'electricity_price_id' => 1,
                'interval_index' => 34, // 8:30
                'start_time' => '08:30:00',
                'end_time' => '08:45:00',
                'price_eur_mwh' => 89.75,
            ],
            [
                'electricity_price_id' => 1,
                'interval_index' => 35, // 8:45
                'start_time' => '08:45:00',
                'end_time' => '09:00:00',
                'price_eur_mwh' => 91.00,
            ],

            // Intervalos de hora valle (madrugada)
            [
                'electricity_price_id' => 2,
                'interval_index' => 0, // 0:00
                'start_time' => '00:00:00',
                'end_time' => '00:15:00',
                'price_eur_mwh' => 45.25,
            ],
            [
                'electricity_price_id' => 2,
                'interval_index' => 1, // 0:15
                'start_time' => '00:15:00',
                'end_time' => '00:30:00',
                'price_eur_mwh' => 44.75,
            ],
            [
                'electricity_price_id' => 2,
                'interval_index' => 2, // 0:30
                'start_time' => '00:30:00',
                'end_time' => '00:45:00',
                'price_eur_mwh' => 44.50,
            ],
            [
                'electricity_price_id' => 2,
                'interval_index' => 3, // 0:45
                'start_time' => '00:45:00',
                'end_time' => '01:00:00',
                'price_eur_mwh' => 45.00,
            ],

            // Intervalos de hora normal (mediodía)
            [
                'electricity_price_id' => 3,
                'interval_index' => 48, // 12:00
                'start_time' => '12:00:00',
                'end_time' => '12:15:00',
                'price_eur_mwh' => 65.75,
            ],
            [
                'electricity_price_id' => 3,
                'interval_index' => 49, // 12:15
                'start_time' => '12:15:00',
                'end_time' => '12:30:00',
                'price_eur_mwh' => 66.25,
            ],
            [
                'electricity_price_id' => 3,
                'interval_index' => 50, // 12:30
                'start_time' => '12:30:00',
                'end_time' => '12:45:00',
                'price_eur_mwh' => 67.00,
            ],
            [
                'electricity_price_id' => 3,
                'interval_index' => 51, // 12:45
                'start_time' => '12:45:00',
                'end_time' => '13:00:00',
                'price_eur_mwh' => 67.50,
            ],

            // Intervalos de hora pico (tarde)
            [
                'electricity_price_id' => 4,
                'interval_index' => 72, // 18:00
                'start_time' => '18:00:00',
                'end_time' => '18:15:00',
                'price_eur_mwh' => 95.25,
            ],
            [
                'electricity_price_id' => 4,
                'interval_index' => 73, // 18:15
                'start_time' => '18:15:00',
                'end_time' => '18:30:00',
                'price_eur_mwh' => 97.75,
            ],
            [
                'electricity_price_id' => 4,
                'interval_index' => 74, // 18:30
                'start_time' => '18:30:00',
                'end_time' => '18:45:00',
                'price_eur_mwh' => 99.50,
            ],
            [
                'electricity_price_id' => 4,
                'interval_index' => 75, // 18:45
                'start_time' => '18:45:00',
                'end_time' => '19:00:00',
                'price_eur_mwh' => 101.25,
            ],
        ];
    }
}
