<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Stat;
use App\Http\Resources\V1\StatResource;
use App\Http\Requests\StoreStatRequest;
use App\Http\Requests\UpdateStatRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

/**
 * @group Statistics
 *
 * APIs para la gestión de estadísticas del sistema.
 * Permite crear, consultar y gestionar estadísticas y métricas.
 */
class StatController extends Controller
{
    /**
     * Display a listing of statistics
     *
     * Obtiene una lista paginada de todas las estadísticas.
     *
     * @queryParam page int Número de página. Example: 1
     * @queryParam per_page int Cantidad por página (máx 100). Example: 20
     * @queryParam type string Filtrar por tipo de estadística (user, system, energy, carbon). Example: energy
     * @queryParam period string Filtrar por período (daily, weekly, monthly, yearly). Example: monthly
     * @queryParam date_from string Filtrar desde fecha (YYYY-MM-DD). Example: 2024-01-01
     * @queryParam date_to string Filtrar hasta fecha (YYYY-MM-DD). Example: 2024-01-31
     *
     * @response 200 {
     *   "data": [
     *     {
     *       "id": 1,
     *       "name": "Consumo Energético",
     *       "type": "energy",
     *       "value": 1250.5,
     *       "unit": "kWh",
     *       "period": "monthly",
     *       "date": "2024-01-01",
     *       "metadata": {...}
     *     }
     *   ],
     *   "meta": {...}
     * }
     *
     * @apiResourceCollection App\Http\Resources\V1\StatResource
     * @apiResourceModel App\Models\Stat
     * @authenticated
     */
    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'page' => 'sometimes|integer|min:1',
            'per_page' => 'sometimes|integer|min:1|max:100',
            'type' => 'sometimes|string|in:user,system,energy,carbon',
            'period' => 'sometimes|string|in:daily,weekly,monthly,yearly',
            'date_from' => 'sometimes|date',
            'date_to' => 'sometimes|date|after_or_equal:date_from'
        ]);

        $query = Stat::query();

        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        if ($request->has('period')) {
            $query->where('period', $request->period);
        }

        if ($request->has('date_from')) {
            $query->where('date', '>=', $request->date_from);
        }

        if ($request->has('date_to')) {
            $query->where('date', '<=', $request->date_to);
        }

        $stats = $query->orderBy('date', 'desc')
                      ->orderBy('created_at', 'desc')
                      ->paginate($request->get('per_page', 20));

        return response()->json([
            'data' => StatResource::collection($stats),
            'meta' => [
                'current_page' => $stats->currentPage(),
                'last_page' => $stats->lastPage(),
                'per_page' => $stats->perPage(),
                'total' => $stats->total(),
            ]
        ]);
    }

    /**
     * Store a newly created statistic
     *
     * Crea una nueva estadística en el sistema.
     *
     * @bodyParam name string required Nombre de la estadística. Example: Consumo Energético
     * @bodyParam type string required Tipo de estadística (user, system, energy, carbon). Example: energy
     * @bodyParam value number required Valor numérico de la estadística. Example: 1250.5
     * @bodyParam unit string Unidad de medida. Example: kWh
     * @bodyParam period string required Período de la estadística (daily, weekly, monthly, yearly). Example: monthly
     * @bodyParam date string required Fecha de la estadística (YYYY-MM-DD). Example: 2024-01-01
     * @bodyParam metadata object Metadatos adicionales (JSON). Example: {"source": "smart_meter", "location": "building_a"}
     * @bodyParam user_id integer ID del usuario (opcional). Example: 1
     *
     * @response 201 {
     *   "data": {
     *     "id": 1,
     *     "name": "Consumo Energético",
     *     "type": "energy",
     *     "value": 1250.5,
     *     "unit": "kWh",
     *     "period": "monthly",
     *     "date": "2024-01-01",
     *     "created_at": "2024-01-01T00:00:00.000000Z"
     *   }
     * }
     *
     * @response 422 {
     *   "message": "Los datos proporcionados no son válidos.",
     *   "errors": {...}
     * }
     *
     * @apiResourceModel App\Models\Stat
     * @authenticated
     */
    public function store(StoreStatRequest $request): JsonResponse
    {
        $data = $request->validated();
        
        $stat = Stat::create($data);

        return response()->json([
            'data' => new StatResource($stat)
        ], 201);
    }

    /**
     * Display the specified statistic
     *
     * Obtiene los detalles de una estadística específica.
     *
     * @urlParam stat integer ID de la estadística. Example: 1
     *
     * @response 200 {
     *   "data": {
     *     "id": 1,
     *     "name": "Consumo Energético",
     *     "type": "energy",
     *     "value": 1250.5,
     *     "unit": "kWh",
     *     "period": "monthly",
     *     "date": "2024-01-01",
     *     "metadata": {
     *       "source": "smart_meter",
     *       "location": "building_a"
     *     },
     *     "created_at": "2024-01-01T00:00:00.000000Z"
     *   }
     * }
     *
     * @response 404 {
     *   "message": "Estadística no encontrada"
     * }
     *
     * @apiResourceModel App\Models\Stat
     * @authenticated
     */
    public function show(Stat $stat): JsonResponse
    {
        return response()->json([
            'data' => new StatResource($stat)
        ]);
    }

    /**
     * Update the specified statistic
     *
     * Actualiza una estadística existente.
     *
     * @urlParam stat integer ID de la estadística. Example: 1
     * @bodyParam name string Nombre de la estadística. Example: Consumo Energético Actualizado
     * @bodyParam type string Tipo de estadística (user, system, energy, carbon). Example: energy
     * @bodyParam value number Valor numérico de la estadística. Example: 1350.0
     * @bodyParam unit string Unidad de medida. Example: kWh
     * @bodyParam period string Período de la estadística (daily, weekly, monthly, yearly). Example: monthly
     * @bodyParam date string Fecha de la estadística (YYYY-MM-DD). Example: 2024-01-01
     * @bodyParam metadata object Metadatos adicionales (JSON). Example: {"source": "smart_meter_v2", "location": "building_a"}
     * @bodyParam user_id integer ID del usuario (opcional). Example: 1
     *
     * @response 200 {
     *   "data": {
     *     "id": 1,
     *     "name": "Consumo Energético Actualizado",
     *     "type": "energy",
     *     "value": 1350.0,
     *     "unit": "kWh",
     *     "period": "monthly",
     *     "date": "2024-01-01",
     *     "updated_at": "2024-01-02T00:00:00.000000Z"
     *   }
     * }
     *
     * @response 404 {
     *   "message": "Estadística no encontrada"
     * }
     *
     * @response 422 {
     *   "message": "Los datos proporcionados no son válidos.",
     *   "errors": {...}
     * }
     *
     * @apiResourceModel App\Models\Stat
     * @authenticated
     */
    public function update(UpdateStatRequest $request, Stat $stat): JsonResponse
    {
        $data = $request->validated();
        
        $stat->update($data);

        return response()->json([
            'data' => new StatResource($stat)
        ]);
    }

    /**
     * Remove the specified statistic
     *
     * Elimina una estadística del sistema.
     *
     * @urlParam stat integer ID de la estadística. Example: 1
     *
     * @response 204 {
     *   "message": "Estadística eliminada exitosamente"
     * }
     *
     * @response 404 {
     *   "message": "Estadística no encontrada"
     * }
     *
     * @authenticated
     */
    public function destroy(Stat $stat): JsonResponse
    {
        $stat->delete();

        return response()->json([
            'message' => 'Estadística eliminada exitosamente'
        ], 204);
    }
}
