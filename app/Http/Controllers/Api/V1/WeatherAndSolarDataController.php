<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\WeatherAndSolarDataResource;
use App\Models\WeatherAndSolarData;
use Illuminate\Http\Request;
use Carbon\Carbon;

/**
 * @OA\Tag(
 *     name="Weather & Solar Data",
 *     description="API endpoints para datos meteorológicos y optimización solar"
 * )
 */
class WeatherAndSolarDataController extends Controller
{
    public function index(Request $request)
    {
        $data = WeatherAndSolarData::with(['municipality'])
            ->when($request->data_type, function($query, $type) {
                return $query->where('data_type', $type);
            })
            ->when($request->municipality_id, function($query, $id) {
                return $query->where('municipality_id', $id);
            })
            ->when($request->from_date, function($query, $date) {
                return $query->where('datetime', '>=', $date);
            })
            ->when($request->to_date, function($query, $date) {
                return $query->where('datetime', '<=', $date);
            })
            ->orderBy('datetime', 'desc')
            ->paginate($request->get('per_page', 15));

        return WeatherAndSolarDataResource::collection($data);
    }

    public function show(WeatherAndSolarData $weatherAndSolarData)
    {
        $weatherAndSolarData->load(['municipality']);
        return new WeatherAndSolarDataResource($weatherAndSolarData);
    }

    public function current(Request $request)
    {
        $data = WeatherAndSolarData::with(['municipality'])
            ->current()
            ->when($request->municipality_id, function($query, $id) {
                return $query->where('municipality_id', $id);
            })
            ->when($request->lat && $request->lng, function($query) use ($request) {
                return $query->nearLocation($request->lat, $request->lng, $request->get('radius', 50));
            })
            ->orderBy('datetime', 'desc')
            ->paginate(15);

        return WeatherAndSolarDataResource::collection($data);
    }

    public function forecast(Request $request)
    {
        $days = $request->get('days', 7);
        $endDate = now()->addDays($days);

        $data = WeatherAndSolarData::with(['municipality'])
            ->forecast()
            ->whereBetween('datetime', [now(), $endDate])
            ->when($request->municipality_id, function($query, $id) {
                return $query->where('municipality_id', $id);
            })
            ->orderBy('datetime')
            ->paginate(24); // 24 horas por página

        return WeatherAndSolarDataResource::collection($data);
    }

    public function optimalSolar(Request $request)
    {
        $data = WeatherAndSolarData::with(['municipality'])
            ->optimalSolar()
            ->when($request->from_date, function($query, $date) {
                return $query->where('datetime', '>=', $date);
            })
            ->when($request->municipality_id, function($query, $id) {
                return $query->where('municipality_id', $id);
            })
            ->orderBy('solar_irradiance', 'desc')
            ->paginate(15);

        return WeatherAndSolarDataResource::collection($data);
    }

    public function optimalWind(Request $request)
    {
        $data = WeatherAndSolarData::with(['municipality'])
            ->optimalWind()
            ->when($request->from_date, function($query, $date) {
                return $query->where('datetime', '>=', $date);
            })
            ->when($request->municipality_id, function($query, $id) {
                return $query->where('municipality_id', $id);
            })
            ->orderBy('wind_speed', 'desc')
            ->paginate(15);

        return WeatherAndSolarDataResource::collection($data);
    }

    public function calculateProduction(Request $request)
    {
        $validated = $request->validate([
            'weather_data_id' => 'required|exists:weather_and_solar_data,id',
            'installation_capacity_kw' => 'required|numeric|min:0.1|max:1000',
            'technology' => 'required|in:solar,wind,both',
        ]);

        $weatherData = WeatherAndSolarData::findOrFail($validated['weather_data_id']);
        $capacity = $validated['installation_capacity_kw'];
        $technology = $validated['technology'];

        $production = [];

        if ($technology === 'solar' || $technology === 'both') {
            $solarProduction = $weatherData->calculateSolarPotential($capacity);
            $production['solar'] = [
                'capacity_kw' => $capacity,
                'estimated_production_kwh' => $solarProduction,
                'capacity_factor' => $capacity > 0 ? round(($solarProduction / $capacity) * 100, 1) : 0,
                'conditions_quality' => $weatherData->solar_quality_class,
                'irradiance_wm2' => $weatherData->solar_irradiance,
                'temperature_c' => $weatherData->temperature,
                'cloud_coverage_percent' => $weatherData->cloud_coverage,
            ];
        }

        if ($technology === 'wind' || $technology === 'both') {
            $windProduction = $weatherData->calculateWindPotential($capacity);
            $production['wind'] = [
                'capacity_kw' => $capacity,
                'estimated_production_kwh' => $windProduction,
                'capacity_factor' => $capacity > 0 ? round(($windProduction / $capacity) * 100, 1) : 0,
                'wind_speed_ms' => $weatherData->wind_speed,
                'wind_direction_deg' => $weatherData->wind_direction,
                'wind_gust_ms' => $weatherData->wind_gust,
            ];
        }

        return response()->json([
            'data' => [
                'weather_data' => new WeatherAndSolarDataResource($weatherData),
                'production_estimates' => $production,
                'optimization_recommendations' => $weatherData->optimization_recommendations,
                'timestamp' => $weatherData->datetime->toISOString(),
            ]
        ]);
    }

    public function dailyOptimization(Request $request)
    {
        $date = $request->get('date', now()->toDateString());
        $municipalityId = $request->get('municipality_id');

        $query = WeatherAndSolarData::with(['municipality'])
            ->whereDate('datetime', $date);

        if ($municipalityId) {
            $query->where('municipality_id', $municipalityId);
        }

        $dayData = $query->orderBy('datetime')->get();

        if ($dayData->isEmpty()) {
            return response()->json(['message' => 'No hay datos para esta fecha'], 404);
        }

        // Análisis del día
        $analysis = [
            'date' => $date,
            'total_records' => $dayData->count(),
            'solar_analysis' => [
                'peak_irradiance' => $dayData->max('solar_irradiance'),
                'peak_irradiance_time' => $dayData->where('solar_irradiance', $dayData->max('solar_irradiance'))->first()?->datetime->format('H:i'),
                'avg_irradiance' => round($dayData->avg('solar_irradiance'), 1),
                'optimal_hours' => $dayData->where('is_optimal_solar', true)->count(),
                'estimated_daily_production_1kw' => round($dayData->sum('solar_potential'), 2),
            ],
            'wind_analysis' => [
                'peak_wind_speed' => $dayData->max('wind_speed'),
                'peak_wind_time' => $dayData->where('wind_speed', $dayData->max('wind_speed'))->first()?->datetime->format('H:i'),
                'avg_wind_speed' => round($dayData->avg('wind_speed'), 1),
                'optimal_hours' => $dayData->where('is_optimal_wind', true)->count(),
                'estimated_daily_production_1kw' => round($dayData->sum('wind_potential'), 2),
            ],
            'weather_summary' => [
                'avg_temperature' => round($dayData->avg('temperature'), 1),
                'min_temperature' => $dayData->min('temperature'),
                'max_temperature' => $dayData->max('temperature'),
                'avg_humidity' => round($dayData->avg('humidity'), 1),
                'avg_cloud_coverage' => round($dayData->avg('cloud_coverage'), 1),
                'total_precipitation' => round($dayData->sum('precipitation'), 1),
            ],
            'best_times' => [
                'solar' => $dayData->where('is_optimal_solar', true)->take(3)->map(function($item) {
                    return [
                        'time' => $item->datetime->format('H:i'),
                        'irradiance' => $item->solar_irradiance,
                        'temperature' => $item->temperature,
                    ];
                }),
                'wind' => $dayData->where('is_optimal_wind', true)->take(3)->map(function($item) {
                    return [
                        'time' => $item->datetime->format('H:i'),
                        'wind_speed' => $item->wind_speed,
                        'wind_direction' => $item->wind_direction,
                    ];
                }),
            ],
        ];

        return response()->json(['data' => $analysis]);
    }

    public function nearLocation(Request $request)
    {
        $validated = $request->validate([
            'lat' => 'required|numeric|between:-90,90',
            'lng' => 'required|numeric|between:-180,180',
            'radius_km' => 'nullable|numeric|min:1|max:500',
            'data_type' => 'nullable|in:historical,current,forecast',
        ]);

        $data = WeatherAndSolarData::with(['municipality'])
            ->nearLocation(
                $validated['lat'], 
                $validated['lng'], 
                $validated['radius_km'] ?? 50
            )
            ->when($validated['data_type'] ?? null, function($query, $type) {
                return $query->where('data_type', $type);
            })
            ->orderBy('datetime', 'desc')
            ->paginate(15);

        return WeatherAndSolarDataResource::collection($data);
    }

    public function statistics(Request $request)
    {
        $municipalityId = $request->get('municipality_id');
        $fromDate = $request->get('from_date', now()->subDays(30)->toDateString());
        $toDate = $request->get('to_date', now()->toDateString());

        $query = WeatherAndSolarData::whereBetween('datetime', [$fromDate, $toDate]);
        
        if ($municipalityId) {
            $query->where('municipality_id', $municipalityId);
        }

        $data = $query->get();

        $stats = [
            'period' => [
                'from' => $fromDate,
                'to' => $toDate,
                'total_records' => $data->count(),
            ],
            'solar_statistics' => [
                'avg_irradiance' => round($data->avg('solar_irradiance'), 1),
                'max_irradiance' => $data->max('solar_irradiance'),
                'optimal_conditions_percent' => $data->count() > 0 ? round(($data->where('is_optimal_solar', true)->count() / $data->count()) * 100, 1) : 0,
                'estimated_total_production_1kw' => round($data->sum('solar_potential'), 2),
            ],
            'wind_statistics' => [
                'avg_wind_speed' => round($data->avg('wind_speed'), 1),
                'max_wind_speed' => $data->max('wind_speed'),
                'optimal_conditions_percent' => $data->count() > 0 ? round(($data->where('is_optimal_wind', true)->count() / $data->count()) * 100, 1) : 0,
                'estimated_total_production_1kw' => round($data->sum('wind_potential'), 2),
            ],
            'weather_averages' => [
                'temperature' => round($data->avg('temperature'), 1),
                'humidity' => round($data->avg('humidity'), 1),
                'cloud_coverage' => round($data->avg('cloud_coverage'), 1),
                'precipitation_total' => round($data->sum('precipitation'), 1),
            ],
            'data_sources' => $data->groupBy('source')->map->count(),
            'data_types' => $data->groupBy('data_type')->map->count(),
        ];

        return response()->json(['data' => $stats]);
    }
}