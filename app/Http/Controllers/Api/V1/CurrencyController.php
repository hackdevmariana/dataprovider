<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Currency;
use App\Http\Resources\V1\CurrencyResource;
use App\Http\Requests\StoreCurrencyRequest;
use App\Http\Requests\UpdateCurrencyRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

/**
 * @group Currencies
 *
 * APIs para la gestión de monedas del sistema.
 * Permite crear, consultar y gestionar monedas y tasas de cambio.
 */
class CurrencyController extends Controller
{
    /**
     * Display a listing of currencies
     *
     * Obtiene una lista paginada de todas las monedas.
     *
     * @queryParam page int Número de página. Example: 1
     * @queryParam per_page int Cantidad por página (máx 100). Example: 20
     * @queryParam is_active boolean Filtrar por monedas activas. Example: true
     * @queryParam search string Buscar por código o nombre. Example: EUR
     *
     * @response 200 {
     *   "data": [
     *     {
     *       "id": 1,
     *       "code": "EUR",
     *       "name": "Euro",
     *       "symbol": "€",
     *       "is_active": true,
     *       "exchange_rate": 1.0
     *     }
     *   ],
     *   "meta": {...}
     * }
     *
     * @apiResourceCollection App\Http\Resources\V1\CurrencyResource
     * @apiResourceModel App\Models\Currency
     */
    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'page' => 'sometimes|integer|min:1',
            'per_page' => 'sometimes|integer|min:1|max:100',
            'is_active' => 'sometimes|boolean',
            'search' => 'sometimes|string|max:10'
        ]);

        $query = Currency::query();

        if ($request->has('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        if ($request->has('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('code', 'like', '%' . $request->search . '%')
                  ->orWhere('name', 'like', '%' . $request->search . '%');
            });
        }

        $currencies = $query->orderBy('code')
                           ->paginate($request->get('per_page', 20));

        return response()->json([
            'data' => CurrencyResource::collection($currencies),
            'meta' => [
                'current_page' => $currencies->currentPage(),
                'last_page' => $currencies->lastPage(),
                'per_page' => $currencies->perPage(),
                'total' => $currencies->total(),
            ]
        ]);
    }

    /**
     * Store a newly created currency
     *
     * Crea una nueva moneda en el sistema.
     *
     * @bodyParam code string required Código ISO de la moneda. Example: EUR
     * @bodyParam name string required Nombre de la moneda. Example: Euro
     * @bodyParam symbol string required Símbolo de la moneda. Example: €
     * @bodyParam exchange_rate number required Tasa de cambio base. Example: 1.0
     * @bodyParam is_active boolean Si la moneda está activa. Example: true
     * @bodyParam decimal_places integer Número de decimales. Example: 2
     *
     * @response 201 {
     *   "data": {
     *     "id": 1,
     *     "code": "EUR",
     *     "name": "Euro",
     *     "symbol": "€",
     *     "exchange_rate": 1.0,
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
     * @apiResourceModel App\Models\Currency
     * @authenticated
     */
    public function store(StoreCurrencyRequest $request): JsonResponse
    {
        $data = $request->validated();
        
        $currency = Currency::create($data);

        return response()->json([
            'data' => new CurrencyResource($currency)
        ], 201);
    }

    /**
     * Display the specified currency
     *
     * Obtiene los detalles de una moneda específica.
     *
     * @urlParam currency integer ID de la moneda. Example: 1
     *
     * @response 200 {
     *   "data": {
     *     "id": 1,
     *     "code": "EUR",
     *     "name": "Euro",
     *     "symbol": "€",
     *     "exchange_rate": 1.0,
     *     "is_active": true,
     *     "decimal_places": 2
     *   }
     * }
     *
     * @response 404 {
     *   "message": "Moneda no encontrada"
     * }
     *
     * @apiResourceModel App\Models\Currency
     */
    public function show(Currency $currency): JsonResponse
    {
        return response()->json([
            'data' => new CurrencyResource($currency)
        ]);
    }

    /**
     * Update the specified currency
     *
     * Actualiza una moneda existente.
     *
     * @urlParam currency integer ID de la moneda. Example: 1
     * @bodyParam code string Código ISO de la moneda. Example: EUR
     * @bodyParam name string Nombre de la moneda. Example: Euro Comunitario
     * @bodyParam symbol string Símbolo de la moneda. Example: €
     * @bodyParam exchange_rate number Tasa de cambio base. Example: 1.0
     * @bodyParam is_active boolean Si la moneda está activa. Example: true
     * @bodyParam decimal_places integer Número de decimales. Example: 2
     *
     * @response 200 {
     *   "data": {
     *     "id": 1,
     *     "code": "EUR",
     *     "name": "Euro Comunitario",
     *     "symbol": "€",
     *     "exchange_rate": 1.0,
     *     "is_active": true,
     *     "updated_at": "2024-01-02T00:00:00.000000Z"
     *   }
     * }
     *
     * @response 404 {
     *   "message": "Moneda no encontrada"
     * }
     *
     * @response 422 {
     *   "message": "Los datos proporcionados no son válidos.",
     *   "errors": {...}
     * }
     *
     * @apiResourceModel App\Models\Currency
     * @authenticated
     */
    public function update(UpdateCurrencyRequest $request, Currency $currency): JsonResponse
    {
        $data = $request->validated();
        
        $currency->update($data);

        return response()->json([
            'data' => new CurrencyResource($currency)
        ]);
    }

    /**
     * Remove the specified currency
     *
     * Elimina una moneda del sistema.
     *
     * @urlParam currency integer ID de la moneda. Example: 1
     *
     * @response 204 {
     *   "message": "Moneda eliminada exitosamente"
     * }
     *
     * @response 404 {
     *   "message": "Moneda no encontrada"
     * }
     *
     * @authenticated
     */
    public function destroy(Currency $currency): JsonResponse
    {
        $currency->delete();

        return response()->json([
            'message' => 'Moneda eliminada exitosamente'
        ], 204);
    }
}
