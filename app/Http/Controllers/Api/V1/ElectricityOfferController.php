<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\ElectricityOfferResource;
use App\Models\ElectricityOffer;
use App\Models\EnergyCompany;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use OpenApi\Annotations as OA;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

/**
 * @group Electricity Offers
 *
 * APIs para la gestión de ofertas de electricidad de las empresas energéticas.
 * Permite a los usuarios consultar y comparar diferentes ofertas
 * de tarifas eléctricas disponibles en su zona.
 */
/**
 * @OA\Tag(
 *     name="Ofertas de Electricidad",
 *     description="APIs para la gestión de Ofertas de Electricidad"
 * )
 */
class ElectricityOfferController extends Controller
{
    /**
     * Display a listing of electricity offers
     *
     * Obtiene una lista de ofertas de electricidad con opciones de filtrado.
     *
     * @queryParam company_id int ID de la empresa energética. Example: 1
     * @queryParam offer_type string Tipo de oferta (fixed, variable, indexed, green, night). Example: fixed
     * @queryParam contract_duration int Duración del contrato en meses. Example: 12
     * @queryParam min_power_kw int Potencia mínima en kW. Example: 3.45
     * @queryParam max_power_kw int Potencia máxima en kW. Example: 10
     * @queryParam municipality_id int ID del municipio para filtrado geográfico. Example: 1
     * @queryParam is_green boolean Solo ofertas de energía verde. Example: true
     * @queryParam has_discount boolean Solo ofertas con descuentos. Example: false
     * @queryParam sort string Ordenamiento (price_asc, price_desc, company_name, rating). Example: price_asc
     * @queryParam page int Número de página. Example: 1
     * @queryParam per_page int Cantidad por página (máx 100). Example: 15
     *
     * @apiResourceCollection App\Http\Resources\V1\ElectricityOfferResource
     * @apiResourceModel App\Models\ElectricityOffer
     */
    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'company_id' => 'sometimes|integer|exists:energy_companies,id',
            'offer_type' => 'sometimes|string|in:fixed,variable,indexed,green,night',
            'contract_duration' => 'sometimes|integer|min:1|max:120',
            'min_power_kw' => 'sometimes|numeric|min:0.1',
            'max_power_kw' => 'sometimes|numeric|min:0.1',
            'municipality_id' => 'sometimes|integer|exists:municipalities,id',
            'is_green' => 'sometimes|boolean',
            'has_discount' => 'sometimes|boolean',
            'sort' => 'sometimes|string|in:price_asc,price_desc,company_name,rating',
            'page' => 'sometimes|integer|min:1',
            'per_page' => 'sometimes|integer|min:1|max:100'
        ]);

        $query = ElectricityOffer::with(['company', 'municipality']);

        // Filtros
        if ($request->filled('company_id')) {
            $query->where('company_id', $request->company_id);
        }

        if ($request->filled('offer_type')) {
            $query->where('offer_type', $request->offer_type);
        }

        if ($request->filled('contract_duration')) {
            $query->where('contract_duration', $request->contract_duration);
        }

        if ($request->filled('min_power_kw')) {
            $query->where('max_power_kw', '>=', $request->min_power_kw);
        }

        if ($request->filled('max_power_kw')) {
            $query->where('min_power_kw', '<=', $request->max_power_kw);
        }

        if ($request->filled('municipality_id')) {
            $query->where('municipality_id', $request->municipality_id);
        }

        if ($request->filled('is_green')) {
            $query->where('is_green', $request->boolean('is_green'));
        }

        if ($request->filled('has_discount')) {
            $query->where('has_discount', $request->boolean('has_discount'));
        }

        // Ordenamiento
        $sort = $request->get('sort', 'price_asc');
        switch ($sort) {
            case 'price_desc':
                $query->orderBy('price_per_kwh', 'desc');
                break;
            case 'company_name':
                $query->orderBy('company_id')->orderBy('price_per_kwh', 'asc');
                break;
            case 'rating':
                $query->orderBy('company_rating', 'desc')->orderBy('price_per_kwh', 'asc');
                break;
            default: // price_asc
                $query->orderBy('price_per_kwh', 'asc');
        }

        $perPage = min($request->get('per_page', 15), 100);
        $offers = $query->paginate($perPage);

        return ElectricityOfferResource::collection($offers)->response();
    }

    /**
     * Store a newly created electricity offer
     *
     * Crea una nueva oferta de electricidad. Solo las empresas energéticas
     * pueden crear ofertas.
     *
     * @bodyParam company_id int required ID de la empresa energética. Example: 1
     * @bodyParam offer_name string required Nombre de la oferta. Example: Tarifa Verde Premium
     * @bodyParam offer_type string required Tipo de oferta. Example: fixed
     * @bodyParam price_per_kwh number required Precio por kWh en euros. Example: 0.145
     * @bodyParam fixed_cost_eur number Costo fijo mensual en euros. Example: 15.50
     * @bodyParam contract_duration int Duración del contrato en meses. Example: 12
     * @bodyParam min_power_kw number Potencia mínima en kW. Example: 3.45
     * @bodyParam max_power_kw number Potencia máxima en kW. Example: 10
     * @bodyParam municipality_id int ID del municipio donde aplica. Example: 1
     * @bodyParam is_green boolean Si es energía verde. Example: true
     * @bodyParam has_discount boolean Si tiene descuentos. Example: false
     * @bodyParam discount_details text Detalles de descuentos. Example: 10% descuento primer año
     * @bodyParam conditions text Condiciones especiales. Example: Sin permanencia mínima
     *
     * @response 201 {
     *   "data": {
     *     "id": 1,
     *     "company_id": 1,
     *     "offer_name": "Tarifa Verde Premium",
     *     "offer_type": "fixed",
     *     "price_per_kwh": 0.145,
     *     "status": "active",
     *     "created_at": "2024-01-15T10:00:00.000000Z"
     *   },
     *   "message": "Oferta de electricidad creada exitosamente"
     * }
     *
     * @response 422 {
     *   "message": "Solo las empresas energéticas pueden crear ofertas",
     *   "errors": {
     *     "company_id": ["La empresa especificada no puede crear ofertas"]
     *   }
     * }
     *
     * @authenticated
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'company_id' => 'required|integer|exists:energy_companies,id',
            'offer_name' => 'required|string|max:255',
            'offer_type' => 'required|string|in:fixed,variable,indexed,green,night',
            'price_per_kwh' => 'required|numeric|min:0.001|max:10',
            'fixed_cost_eur' => 'sometimes|numeric|min:0|max:1000',
            'contract_duration' => 'sometimes|integer|min:1|max:120',
            'min_power_kw' => 'sometimes|numeric|min:0.1|max:100',
            'max_power_kw' => 'sometimes|numeric|min:0.1|max:100',
            'municipality_id' => 'sometimes|integer|exists:municipalities,id',
            'is_green' => 'sometimes|boolean',
            'has_discount' => 'sometimes|boolean',
            'discount_details' => 'sometimes|string|max:1000',
            'conditions' => 'sometimes|string|max:2000'
        ]);

        // Verificar que la empresa puede crear ofertas
        $company = EnergyCompany::findOrFail($request->company_id);
        if (!in_array($company->company_type, ['comercializadora', 'cooperativa'])) {
            throw ValidationException::withMessages([
                'company_id' => ['La empresa especificada no puede crear ofertas']
            ]);
        }

        $offer = ElectricityOffer::create([
            'company_id' => $request->company_id,
            'offer_name' => $request->offer_name,
            'offer_type' => $request->offer_type,
            'price_per_kwh' => $request->price_per_kwh,
            'fixed_cost_eur' => $request->fixed_cost_eur ?? 0,
            'contract_duration' => $request->contract_duration ?? 12,
            'min_power_kw' => $request->min_power_kw ?? 0.1,
            'max_power_kw' => $request->max_power_kw ?? 100,
            'municipality_id' => $request->municipality_id,
            'is_green' => $request->boolean('is_green', false),
            'has_discount' => $request->boolean('has_discount', false),
            'discount_details' => $request->discount_details,
            'conditions' => $request->conditions,
            'status' => 'active',
            'is_featured' => false
        ]);

        return (new ElectricityOfferResource($offer))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Display the specified electricity offer
     *
     * Obtiene los detalles de una oferta de electricidad específica.
     *
     * @urlParam electricityOffer int required ID de la oferta. Example: 1
     *
     * @response 200 {
     *   "data": {
     *     "id": 1,
     *     "company_id": 1,
     *     "offer_name": "Tarifa Verde Premium",
     *     "offer_type": "fixed",
     *     "price_per_kwh": 0.145,
     *     "fixed_cost_eur": 15.50,
     *     "contract_duration": 12,
     *     "is_green": true,
     *     "created_at": "2024-01-15T10:00:00.000000Z"
     *   }
     * }
     *
     * @response 404 {
     *   "message": "Oferta de electricidad no encontrada"
     * }
     */
    public function show(ElectricityOffer $electricityOffer): JsonResponse
    {
        $electricityOffer->load(['company', 'municipality']);
        return (new ElectricityOfferResource($electricityOffer))->response();
    }

    /**
     * Update the specified electricity offer
     *
     * Actualiza una oferta de electricidad existente. Solo la empresa propietaria
     * puede modificarla.
     *
     * @urlParam electricityOffer int required ID de la oferta. Example: 1
     * @bodyParam offer_name string Nombre de la oferta. Example: Tarifa Verde Premium Actualizada
     * @bodyParam price_per_kwh number Precio por kWh en euros. Example: 0.142
     * @bodyParam fixed_cost_eur number Costo fijo mensual en euros. Example: 14.50
     * @bodyParam is_green boolean Si es energía verde. Example: true
     * @bodyParam has_discount boolean Si tiene descuentos. Example: true
     * @bodyParam discount_details text Detalles de descuentos. Example: 15% descuento primer año
     *
     * @response 200 {
     *   "data": {
     *     "id": 1,
     *     "offer_name": "Tarifa Verde Premium Actualizada",
     *     "price_per_kwh": 0.142,
     *     "updated_at": "2024-01-15T11:00:00.000000Z"
     *   },
     *   "message": "Oferta de electricidad actualizada exitosamente"
     * }
     *
     * @response 403 {
     *   "message": "No tienes permiso para modificar esta oferta"
     * }
     *
     * @authenticated
     */
    public function update(Request $request, ElectricityOffer $electricityOffer): JsonResponse
    {
        // Verificar permisos (solo la empresa propietaria puede modificar)
        if ($electricityOffer->company_id !== Auth::guard('sanctum')->user()->company_id) {
            return response()->json([
                'message' => 'No tienes permiso para modificar esta oferta'
            ], 403);
        }

        $request->validate([
            'offer_name' => 'sometimes|string|max:255',
            'price_per_kwh' => 'sometimes|numeric|min:0.001|max:10',
            'fixed_cost_eur' => 'sometimes|numeric|min:0|max:1000',
            'is_green' => 'sometimes|boolean',
            'has_discount' => 'sometimes|boolean',
            'discount_details' => 'sometimes|string|max:1000'
        ]);

        $electricityOffer->update($request->only([
            'offer_name', 'price_per_kwh', 'fixed_cost_eur', 
            'is_green', 'has_discount', 'discount_details'
        ]));

        return (new ElectricityOfferResource($electricityOffer))
            ->response()
            ->setStatusCode(200);
    }

    /**
     * Remove the specified electricity offer
     *
     * Elimina una oferta de electricidad. Solo la empresa propietaria
     * puede eliminarla.
     *
     * @urlParam electricityOffer int required ID de la oferta. Example: 1
     *
     * @response 200 {
     *   "message": "Oferta de electricidad eliminada exitosamente"
     * }
     *
     * @response 403 {
     *   "message": "No tienes permiso para eliminar esta oferta"
     * }
     *
     * @authenticated
     */
    public function destroy(ElectricityOffer $electricityOffer): JsonResponse
    {
        // Verificar permisos
        if ($electricityOffer->company_id !== Auth::guard('sanctum')->user()->company_id) {
            return response()->json([
                'message' => 'No tienes permiso para eliminar esta oferta'
            ], 403);
        }

        $electricityOffer->delete();

        return response()->json([
            'message' => 'Oferta de electricidad eliminada exitosamente'
        ]);
    }

    /**
     * Compare electricity offers
     *
     * Compara múltiples ofertas de electricidad para facilitar
     * la toma de decisiones del usuario.
     *
     * @queryParam offers array required IDs de las ofertas a comparar. Example: [1,2,3]
     * @queryParam power_kw number Potencia contratada en kW para el cálculo. Example: 3.45
     * @queryParam monthly_consumption_kwh number Consumo mensual estimado en kWh. Example: 300
     *
     * @response 200 {
     *   "data": [
     *     {
     *       "id": 1,
     *       "company_name": "Empresa Verde",
     *       "monthly_cost": 58.50,
     *       "annual_cost": 702.00,
     *       "savings_vs_average": 15.20
     *     }
     *   ]
     * }
     */
    public function compare(Request $request): JsonResponse
    {
        $request->validate([
            'offers' => 'required|array|min:2|max:5',
            'offers.*' => 'integer|exists:electricity_offers,id',
            'power_kw' => 'sometimes|numeric|min:0.1|max:100',
            'monthly_consumption_kwh' => 'sometimes|numeric|min:1|max:10000'
        ]);

        $offers = ElectricityOffer::whereIn('id', $request->offers)
            ->with('company')
            ->get();

        $powerKw = $request->get('power_kw', 3.45);
        $monthlyConsumption = $request->get('monthly_consumption_kwh', 300);

        $comparison = $offers->map(function ($offer) use ($powerKw, $monthlyConsumption) {
            $monthlyCost = ($offer->price_per_kwh * $monthlyConsumption) + ($offer->fixed_cost_eur ?? 0);
            $annualCost = $monthlyCost * 12;

            return [
                'id' => $offer->id,
                'company_name' => $offer->company->name,
                'offer_name' => $offer->offer_name,
                'offer_type' => $offer->offer_type,
                'price_per_kwh' => $offer->price_per_kwh,
                'fixed_cost_eur' => $offer->fixed_cost_eur ?? 0,
                'monthly_cost' => round($monthlyCost, 2),
                'annual_cost' => round($annualCost, 2),
                'is_green' => $offer->is_green,
                'has_discount' => $offer->has_discount,
                'contract_duration' => $offer->contract_duration
            ];
        });

        return response()->json([
            'data' => $comparison,
            'meta' => [
                'power_kw' => $powerKw,
                'monthly_consumption_kwh' => $monthlyConsumption,
                'total_offers' => $comparison->count()
            ]
        ]);
    }
}
