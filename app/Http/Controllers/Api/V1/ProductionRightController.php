<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\ProductionRightResource;
use App\Models\ProductionRight;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

/**
 * @group Production Rights
 *
 * APIs para la gestión de derechos de producción energética.
 * Permite a los usuarios comprar, vender y gestionar derechos
 * de producción de energía renovable.
 */
class ProductionRightController extends Controller
{
    /**
     * Display a listing of production rights
     *
     * Obtiene una lista de derechos de producción con opciones de filtrado.
     *
     * @queryParam seller_id int ID del vendedor. Example: 1
     * @queryParam buyer_id int ID del comprador. Example: 2
     * @queryParam energy_type string Tipo de energía (solar, wind, hydro, biomass, geothermal). Example: solar
     * @queryParam status string Estado del derecho (available, reserved, sold, expired, cancelled). Example: available
     * @queryParam min_kwh int Potencia mínima en kWh. Example: 1000
     * @queryParam max_kwh int Potencia máxima en kWh. Example: 10000
     * @queryParam min_price_eur int Precio mínimo en euros. Example: 100
     * @queryParam max_price_eur int Precio máximo en euros. Example: 1000
     * @queryParam location string Ubicación del derecho. Example: Madrid
     * @queryParam sort string Ordenamiento (price_asc, price_desc, kwh_asc, kwh_desc, recent, oldest). Example: price_asc
     * @queryParam page int Número de página. Example: 1
     * @queryParam per_page int Cantidad por página (máx 100). Example: 15
     *
     * @apiResourceCollection App\Http\Resources\V1\ProductionRightResource
     * @apiResourceModel App\Models\ProductionRight
     */
    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'seller_id' => 'sometimes|integer|exists:users,id',
            'buyer_id' => 'sometimes|integer|exists:users,id',
            'energy_type' => 'sometimes|string|in:solar,wind,hydro,biomass,geothermal',
            'status' => 'sometimes|string|in:available,reserved,sold,expired,cancelled',
            'min_kwh' => 'sometimes|integer|min:0',
            'max_kwh' => 'sometimes|integer|min:0',
            'min_price_eur' => 'sometimes|integer|min:0',
            'max_price_eur' => 'sometimes|integer|min:0',
            'location' => 'sometimes|string|max:255',
            'sort' => 'sometimes|string|in:price_asc,price_desc,kwh_asc,kwh_desc,recent,oldest',
            'page' => 'sometimes|integer|min:1',
            'per_page' => 'sometimes|integer|min:1|max:100'
        ]);

        $query = ProductionRight::with(['seller', 'buyer', 'energyInstallation']);

        // Filtros
        if ($request->filled('seller_id')) {
            $query->where('seller_id', $request->seller_id);
        }

        if ($request->filled('buyer_id')) {
            $query->where('buyer_id', $request->buyer_id);
        }

        if ($request->filled('energy_type')) {
            $query->where('energy_type', $request->energy_type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('min_kwh')) {
            $query->where('kwh_amount', '>=', $request->min_kwh);
        }

        if ($request->filled('max_kwh')) {
            $query->where('kwh_amount', '<=', $request->max_kwh);
        }

        if ($request->filled('min_price_eur')) {
            $query->where('price_eur', '>=', $request->min_price_eur);
        }

        if ($request->filled('max_price_eur')) {
            $query->where('price_eur', '<=', $request->max_price_eur);
        }

        if ($request->filled('location')) {
            $query->where('location', 'like', '%' . $request->location . '%');
        }

        // Ordenamiento
        $sort = $request->get('sort', 'recent');
        switch ($sort) {
            case 'price_asc':
                $query->orderBy('price_eur', 'asc');
                break;
            case 'price_desc':
                $query->orderBy('price_eur', 'desc');
                break;
            case 'kwh_asc':
                $query->orderBy('kwh_amount', 'asc');
                break;
            case 'kwh_desc':
                $query->orderBy('kwh_amount', 'desc');
                break;
            case 'oldest':
                $query->orderBy('created_at', 'asc');
                break;
            default: // recent
                $query->orderBy('created_at', 'desc');
        }

        $perPage = min($request->get('per_page', 15), 100);
        $rights = $query->paginate($perPage);

        return ProductionRightResource::collection($rights)->response();
    }

    /**
     * Store a newly created production right
     *
     * Crea un nuevo derecho de producción energética para la venta.
     *
     * @bodyParam energy_installation_id int required ID de la instalación energética. Example: 1
     * @bodyParam kwh_amount int required Cantidad de kWh disponibles. Example: 5000
     * @bodyParam price_eur int required Precio en euros por kWh. Example: 0.15
     * @bodyParam description text Descripción del derecho. Example: Derechos de producción de energía solar de mi instalación residencial
     * @bodyParam location string Ubicación del derecho. Example: Madrid, España
     * @bodyParam energy_type string Tipo de energía. Example: solar
     * @bodyParam validity_period int Período de validez en días. Example: 365
     * @bodyParam terms_conditions text Términos y condiciones. Example: El comprador debe cumplir con las regulaciones locales
     * @bodyParam is_negotiable boolean Si el precio es negociable. Example: true
     * @bodyParam minimum_purchase int Compra mínima en kWh. Example: 100
     *
     * @response 201 {
     *   "data": {
     *     "id": 1,
     *     "seller_id": 1,
     *     "energy_installation_id": 1,
     *     "kwh_amount": 5000,
     *     "price_eur": 0.15,
     *     "status": "available",
     *     "created_at": "2024-01-15T10:00:00.000000Z"
     *   },
     *   "message": "Derecho de producción creado exitosamente"
     * }
     *
     * @response 422 {
     *   "message": "La instalación no pertenece al usuario",
     *   "errors": {
     *     "energy_installation_id": ["La instalación especificada no pertenece al usuario"]
     *   }
     * }
     *
     * @authenticated
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'energy_installation_id' => 'required|integer|exists:energy_installations,id',
            'kwh_amount' => 'required|integer|min:1',
            'price_eur' => 'required|numeric|min:0.01',
            'description' => 'sometimes|string|max:1000',
            'location' => 'sometimes|string|max:255',
            'energy_type' => 'sometimes|string|in:solar,wind,hydro,biomass,geothermal',
            'validity_period' => 'sometimes|integer|min:1|max:3650',
            'terms_conditions' => 'sometimes|string|max:2000',
            'is_negotiable' => 'sometimes|boolean',
            'minimum_purchase' => 'sometimes|integer|min:1'
        ]);

        $userId = Auth::guard('sanctum')->user()->id;

        // Verificar que la instalación pertenece al usuario
        $installation = \App\Models\EnergyInstallation::where('id', $request->energy_installation_id)
            ->where('owner_id', $userId)
            ->first();

        if (!$installation) {
            throw ValidationException::withMessages([
                'energy_installation_id' => ['La instalación especificada no pertenece al usuario']
            ]);
        }

        $right = ProductionRight::create([
            'seller_id' => $userId,
            'energy_installation_id' => $request->energy_installation_id,
            'kwh_amount' => $request->kwh_amount,
            'price_eur' => $request->price_eur,
            'description' => $request->description,
            'location' => $request->location ?? $installation->location,
            'energy_type' => $request->energy_type ?? $installation->energy_type,
            'validity_period' => $request->validity_period ?? 365,
            'terms_conditions' => $request->terms_conditions,
            'is_negotiable' => $request->boolean('is_negotiable', true),
            'minimum_purchase' => $request->minimum_purchase ?? 1,
            'status' => 'available',
            'expires_at' => now()->addDays($request->validity_period ?? 365)
        ]);

        return (new ProductionRightResource($right))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Display the specified production right
     *
     * Obtiene los detalles de un derecho de producción específico.
     *
     * @urlParam id int required ID del derecho de producción. Example: 1
     *
     * @response 200 {
     *   "data": {
     *     "id": 1,
     *     "seller_id": 1,
     *     "energy_installation_id": 1,
     *     "kwh_amount": 5000,
     *     "price_eur": 0.15,
     *     "status": "available",
     *     "description": "Derechos de producción de energía solar...",
     *     "created_at": "2024-01-15T10:00:00.000000Z"
     *   }
     * }
     *
     * @response 404 {
     *   "message": "Derecho de producción no encontrado"
     * }
     */
    public function show(string $id): JsonResponse
    {
        $right = ProductionRight::with(['seller', 'buyer', 'energyInstallation'])
            ->findOrFail($id);

        return (new ProductionRightResource($right))->response();
    }

    /**
     * Update the specified production right
     *
     * Actualiza un derecho de producción existente. Solo el vendedor puede modificarlo
     * y solo si está disponible.
     *
     * @urlParam id int required ID del derecho de producción. Example: 1
     * @bodyParam kwh_amount int Cantidad de kWh disponibles. Example: 4000
     * @bodyParam price_eur int Precio en euros por kWh. Example: 0.18
     * @bodyParam description text Descripción del derecho. Example: Derechos actualizados de producción
     * @bodyParam is_negotiable boolean Si el precio es negociable. Example: false
     * @bodyParam minimum_purchase int Compra mínima en kWh. Example: 200
     *
     * @response 200 {
     *   "data": {
     *     "id": 1,
     *     "kwh_amount": 4000,
     *     "price_eur": 0.18,
     *     "updated_at": "2024-01-15T11:00:00.000000Z"
     *   },
     *   "message": "Derecho de producción actualizado exitosamente"
     * }
     *
     * @response 403 {
     *   "message": "No tienes permiso para modificar este derecho"
     * }
     *
     * @response 422 {
     *   "message": "No se puede modificar un derecho vendido o reservado"
     * }
     *
     * @authenticated
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $right = ProductionRight::findOrFail($id);

        // Verificar permisos
        if ($right->seller_id !== Auth::guard('sanctum')->user()->id) {
            return response()->json([
                'message' => 'No tienes permiso para modificar este derecho'
            ], 403);
        }

        if (!in_array($right->status, ['available', 'draft'])) {
            return response()->json([
                'message' => 'No se puede modificar un derecho vendido o reservado'
            ], 422);
        }

        $request->validate([
            'kwh_amount' => 'sometimes|integer|min:1',
            'price_eur' => 'sometimes|numeric|min:0.01',
            'description' => 'sometimes|string|max:1000',
            'is_negotiable' => 'sometimes|boolean',
            'minimum_purchase' => 'sometimes|integer|min:1'
        ]);

        $right->update($request->only([
            'kwh_amount', 'price_eur', 'description', 'is_negotiable', 'minimum_purchase'
        ]));

        return (new ProductionRightResource($right))
            ->response()
            ->setStatusCode(200);
    }

    /**
     * Remove the specified production right
     *
     * Elimina un derecho de producción. Solo el vendedor puede eliminarlo
     * y solo si está disponible.
     *
     * @urlParam id int required ID del derecho de producción. Example: 1
     *
     * @response 200 {
     *   "message": "Derecho de producción eliminado exitosamente"
     * }
     *
     * @response 403 {
     *   "message": "No tienes permiso para eliminar este derecho"
     * }
     *
     * @response 422 {
     *   "message": "No se puede eliminar un derecho vendido o reservado"
     * }
     *
     * @authenticated
     */
    public function destroy(string $id): JsonResponse
    {
        $right = ProductionRight::findOrFail($id);

        // Verificar permisos
        if ($right->seller_id !== Auth::guard('sanctum')->user()->id) {
            return response()->json([
                'message' => 'No tienes permiso para eliminar este derecho'
            ], 403);
        }

        if (!in_array($right->status, ['available', 'draft'])) {
            return response()->json([
                'message' => 'No se puede eliminar un derecho vendido o reservado'
            ], 422);
        }

        $right->delete();

        return response()->json([
            'message' => 'Derecho de producción eliminado exitosamente'
        ]);
    }

    /**
     * Get marketplace overview
     *
     * Obtiene una vista general del marketplace de derechos de producción.
     *
     * @queryParam energy_type string Filtrar por tipo de energía. Example: solar
     * @queryParam location string Filtrar por ubicación. Example: Madrid
     *
     * @response 200 {
     *   "data": {
     *     "total_rights": 150,
     *     "total_kwh": 750000,
     *     "average_price": 0.18,
     *     "energy_types": {
     *       "solar": 100,
     *       "wind": 30,
     *       "hydro": 20
     *     },
     *     "price_ranges": {
     *       "low": 0.10,
     *       "medium": 0.18,
     *       "high": 0.25
     *     }
     *   }
     * }
     */
    public function marketplace(Request $request): JsonResponse
    {
        $query = ProductionRight::where('status', 'available');

        if ($request->filled('energy_type')) {
            $query->where('energy_type', $request->energy_type);
        }

        if ($request->filled('location')) {
            $query->where('location', 'like', '%' . $request->location . '%');
        }

        $rights = $query->get();

        $totalRights = $rights->count();
        $totalKwh = $rights->sum('kwh_amount');
        $averagePrice = $rights->avg('price_eur');

        $energyTypes = $rights->groupBy('energy_type')
            ->map(function ($group) {
                return $group->count();
            });

        $prices = $rights->pluck('price_eur')->sort();
        $priceRanges = [
            'low' => $prices->first(),
            'medium' => $prices->median(),
            'high' => $prices->last()
        ];

        return response()->json([
            'data' => [
                'total_rights' => $totalRights,
                'total_kwh' => $totalKwh,
                'average_price' => round($averagePrice, 2),
                'energy_types' => $energyTypes,
                'price_ranges' => $priceRanges
            ]
        ]);
    }
}
