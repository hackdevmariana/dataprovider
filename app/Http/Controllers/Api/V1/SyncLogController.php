<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\SyncLog;
use App\Http\Resources\V1\SyncLogResource;
use App\Http\Requests\StoreSyncLogRequest;
use App\Http\Requests\UpdateSyncLogRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
/**
 * @group Sync Logs
 *
 * APIs para la gestión de logs de sincronización del sistema.
 * Permite consultar y gestionar el historial de sincronizaciones de datos.
 */
class SyncLogController extends Controller
{
    /**
     * Display a listing of sync logs
     *
     * Obtiene una lista paginada de todos los logs de sincronización.
     *
     * @queryParam page int Número de página. Example: 1
     * @queryParam per_page int Cantidad por página (máx 100). Example: 20
     * @queryParam data_source_id integer Filtrar por fuente de datos. Example: 1
     * @queryParam status string Filtrar por estado (success, failed, pending). Example: success
     * @queryParam date_from string Filtrar desde fecha (YYYY-MM-DD). Example: 2024-01-01
     * @queryParam date_to string Filtrar hasta fecha (YYYY-MM-DD). Example: 2024-01-31
     * @queryParam type string Filtrar por tipo (full, incremental, manual). Example: full
     *
     * @response 200 {
     *   "data": [
     *     {
     *       "id": 1,
     *       "data_source_id": 1,
     *       "status": "success",
     *       "type": "full",
     *       "started_at": "2024-01-01T10:00:00.000000Z",
     *       "completed_at": "2024-01-01T10:05:00.000000Z",
     *       "records_processed": 1500,
     *       "records_created": 1200,
     *       "records_updated": 300
     *     }
     *   ],
     *   "meta": {...}
     * }
     *
     * @apiResourceCollection App\Http\Resources\V1\SyncLogResource
     * @apiResourceModel App\Models\SyncLog
     * @authenticated
     */
    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'page' => 'sometimes|integer|min:1',
            'per_page' => 'sometimes|integer|min:1|max:100',
            'data_source_id' => 'sometimes|integer|exists:data_sources,id',
            'status' => 'sometimes|string|in:success,failed,pending',
            'date_from' => 'sometimes|date',
            'date_to' => 'sometimes|date|after_or_equal:date_from',
            'type' => 'sometimes|string|in:full,incremental,manual'
        ]);

        $query = SyncLog::with(['dataSource']);

        if ($request->has('data_source_id')) {
            $query->where('data_source_id', $request->data_source_id);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        if ($request->has('date_from')) {
            $query->where('started_at', '>=', $request->date_from);
        }

        if ($request->has('date_to')) {
            $query->where('started_at', '<=', $request->date_to);
        }

        $syncLogs = $query->orderBy('started_at', 'desc')
                          ->paginate($request->get('per_page', 20));

        return response()->json([
            'data' => SyncLogResource::collection($syncLogs),
            'meta' => [
                'current_page' => $syncLogs->currentPage(),
                'last_page' => $syncLogs->lastPage(),
                'per_page' => $syncLogs->perPage(),
                'total' => $syncLogs->total(),
            ]
        ]);
    }

    /**
     * Store a newly created sync log
     *
     * Crea un nuevo log de sincronización en el sistema.
     *
     * @bodyParam data_source_id integer required ID de la fuente de datos. Example: 1
     * @bodyParam status string required Estado de la sincronización (success, failed, pending). Example: success
     * @bodyParam type string required Tipo de sincronización (full, incremental, manual). Example: full
     * @bodyParam started_at string required Fecha de inicio (ISO 8601). Example: 2024-01-01T10:00:00Z
     * @bodyParam completed_at string Fecha de finalización (ISO 8601). Example: 2024-01-01T10:05:00Z
     * @bodyParam records_processed integer Número de registros procesados. Example: 1500
     * @bodyParam records_created integer Número de registros creados. Example: 1200
     * @bodyParam records_updated integer Número de registros actualizados. Example: 300
     * @bodyParam error_message string Mensaje de error si falló. Example: Connection timeout
     * @bodyParam metadata object Metadatos adicionales (JSON). Example: {"duration": 300, "memory_usage": "256MB"}
     *
     * @response 201 {
     *   "data": {
     *     "id": 1,
     *     "data_source_id": 1,
     *     "status": "success",
     *     "type": "full",
     *     "started_at": "2024-01-01T10:00:00.000000Z",
     *     "completed_at": "2024-01-01T10:05:00.000000Z",
     *     "records_processed": 1500,
     *     "created_at": "2024-01-01T10:05:00.000000Z"
     *   }
     * }
     *
     * @response 422 {
     *   "message": "Los datos proporcionados no son válidos.",
     *   "errors": {...}
     * }
     *
     * @apiResourceModel App\Models\SyncLog
     * @authenticated
     */
    public function store(StoreSyncLogRequest $request): JsonResponse
    {
        $data = $request->validated();
        
        $syncLog = SyncLog::create($data);

        return response()->json([
            'data' => new SyncLogResource($syncLog->load('dataSource'))
        ], 201);
    }

    /**
     * Display the specified sync log
     *
     * Obtiene los detalles de un log de sincronización específico.
     *
     * @urlParam syncLog integer ID del log de sincronización. Example: 1
     *
     * @response 200 {
     *   "data": {
     *     "id": 1,
     *     "data_source_id": 1,
     *     "status": "success",
     *     "type": "full",
     *     "started_at": "2024-01-01T10:00:00.000000Z",
     *     "completed_at": "2024-01-01T10:05:00.000000Z",
     *     "records_processed": 1500,
     *     "records_created": 1200,
     *     "records_updated": 300,
     *     "error_message": null,
     *     "metadata": {
     *       "duration": 300,
     *       "memory_usage": "256MB"
     *     },
     *     "data_source": {...}
     *   }
     * }
     *
     * @response 404 {
     *   "message": "Log de sincronización no encontrado"
     * }
     *
     * @apiResourceModel App\Models\SyncLog
     * @authenticated
     */
    public function show(SyncLog $syncLog): JsonResponse
    {
        return response()->json([
            'data' => new SyncLogResource($syncLog->load('dataSource'))
        ]);
    }

    /**
     * Update the specified sync log
     *
     * Actualiza un log de sincronización existente.
     *
     * @urlParam syncLog integer ID del log de sincronización. Example: 1
     * @bodyParam status string Estado de la sincronización (success, failed, pending). Example: success
     * @bodyParam type string Tipo de sincronización (full, incremental, manual). Example: full
     * @bodyParam started_at string Fecha de inicio (ISO 8601). Example: 2024-01-01T10:00:00Z
     * @bodyParam completed_at string Fecha de finalización (ISO 8601). Example: 2024-01-01T10:05:00Z
     * @bodyParam records_processed integer Número de registros procesados. Example: 1500
     * @bodyParam records_created integer Número de registros creados. Example: 1200
     * @bodyParam records_updated integer Número de registros actualizados. Example: 300
     * @bodyParam error_message string Mensaje de error si falló. Example: Connection timeout
     * @bodyParam metadata object Metadatos adicionales (JSON). Example: {"duration": 300, "memory_usage": "256MB"}
     *
     * @response 200 {
     *   "data": {
     *     "id": 1,
     *     "status": "success",
     *     "type": "full",
     *     "completed_at": "2024-01-01T10:05:00.000000Z",
     *     "records_processed": 1500,
     *     "updated_at": "2024-01-01T10:05:00.000000Z"
     *   }
     * }
     *
     * @response 404 {
     *   "message": "Log de sincronización no encontrado"
     * }
     *
     * @response 422 {
     *   "message": "Los datos proporcionados no son válidos.",
     *   "errors": {...}
     * }
     *
     * @apiResourceModel App\Models\SyncLog
     * @authenticated
     */
    public function update(UpdateSyncLogRequest $request, SyncLog $syncLog): JsonResponse
    {
        $data = $request->validated();
        
        $syncLog->update($data);

        return response()->json([
            'data' => new SyncLogResource($syncLog->load('dataSource'))
        ]);
    }

    /**
     * Remove the specified sync log
     *
     * Elimina un log de sincronización del sistema.
     *
     * @urlParam syncLog integer ID del log de sincronización. Example: 1
     *
     * @response 204 {
     *   "message": "Log de sincronización eliminado exitosamente"
     * }
     *
     * @response 404 {
     *   "message": "Log de sincronización no encontrado"
     * }
     *
     * @authenticated
     */
    public function destroy(SyncLog $syncLog): JsonResponse
    {
        $syncLog->delete();

        return response()->json([
            'message' => 'Log de sincronización eliminado exitosamente'
        ], 204);
    }
}
