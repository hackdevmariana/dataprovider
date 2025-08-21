<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\PriceUnit;
use App\Http\Resources\V1\PriceUnitResource;
use App\Http\Requests\StorePriceUnitRequest;
use App\Http\Requests\UpdatePriceUnitRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

/**
 * @group Price Units
 *
 * APIs para la gestión de unidades de precio del sistema.
 * Permite crear, consultar y gestionar unidades de medida para precios.
 */
class PriceUnitController extends Controller
{
    /**
     * Display a listing of price units
     *
     * Obtiene una lista paginada de todas las unidades de precio.
     *
     * @queryParam page int Número de página. Example: 1
     * @queryParam per_page int Cantidad por página (máx 100). Example: 20
     * @queryParam type string Filtrar por tipo (time, quantity, service). Example: time
     * @queryParam is_active boolean Filtrar por unidades activas. Example: true
     * @queryParam search string Buscar por nombre o símbolo. Example: hora
     *
     * @response 200 {
     *   "data": [
     *     {
     *       "id": 1,
     *       "name": "Por Hora",
     *       "symbol": "/h",
     *       "type": "time",
     *       "is_active": true,
     *       "description": "Precio por hora de servicio"
     *     }
     *   ],
     *   "meta": {...}
     * }
     *
     * @apiResourceCollection App\Http\Resources\V1\PriceUnitResource
     * @apiResourceModel App\Models\PriceUnit
     */
    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'page' => 'sometimes|integer|min:1',
            'per_page' => 'sometimes|integer|min:1|max:100',
            'type' => 'sometimes|string|in:time,quantity,service',
            'is_active' => 'sometimes|boolean',
            'search' => 'sometimes|string|max:255'
        ]);

        $query = PriceUnit::query();

        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        if ($request->has('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        if ($request->has('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('symbol', 'like', '%' . $request->search . '%');
            });
        }

        $priceUnits = $query->orderBy('type')
                           ->orderBy('name')
                           ->paginate($request->get('per_page', 20));

        return response()->json([
            'data' => PriceUnitResource::collection($priceUnits),
            'meta' => [
                'current_page' => $priceUnits->currentPage(),
                'last_page' => $priceUnits->lastPage(),
                'per_page' => $priceUnits->perPage(),
                'total' => $priceUnits->total(),
            ]
        ]);
    }

    /**
     * Store a newly created price unit
     *
     * Crea una nueva unidad de precio en el sistema.
     *
     * @bodyParam name string required Nombre de la unidad. Example: Por Hora
     * @bodyParam symbol string required Símbolo de la unidad. Example: /h
     * @bodyParam type string required Tipo de unidad (time, quantity, service). Example: time
     * @bodyParam description string Descripción de la unidad. Example: Precio por hora de servicio
     * @bodyParam is_active boolean Si la unidad está activa. Example: true
     * @bodyParam conversion_factor number Factor de conversión a unidad base. Example: 1.0
     *
     * @response 201 {
     *   "data": {
     *     "id": 1,
     *     "name": "Por Hora",
     *     "symbol": "/h",
     *     "type": "time",
     *     "description": "Precio por hora de servicio",
     *     "is_active": true,
     *     "created_at": "2024-01-01T00:00:00.000000Z"
     *   }
     * }
     *
     * @response 422 {
     *   "message": "Los datos proporcionados no son válidos.",
     *   "errors": {...}
     * }
     *
     * @apiResourceModel App\Models\PriceUnit
     * @authenticated
     */
    public function store(StorePriceUnitRequest $request): JsonResponse
    {
        $data = $request->validated();
        
        $priceUnit = PriceUnit::create($data);

        return response()->json([
            'data' => new PriceUnitResource($priceUnit)
        ], 201);
    }

    /**
     * Display the specified price unit
     *
     * Obtiene los detalles de una unidad de precio específica.
     *
     * @urlParam priceUnit integer ID de la unidad de precio. Example: 1
     *
     * @response 200 {
     *   "data": {
     *     "id": 1,
     *     "name": "Por Hora",
     *     "symbol": "/h",
     *     "type": "time",
     *     "description": "Precio por hora de servicio",
     *     "is_active": true,
     *     "conversion_factor": 1.0
     *   }
     * }
     *
     * @response 404 {
     *   "message": "Unidad de precio no encontrada"
     * }
     *
     * @apiResourceModel App\Models\PriceUnit
     */
    public function show(PriceUnit $priceUnit): JsonResponse
    {
        return response()->json([
            'data' => new PriceUnitResource($priceUnit)
        ]);
    }

    /**
     * Update the specified price unit
     *
     * Actualiza una unidad de precio existente.
     *
     * @urlParam priceUnit integer ID de la unidad de precio. Example: 1
     * @bodyParam name string Nombre de la unidad. Example: Por Hora de Servicio
     * @bodyParam symbol string Símbolo de la unidad. Example: /h
     * @bodyParam type string Tipo de unidad (time, quantity, service). Example: time
     * @bodyParam description string Descripción de la unidad. Example: Precio por hora de servicio profesional
     * @bodyParam is_active boolean Si la unidad está activa. Example: true
     * @bodyParam conversion_factor number Factor de conversión a unidad base. Example: 1.0
     *
     * @response 200 {
     *   "data": {
     *     "id": 1,
     *     "name": "Por Hora de Servicio",
     *     "symbol": "/h",
     *     "type": "time",
     *     "description": "Precio por hora de servicio profesional",
     *     "is_active": true,
     *     "updated_at": "2024-01-02T00:00:00.000000Z"
     *   }
     * }
     *
     * @response 404 {
     *   "message": "Unidad de precio no encontrada"
     * }
     *
     * @response 422 {
     *   "message": "Los datos proporcionados no son válidos.",
     *   "errors": {...}
     * }
     *
     * @apiResourceModel App\Models\PriceUnit
     * @authenticated
     */
    public function update(UpdatePriceUnitRequest $request, PriceUnit $priceUnit): JsonResponse
    {
        $data = $request->validated();
        
        $priceUnit->update($data);

        return response()->json([
            'data' => new PriceUnitResource($priceUnit)
        ]);
    }

    /**
     * Remove the specified price unit
     *
     * Elimina una unidad de precio del sistema.
     *
     * @urlParam priceUnit integer ID de la unidad de precio. Example: 1
     *
     * @response 204 {
     *   "message": "Unidad de precio eliminada exitosamente"
     * }
     *
     * @response 404 {
     *   "message": "Unidad de precio no encontrada"
     * }
     *
     * @authenticated
     */
    public function destroy(PriceUnit $priceUnit): JsonResponse
    {
        $priceUnit->delete();

        return response()->json([
            'message' => 'Unidad de precio eliminada exitosamente'
        ], 204);
    }
}
