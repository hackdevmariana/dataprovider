<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\ElectricityPrice;
use App\Http\Resources\V1\ElectricityPriceResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * @OA\Tag(
 *     name="Electricity Prices",
 *     description="API for electricity prices (PVPC, spot market)"
 * )
 */
class ElectricityPriceController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/v1/electricity-prices",
     *     summary="Get electricity prices",
     *     tags={"Electricity Prices"},
     *     @OA\Parameter(
     *         name="date",
     *         in="query",
     *         description="Filter by specific date (YYYY-MM-DD)",
     *         @OA\Schema(type="string", format="date", example="2024-08-17")
     *     ),
     *     @OA\Parameter(
     *         name="type",
     *         in="query",
     *         description="Price type filter",
     *         @OA\Schema(type="string", enum={"pvpc", "spot"}, example="pvpc")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="List of electricity prices"
     *     )
     * )
     */
    public function index(Request $request)
    {
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

        $prices = $query->orderBy('date', 'desc')
            ->orderBy('hour', 'asc')
            ->paginate(24);

        return ElectricityPriceResource::collection($prices);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/electricity-prices/{id}",
     *     summary="Get specific electricity price",
     *     tags={"Electricity Prices"},
     *     @OA\Response(response=200, description="Electricity price details")
     * )
     */
    public function show($id)
    {
        $price = ElectricityPrice::with('priceUnit')->findOrFail($id);
        return new ElectricityPriceResource($price);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/electricity-prices/today",
     *     summary="Get today's electricity prices",
     *     tags={"Electricity Prices"},
     *     @OA\Response(response=200, description="Today's hourly electricity prices")
     * )
     */
    public function today(Request $request)
    {
        $query = ElectricityPrice::with('priceUnit')
            ->whereDate('date', today());

        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        $prices = $query->orderBy('hour')->get();
        return ElectricityPriceResource::collection($prices);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/electricity-prices/current-hour",
     *     summary="Get current hour electricity price",
     *     tags={"Electricity Prices"},
     *     @OA\Response(response=200, description="Current hour electricity price")
     * )
     */
    public function currentHour(Request $request)
    {
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
            return response()->json(['message' => 'Price not found for current hour'], 404);
        }

        return new ElectricityPriceResource($price);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/electricity-prices/cheapest-hours",
     *     summary="Get cheapest hours for a date",
     *     tags={"Electricity Prices"},
     *     @OA\Response(response=200, description="Cheapest hours for electricity consumption")
     * )
     */
    public function cheapestHours(Request $request)
    {
        $date = $request->get('date', today()->format('Y-m-d'));
        $hours = min(max((int)$request->get('hours', 6), 1), 24);

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

        return ElectricityPriceResource::collection($prices);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/electricity-prices/daily-summary",
     *     summary="Get daily price summary (min, max, avg)",
     *     tags={"Electricity Prices"},
     *     @OA\Response(response=200, description="Daily price summary statistics")
     * )
     */
    public function dailySummary(Request $request)
    {
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
            return response()->json(['message' => 'No data found for the specified date'], 404);
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