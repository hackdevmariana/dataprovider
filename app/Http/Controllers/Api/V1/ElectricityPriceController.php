<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\ElectricityPrice;
use App\Http\Resources\V1\ElectricityPriceResource;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

/**
 * @group Electricity Prices
 *
 * APIs para la gestión de precios de electricidad (PVPC, mercado spot).
 * Permite consultar precios históricos, actuales y estadísticas de precios.
 */
class ElectricityPriceController extends Controller
{
    /**
     * Display a listing of electricity prices
     *
     * Obtiene una lista de precios de electricidad con opciones de filtrado.
     *
     * @queryParam date string Filtrar por fecha específica (YYYY-MM-DD). Example: 2024-08-17
     * @queryParam type string Tipo de precio (pvpc, spot). Example: pvpc
     * @queryParam start_date string Fecha de inicio para rango (YYYY-MM-DD). Example: 2024-08-01
     * @queryParam end_date string Fecha de fin para rango (YYYY-MM-DD). Example: 2024-08-31
     * @queryParam page int Número de página. Example: 1
     * @queryParam per_page int Cantidad por página (máx 100). Example: 24
     *
     * @response 200 {
     *   "data": [
     *     {
     *       "id": 1,
     *       "date": "2024-08-17",
     *       "hour": 0,
     *       "price_eur_mwh": 45.23,
     *       "type": "pvpc",
     *       "price_unit": {
     *         "id": 1,
     *         "name": "EUR/MWh"
     *       }
     *     }
     *   ],
     *   "meta": {
     *     "current_page": 1,
     *     "last_page": 1,
     *     "per_page": 24,
     *     "total": 24
     *   }
     * }
     *
     * @apiResourceCollection App\Http\Resources\V1\ElectricityPriceResource
     * @apiResourceModel App\Models\ElectricityPrice
     */
    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'date' => 'sometimes|date',
            'type' => 'sometimes|string|in:pvpc,spot',
            'start_date' => 'sometimes|date',
            'end_date' => 'sometimes|date|after_or_equal:start_date',
            'page' => 'sometimes|integer|min:1',
            'per_page' => 'sometimes|integer|min:1|max:100'
        ]);

        $query = ElectricityPrice::with('priceUnit');

        if ($request->has('date')) {
            $query->whereDate('date', $request->date);
        }

        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        if ($request->has('start_date')) {
            $query->whereDate('date', '>=', $request->start_date);
        }

        if ($request->has('end_date')) {
            $query->whereDate('date', '<=', $request->end_date);
        }

        $perPage = min($request->get('per_page', 24), 100);
        $prices = $query->orderBy('date', 'desc')
            ->orderBy('hour', 'asc')
            ->paginate($perPage);

        return response()->json([
            'data' => ElectricityPriceResource::collection($prices),
            'meta' => [
                'current_page' => $prices->currentPage(),
                'last_page' => $prices->lastPage(),
                'per_page' => $prices->perPage(),
                'total' => $prices->total(),
            ]
        ]);
    }

    /**
     * Display the specified electricity price
     *
     * Obtiene los detalles de un precio de electricidad específico.
     *
     * @urlParam id int ID del precio de electricidad. Example: 1
     *
     * @response 200 {
     *   "data": {
     *     "id": 1,
     *     "date": "2024-08-17",
     *     "hour": 0,
     *     "price_eur_mwh": 45.23,
     *     "type": "pvpc",
     *     "price_unit": {
     *       "id": 1,
     *       "name": "EUR/MWh"
     *     }
     *   }
     * }
     *
     * @response 404 {
     *   "message": "Precio de electricidad no encontrado"
     * }
     *
     * @apiResourceModel App\Models\ElectricityPrice
     */
    public function show($id): JsonResponse
    {
        $price = ElectricityPrice::with('priceUnit')->findOrFail($id);
        
        return response()->json([
            'data' => new ElectricityPriceResource($price)
        ]);
    }

    /**
     * Get today's electricity prices
     *
     * Obtiene los precios de electricidad de hoy por hora.
     *
     * @queryParam type string Tipo de precio (pvpc, spot). Example: pvpc
     *
     * @response 200 {
     *   "data": [
     *     {
     *       "id": 1,
     *       "date": "2024-08-17",
     *       "hour": 0,
     *       "price_eur_mwh": 45.23,
     *       "type": "pvpc"
     *     }
     *   ]
     * }
     *
     * @apiResourceCollection App\Http\Resources\V1\ElectricityPriceResource
     * @apiResourceModel App\Models\ElectricityPrice
     */
    public function today(Request $request): JsonResponse
    {
        $request->validate([
            'type' => 'sometimes|string|in:pvpc,spot'
        ]);

        $query = ElectricityPrice::with('priceUnit')
            ->whereDate('date', today());

        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        $prices = $query->orderBy('hour')->get();
        
        return response()->json([
            'data' => ElectricityPriceResource::collection($prices)
        ]);
    }

    /**
     * Get current hour electricity price
     *
     * Obtiene el precio de electricidad de la hora actual.
     *
     * @queryParam type string Tipo de precio (pvpc, spot). Example: pvpc
     *
     * @response 200 {
     *   "data": {
     *     "id": 1,
     *     "date": "2024-08-17",
     *     "hour": 14,
     *     "price_eur_mwh": 67.89,
     *     "type": "pvpc"
     *   }
     * }
     *
     * @response 404 {
     *   "message": "Precio no encontrado para la hora actual"
     * }
     *
     * @apiResourceModel App\Models\ElectricityPrice
     */
    public function currentHour(Request $request): JsonResponse
    {
        $request->validate([
            'type' => 'sometimes|string|in:pvpc,spot'
        ]);

        $currentHour = now()->hour;
        
        $query = ElectricityPrice::with('priceUnit')
            ->whereDate('date', today())
            ->where('hour', $currentHour);

        if ($request->has('type')) {
            $query->where('type', $request->type);
        } else {
            $query->where('type', 'pvpc');
        }

        $price = $query->first();

        if (!$price) {
            return response()->json([
                'message' => 'Precio no encontrado para la hora actual'
            ], 404);
        }

        return response()->json([
            'data' => new ElectricityPriceResource($price)
        ]);
    }

    /**
     * Get cheapest hours for a date
     *
     * Obtiene las horas más baratas para consumo de electricidad en una fecha.
     *
     * @queryParam date string Fecha para consultar (YYYY-MM-DD). Example: 2024-08-17
     * @queryParam hours int Número de horas a retornar (1-24). Example: 6
     * @queryParam type string Tipo de precio (pvpc, spot). Example: pvpc
     *
     * @response 200 {
     *   "data": [
     *     {
     *       "id": 1,
     *       "date": "2024-08-17",
     *       "hour": 3,
     *       "price_eur_mwh": 25.45,
     *       "type": "pvpc"
     *     }
     *   ]
     * }
     *
     * @apiResourceCollection App\Http\Resources\V1\ElectricityPriceResource
     * @apiResourceModel App\Models\ElectricityPrice
     */
    public function cheapestHours(Request $request): JsonResponse
    {
        $request->validate([
            'date' => 'sometimes|date',
            'hours' => 'sometimes|integer|min:1|max:24',
            'type' => 'sometimes|string|in:pvpc,spot',
        ]);

        $date = $request->get('date', today()->format('Y-m-d'));
        $hours = (int)$request->get('hours', 6);

        $query = ElectricityPrice::with('priceUnit')
            ->whereDate('date', $date)
            ->whereNotNull('hour');

        if ($request->has('type')) {
            $query->where('type', $request->type);
        } else {
            $query->where('type', 'pvpc');
        }

        $prices = $query->orderBy('price_eur_mwh', 'asc')
            ->limit($hours)
            ->get();

        return response()->json([
            'data' => ElectricityPriceResource::collection($prices)
        ]);
    }

    /**
     * Get daily price summary
     *
     * Obtiene un resumen diario de precios (mínimo, máximo, promedio).
     *
     * @queryParam date string Fecha para consultar (YYYY-MM-DD). Example: 2024-08-17
     * @queryParam type string Tipo de precio (pvpc, spot). Example: pvpc
     *
     * @response 200 {
     *   "date": "2024-08-17",
     *   "type": "pvpc",
     *   "summary": {
     *     "min_price_eur_mwh": 25.45,
     *     "max_price_eur_mwh": 89.67,
     *     "avg_price_eur_mwh": 57.34,
     *     "min_price_eur_kwh": 0.02545,
     *     "max_price_eur_kwh": 0.08967,
     *     "avg_price_eur_kwh": 0.05734,
     *     "total_hours": 24
     *   }
     * }
     *
     * @response 404 {
     *   "message": "No se encontraron datos para la fecha especificada"
     * }
     */
    public function dailySummary(Request $request): JsonResponse
    {
        $request->validate([
            'date' => 'sometimes|date',
            'type' => 'sometimes|string|in:pvpc,spot'
        ]);

        $date = $request->get('date', today()->format('Y-m-d'));
        $type = $request->get('type', 'pvpc');

        $summary = ElectricityPrice::whereDate('date', $date)
            ->where('type', $type)
            ->whereNotNull('hour')
            ->select([
                DB::raw('MIN(price_eur_mwh) as min_price'),
                DB::raw('MAX(price_eur_mwh) as max_price'),
                DB::raw('AVG(price_eur_mwh) as avg_price'),
                DB::raw('COUNT(*) as total_hours'),
                'date',
                'type'
            ])
            ->groupBy('date', 'type')
            ->first();

        if (!$summary) {
            return response()->json([
                'message' => 'No se encontraron datos para la fecha especificada'
            ], 404);
        }

        return response()->json([
            'date' => $date,
            'type' => $type,
            'summary' => [
                'min_price_eur_mwh' => (float) $summary->min_price,
                'max_price_eur_mwh' => (float) $summary->max_price,
                'avg_price_eur_mwh' => (float) $summary->avg_price,
                'min_price_eur_kwh' => (float) $summary->min_price / 1000,
                'max_price_eur_kwh' => (float) $summary->max_price / 1000,
                'avg_price_eur_kwh' => (float) $summary->avg_price / 1000,
                'total_hours' => $summary->total_hours,
            ],
        ]);
    }
}