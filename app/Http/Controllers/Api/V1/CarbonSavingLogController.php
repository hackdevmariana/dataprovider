<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\CarbonSavingLog;
use App\Http\Resources\V1\CarbonSavingLogResource;
use App\Http\Requests\StoreCarbonSavingLogRequest;
use App\Http\Requests\UpdateCarbonSavingLogRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
/**
 * @group Carbon Saving Logs
 *
 * APIs para la gestión de logs de ahorro de carbono del sistema.
 * Permite crear, consultar y gestionar registros de ahorro de emisiones de CO2.
 */
class CarbonSavingLogController extends Controller
{
    /**
     * Display a listing of carbon saving logs
     *
     * Obtiene una lista paginada de todos los logs de ahorro de carbono.
     *
     * @queryParam page int Número de página. Example: 1
     * @queryParam per_page int Cantidad por página (máx 100). Example: 20
     * @queryParam user_id integer Filtrar por usuario. Example: 1
     * @queryParam activity_type string Filtrar por tipo de actividad (transport, energy, waste). Example: transport
     * @queryParam date_from string Filtrar desde fecha (YYYY-MM-DD). Example: 2024-01-01
     * @queryParam date_to string Filtrar hasta fecha (YYYY-MM-DD). Example: 2024-01-31
     *
     * @response 200 {
     *   "data": [
     *     {
     *       "id": 1,
     *       "user_id": 1,
     *       "activity_type": "transport",
     *       "carbon_saved": 2.5,
     *       "activity_date": "2024-01-01",
     *       "description": "Uso de transporte público en lugar de coche"
     *     }
     *   ],
     *   "meta": {...}
     * }
     *
     * @apiResourceCollection App\Http\Resources\V1\CarbonSavingLogResource
     * @apiResourceModel App\Models\CarbonSavingLog
     * @authenticated
     */
    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'page' => 'sometimes|integer|min:1',
            'per_page' => 'sometimes|integer|min:1|max:100',
            'user_id' => 'sometimes|integer|exists:users,id',
            'activity_type' => 'sometimes|string|in:transport,energy,waste,recycling',
            'date_from' => 'sometimes|date',
            'date_to' => 'sometimes|date|after_or_equal:date_from'
        ]);

        $query = CarbonSavingLog::with(['user']);

        if ($request->has('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->has('activity_type')) {
            $query->where('activity_type', $request->activity_type);
        }

        if ($request->has('date_from')) {
            $query->where('activity_date', '>=', $request->date_from);
        }

        if ($request->has('date_to')) {
            $query->where('activity_date', '<=', $request->date_to);
        }

        $logs = $query->orderBy('activity_date', 'desc')
                     ->paginate($request->get('per_page', 20));

        return response()->json([
            'data' => CarbonSavingLogResource::collection($logs),
            'meta' => [
                'current_page' => $logs->currentPage(),
                'last_page' => $logs->lastPage(),
                'per_page' => $logs->perPage(),
                'total' => $logs->total(),
            ]
        ]);
    }

    /**
     * Store a newly created carbon saving log
     *
     * Crea un nuevo log de ahorro de carbono en el sistema.
     *
     * @bodyParam user_id integer required ID del usuario. Example: 1
     * @bodyParam activity_type string required Tipo de actividad (transport, energy, waste). Example: transport
     * @bodyParam carbon_saved number required Cantidad de CO2 ahorrado en kg. Example: 2.5
     * @bodyParam activity_date string required Fecha de la actividad (YYYY-MM-DD). Example: 2024-01-01
     * @bodyParam description string Descripción de la actividad. Example: Uso de transporte público en lugar de coche
     * @bodyParam metadata object Metadatos adicionales (JSON). Example: {"distance": "10km", "fuel_type": "gasoline"}
     *
     * @response 201 {
     *   "data": {
     *     "id": 1,
     *       "user_id": 1,
     *       "activity_type": "transport",
     *       "carbon_saved": 2.5,
     *       "activity_date": "2024-01-01",
     *       "description": "Uso de transporte público en lugar de coche",
     *       "created_at": "2024-01-01T00:00:00.000000Z"
     *   }
     * }
     *
     * @response 422 {
     *   "message": "Los datos proporcionados no son válidos.",
     *   "errors": {...}
     * }
     *
     * @apiResourceModel App\Models\CarbonSavingLog
     * @authenticated
     */
    public function store(StoreCarbonSavingLogRequest $request): JsonResponse
    {
        $data = $request->validated();
        
        $log = CarbonSavingLog::create($data);

        return response()->json([
            'data' => new CarbonSavingLogResource($log->load('user'))
        ], 201);
    }

    /**
     * Display the specified carbon saving log
     *
     * Obtiene los detalles de un log de ahorro de carbono específico.
     *
     * @urlParam carbonSavingLog integer ID del log. Example: 1
     *
     * @response 200 {
     *   "data": {
     *     "id": 1,
     *       "user_id": 1,
     *       "activity_type": "transport",
     *       "carbon_saved": 2.5,
     *       "activity_date": "2024-01-01",
     *       "description": "Uso de transporte público en lugar de coche",
     *       "metadata": {
     *         "distance": "10km",
     *         "fuel_type": "gasoline"
     *       },
     *       "user": {...}
     *   }
     * }
     *
     * @response 404 {
     *   "message": "Log de ahorro de carbono no encontrado"
     * }
     *
     * @apiResourceModel App\Models\CarbonSavingLog
     * @authenticated
     */
    public function show(CarbonSavingLog $carbonSavingLog): JsonResponse
    {
        return response()->json([
            'data' => new CarbonSavingLogResource($carbonSavingLog->load('user'))
        ]);
    }

    /**
     * Update the specified carbon saving log
     *
     * Actualiza un log de ahorro de carbono existente.
     *
     * @urlParam carbonSavingLog integer ID del log. Example: 1
     * @bodyParam user_id integer ID del usuario. Example: 1
     * @bodyParam activity_type string Tipo de actividad (transport, energy, waste). Example: transport
     * @bodyParam carbon_saved number Cantidad de CO2 ahorrado en kg. Example: 3.0
     * @bodyParam activity_date string Fecha de la actividad (YYYY-MM-DD). Example: 2024-01-01
     * @bodyParam description string Descripción de la actividad. Example: Uso de transporte público en lugar de coche personal
     * @bodyParam metadata object Metadatos adicionales (JSON). Example: {"distance": "12km", "fuel_type": "gasoline", "duration": "45min"}
     *
     * @response 200 {
     *   "data": {
     *     "id": 1,
     *       "user_id": 1,
     *       "activity_type": "transport",
     *       "carbon_saved": 3.0,
     *       "activity_date": "2024-01-01",
     *       "description": "Uso de transporte público en lugar de coche personal",
     *       "metadata": {
     *         "distance": "12km",
     *         "fuel_type": "gasoline",
     *         "duration": "45min"
     *       },
     *       "updated_at": "2024-01-02T00:00:00.000000Z"
     *   }
     * }
     *
     * @response 404 {
     *   "message": "Log de ahorro de carbono no encontrado"
     * }
     *
     * @response 422 {
     *   "message": "Los datos proporcionados no son válidos.",
     *   "errors": {...}
     * }
     *
     * @apiResourceModel App\Models\CarbonSavingLog
     * @authenticated
     */
    public function update(UpdateCarbonSavingLogRequest $request, CarbonSavingLog $carbonSavingLog): JsonResponse
    {
        $data = $request->validated();
        
        $carbonSavingLog->update($data);

        return response()->json([
            'data' => new CarbonSavingLogResource($carbonSavingLog->load('user'))
        ]);
    }

    /**
     * Remove the specified carbon saving log
     *
     * Elimina un log de ahorro de carbono del sistema.
     *
     * @urlParam carbonSavingLog integer ID del log. Example: 1
     *
     * @response 204 {
     *   "message": "Log de ahorro de carbono eliminado exitosamente"
     * }
     *
     * @response 404 {
     *   "message": "Log de ahorro de carbono no encontrado"
     * }
     *
     * @authenticated
     */
    public function destroy(CarbonSavingLog $carbonSavingLog): JsonResponse
    {
        $carbonSavingLog->delete();

        return response()->json([
            'message' => 'Log de ahorro de carbono eliminado exitosamente'
        ], 204);
    }
}
