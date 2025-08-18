<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\WeatherAndSolarData;
use App\Models\Municipality;
use Carbon\Carbon;

class WeatherAndSolarDataSeeder extends Seeder
{
    public function run(): void
    {
        // Datos de ejemplo para principales ciudades españolas
        $cities = [
            [
                'name' => 'Madrid',
                'lat' => 40.4168,
                'lng' => -3.7038,
                'municipality_name' => 'Madrid',
            ],
            [
                'name' => 'Barcelona', 
                'lat' => 41.3851,
                'lng' => 2.1734,
                'municipality_name' => 'Barcelona',
            ],
            [
                'name' => 'Valencia',
                'lat' => 39.4699,
                'lng' => -0.3763,
                'municipality_name' => 'Valencia',
            ],
            [
                'name' => 'Sevilla',
                'lat' => 37.3891,
                'lng' => -5.9845,
                'municipality_name' => 'Sevilla',
            ],
        ];

        foreach ($cities as $city) {
            $municipality = Municipality::where('name', 'LIKE', "%{$city['municipality_name']}%")->first();
            
            // Datos históricos (últimos 7 días)
            for ($i = 7; $i >= 1; $i--) {
                $date = now()->subDays($i);
                
                // Crear datos cada 3 horas para cada día
                for ($hour = 0; $hour < 24; $hour += 3) {
                    $datetime = $date->copy()->setHour($hour);
                    
                    $data = $this->generateRealisticData($datetime, $city);
                    $data['municipality_id'] = $municipality?->id;
                    $data['data_type'] = 'historical';
                    
                    WeatherAndSolarData::create($data);
                }
            }
            
            // Datos actuales (hoy)
            $now = now();
            $data = $this->generateRealisticData($now, $city);
            $data['municipality_id'] = $municipality?->id;
            $data['data_type'] = 'current';
            
            WeatherAndSolarData::create($data);
            
            // Predicciones (próximos 3 días)
            for ($i = 1; $i <= 3; $i++) {
                $date = now()->addDays($i);
                
                // Predicción cada 6 horas
                for ($hour = 0; $hour < 24; $hour += 6) {
                    $datetime = $date->copy()->setHour($hour);
                    
                    $data = $this->generateRealisticData($datetime, $city);
                    $data['municipality_id'] = $municipality?->id;
                    $data['data_type'] = 'forecast';
                    
                    WeatherAndSolarData::create($data);
                }
            }
        }

        // Crear datos adicionales con factory
        WeatherAndSolarData::factory(20)->current()->create();
        WeatherAndSolarData::factory(15)->optimalSolar()->create();
        WeatherAndSolarData::factory(10)->optimalWind()->create();

        $total = WeatherAndSolarData::count();
        $current = WeatherAndSolarData::current()->count();
        $historical = WeatherAndSolarData::historical()->count();
        $forecast = WeatherAndSolarData::forecast()->count();
        $optimalSolar = WeatherAndSolarData::optimalSolar()->count();
        $optimalWind = WeatherAndSolarData::optimalWind()->count();

        $this->command->info("Weather & Solar Data seeder completed: {$total} records created.");
        $this->command->info("- Historical: {$historical}");
        $this->command->info("- Current: {$current}");
        $this->command->info("- Forecast: {$forecast}");
        $this->command->info("- Optimal solar: {$optimalSolar}");
        $this->command->info("- Optimal wind: {$optimalWind}");
        $this->command->info('Data includes:');
        $this->command->info('- Real Spanish cities coordinates');
        $this->command->info('- Realistic solar irradiance patterns by hour');
        $this->command->info('- Seasonal temperature variations');
        $this->command->info('- Production potential calculations');
    }

    /**
     * Generate realistic weather data for Spanish conditions.
     */
    private function generateRealisticData($datetime, $city)
    {
        $hour = $datetime->hour;
        $month = $datetime->month;
        $season = $this->getSeason($month);
        
        // Base temperature by season and city
        $baseTempByCity = [
            'Madrid' => ['winter' => 8, 'spring' => 15, 'summer' => 28, 'autumn' => 16],
            'Barcelona' => ['winter' => 12, 'spring' => 18, 'summer' => 26, 'autumn' => 19],
            'Valencia' => ['winter' => 14, 'spring' => 19, 'summer' => 28, 'autumn' => 20],
            'Sevilla' => ['winter' => 15, 'spring' => 22, 'summer' => 32, 'autumn' => 23],
        ];

        $baseTemp = $baseTempByCity[$city['name']][$season] ?? 20;
        $tempVariation = rand(-5, 5);
        $temperature = $baseTemp + $tempVariation;

        // Solar irradiance by hour (W/m²)
        $solarIrradiance = $this->getSolarIrradiance($hour, $season);
        
        // Wind speed (coastal cities have more wind)
        $isCoastal = in_array($city['name'], ['Barcelona', 'Valencia']);
        $baseWind = $isCoastal ? 3 : 1;
        $windSpeed = $baseWind + (rand(0, 100) / 10);

        // Cloud coverage affects irradiance
        $cloudCoverage = rand(0, 80);
        $solarIrradiance *= (1 - ($cloudCoverage / 150)); // Reduce irradiance with clouds

        $precipitation = $cloudCoverage > 60 ? rand(0, 50) / 10 : 0;
        
        return [
            'datetime' => $datetime,
            'location' => $city['name'] . ' Centro',
            'latitude' => $city['lat'] + (rand(-10, 10) / 100), // Small variation
            'longitude' => $city['lng'] + (rand(-10, 10) / 100),
            'temperature' => round($temperature, 1),
            'temperature_min' => round($temperature - rand(2, 6), 1),
            'temperature_max' => round($temperature + rand(2, 6), 1),
            'humidity' => rand(30, 85),
            'cloud_coverage' => $cloudCoverage,
            'solar_irradiance' => max(0, round($solarIrradiance)),
            'solar_irradiance_daily' => $this->getDailyIrradiance($season),
            'uv_index' => $solarIrradiance > 200 ? rand(1, 10) : 0,
            'wind_speed' => round($windSpeed, 1),
            'wind_direction' => rand(0, 360),
            'wind_gust' => round($windSpeed * (1 + rand(10, 50) / 100), 1),
            'precipitation' => $precipitation,
            'pressure' => rand(995, 1025),
            'visibility' => rand(10, 50),
            'weather_condition' => $this->getWeatherCondition($cloudCoverage, $precipitation),
            'source' => 'AEMET',
            'solar_potential' => max(0, round(($solarIrradiance / 1000) * 0.15 * 0.85, 3)),
            'wind_potential' => $this->calculateWindPotential($windSpeed),
            'is_optimal_solar' => $solarIrradiance >= 600 && $cloudCoverage <= 30 && $temperature >= 15 && $temperature <= 30,
            'is_optimal_wind' => $windSpeed >= 7 && $windSpeed <= 25 && $precipitation <= 5,
            'air_quality_index' => rand(15, 120),
        ];
    }

    /**
     * Get season from month.
     */
    private function getSeason($month)
    {
        if ($month >= 3 && $month <= 5) return 'spring';
        if ($month >= 6 && $month <= 8) return 'summer';
        if ($month >= 9 && $month <= 11) return 'autumn';
        return 'winter';
    }

    /**
     * Get realistic solar irradiance by hour and season.
     */
    private function getSolarIrradiance($hour, $season)
    {
        // Base hourly pattern
        $hourlyPattern = [
            0 => 0, 1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0,
            6 => 30, 7 => 120, 8 => 280, 9 => 450, 10 => 600,
            11 => 730, 12 => 820, 13 => 850, 14 => 800, 15 => 700,
            16 => 550, 17 => 380, 18 => 200, 19 => 80, 20 => 20,
            21 => 0, 22 => 0, 23 => 0
        ];

        $seasonMultiplier = [
            'winter' => 0.5,
            'spring' => 0.8,
            'summer' => 1.0,
            'autumn' => 0.7,
        ];

        $baseIrradiance = $hourlyPattern[$hour] ?? 0;
        $multiplier = $seasonMultiplier[$season] ?? 0.8;
        
        return $baseIrradiance * $multiplier * (0.8 + rand(0, 40) / 100);
    }

    /**
     * Get daily irradiance by season.
     */
    private function getDailyIrradiance($season)
    {
        $daily = [
            'winter' => 2.5,
            'spring' => 5.0,
            'summer' => 7.5,
            'autumn' => 4.0,
        ];

        return $daily[$season] + (rand(-50, 50) / 100);
    }

    /**
     * Get weather condition.
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
     * Calculate wind potential.
     */
    private function calculateWindPotential($windSpeed)
    {
        if ($windSpeed < 3) return 0;
        return round(min(pow($windSpeed, 3) * 0.001 * 0.35, 1), 3);
    }
}