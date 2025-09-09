<?php

namespace Database\Seeders;

use App\Models\PriceForecast;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Carbon\Carbon;

class PriceForecastSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // Crear pronósticos de electricidad (40%)
        PriceForecast::factory()
            ->count(40)
            ->electricity()
            ->create();

        // Crear pronósticos de gas (25%)
        PriceForecast::factory()
            ->count(25)
            ->gas()
            ->create();

        // Crear pronósticos renovables (20%)
        PriceForecast::factory()
            ->count(20)
            ->renewable()
            ->create();

        // Crear pronósticos de alta confianza (30%)
        PriceForecast::factory()
            ->count(30)
            ->highConfidence()
            ->create();

        // Crear pronósticos de baja confianza (15%)
        PriceForecast::factory()
            ->count(15)
            ->lowConfidence()
            ->create();

        // Crear pronósticos precisos (25%)
        PriceForecast::factory()
            ->count(25)
            ->accurate()
            ->create();

        // Crear pronósticos imprecisos (10%)
        PriceForecast::factory()
            ->count(10)
            ->inaccurate()
            ->create();

        // Crear pronósticos para Península (60%)
        PriceForecast::factory()
            ->count(60)
            ->peninsula()
            ->create();

        // Crear pronósticos para Canarias (15%)
        PriceForecast::factory()
            ->count(15)
            ->canarias()
            ->create();

        // Crear pronósticos para Baleares (10%)
        PriceForecast::factory()
            ->count(10)
            ->baleares()
            ->create();

        // Crear pronósticos futuros (40%)
        PriceForecast::factory()
            ->count(40)
            ->upcoming()
            ->create();

        // Crear pronósticos pasados (30%)
        PriceForecast::factory()
            ->count(30)
            ->past()
            ->create();

        // Crear pronósticos para hoy (5%)
        PriceForecast::factory()
            ->count(5)
            ->today()
            ->create();

        // Crear algunos pronósticos específicos y realistas
        $this->createSpecificForecasts();
    }

    private function createSpecificForecasts(): void
    {
        // Pronóstico de electricidad para Península - Alta confianza
        PriceForecast::create([
            'energy_type' => 'electricity',
            'zone' => 'peninsula',
            'forecast_time' => now()->subDays(2),
            'target_time' => now()->addDays(7),
            'predicted_price' => 85.50,
            'confidence_level' => 0.92,
            'forecast_model' => 'Ensemble LSTM + Prophet',
            'factors' => [
                'weather_conditions' => 'sunny',
                'demand_forecast' => 'medium',
                'supply_availability' => 'normal',
                'market_volatility' => 'low',
                'seasonal_patterns' => 'summer',
                'renewable_adoption' => 'high',
            ],
            'min_price' => 78.20,
            'max_price' => 92.80,
            'accuracy_score' => '0.88',
        ]);

        // Pronóstico de gas para Canarias - Media confianza
        PriceForecast::create([
            'energy_type' => 'gas',
            'zone' => 'canarias',
            'forecast_time' => now()->subDays(1),
            'target_time' => now()->addDays(14),
            'predicted_price' => 45.30,
            'confidence_level' => 0.75,
            'forecast_model' => 'ARIMA + Market Analysis',
            'factors' => [
                'weather_conditions' => 'windy',
                'demand_forecast' => 'high',
                'supply_availability' => 'limited',
                'market_volatility' => 'medium',
                'international_markets' => 'oil_prices',
            ],
            'min_price' => 38.50,
            'max_price' => 52.10,
            'accuracy_score' => '0.72',
        ]);

        // Pronóstico renovable para Baleares - Baja confianza
        PriceForecast::create([
            'energy_type' => 'renewable',
            'zone' => 'baleares',
            'forecast_time' => now()->subHours(6),
            'target_time' => now()->addDays(3),
            'predicted_price' => 65.80,
            'confidence_level' => 0.45,
            'forecast_model' => 'Neural Network',
            'factors' => [
                'weather_conditions' => 'volatile',
                'demand_forecast' => 'low',
                'supply_availability' => 'abundant',
                'market_volatility' => 'high',
                'technological_advances' => 'storage_improvements',
            ],
            'min_price' => 45.20,
            'max_price' => 86.40,
            'accuracy_score' => null,
        ]);

        // Pronóstico de electricidad para hoy - Muy alta confianza
        PriceForecast::create([
            'energy_type' => 'electricity',
            'zone' => 'peninsula',
            'forecast_time' => now()->subHours(2),
            'target_time' => now(),
            'predicted_price' => 92.15,
            'confidence_level' => 0.95,
            'forecast_model' => 'Real-time ML Model',
            'factors' => [
                'weather_conditions' => 'cloudy',
                'demand_forecast' => 'high',
                'supply_availability' => 'normal',
                'market_volatility' => 'low',
                'seasonal_patterns' => 'summer',
            ],
            'min_price' => 89.50,
            'max_price' => 94.80,
            'accuracy_score' => '0.91',
        ]);

        // Pronóstico de petróleo para Península - Pronóstico a largo plazo
        PriceForecast::create([
            'energy_type' => 'oil',
            'zone' => 'peninsula',
            'forecast_time' => now()->subDays(5),
            'target_time' => now()->addMonths(3),
            'predicted_price' => 125.75,
            'confidence_level' => 0.68,
            'forecast_model' => 'Time Series Decomposition',
            'factors' => [
                'international_markets' => 'oil_prices',
                'economic_indicators' => 'gdp_growth',
                'political_events' => 'policy_changes',
                'market_volatility' => 'high',
                'seasonal_patterns' => 'autumn',
            ],
            'min_price' => 95.20,
            'max_price' => 156.30,
            'accuracy_score' => '0.65',
        ]);

        // Pronóstico de carbón - Pronóstico pasado con evaluación
        PriceForecast::create([
            'energy_type' => 'coal',
            'zone' => 'peninsula',
            'forecast_time' => now()->subDays(30),
            'target_time' => now()->subDays(15),
            'predicted_price' => 78.40,
            'confidence_level' => 0.82,
            'forecast_model' => 'Random Forest',
            'factors' => [
                'weather_conditions' => 'cold',
                'demand_forecast' => 'high',
                'supply_availability' => 'limited',
                'market_volatility' => 'medium',
                'seasonal_patterns' => 'winter',
            ],
            'min_price' => 72.10,
            'max_price' => 84.70,
            'accuracy_score' => '0.79',
        ]);

        // Pronóstico nuclear - Pronóstico de emergencia
        PriceForecast::create([
            'energy_type' => 'nuclear',
            'zone' => 'peninsula',
            'forecast_time' => now()->subHours(12),
            'target_time' => now()->addDays(1),
            'predicted_price' => 55.20,
            'confidence_level' => 0.88,
            'forecast_model' => 'Expert Opinion + ML',
            'factors' => [
                'weather_conditions' => 'stormy',
                'demand_forecast' => 'high',
                'supply_availability' => 'limited',
                'market_volatility' => 'high',
                'natural_disasters' => 'storm',
            ],
            'min_price' => 48.50,
            'max_price' => 61.90,
            'accuracy_score' => null,
        ]);

        // Pronóstico renovable para Ceuta - Zona especial
        PriceForecast::create([
            'energy_type' => 'renewable',
            'zone' => 'ceuta',
            'forecast_time' => now()->subDays(3),
            'target_time' => now()->addDays(10),
            'predicted_price' => 72.30,
            'confidence_level' => 0.70,
            'forecast_model' => 'SVM + Weather Data',
            'factors' => [
                'weather_conditions' => 'sunny',
                'demand_forecast' => 'medium',
                'supply_availability' => 'abundant',
                'market_volatility' => 'low',
                'technological_advances' => 'renewable_adoption',
            ],
            'min_price' => 65.80,
            'max_price' => 78.80,
            'accuracy_score' => '0.73',
        ]);

        // Pronóstico de gas para Melilla - Zona especial
        PriceForecast::create([
            'energy_type' => 'gas',
            'zone' => 'melilla',
            'forecast_time' => now()->subDays(1),
            'target_time' => now()->addDays(5),
            'predicted_price' => 52.80,
            'confidence_level' => 0.65,
            'forecast_model' => 'Linear Regression',
            'factors' => [
                'weather_conditions' => 'windy',
                'demand_forecast' => 'medium',
                'supply_availability' => 'normal',
                'market_volatility' => 'medium',
                'international_markets' => 'gas_prices',
            ],
            'min_price' => 45.20,
            'max_price' => 60.40,
            'accuracy_score' => '0.68',
        ]);
    }
}
