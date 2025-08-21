<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\WeatherAndSolarDataResource;
use App\Models\WeatherAndSolarData;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;

/**
 * @group Weather & Solar Data
 *
 * APIs para datos meteorológicos y optimización solar.
 * Permite consultar información del clima y datos solares para optimización energética.
 */
class WeatherAndSolarDataController extends Controller
{
    /**
     * Display a listing of weather and solar data
     *
     * Obtiene una lista paginada de datos meteorológicos y solares.
     *
     * @queryParam data_type string Filtrar por tipo de dato. Example: weather
     * @queryParam municipality_id integer Filtrar por municipio. Example: 1
     * @queryParam from_date string Filtrar desde fecha (YYYY-MM-DD). Example: 2024-01-01
     * @queryParam to_date string Filtrar hasta fecha (YYYY-MM-DD). Example: 2024-01-31
     * @queryParam per_page int Cantidad por página (máx 100). Example: 15
     * @queryParam page int Número de página. Example: 1
     *
     * @response 200 {
     *   "data": [
     *     {
     *       "id": 1,
     *       "data_type": "weather",
     *       "datetime": "2024-01-01T12:00:00Z",
     *       "temperature": 25.5,
     *       "humidity": 60,
     *       "municipality": {...}
     *     }
     *   ],
     *   "meta": {...}
     * }
     *
     * @apiResourceCollection App\Http\Resources\V1\WeatherAndSolarDataResource
     * @apiResourceModel App\Models\WeatherAndSolarData
     */
    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'data_type' => 'sometimes|string|max:100',
            'municipality_id' => 'sometimes|integer|exists:municipalities,id',
            'from_date' => 'sometimes|date',
            'to_date' => 'sometimes|date|after_or_equal:from_date',
            'per_page' => 'sometimes|integer|min:1|max:100',
            'page' => 'sometimes|integer|min:1'
        ]);

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

        return response()->json([
            'data' => WeatherAndSolarDataResource::collection($data),
            'meta' => [
                'current_page' => $data->currentPage(),
                'last_page' => $data->lastPage(),
                'per_page' => $data->perPage(),
                'total' => $data->total(),
            ]
        ]);
    }

    /**
     * Display the specified weather and solar data
     *
     * Obtiene los detalles de un registro específico de datos meteorológicos.
     *
     * @urlParam weatherAndSolarData integer ID del registro. Example: 1
     *
     * @response 200 {
     *   "data": {
     *     "id": 1,
     *       "data_type": "weather",
     *       "datetime": "2024-01-01T12:00:00Z",
     *       "temperature": 25.5,
     *       "humidity": 60,
     *       "municipality": {...}
     *   }
     * }
     *
     * @apiResourceModel App\Models\WeatherAndSolarData
     */
    public function show(WeatherAndSolarData $weatherAndSolarData): JsonResponse
    {
        $weatherAndSolarData->load(['municipality']);
        
        return response()->json([
            'data' => new WeatherAndSolarDataResource($weatherAndSolarData)
        ]);
    }

    /**
     * Get current weather and solar data
     *
     * Obtiene datos meteorológicos y solares actuales.
     *
     * @queryParam municipality_id integer Filtrar por municipio. Example: 1
     * @queryParam lat number Latitud para búsqueda por ubicación. Example: 40.4168
     * @queryParam lng number Longitud para búsqueda por ubicación. Example: -3.7038
     * @queryParam radius number Radio de búsqueda en km (máx 100). Example: 50
     *
     * @response 200 {
     *   "data": [
     *     {
     *       "id": 1,
     *       "data_type": "weather",
     *       "datetime": "2024-01-01T12:00:00Z",
     *       "temperature": 25.5,
     *       "humidity": 60,
     *       "municipality": {...}
     *     }
     *   ],
     *   "meta": {...}
     * }
     *
     * @apiResourceCollection App\Http\Resources\V1\WeatherAndSolarDataResource
     * @apiResourceModel App\Models\WeatherAndSolarData
     */
    public function current(Request $request): JsonResponse
    {
        $request->validate([
            'municipality_id' => 'sometimes|integer|exists:municipalities,id',
            'lat' => 'sometimes|numeric|between:-90,90',
            'lng' => 'sometimes|numeric|between:-180,180',
            'radius' => 'sometimes|numeric|min:1|max:100'
        ]);

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

        return response()->json([
            'data' => WeatherAndSolarDataResource::collection($data),
            'meta' => [
                'current_page' => $data->currentPage(),
                'last_page' => $data->lastPage(),
                'per_page' => $data->perPage(),
                'total' => $data->total(),
            ]
        ]);
    }

    /**
     * Get weather and solar forecast
     *
     * Obtiene pronóstico meteorológico y solar.
     *
     * @queryParam days integer Días de pronóstico (1-30). Example: 7
     * @queryParam municipality_id integer Filtrar por municipio. Example: 1
     *
     * @response 200 {
     *   "data": [
     *     {
     *       "id": 1,
     *       "data_type": "forecast",
     *       "datetime": "2024-01-01T12:00:00Z",
     *       "temperature": 25.5,
     *       "solar_irradiance": 800,
     *       "municipality": {...}
     *     }
     *   ],
     *   "meta": {...}
     * }
     *
     * @apiResourceCollection App\Http\Resources\V1\WeatherAndSolarDataResource
     * @apiResourceModel App\Models\WeatherAndSolarData
     */
    public function forecast(Request $request): JsonResponse
    {
        $request->validate([
            'days' => 'sometimes|integer|min:1|max:30',
            'municipality_id' => 'sometimes|integer|exists:municipalities,id'
        ]);

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

        return response()->json([
            'data' => WeatherAndSolarDataResource::collection($data),
            'meta' => [
                'current_page' => $data->currentPage(),
                'last_page' => $data->lastPage(),
                'per_page' => $data->perPage(),
                'total' => $data->total(),
            ]
        ]);
    }

    /**
     * Get optimal solar conditions
     *
     * Obtiene condiciones solares óptimas para generación de energía.
     *
     * @queryParam from_date string Filtrar desde fecha (YYYY-MM-DD). Example: 2024-01-01
     * @queryParam municipality_id integer Filtrar por municipio. Example: 1
     *
     * @response 200 {
     *   "data": [
     *     {
     *       "id": 1,
     *       "data_type": "solar",
     *       "datetime": "2024-01-01T12:00:00Z",
     *       "solar_irradiance": 1000,
     *       "municipality": {...}
     *     }
     *   ],
     *   "meta": {...}
     * }
     *
     * @apiResourceCollection App\Http\Resources\V1\WeatherAndSolarDataResource
     * @apiResourceModel App\Models\WeatherAndSolarData
     */
    public function optimalSolar(Request $request): JsonResponse
    {
        $request->validate([
            'from_date' => 'sometimes|date',
            'municipality_id' => 'sometimes|integer|exists:municipalities,id'
        ]);

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

        return response()->json([
            'data' => WeatherAndSolarDataResource::collection($data),
            'meta' => [
                'current_page' => $data->currentPage(),
                'last_page' => $data->lastPage(),
                'per_page' => $data->perPage(),
                'total' => $data->total(),
            ]
        ]);
    }

    /**
     * Get optimal wind conditions
     *
     * Obtiene condiciones de viento óptimas para generación eólica.
     *
     * @queryParam from_date string Filtrar desde fecha (YYYY-MM-DD). Example: 2024-01-01
     * @queryParam municipality_id integer Filtrar por municipio. Example: 1
     *
     * @response 200 {
     *   "data": [
     *     {
     *       "id": 1,
     *       "data_type": "wind",
     *       "datetime": "2024-01-01T12:00:00Z",
     *       "wind_speed": 15.5,
     *       "municipality": {...}
     *     }
     *   ],
     *   "meta": {...}
     * }
     *
     * @apiResourceCollection App\Http\Resources\V1\WeatherAndSolarDataResource
     * @apiResourceModel App\Models\WeatherAndSolarData
     */
    public function optimalWind(Request $request): JsonResponse
    {
        $request->validate([
            'from_date' => 'sometimes|date',
            'municipality_id' => 'sometimes|integer|exists:municipalities,id'
        ]);

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

        return response()->json([
            'data' => WeatherAndSolarDataResource::collection($data),
            'meta' => [
                'current_page' => $data->currentPage(),
                'last_page' => $data->lastPage(),
                'per_page' => $data->perPage(),
                'total' => $data->total(),
            ]
        ]);
    }
}
