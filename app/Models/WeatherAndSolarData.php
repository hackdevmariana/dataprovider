<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

/**
 * Datos meteorológicos y solares para optimización energética.
 * 
 * Almacena información meteorológica histórica y de predicción
 * junto con datos de irradiación solar para optimizar la
 * producción de instalaciones fotovoltaicas y eólicas.
 * 
 * @property int $id
 * @property \Carbon\Carbon $datetime Fecha y hora del registro
 * @property string|null $location Ubicación geográfica
 * @property int|null $municipality_id Municipio
 * @property float|null $latitude Latitud
 * @property float|null $longitude Longitud
 * @property float|null $temperature Temperatura en °C
 * @property float|null $temperature_min Temperatura mínima °C
 * @property float|null $temperature_max Temperatura máxima °C
 * @property float|null $humidity Humedad relativa %
 * @property float|null $cloud_coverage Nubosidad %
 * @property float|null $solar_irradiance Irradiación solar W/m²
 * @property float|null $solar_irradiance_daily Irradiación diaria kWh/m²
 * @property float|null $uv_index Índice UV
 * @property float|null $wind_speed Velocidad viento m/s
 * @property float|null $wind_direction Dirección viento grados
 * @property float|null $wind_gust Ráfagas viento m/s
 * @property float|null $precipitation Precipitación mm
 * @property float|null $pressure Presión atmosférica hPa
 * @property float|null $visibility Visibilidad km
 * @property string|null $weather_condition Condición meteorológica
 * @property string $data_type Tipo: historical, current, forecast
 * @property string $source Fuente de datos
 * @property string|null $source_url URL de la fuente
 * @property float|null $solar_potential Potencial solar estimado kWh/kWp
 * @property float|null $wind_potential Potencial eólico estimado
 * @property bool $is_optimal_solar Si es óptimo para solar
 * @property bool $is_optimal_wind Si es óptimo para eólico
 * @property int|null $air_quality_index Índice calidad aire
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * 
 * @property-read \App\Models\Municipality|null $municipality
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\SolarProduction[] $solarProductions
 */
class WeatherAndSolarData extends Model
{
    use HasFactory;

    protected $fillable = [
        'datetime',
        'location',
        'municipality_id',
        'latitude',
        'longitude',
        'temperature',
        'temperature_min',
        'temperature_max',
        'humidity',
        'cloud_coverage',
        'solar_irradiance',
        'solar_irradiance_daily',
        'uv_index',
        'wind_speed',
        'wind_direction',
        'wind_gust',
        'precipitation',
        'pressure',
        'visibility',
        'weather_condition',
        'data_type',
        'source',
        'source_url',
        'solar_potential',
        'wind_potential',
        'is_optimal_solar',
        'is_optimal_wind',
        'air_quality_index',
    ];

    protected $casts = [
        'datetime' => 'datetime',
        'latitude' => 'float',
        'longitude' => 'float',
        'temperature' => 'float',
        'temperature_min' => 'float',
        'temperature_max' => 'float',
        'humidity' => 'float',
        'cloud_coverage' => 'float',
        'solar_irradiance' => 'float',
        'solar_irradiance_daily' => 'float',
        'uv_index' => 'float',
        'wind_speed' => 'float',
        'wind_direction' => 'float',
        'wind_gust' => 'float',
        'precipitation' => 'float',
        'pressure' => 'float',
        'visibility' => 'float',
        'solar_potential' => 'float',
        'wind_potential' => 'float',
        'is_optimal_solar' => 'boolean',
        'is_optimal_wind' => 'boolean',
        'air_quality_index' => 'integer',
    ];

    /**
     * Municipio donde se registran los datos.
     */
    public function municipality(): BelongsTo
    {
        return $this->belongsTo(Municipality::class);
    }

    /**
     * Producciones solares asociadas.
     */
    public function solarProductions(): HasMany
    {
        return $this->hasMany(SolarProduction::class);
    }

    /**
     * Scope para datos históricos.
     */
    public function scopeHistorical($query)
    {
        return $query->where('data_type', 'historical');
    }

    /**
     * Scope para datos actuales.
     */
    public function scopeCurrent($query)
    {
        return $query->where('data_type', 'current');
    }

    /**
     * Scope para predicciones.
     */
    public function scopeForecast($query)
    {
        return $query->where('data_type', 'forecast');
    }

    /**
     * Scope para condiciones solares óptimas.
     */
    public function scopeOptimalSolar($query)
    {
        return $query->where('is_optimal_solar', true);
    }

    /**
     * Scope para condiciones eólicas óptimas.
     */
    public function scopeOptimalWind($query)
    {
        return $query->where('is_optimal_wind', true);
    }

    /**
     * Scope para rangos de fecha.
     */
    public function scopeBetweenDates($query, $start, $end)
    {
        return $query->whereBetween('datetime', [$start, $end]);
    }

    /**
     * Scope para ubicación específica.
     */
    public function scopeNearLocation($query, $lat, $lng, $radiusKm = 50)
    {
        return $query->whereRaw(
            '(6371 * acos(cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) + sin(radians(?)) * sin(radians(latitude)))) <= ?',
            [$lat, $lng, $lat, $radiusKm]
        );
    }

    /**
     * Calcular potencial de producción solar.
     */
    public function calculateSolarPotential($installedCapacityKw = 1)
    {
        if (!$this->solar_irradiance) {
            return 0;
        }

        // Fórmula simplificada: kWh = kW * irradiancia * eficiencia * factor corrección
        $efficiency = 0.15; // 15% eficiencia paneles
        $systemLosses = 0.85; // Factor pérdidas sistema
        $temperatureCorrection = $this->getTemperatureCorrection();
        
        return round(
            $installedCapacityKw * 
            ($this->solar_irradiance / 1000) * // Convertir W/m² a kW/m²
            $efficiency * 
            $systemLosses * 
            $temperatureCorrection,
            3
        );
    }

    /**
     * Calcular potencial de producción eólica.
     */
    public function calculateWindPotential($installedCapacityKw = 1)
    {
        if (!$this->wind_speed) {
            return 0;
        }

        // Fórmula simplificada potencia eólica: P = 0.5 * ρ * A * v³ * Cp
        $airDensity = $this->getAirDensity();
        $windSpeedCubed = pow($this->wind_speed, 3);
        $powerCoefficient = 0.35; // Coeficiente potencia típico
        
        // Simplificado para aerogenerador pequeño
        $basePower = $windSpeedCubed * 0.001 * $powerCoefficient;
        
        return round(min($basePower, $installedCapacityKw), 3);
    }

    /**
     * Obtener corrección por temperatura para paneles solares.
     */
    private function getTemperatureCorrection()
    {
        if (!$this->temperature) {
            return 1;
        }

        // Los paneles pierden eficiencia con calor: ~0.4% por grado sobre 25°C
        $tempStandard = 25;
        $lossPerDegree = 0.004;
        
        if ($this->temperature > $tempStandard) {
            $tempDifference = $this->temperature - $tempStandard;
            return 1 - ($tempDifference * $lossPerDegree);
        }
        
        return 1;
    }

    /**
     * Obtener densidad del aire para cálculos eólicos.
     */
    private function getAirDensity()
    {
        // Densidad aire estándar 1.225 kg/m³ a nivel mar, 15°C
        $standardDensity = 1.225;
        
        if ($this->temperature && $this->pressure) {
            // Corrección por temperatura y presión
            $standardTemp = 288.15; // 15°C en Kelvin
            $standardPressure = 1013.25; // hPa
            
            $tempKelvin = $this->temperature + 273.15;
            
            return $standardDensity * 
                   ($this->pressure / $standardPressure) * 
                   ($standardTemp / $tempKelvin);
        }
        
        return $standardDensity;
    }

    /**
     * Evaluar si las condiciones son óptimas para energía solar.
     */
    public function evaluateOptimalSolar()
    {
        $score = 0;
        
        // Irradiación alta (>600 W/m²)
        if ($this->solar_irradiance >= 600) $score += 30;
        elseif ($this->solar_irradiance >= 400) $score += 20;
        elseif ($this->solar_irradiance >= 200) $score += 10;
        
        // Nubosidad baja (<30%)
        if ($this->cloud_coverage <= 30) $score += 25;
        elseif ($this->cloud_coverage <= 60) $score += 15;
        
        // Temperatura moderada (15-30°C)
        if ($this->temperature >= 15 && $this->temperature <= 30) $score += 20;
        elseif ($this->temperature >= 10 && $this->temperature <= 35) $score += 10;
        
        // Sin precipitación
        if ($this->precipitation == 0) $score += 15;
        elseif ($this->precipitation <= 1) $score += 5;
        
        // Visibilidad alta
        if ($this->visibility >= 10) $score += 10;
        
        return $score >= 70; // Óptimo si score >= 70/100
    }

    /**
     * Evaluar si las condiciones son óptimas para energía eólica.
     */
    public function evaluateOptimalWind()
    {
        if (!$this->wind_speed) {
            return false;
        }
        
        // Velocidad viento óptima: 7-25 m/s
        $isOptimalSpeed = $this->wind_speed >= 7 && $this->wind_speed <= 25;
        
        // Sin precipitación intensa
        $isOptimalWeather = $this->precipitation <= 5;
        
        // Viento sostenido (ráfagas no muy superiores a velocidad media)
        $isOptimalGust = !$this->wind_gust || ($this->wind_gust <= $this->wind_speed * 1.5);
        
        return $isOptimalSpeed && $isOptimalWeather && $isOptimalGust;
    }

    /**
     * Obtener resumen de condiciones.
     */
    public function getConditionsSummaryAttribute()
    {
        return [
            'temperature' => $this->temperature . '°C',
            'humidity' => $this->humidity . '%',
            'cloud_coverage' => $this->cloud_coverage . '%',
            'wind_speed' => $this->wind_speed . ' m/s',
            'solar_irradiance' => $this->solar_irradiance . ' W/m²',
            'weather_condition' => $this->weather_condition,
            'is_optimal_solar' => $this->is_optimal_solar,
            'is_optimal_wind' => $this->is_optimal_wind,
        ];
    }

    /**
     * Obtener clase CSS para visualización de calidad.
     */
    public function getSolarQualityClassAttribute()
    {
        if ($this->is_optimal_solar) return 'excellent';
        if ($this->solar_irradiance >= 400) return 'good';
        if ($this->solar_irradiance >= 200) return 'fair';
        return 'poor';
    }

    /**
     * Obtener recomendaciones de optimización.
     */
    public function getOptimizationRecommendationsAttribute()
    {
        $recommendations = [];
        
        if ($this->is_optimal_solar) {
            $recommendations[] = 'Condiciones excelentes para producción solar';
        } elseif ($this->solar_irradiance >= 300) {
            $recommendations[] = 'Buenas condiciones para energía solar';
        }
        
        if ($this->is_optimal_wind) {
            $recommendations[] = 'Condiciones óptimas para energía eólica';
        } elseif ($this->wind_speed >= 4) {
            $recommendations[] = 'Condiciones aceptables para micro-eólica';
        }
        
        if ($this->temperature > 35) {
            $recommendations[] = 'Alta temperatura - reducción eficiencia paneles';
        }
        
        if ($this->cloud_coverage > 70) {
            $recommendations[] = 'Alta nubosidad - considerar almacenamiento energético';
        }
        
        return $recommendations;
    }
}
