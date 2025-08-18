<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ExchangeRate;

class ExchangeRateSeeder extends Seeder
{
    /**
     * Ejecutar el seeder para tipos de cambio.
     */
    public function run(): void
    {
        $this->command->info('Creando tipos de cambio para CambioEuro...');

        // Crear tipos de cambio principales activos
        $mainRates = ExchangeRate::factory()
            ->count(10)
            ->active()
            ->create();

        $this->command->info("✅ Creados {$mainRates->count()} tipos de cambio principales activos");

        // Crear tipos de cambio de criptomonedas
        $cryptoRates = ExchangeRate::factory()
            ->count(8)
            ->crypto()
            ->active()
            ->create();

        $this->command->info("✅ Creados {$cryptoRates->count()} tipos de cambio de criptomonedas");

        // Crear tipos de cambio de metales preciosos
        $metalRates = ExchangeRate::factory()
            ->count(3)
            ->metal()
            ->active()
            ->create();

        $this->command->info("✅ Creados {$metalRates->count()} tipos de cambio de metales preciosos");

        // Crear algunos tipos de cambio promocionados
        $promotedRates = ExchangeRate::factory()
            ->count(5)
            ->promoted()
            ->create();

        $this->command->info("✅ Creados {$promotedRates->count()} tipos de cambio promocionados");

        // Crear datos históricos (últimos 7 días)
        $this->createHistoricalData();

        $totalRates = ExchangeRate::count();
        $this->command->info("🎉 Total de tipos de cambio creados: {$totalRates}");
        
        // Mostrar estadísticas
        $this->showStatistics();
    }

    /**
     * Crear datos históricos de tipos de cambio.
     */
    private function createHistoricalData(): void
    {
        $this->command->info('Creando datos históricos de tipos de cambio...');

        // Principales monedas con datos históricos
        $mainCurrencies = [
            ['currency' => 'USD', 'base_rate' => 1.08],
            ['currency' => 'GBP', 'base_rate' => 0.87],
            ['currency' => 'JPY', 'base_rate' => 155.0],
            ['currency' => 'BTC', 'base_rate' => 0.000020],
        ];

        $historicalCount = 0;

        foreach ($mainCurrencies as $currencyData) {
            for ($i = 7; $i >= 1; $i--) {
                // Generar variación realista (±2% diario)
                $variation = 1 + (fake()->randomFloat(4, -0.02, 0.02));
                $rate = $currencyData['base_rate'] * $variation;

                ExchangeRate::create([
                    'from_currency' => 'EUR',
                    'to_currency' => $currencyData['currency'],
                    'rate' => $rate,
                    'date' => now()->subDays($i)->format('Y-m-d'),
                    'source' => $currencyData['currency'] === 'BTC' ? 'coingecko' : 'exchangerate.host',
                    'market_type' => $currencyData['currency'] === 'BTC' ? 'crypto' : 'fiat',
                    'precision' => $currencyData['currency'] === 'BTC' ? 8 : 4,
                    'unit' => $this->getCurrencyUnit($currencyData['currency']),
                    'volume_usd' => fake()->numberBetween(1000000000, 5000000000),
                    'retrieved_at' => now()->subDays($i),
                    'is_active' => true,
                    'is_promoted' => false,
                ]);

                $historicalCount++;
            }
        }

        $this->command->info("✅ Creados {$historicalCount} registros históricos");
    }

    /**
     * Obtener la unidad de una moneda.
     */
    private function getCurrencyUnit(string $currency): string
    {
        return match($currency) {
            'USD' => 'dólar estadounidense',
            'GBP' => 'libra esterlina',
            'JPY' => 'yen japonés',
            'BTC' => 'bitcoin',
            'ETH' => 'ethereum',
            'XAU' => 'gramo de oro',
            'XAG' => 'gramo de plata',
            default => strtolower($currency)
        };
    }

    /**
     * Mostrar estadísticas de los tipos de cambio creados.
     */
    private function showStatistics(): void
    {
        $stats = [
            'Total tipos de cambio' => ExchangeRate::count(),
            'Tipos activos' => ExchangeRate::where('is_active', true)->count(),
            'Tipos promocionados' => ExchangeRate::where('is_promoted', true)->count(),
            'Monedas fiat' => ExchangeRate::where('market_type', 'fiat')->count(),
            'Criptomonedas' => ExchangeRate::where('market_type', 'crypto')->count(),
            'Metales preciosos' => ExchangeRate::where('market_type', 'metal')->count(),
            'Commodities' => ExchangeRate::where('unit', 'LIKE', '%kilovatio%')->count(),
            'Datos de hoy' => ExchangeRate::whereDate('date', today())->count(),
            'Datos históricos' => ExchangeRate::where('date', '<', today())->count(),
        ];

        $this->command->info("\n📊 Estadísticas de tipos de cambio:");
        foreach ($stats as $type => $count) {
            $this->command->info("   {$type}: {$count}");
        }

        // Mostrar principales monedas
        $topCurrencies = ExchangeRate::selectRaw('to_currency, COUNT(*) as count')
                                    ->where('is_active', true)
                                    ->groupBy('to_currency')
                                    ->orderBy('count', 'desc')
                                    ->limit(5)
                                    ->get();

        if ($topCurrencies->isNotEmpty()) {
            $this->command->info("\n💱 Principales monedas disponibles:");
            foreach ($topCurrencies as $currency) {
                $this->command->info("   {$currency->to_currency}: {$currency->count} registros");
            }
        }

        // Información sobre fuentes
        $sources = ExchangeRate::selectRaw('source, COUNT(*) as count')
                              ->groupBy('source')
                              ->get();
        
        $this->command->info("\n📡 Fuentes de datos:");
        foreach ($sources as $source) {
            $this->command->info("   {$source->source}: {$source->count} registros");
        }
    }
}
