<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\DataSource;
use App\Http\Resources\V1\DataSourceResource;
use App\Http\Requests\StoreDataSourceRequest;
use App\Http\Requests\UpdateDataSourceRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

/**
 * @group Data Sources
 *
 * APIs para la gestión de fuentes de datos del sistema.
 * Permite crear, consultar y gestionar fuentes de información.
 */
class DataSourceController extends Controller
{
    /**
     * Display a listing of data sources
     *
     * Obtiene una lista paginada de todas las fuentes de datos.
     *
     * @queryParam page int Número de página. Example: 1
     * @queryParam per_page int Cantidad por página (máx 100). Example: 20
     * @queryParam type string Filtrar por tipo de fuente (api, file, database, web). Example: api
     * @queryParam status string Filtrar por estado (active, inactive, error). Example: active
     * @queryParam search string Buscar por nombre o descripción. Example: weather
     *
     * @response 200 {
     *   "data": [
     *     {
     *       "id": 1,
     *       "name": "Weather API",
     *       "slug": "weather-api",
     *       "type": "api",
     *       "url": "https://api.weather.com",
     *       "status": "active",
     *       "last_sync_at": "2024-01-01T00:00:00.000000Z"
     *     }
     *   ],
     *   "meta": {...}
     * }
     *
     * @apiResourceCollection App\Http\Resources\V1\DataSourceResource
     * @apiResourceModel App\Models\DataSource
     * @authenticated
     */
    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'page' => 'sometimes|integer|min:1',
            'per_page' => 'sometimes|integer|min:1|max:100',
            'type' => 'sometimes|string|in:api,file,database,web',
            'status' => 'sometimes|string|in:active,inactive,error',
            'search' => 'sometimes|string|max:255'
        ]);

        $query = DataSource::query();

        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        $dataSources = $query->orderBy('created_at', 'desc')
                            ->paginate($request->get('per_page', 20));

        return response()->json([
            'data' => DataSourceResource::collection($dataSources),
            'meta' => [
                'current_page' => $dataSources->currentPage(),
                'last_page' => $dataSources->lastPage(),
                'per_page' => $dataSources->perPage(),
                'total' => $dataSources->total(),
            ]
        ]);
    }

    /**
     * Store a newly created data source
     *
     * Crea una nueva fuente de datos en el sistema.
     *
     * @bodyParam name string required Nombre de la fuente de datos. Example: Weather API
     * @bodyParam slug string Slug único de la fuente. Example: weather-api
     * @bodyParam type string required Tipo de fuente (api, file, database, web). Example: api
     * @bodyParam url string URL de la fuente de datos. Example: https://api.weather.com
     * @bodyParam description string Descripción de la fuente. Example: API para datos meteorológicos
     * @bodyParam credentials object Credenciales de acceso (JSON). Example: {"api_key": "abc123"}
     * @bodyParam sync_frequency string Frecuencia de sincronización (hourly, daily, weekly). Example: daily
     * @bodyParam is_active boolean Si la fuente está activa. Example: true
     *
     * @response 201 {
     *   "data": {
     *     "id": 1,
     *     "name": "Weather API",
     *     "slug": "weather-api",
     *     "type": "api",
     *     "url": "https://api.weather.com",
     *     "status": "active",
     *     "created_at": "2024-01-01T00:00:00.000000Z"
     *   }
     * }
     *
     * @response 422 {
     *   "message": "Los datos proporcionados no son válidos.",
     *   "errors": {...}
     * }
     *
     * @apiResourceModel App\Models\DataSource
     * @authenticated
     */
    public function store(StoreDataSourceRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['status'] = 'active';
        
        $dataSource = DataSource::create($data);

        return response()->json([
            'data' => new DataSourceResource($dataSource)
        ], 201);
    }

    /**
     * Display the specified data source
     *
     * Obtiene los detalles de una fuente de datos específica.
     *
     * @urlParam dataSource integer ID de la fuente de datos. Example: 1
     *
     * @response 200 {
     *   "data": {
     *     "id": 1,
     *     "name": "Weather API",
     *     "slug": "weather-api",
     *     "type": "api",
     *     "url": "https://api.weather.com",
     *     "status": "active",
     *     "last_sync_at": "2024-01-01T00:00:00.000000Z",
     *     "sync_frequency": "daily"
     *   }
     * }
     *
     * @response 404 {
     *   "message": "Fuente de datos no encontrada"
     * }
     *
     * @apiResourceModel App\Models\DataSource
     * @authenticated
     */
    public function show(DataSource $dataSource): JsonResponse
    {
        return response()->json([
            'data' => new DataSourceResource($dataSource)
        ]);
    }

    /**
     * Update the specified data source
     *
     * Actualiza una fuente de datos existente.
     *
     * @urlParam dataSource integer ID de la fuente de datos. Example: 1
     * @bodyParam name string Nombre de la fuente de datos. Example: Weather API v2
     * @bodyParam slug string Slug único de la fuente. Example: weather-api-v2
     * @bodyParam type string Tipo de fuente (api, file, database, web). Example: api
     * @bodyParam url string URL de la fuente de datos. Example: https://api.weather.com/v2
     * @bodyParam description string Descripción de la fuente. Example: API para datos meteorológicos v2
     * @bodyParam credentials object Credenciales de acceso (JSON). Example: {"api_key": "xyz789"}
     * @bodyParam sync_frequency string Frecuencia de sincronización (hourly, daily, weekly). Example: hourly
     * @bodyParam is_active boolean Si la fuente está activa. Example: true
     *
     * @response 200 {
     *   "data": {
     *     "id": 1,
     *     "name": "Weather API v2",
     *     "slug": "weather-api-v2",
     *     "type": "api",
     *     "url": "https://api.weather.com/v2",
     *     "status": "active",
     *     "updated_at": "2024-01-02T00:00:00.000000Z"
     *   }
     * }
     *
     * @response 404 {
     *   "message": "Fuente de datos no encontrada"
     * }
     *
     * @response 422 {
     *   "message": "Los datos proporcionados no son válidos.",
     *   "errors": {...}
     * }
     *
     * @apiResourceModel App\Models\DataSource
     * @authenticated
     */
    public function update(UpdateDataSourceRequest $request, DataSource $dataSource): JsonResponse
    {
        $data = $request->validated();
        
        $dataSource->update($data);

        return response()->json([
            'data' => new DataSourceResource($dataSource)
        ]);
    }

    /**
     * Remove the specified data source
     *
     * Elimina una fuente de datos del sistema.
     *
     * @urlParam dataSource integer ID de la fuente de datos. Example: 1
     *
     * @response 204 {
     *   "message": "Fuente de datos eliminada exitosamente"
     * }
     *
     * @response 404 {
     *   "message": "Fuente de datos no encontrada"
     * }
     *
     * @authenticated
     */
    public function destroy(DataSource $dataSource): JsonResponse
    {
        $dataSource->delete();

        return response()->json([
            'message' => 'Fuente de datos eliminada exitosamente'
        ], 204);
    }
}
