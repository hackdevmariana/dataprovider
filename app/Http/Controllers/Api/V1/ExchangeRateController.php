<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\ExchangeRateResource;
use App\Models\ExchangeRate;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

/**
 * @group Exchange Rates
 *
 * APIs para la gestión de tipos de cambio de divisas y commodities.
 * Permite a los usuarios consultar y gestionar los tipos de cambio
 * para diferentes monedas y activos energéticos.
 */
class ExchangeRateController extends Controller
{
    /**
     * Display a listing of exchange rates
     *
     * Obtiene una lista de tipos de cambio con opciones de filtrado.
     *
     * @queryParam base_currency string Moneda base (EUR, USD, GBP, etc.). Example: EUR
     * @queryParam target_currency string Moneda objetivo (USD, GBP, JPY, etc.). Example: USD
     * @queryParam market_type string Tipo de mercado (forex, crypto, commodity, metal). Example: forex
     * @queryParam source string Fuente de datos (ECB, FED, private, etc.). Example: ECB
     * @queryParam date_from date Fecha de inicio (YYYY-MM-DD). Example: 2024-01-01
     * @queryParam date_to date Fecha de fin (YYYY-MM-DD). Example: 2024-12-31
     * @queryParam min_rate number Tasa mínima. Example: 1.0
     * @queryParam max_rate number Tasa máxima. Example: 2.0
     * @queryParam is_active boolean Solo tasas activas. Example: true
     * @queryParam sort string Ordenamiento (recent, oldest, rate_asc, rate_desc). Example: recent
     * @queryParam page int Número de página. Example: 1
     * @queryParam per_page int Cantidad por página (máx 100). Example: 15
     *
     * @apiResourceCollection App\Http\Resources\V1\ExchangeRateResource
     * @apiResourceModel App\Models\ExchangeRate
     */
    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'base_currency' => 'sometimes|string|max:10',
            'target_currency' => 'sometimes|string|max:10',
            'market_type' => 'sometimes|string|in:forex,crypto,commodity,metal',
            'source' => 'sometimes|string|max:100',
            'date_from' => 'sometimes|date',
            'date_to' => 'sometimes|date',
            'min_rate' => 'sometimes|numeric|min:0',
            'max_rate' => 'sometimes|numeric|min:0',
            'is_active' => 'sometimes|boolean',
            'sort' => 'sometimes|string|in:recent,oldest,rate_asc,rate_desc',
            'page' => 'sometimes|integer|min:1',
            'per_page' => 'sometimes|integer|min:1|max:100'
        ]);

        $query = ExchangeRate::query();

        // Filtros
        if ($request->filled('base_currency')) {
            $query->where('base_currency', strtoupper($request->base_currency));
        }

        if ($request->filled('target_currency')) {
            $query->where('target_currency', strtoupper($request->target_currency));
        }

        if ($request->filled('market_type')) {
            $query->where('market_type', $request->market_type);
        }

        if ($request->filled('source')) {
            $query->where('source', 'like', '%' . $request->source . '%');
        }

        if ($request->filled('date_from')) {
            $query->whereDate('date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('date', '<=', $request->date_to);
        }

        if ($request->filled('min_rate')) {
            $query->where('rate', '>=', $request->min_rate);
        }

        if ($request->filled('max_rate')) {
            $query->where('rate', '<=', $request->max_rate);
        }

        if ($request->filled('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        // Ordenamiento
        $sort = $request->get('sort', 'recent');
        switch ($sort) {
            case 'oldest':
                $query->orderBy('date', 'asc');
                break;
            case 'rate_asc':
                $query->orderBy('rate', 'asc');
                break;
            case 'rate_desc':
                $query->orderBy('rate', 'desc');
                break;
            default: // recent
                $query->orderBy('date', 'desc');
        }

        $perPage = min($request->get('per_page', 15), 100);
        $rates = $query->paginate($perPage);

        return ExchangeRateResource::collection($rates)->response();
    }

    /**
     * Store a newly created exchange rate
     *
     * Crea un nuevo tipo de cambio. Solo administradores pueden crear tasas.
     *
     * @bodyParam base_currency string required Moneda base. Example: EUR
     * @bodyParam target_currency string required Moneda objetivo. Example: USD
     * @bodyParam rate number required Tasa de cambio. Example: 1.0850
     * @bodyParam date date required Fecha de la tasa (YYYY-MM-DD). Example: 2024-01-15
     * @bodyParam market_type string Tipo de mercado. Example: forex
     * @bodyParam source string Fuente de datos. Example: ECB
     * @bodyParam bid_rate number Tasa de compra. Example: 1.0845
     * @bodyParam ask_rate number Tasa de venta. Example: 1.0855
     * @bodyParam volume_24h number Volumen de 24 horas. Example: 1000000
     * @bodyParam change_24h number Cambio en 24 horas. Example: 0.0020
     * @bodyParam is_active boolean Si la tasa está activa. Example: true
     * @bodyParam metadata json Metadatos adicionales. Example: {"spread": 0.0010, "volatility": 0.15}
     *
     * @response 201 {
     *   "data": {
     *     "id": 1,
     *     "base_currency": "EUR",
     *     "target_currency": "USD",
     *     "rate": 1.0850,
     *     "date": "2024-01-15",
     *     "market_type": "forex",
     *     "source": "ECB",
     *     "is_active": true,
     *     "created_at": "2024-01-15T10:00:00.000000Z"
     *   },
     *   "message": "Tipo de cambio creado exitosamente"
     * }
     *
     * @response 422 {
     *   "message": "Ya existe una tasa para esta moneda en esta fecha",
     *   "errors": {
     *     "date": ["Ya existe una tasa para EUR/USD en esta fecha"]
     *   }
     * }
     *
     * @authenticated
     */
    public function store(Request $request): JsonResponse
    {
        // Verificar permisos de administrador
        if (!Auth::guard('sanctum')->user()->hasRole('admin')) {
            return response()->json([
                'message' => 'No tienes permisos para crear tipos de cambio'
            ], 403);
        }

        $request->validate([
            'base_currency' => 'required|string|max:10',
            'target_currency' => 'required|string|max:10|different:base_currency',
            'rate' => 'required|numeric|min:0.0001|max:1000000',
            'date' => 'required|date',
            'market_type' => 'sometimes|string|in:forex,crypto,commodity,metal',
            'source' => 'sometimes|string|max:100',
            'bid_rate' => 'sometimes|numeric|min:0.0001|max:1000000',
            'ask_rate' => 'sometimes|numeric|min:0.0001|max:1000000',
            'volume_24h' => 'sometimes|numeric|min:0',
            'change_24h' => 'sometimes|numeric',
            'is_active' => 'sometimes|boolean',
            'metadata' => 'sometimes|json'
        ]);

        // Verificar que no exista una tasa para la misma moneda en la misma fecha
        $existingRate = ExchangeRate::where('base_currency', strtoupper($request->base_currency))
            ->where('target_currency', strtoupper($request->target_currency))
            ->where('date', $request->date)
            ->first();

        if ($existingRate) {
            throw ValidationException::withMessages([
                'date' => ['Ya existe una tasa para ' . $request->base_currency . '/' . $request->target_currency . ' en esta fecha']
            ]);
        }

        $rate = ExchangeRate::create([
            'base_currency' => strtoupper($request->base_currency),
            'target_currency' => strtoupper($request->target_currency),
            'rate' => $request->rate,
            'date' => $request->date,
            'market_type' => $request->market_type ?? 'forex',
            'source' => $request->source ?? 'API',
            'bid_rate' => $request->bid_rate ?? $request->rate,
            'ask_rate' => $request->ask_rate ?? $request->rate,
            'volume_24h' => $request->volume_24h,
            'change_24h' => $request->change_24h,
            'is_active' => $request->boolean('is_active', true),
            'metadata' => $request->metadata ?? [],
            'created_by' => Auth::guard('sanctum')->user()->id
        ]);

        return (new ExchangeRateResource($rate))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Display the specified exchange rate
     *
     * Obtiene los detalles de un tipo de cambio específico.
     *
     * @urlParam exchangeRate int required ID del tipo de cambio. Example: 1
     *
     * @response 200 {
     *   "data": {
     *     "id": 1,
     *     "base_currency": "EUR",
     *     "target_currency": "USD",
     *     "rate": 1.0850,
     *     "date": "2024-01-15",
     *     "market_type": "forex",
     *     "source": "ECB",
     *     "bid_rate": 1.0845,
     *     "ask_rate": 1.0855,
     *     "created_at": "2024-01-15T10:00:00.000000Z"
     *   }
     * }
     *
     * @response 404 {
     *   "message": "Tipo de cambio no encontrado"
     * }
     */
    public function show(ExchangeRate $exchangeRate): JsonResponse
    {
        return (new ExchangeRateResource($exchangeRate))->response();
    }

    /**
     * Update the specified exchange rate
     *
     * Actualiza un tipo de cambio existente. Solo administradores pueden modificarlo.
     *
     * @urlParam exchangeRate int required ID del tipo de cambio. Example: 1
     * @bodyParam rate number Tasa de cambio. Example: 1.0900
     * @bodyParam bid_rate number Tasa de compra. Example: 1.0895
     * @bodyParam ask_rate number Tasa de venta. Example: 1.0905
     * @bodyParam volume_24h number Volumen de 24 horas. Example: 1200000
     * @bodyParam change_24h number Cambio en 24 horas. Example: 0.0050
     * @bodyParam is_active boolean Si la tasa está activa. Example: true
     *
     * @response 200 {
     *   "data": {
     *     "id": 1,
     *     "rate": 1.0900,
     *     "bid_rate": 1.0895,
     *     "ask_rate": 1.0905,
     *     "updated_at": "2024-01-15T11:00:00.000000Z"
     *   },
     *   "message": "Tipo de cambio actualizado exitosamente"
     * }
     *
     * @response 403 {
     *   "message": "No tienes permisos para modificar tipos de cambio"
     * }
     *
     * @authenticated
     */
    public function update(Request $request, ExchangeRate $exchangeRate): JsonResponse
    {
        // Verificar permisos de administrador
        if (!Auth::guard('sanctum')->user()->hasRole('admin')) {
            return response()->json([
                'message' => 'No tienes permisos para modificar tipos de cambio'
            ], 403);
        }

        $request->validate([
            'rate' => 'sometimes|numeric|min:0.0001|max:1000000',
            'bid_rate' => 'sometimes|numeric|min:0.0001|max:1000000',
            'ask_rate' => 'sometimes|numeric|min:0.0001|max:1000000',
            'volume_24h' => 'sometimes|numeric|min:0',
            'change_24h' => 'sometimes|numeric',
            'is_active' => 'sometimes|boolean'
        ]);

        $exchangeRate->update($request->only([
            'rate', 'bid_rate', 'ask_rate', 'volume_24h', 'change_24h', 'is_active'
        ]));

        return (new ExchangeRateResource($exchangeRate))
            ->response()
            ->setStatusCode(200);
    }

    /**
     * Remove the specified exchange rate
     *
     * Elimina un tipo de cambio. Solo administradores pueden eliminarlo.
     *
     * @urlParam exchangeRate int required ID del tipo de cambio. Example: 1
     *
     * @response 200 {
     *   "message": "Tipo de cambio eliminado exitosamente"
     * }
     *
     * @response 403 {
     *   "message": "No tienes permisos para eliminar tipos de cambio"
     * }
     *
     * @authenticated
     */
    public function destroy(ExchangeRate $exchangeRate): JsonResponse
    {
        // Verificar permisos de administrador
        if (!Auth::guard('sanctum')->user()->hasRole('admin')) {
            return response()->json([
                'message' => 'No tienes permisos para eliminar tipos de cambio'
            ], 403);
        }

        $exchangeRate->delete();

        return response()->json([
            'message' => 'Tipo de cambio eliminado exitosamente'
        ]);
    }

    /**
     * Get latest exchange rate for a currency pair
     *
     * Obtiene la tasa de cambio más reciente para un par de monedas.
     *
     * @urlParam base_currency string required Moneda base. Example: EUR
     * @urlParam target_currency string required Moneda objetivo. Example: USD
     *
     * @response 200 {
     *   "data": {
     *     "id": 1,
     *     "base_currency": "EUR",
     *     "target_currency": "USD",
     *     "rate": 1.0850,
     *     "date": "2024-01-15",
     *     "market_type": "forex",
     *     "source": "ECB"
     *   }
     * }
     *
     * @response 404 {
     *   "message": "No se encontró tasa de cambio para EUR/USD"
     * }
     */
    public function latest(string $baseCurrency, string $targetCurrency): JsonResponse
    {
        $rate = ExchangeRate::where('base_currency', strtoupper($baseCurrency))
            ->where('target_currency', strtoupper($targetCurrency))
            ->where('is_active', true)
            ->orderBy('date', 'desc')
            ->first();

        if (!$rate) {
            return response()->json([
                'message' => 'No se encontró tasa de cambio para ' . strtoupper($baseCurrency) . '/' . strtoupper($targetCurrency)
            ], 404);
        }

        return (new ExchangeRateResource($rate))->response();
    }

    /**
     * Get exchange rate history for a currency pair
     *
     * Obtiene el historial de tasas de cambio para un par de monedas.
     *
     * @urlParam base_currency string required Moneda base. Example: EUR
     * @urlParam target_currency string required Moneda objetivo. Example: USD
     * @queryParam days int Número de días de historial (máx 365). Example: 30
     * @queryParam include_inactive boolean Incluir tasas inactivas. Example: false
     *
     * @response 200 {
     *   "data": [
     *     {
     *       "id": 1,
     *       "date": "2024-01-15",
     *       "rate": 1.0850,
     *       "change_24h": 0.0020
     *     }
     *   ]
     * }
     */
    public function history(string $baseCurrency, string $targetCurrency, Request $request): JsonResponse
    {
        $request->validate([
            'days' => 'sometimes|integer|min:1|max:365',
            'include_inactive' => 'sometimes|boolean'
        ]);

        $days = $request->get('days', 30);
        $includeInactive = $request->boolean('include_inactive', false);

        $query = ExchangeRate::where('base_currency', strtoupper($baseCurrency))
            ->where('target_currency', strtoupper($targetCurrency))
            ->where('date', '>=', now()->subDays($days));

        if (!$includeInactive) {
            $query->where('is_active', true);
        }

        $rates = $query->orderBy('date', 'asc')->get();

        return ExchangeRateResource::collection($rates)->response();
    }

    /**
     * Get exchange rate statistics
     *
     * Obtiene estadísticas de tasas de cambio para un período.
     *
     * @queryParam base_currency string Moneda base. Example: EUR
     * @queryParam target_currency string Moneda objetivo. Example: USD
     * @queryParam days int Número de días (máx 365). Example: 30
     * @queryParam market_type string Tipo de mercado. Example: forex
     *
     * @response 200 {
     *   "data": {
     *     "total_rates": 30,
     *     "average_rate": 1.0850,
     *     "min_rate": 1.0800,
     *     "max_rate": 1.0900,
     *     "volatility": 0.015,
     *     "trend": "increasing"
     *   }
     * }
     */
    public function statistics(Request $request): JsonResponse
    {
        $request->validate([
            'base_currency' => 'sometimes|string|max:10',
            'target_currency' => 'sometimes|string|max:10',
            'days' => 'sometimes|integer|min:1|max:365',
            'market_type' => 'sometimes|string|in:forex,crypto,commodity,metal'
        ]);

        $query = ExchangeRate::where('is_active', true);

        if ($request->filled('base_currency')) {
            $query->where('base_currency', strtoupper($request->base_currency));
        }

        if ($request->filled('target_currency')) {
            $query->where('target_currency', strtoupper($request->target_currency));
        }

        if ($request->filled('market_type')) {
            $query->where('market_type', $request->market_type);
        }

        $days = $request->get('days', 30);
        $query->where('date', '>=', now()->subDays($days));

        $rates = $query->get();

        $totalRates = $rates->count();
        $averageRate = $rates->avg('rate');
        $minRate = $rates->min('rate');
        $maxRate = $rates->max('rate');

        // Calcular volatilidad (desviación estándar)
        $variance = $rates->map(function ($rate) use ($averageRate) {
            return pow($rate->rate - $averageRate, 2);
        })->avg();
        $volatility = sqrt($variance);

        // Determinar tendencia
        $firstHalf = $rates->where('date', '<=', now()->subDays(ceil($days / 2)));
        $secondHalf = $rates->where('date', '>', now()->subDays(ceil($days / 2)));
        
        $firstHalfAvg = $firstHalf->avg('rate');
        $secondHalfAvg = $secondHalf->avg('rate');
        
        $trend = 'stable';
        if ($secondHalfAvg > $firstHalfAvg * 1.01) {
            $trend = 'increasing';
        } elseif ($secondHalfAvg < $firstHalfAvg * 0.99) {
            $trend = 'decreasing';
        }

        return response()->json([
            'data' => [
                'total_rates' => $totalRates,
                'average_rate' => round($averageRate, 6),
                'min_rate' => round($minRate, 6),
                'max_rate' => round($maxRate, 6),
                'volatility' => round($volatility, 6),
                'trend' => $trend,
                'period_days' => $days
            ]
        ]);
    }
}
