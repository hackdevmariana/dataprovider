<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WeatherAndSolarDataResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'datetime' => $this->datetime?->toISOString(),
            'date' => $this->datetime?->toDateString(),
            'time' => $this->datetime?->format('H:i'),
            'location' => [
                'description' => $this->location,
                'latitude' => $this->latitude,
                'longitude' => $this->longitude,
                'municipality' => $this->whenLoaded('municipality', function() {
                    return [
                        'id' => $this->municipality->id,
                        'name' => $this->municipality->name,
                        'slug' => $this->municipality->slug,
                    ];
                }),
            ],
            'temperature' => [
                'current' => $this->temperature,
                'min' => $this->temperature_min,
                'max' => $this->temperature_max,
                'unit' => '°C',
            ],
            'solar_data' => [
                'irradiance_wm2' => $this->solar_irradiance,
                'irradiance_daily_kwh_m2' => $this->solar_irradiance_daily,
                'uv_index' => $this->uv_index,
                'potential_kwh_kw' => $this->solar_potential,
                'is_optimal' => $this->is_optimal_solar,
                'quality_class' => $this->solar_quality_class,
            ],
            'wind_data' => [
                'speed_ms' => $this->wind_speed,
                'direction_deg' => $this->wind_direction,
                'gust_ms' => $this->wind_gust,
                'potential_kwh_kw' => $this->wind_potential,
                'is_optimal' => $this->is_optimal_wind,
            ],
            'atmospheric_conditions' => [
                'humidity_percent' => $this->humidity,
                'cloud_coverage_percent' => $this->cloud_coverage,
                'precipitation_mm' => $this->precipitation,
                'pressure_hpa' => $this->pressure,
                'visibility_km' => $this->visibility,
                'weather_condition' => $this->weather_condition,
                'air_quality_index' => $this->air_quality_index,
            ],
            'optimization' => [
                'is_optimal_solar' => $this->is_optimal_solar,
                'is_optimal_wind' => $this->is_optimal_wind,
                'recommendations' => $this->optimization_recommendations,
                'conditions_summary' => $this->conditions_summary,
            ],
            'metadata' => [
                'data_type' => $this->data_type,
                'source' => $this->source,
                'source_url' => $this->source_url,
            ],
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}