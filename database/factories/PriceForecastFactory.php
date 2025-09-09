<?php

namespace Database\Factories;

use App\Models\PriceForecast;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PriceForecast>
 */
class PriceForecastFactory extends Factory
{
    protected $model = PriceForecast::class;

    public function definition(): array
    {
        $energyTypes = ['electricity', 'gas', 'oil', 'coal', 'renewable', 'nuclear'];
        $zones = ['peninsula', 'canarias', 'baleares', 'ceuta', 'melilla'];
        $forecastModels = [
            'ARIMA', 'LSTM', 'Prophet', 'Random Forest', 'XGBoost', 
            'Linear Regression', 'SVM', 'Neural Network', 'Ensemble',
            'Time Series Decomposition', 'Exponential Smoothing'
        ];

        $forecastTime = $this->faker->dateTimeBetween('-30 days', 'now');
        $targetTime = $this->faker->dateTimeBetween($forecastTime, '+30 days');
        
        $predictedPrice = $this->faker->randomFloat(4, 20, 200);
        $confidenceLevel = $this->faker->randomFloat(2, 0.3, 0.95);
        
        // Calcular rango de incertidumbre basado en el nivel de confianza
        $uncertaintyFactor = (1 - $confidenceLevel) * 0.3; // 0-30% de variación
        $minPrice = $predictedPrice * (1 - $uncertaintyFactor);
        $maxPrice = $predictedPrice * (1 + $uncertaintyFactor);

        return [
            'energy_type' => $this->faker->randomElement($energyTypes),
            'zone' => $this->faker->randomElement($zones),
            'forecast_time' => $forecastTime,
            'target_time' => $targetTime,
            'predicted_price' => $predictedPrice,
            'confidence_level' => $confidenceLevel,
            'forecast_model' => $this->faker->randomElement($forecastModels),
            'factors' => $this->generateFactors(),
            'min_price' => $minPrice,
            'max_price' => $maxPrice,
            'accuracy_score' => $this->faker->optional(0.6)->randomFloat(2, 0.4, 0.95),
        ];
    }

    private function generateFactors(): array
    {
        $allFactors = [
            'weather_conditions' => ['sunny', 'cloudy', 'rainy', 'stormy', 'windy'],
            'demand_forecast' => ['high', 'medium', 'low'],
            'supply_availability' => ['abundant', 'normal', 'limited'],
            'market_volatility' => ['low', 'medium', 'high'],
            'seasonal_patterns' => ['summer', 'winter', 'spring', 'autumn'],
            'economic_indicators' => ['gdp_growth', 'inflation', 'unemployment'],
            'political_events' => ['elections', 'policy_changes', 'regulations'],
            'natural_disasters' => ['earthquake', 'flood', 'drought', 'hurricane'],
            'technological_advances' => ['renewable_adoption', 'storage_improvements'],
            'international_markets' => ['oil_prices', 'gas_prices', 'coal_prices'],
        ];

        $selectedFactors = [];
        $factorCount = $this->faker->numberBetween(3, 7);
        $availableFactors = array_keys($allFactors);
        $selectedFactorKeys = $this->faker->randomElements($availableFactors, $factorCount);

        foreach ($selectedFactorKeys as $factorKey) {
            $selectedFactors[$factorKey] = $this->faker->randomElement($allFactors[$factorKey]);
        }

        return $selectedFactors;
    }

    public function electricity(): static
    {
        return $this->state(fn (array $attributes) => [
            'energy_type' => 'electricity',
            'predicted_price' => $this->faker->randomFloat(4, 30, 150),
        ]);
    }

    public function gas(): static
    {
        return $this->state(fn (array $attributes) => [
            'energy_type' => 'gas',
            'predicted_price' => $this->faker->randomFloat(4, 15, 80),
        ]);
    }

    public function renewable(): static
    {
        return $this->state(fn (array $attributes) => [
            'energy_type' => 'renewable',
            'predicted_price' => $this->faker->randomFloat(4, 20, 100),
        ]);
    }

    public function highConfidence(): static
    {
        return $this->state(fn (array $attributes) => [
            'confidence_level' => $this->faker->randomFloat(2, 0.8, 0.95),
        ]);
    }

    public function lowConfidence(): static
    {
        return $this->state(fn (array $attributes) => [
            'confidence_level' => $this->faker->randomFloat(2, 0.3, 0.6),
        ]);
    }

    public function accurate(): static
    {
        return $this->state(fn (array $attributes) => [
            'accuracy_score' => $this->faker->randomFloat(2, 0.8, 0.95),
        ]);
    }

    public function inaccurate(): static
    {
        return $this->state(fn (array $attributes) => [
            'accuracy_score' => $this->faker->randomFloat(2, 0.3, 0.6),
        ]);
    }

    public function peninsula(): static
    {
        return $this->state(fn (array $attributes) => [
            'zone' => 'peninsula',
        ]);
    }

    public function canarias(): static
    {
        return $this->state(fn (array $attributes) => [
            'zone' => 'canarias',
        ]);
    }

    public function baleares(): static
    {
        return $this->state(fn (array $attributes) => [
            'zone' => 'baleares',
        ]);
    }

    public function upcoming(): static
    {
        return $this->state(fn (array $attributes) => [
            'forecast_time' => $this->faker->dateTimeBetween('-7 days', 'now'),
            'target_time' => $this->faker->dateTimeBetween('now', '+30 days'),
        ]);
    }

    public function past(): static
    {
        return $this->state(fn (array $attributes) => [
            'forecast_time' => $this->faker->dateTimeBetween('-60 days', '-30 days'),
            'target_time' => $this->faker->dateTimeBetween('-30 days', 'now'),
        ]);
    }

    public function today(): static
    {
        return $this->state(fn (array $attributes) => [
            'forecast_time' => $this->faker->dateTimeBetween('-7 days', 'now'),
            'target_time' => now(),
        ]);
    }
}
