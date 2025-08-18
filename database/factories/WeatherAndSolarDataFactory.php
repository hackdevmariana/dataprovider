<?php

namespace Database\Factories;

use App\Models\WeatherAndSolarData;
use App\Models\Municipality;
use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\WeatherAndSolarData>
 */
class WeatherAndSolarDataFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $datetime = $this->faker->dateTimeBetween('-7 days', '+3 days');
        $hour = (int) $datetime->format('H');
        $season = $this->getSeason($datetime);
        
        // Generar datos realistas según la hora y estación
        $solarData = $this->generateSolarData($hour, $season);
        $weatherData = $this->generateWeatherData($season);
        
        return [
            'datetime' => $datetime,
            'location' => $this->faker->randomElement([
                'Madrid Centro',
                'Barcelona Ciudad', 
                'Valencia Costa',
                'Sevilla Sur',
                'Bilbao Norte'
            ]),
            'municipality_id' => Municipality::inRandomOrder()->first()?->id,
            'latitude' => $this->faker->latitude(35.0, 44.0), // España
            'longitude' => $this->faker->longitude(-10.0, 4.0),
            
            // Temperatura
            'temperature' => $weatherData['temp'],
            'temperature_min' => $weatherData['temp'] - $this->faker->numberBetween(2, 8),
            'temperature_max' => $weatherData['temp'] + $this->faker->numberBetween(2, 8),
            
            // Humedad y precipitación
            'humidity' => $this->faker->numberBetween(30, 90),
            'cloud_coverage' => $weatherData['clouds'],
            'precipitation' => $weatherData['rain'],
            
            // Datos solares
            'solar_irradiance' => $solarData['irradiance'],
            'solar_irradiance_daily' => $solarData['daily'],
            'uv_index' => $solarData['uv'],
            
            // Datos de viento
            'wind_speed' => $this->faker->randomFloat(1, 0, 25),
            'wind_direction' => $this->faker->numberBetween(0, 360),
            'wind_gust' => function($attributes) {
                return $attributes['wind_speed'] * $this->faker->randomFloat(2, 1.0, 1.5);
            },
            
            // Datos atmosféricos
            'pressure' => $this->faker->randomFloat(2, 980, 1040),
            'visibility' => $this->faker->randomFloat(1, 5, 50),
            'weather_condition' => $this->getWeatherCondition($weatherData['clouds'], $weatherData['rain']),
            
            // Metadatos
            'data_type' => $this->faker->randomElement(['historical', 'current', 'forecast']),
            'source' => $this->faker->randomElement(['AEMET', 'OpenWeather', 'MeteoGalicia', 'Manual']),
            'source_url' => $this->faker->optional(0.6)->url(),
            
            // Potenciales (se calcularán)
            'solar_potential' => $solarData['potential'],
            'wind_potential' => function($attributes) {
                return $this->calculateWindPotential($attributes['wind_speed']);
            },
            
            // Optimización
            'is_optimal_solar' => $solarData['optimal'],
            'is_optimal_wind' => function($attributes) {
                return $attributes['wind_speed'] >= 7 && $attributes['wind_speed'] <= 25 && $attributes['precipitation'] <= 5;
            },
            
            // Calidad del aire
            'air_quality_index' => $this->faker->optional(0.7)->numberBetween(1, 150),
        ];
    }

    /**
     * Generate realistic solar data based on hour and season.
     */
    private function generateSolarData($hour, $season)
    {
        // Base irradiance patterns (W/m²)
        $hourlyPatterns = [
            0 => 0, 1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0,
            6 => 50, 7 => 150, 8 => 300, 9 => 450, 10 => 600,
            11 => 750, 12 => 850, 13 => 900, 14 => 850, 15 => 750,
            16 => 600, 17 => 450, 18 => 300, 19 => 150, 20 => 50,
            21 => 0, 22 => 0, 23 => 0
        ];

        $seasonMultipliers = [
            'winter' => 0.6,
            'spring' => 0.9,
            'summer' => 1.0,
            'autumn' => 0.8,
        ];

        $baseIrradiance = $hourlyPatterns[$hour] ?? 0;
        $seasonMultiplier = $seasonMultipliers[$season] ?? 0.8;
        $cloudVariation = $this->faker->randomFloat(2, 0.5, 1.2);

        $irradiance = round($baseIrradiance * $seasonMultiplier * $cloudVariation);
        
        return [
            'irradiance' => max(0, $irradiance),
            'daily' => $this->faker->randomFloat(2, 2, 8), // kWh/m²/day
            'uv' => $irradiance > 200 ? $this->faker->numberBetween(1, 11) : 0,
            'potential' => $this->calculateSolarPotential($irradiance),
            'optimal' => $irradiance >= 600 && $hour >= 10 && $hour <= 16,
        ];
    }

    /**
     * Generate realistic weather data based on season.
     */
    private function generateWeatherData($season)
    {
        $seasonData = [
            'winter' => ['temp' => [5, 15], 'clouds' => [40, 80], 'rain' => [0, 10]],
            'spring' => ['temp' => [12, 22], 'clouds' => [20, 60], 'rain' => [0, 8]],
            'summer' => ['temp' => [20, 35], 'clouds' => [0, 40], 'rain' => [0, 3]],
            'autumn' => ['temp' => [10, 20], 'clouds' => [30, 70], 'rain' => [0, 12]],
        ];

        $data = $seasonData[$season] ?? $seasonData['spring'];

        return [
            'temp' => $this->faker->numberBetween($data['temp'][0], $data['temp'][1]),
            'clouds' => $this->faker->numberBetween($data['clouds'][0], $data['clouds'][1]),
            'rain' => $this->faker->randomFloat(1, $data['rain'][0], $data['rain'][1]),
        ];
    }

    /**
     * Determine season from date.
     */
    private function getSeason($datetime)
    {
        $month = (int) $datetime->format('n');
        
        if ($month >= 3 && $month <= 5) return 'spring';
        if ($month >= 6 && $month <= 8) return 'summer';
        if ($month >= 9 && $month <= 11) return 'autumn';
        return 'winter';
    }

    /**
     * Get weather condition based on clouds and rain.
     */
    private function getWeatherCondition($clouds, $rain)
    {
        if ($rain > 5) return 'lluvia';
        if ($rain > 0) return 'llovizna';
        if ($clouds > 70) return 'nublado';
        if ($clouds > 30) return 'parcialmente_nublado';
        return 'soleado';
    }

    /**
     * Calculate simplified solar potential.
     */
    private function calculateSolarPotential($irradiance)
    {
        if ($irradiance <= 0) return 0;
        
        // Simplified: 1kW installation
        $efficiency = 0.15;
        $systemLosses = 0.85;
        
        return round(($irradiance / 1000) * $efficiency * $systemLosses, 3);
    }

    /**
     * Calculate simplified wind potential.
     */
    private function calculateWindPotential($windSpeed)
    {
        if ($windSpeed < 3) return 0;
        
        // Simplified wind power calculation
        $windSpeedCubed = pow($windSpeed, 3);
        $basePower = $windSpeedCubed * 0.001 * 0.35; // Simplified coefficient
        
        return round(min($basePower, 1), 3); // Cap at 1kW
    }

    /**
     * Create optimal solar conditions.
     */
    public function optimalSolar(): static
    {
        return $this->state(fn (array $attributes) => [
            'solar_irradiance' => $this->faker->numberBetween(700, 1000),
            'cloud_coverage' => $this->faker->numberBetween(0, 20),
            'temperature' => $this->faker->numberBetween(20, 28),
            'precipitation' => 0,
            'is_optimal_solar' => true,
            'weather_condition' => 'soleado',
        ]);
    }

    /**
     * Create optimal wind conditions.
     */
    public function optimalWind(): static
    {
        return $this->state(fn (array $attributes) => [
            'wind_speed' => $this->faker->randomFloat(1, 8, 20),
            'wind_gust' => function($attr) { return $attr['wind_speed'] * 1.2; },
            'precipitation' => $this->faker->randomFloat(1, 0, 2),
            'is_optimal_wind' => true,
        ]);
    }

    /**
     * Create current data.
     */
    public function current(): static
    {
        return $this->state(fn (array $attributes) => [
            'datetime' => now()->subMinutes($this->faker->numberBetween(0, 60)),
            'data_type' => 'current',
        ]);
    }

    /**
     * Create forecast data.
     */
    public function forecast(): static
    {
        return $this->state(fn (array $attributes) => [
            'datetime' => now()->addHours($this->faker->numberBetween(1, 72)),
            'data_type' => 'forecast',
        ]);
    }

    /**
     * Create summer data.
     */
    public function summer(): static
    {
        return $this->state(fn (array $attributes) => [
            'datetime' => $this->faker->dateTimeBetween('2024-06-21', '2024-09-21'),
            'temperature' => $this->faker->numberBetween(25, 40),
            'solar_irradiance' => $this->faker->numberBetween(600, 1000),
            'cloud_coverage' => $this->faker->numberBetween(0, 30),
            'precipitation' => $this->faker->randomFloat(1, 0, 2),
        ]);
    }
}