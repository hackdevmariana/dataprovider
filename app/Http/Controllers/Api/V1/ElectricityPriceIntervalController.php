<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\ElectricityPriceIntervalResource;
use App\Models\ElectricityPriceInterval;
use App\Models\ElectricityPrice;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use OpenApi\Annotations as OA;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

/**
 * @group Electricity Price Intervals
 *
 * APIs para la gestión de intervalos de precios de electricidad.
 * Permite a los usuarios consultar y gestionar los precios
 * de electricidad por intervalos horarios y días.
 */
/**
 * @OA\Tag(
 *     name="Intervalos de Precios",
 *     description="APIs para la gestión de Intervalos de Precios"
 * )
 */
class ElectricityPriceIntervalController extends Controller
{
    /**
     * Display a listing of electricity price intervals
     *
     * Obtiene una lista de intervalos de precios de electricidad con opciones de filtrado.
     *
     * @queryParam price_id int ID del precio de electricidad. Example: 1
     * @queryParam date date Fecha específica (YYYY-MM-DD). Example: 2024-01-15
     * @queryParam start_hour int Hora de inicio (0-23). Example: 8
     * @queryParam end_hour int Hora de fin (0-23). Example: 12
     * @queryParam price_type string Tipo de precio (peak, valley, flat). Example: peak
     * @queryParam min_price_eur number Precio mínimo en euros. Example: 0.10
     * @queryParam max_price_eur number Precio máximo en euros. Example: 0.25
     * @queryParam is_holiday boolean Solo intervalos de días festivos. Example: false
     * @queryParam sort string Ordenamiento (recent, oldest, price_asc, price_desc, hour_asc). Example: recent
     * @queryParam page int Número de página. Example: 1
     * @queryParam per_page int Cantidad por página (máx 100). Example: 15
     *
     * @apiResourceCollection App\Http\Resources\V1\ElectricityPriceIntervalResource
     * @apiResourceModel App\Models\ElectricityPriceInterval
     */
    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'price_id' => 'sometimes|integer|exists:electricity_prices,id',
            'date' => 'sometimes|date',
            'start_hour' => 'sometimes|integer|min:0|max:23',
            'end_hour' => 'sometimes|integer|min:0|max:23',
            'price_type' => 'sometimes|string|in:peak,valley,flat',
            'min_price_eur' => 'sometimes|numeric|min:0',
            'max_price_eur' => 'sometimes|numeric|min:0',
            'is_holiday' => 'sometimes|boolean',
            'sort' => 'sometimes|string|in:recent,oldest,price_asc,price_desc,hour_asc',
            'page' => 'sometimes|integer|min:1',
            'per_page' => 'sometimes|integer|min:1|max:100'
        ]);

        $query = ElectricityPriceInterval::with(['electricityPrice']);

        // Filtros
        if ($request->filled('price_id')) {
            $query->where('electricity_price_id', $request->price_id);
        }

        if ($request->filled('date')) {
            $query->whereDate('date', $request->date);
        }

        if ($request->filled('start_hour')) {
            $query->where('start_hour', '>=', $request->start_hour);
        }

        if ($request->filled('end_hour')) {
            $query->where('end_hour', '<=', $request->end_hour);
        }

        if ($request->filled('price_type')) {
            $query->where('price_type', $request->price_type);
        }

        if ($request->filled('min_price_eur')) {
            $query->where('price_eur', '>=', $request->min_price_eur);
        }

        if ($request->filled('max_price_eur')) {
            $query->where('price_eur', '<=', $request->max_price_eur);
        }

        if ($request->filled('is_holiday')) {
            $query->where('is_holiday', $request->boolean('is_holiday'));
        }

        // Ordenamiento
        $sort = $request->get('sort', 'recent');
        switch ($sort) {
            case 'oldest':
                $query->orderBy('date', 'asc')->orderBy('start_hour', 'asc');
                break;
            case 'price_asc':
                $query->orderBy('price_eur', 'asc');
                break;
            case 'price_desc':
                $query->orderBy('price_eur', 'desc');
                break;
            case 'hour_asc':
                $query->orderBy('start_hour', 'asc')->orderBy('date', 'asc');
                break;
            default: // recent
                $query->orderBy('date', 'desc')->orderBy('start_hour', 'asc');
        }

        $perPage = min($request->get('per_page', 15), 100);
        $intervals = $query->paginate($perPage);

        return ElectricityPriceIntervalResource::collection($intervals)->response();
    }

    /**
     * Store a newly created electricity price interval
     *
     * Crea un nuevo intervalo de precio de electricidad. Solo administradores pueden crear intervalos.
     *
     * @bodyParam electricity_price_id int required ID del precio de electricidad. Example: 1
     * @bodyParam date date required Fecha del intervalo (YYYY-MM-DD). Example: 2024-01-15
     * @bodyParam start_hour int required Hora de inicio (0-23). Example: 8
     * @bodyParam end_hour int required Hora de fin (0-23). Example: 12
     * @bodyParam price_eur number required Precio en euros por kWh. Example: 0.18
     * @bodyParam price_type string Tipo de precio. Example: peak
     * @bodyParam is_holiday boolean Si es día festivo. Example: false
     * @bodyParam description text Descripción del intervalo. Example: Horario punta de la mañana
     * @bodyParam demand_factor number Factor de demanda (0.1-2.0). Example: 1.5
     * @bodyParam renewable_percentage number Porcentaje de energía renovable. Example: 45.5
     *
     * @response 201 {
     *   "data": {
     *     "id": 1,
     *     "electricity_price_id": 1,
     *     "date": "2024-01-15",
     *     "start_hour": 8,
     *     "end_hour": 12,
     *     "price_eur": 0.18,
     *     "price_type": "peak",
     *     "created_at": "2024-01-15T10:00:00.000000Z"
     *   },
     *   "message": "Intervalo de precio creado exitosamente"
     * }
     *
     * @response 422 {
     *   "message": "Las horas de inicio y fin no son válidas",
     *   "errors": {
     *     "end_hour": ["La hora de fin debe ser mayor que la hora de inicio"]
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
                'message' => 'No tienes permisos para crear intervalos de precios'
            ], 403);
        }

        $request->validate([
            'electricity_price_id' => 'required|integer|exists:electricity_prices,id',
            'date' => 'required|date',
            'start_hour' => 'required|integer|min:0|max:23',
            'end_hour' => 'required|integer|min:0|max:23',
            'price_eur' => 'required|numeric|min:0.001|max:10',
            'price_type' => 'sometimes|string|in:peak,valley,flat',
            'is_holiday' => 'sometimes|boolean',
            'description' => 'sometimes|string|max:500',
            'demand_factor' => 'sometimes|numeric|min:0.1|max:2.0',
            'renewable_percentage' => 'sometimes|numeric|min:0|max:100'
        ]);

        // Verificar que las horas sean válidas
        if ($request->start_hour >= $request->end_hour) {
            throw ValidationException::withMessages([
                'end_hour' => ['La hora de fin debe ser mayor que la hora de inicio']
            ]);
        }

        // Verificar que no haya solapamiento de intervalos
        $existingInterval = ElectricityPriceInterval::where('electricity_price_id', $request->electricity_price_id)
            ->where('date', $request->date)
            ->where(function ($query) use ($request) {
                $query->whereBetween('start_hour', [$request->start_hour, $request->end_hour - 1])
                      ->orWhereBetween('end_hour', [$request->start_hour + 1, $request->end_hour])
                      ->orWhere(function ($q) use ($request) {
                          $q->where('start_hour', '<=', $request->start_hour)
                            ->where('end_hour', '>=', $request->end_hour);
                      });
            })
            ->first();

        if ($existingInterval) {
            throw ValidationException::withMessages([
                'start_hour' => ['Existe un solapamiento con otro intervalo en la misma fecha'],
                'end_hour' => ['Existe un solapamiento con otro intervalo en la misma fecha']
            ]);
        }

        $interval = ElectricityPriceInterval::create([
            'electricity_price_id' => $request->electricity_price_id,
            'date' => $request->date,
            'start_hour' => $request->start_hour,
            'end_hour' => $request->end_hour,
            'price_eur' => $request->price_eur,
            'price_type' => $request->price_type ?? $this->determinePriceType($request->start_hour, $request->end_hour),
            'is_holiday' => $request->boolean('is_holiday', false),
            'description' => $request->description,
            'demand_factor' => $request->demand_factor ?? 1.0,
            'renewable_percentage' => $request->renewable_percentage,
            'created_by' => Auth::guard('sanctum')->user()->id
        ]);

        return (new ElectricityPriceIntervalResource($interval))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Display the specified electricity price interval
     *
     * Obtiene los detalles de un intervalo de precio específico.
     *
     * @urlParam electricityPriceInterval int required ID del intervalo. Example: 1
     *
     * @response 200 {
     *   "data": {
     *     "id": 1,
     *     "electricity_price_id": 1,
     *     "date": "2024-01-15",
     *     "start_hour": 8,
     *     "end_hour": 12,
     *     "price_eur": 0.18,
     *     "price_type": "peak",
     *     "is_holiday": false,
     *     "created_at": "2024-01-15T10:00:00.000000Z"
     *   }
     * }
     *
     * @response 404 {
     *   "message": "Intervalo de precio no encontrado"
     * }
     */
    public function show(ElectricityPriceInterval $electricityPriceInterval): JsonResponse
    {
        $electricityPriceInterval->load(['electricityPrice']);
        return (new ElectricityPriceIntervalResource($electricityPriceInterval))->response();
    }

    /**
     * Update the specified electricity price interval
     *
     * Actualiza un intervalo de precio existente. Solo administradores pueden modificarlo.
     *
     * @urlParam electricityPriceInterval int required ID del intervalo. Example: 1
     * @bodyParam price_eur number Precio en euros por kWh. Example: 0.20
     * @bodyParam price_type string Tipo de precio. Example: peak
     * @bodyParam description text Descripción del intervalo. Example: Horario punta actualizado
     * @bodyParam demand_factor number Factor de demanda. Example: 1.6
     * @bodyParam renewable_percentage number Porcentaje de energía renovable. Example: 50.0
     *
     * @response 200 {
     *   "data": {
     *     "id": 1,
     *     "price_eur": 0.20,
     *     "price_type": "peak",
     *     "updated_at": "2024-01-15T11:00:00.000000Z"
     *   },
     *   "message": "Intervalo de precio actualizado exitosamente"
     * }
     *
     * @response 403 {
     *   "message": "No tienes permisos para modificar intervalos de precios"
     * }
     *
     * @authenticated
     */
    public function update(Request $request, ElectricityPriceInterval $electricityPriceInterval): JsonResponse
    {
        // Verificar permisos de administrador
        if (!Auth::guard('sanctum')->user()->hasRole('admin')) {
            return response()->json([
                'message' => 'No tienes permisos para modificar intervalos de precios'
            ], 403);
        }

        $request->validate([
            'price_eur' => 'sometimes|numeric|min:0.001|max:10',
            'price_type' => 'sometimes|string|in:peak,valley,flat',
            'description' => 'sometimes|string|max:500',
            'demand_factor' => 'sometimes|numeric|min:0.1|max:2.0',
            'renewable_percentage' => 'sometimes|numeric|min:0|max:100'
        ]);

        $electricityPriceInterval->update($request->only([
            'price_eur', 'price_type', 'description', 'demand_factor', 'renewable_percentage'
        ]));

        return (new ElectricityPriceIntervalResource($electricityPriceInterval))
            ->response()
            ->setStatusCode(200);
    }

    /**
     * Remove the specified electricity price interval
     *
     * Elimina un intervalo de precio. Solo administradores pueden eliminarlo.
     *
     * @urlParam electricityPriceInterval int required ID del intervalo. Example: 1
     *
     * @response 200 {
     *   "message": "Intervalo de precio eliminado exitosamente"
     * }
     *
     * @response 403 {
     *   "message": "No tienes permisos para eliminar intervalos de precios"
     * }
     *
     * @authenticated
     */
    public function destroy(ElectricityPriceInterval $electricityPriceInterval): JsonResponse
    {
        // Verificar permisos de administrador
        if (!Auth::guard('sanctum')->user()->hasRole('admin')) {
            return response()->json([
                'message' => 'No tienes permisos para eliminar intervalos de precios'
            ], 403);
        }

        $electricityPriceInterval->delete();

        return response()->json([
            'message' => 'Intervalo de precio eliminado exitosamente'
        ]);
    }

    /**
     * Get price intervals for a specific date
     *
     * Obtiene todos los intervalos de precios para una fecha específica.
     *
     * @urlParam date string required Fecha (YYYY-MM-DD). Example: 2024-01-15
     * @queryParam price_id int ID del precio de electricidad. Example: 1
     * @queryParam include_holiday boolean Incluir intervalos de días festivos. Example: true
     *
     * @response 200 {
     *   "data": [
     *     {
     *       "id": 1,
     *       "start_hour": 0,
     *       "end_hour": 8,
     *       "price_eur": 0.12,
     *       "price_type": "valley"
     *     }
     *   ]
     * }
     */
    public function byDate(string $date): JsonResponse
    {
        $request = request();
        $request->validate([
            'price_id' => 'sometimes|integer|exists:electricity_prices,id',
            'include_holiday' => 'sometimes|boolean'
        ]);

        $query = ElectricityPriceInterval::where('date', $date)
            ->with(['electricityPrice']);

        if ($request->filled('price_id')) {
            $query->where('electricity_price_id', $request->price_id);
        }

        if (!$request->boolean('include_holiday', true)) {
            $query->where('is_holiday', false);
        }

        $intervals = $query->orderBy('start_hour', 'asc')->get();

        return ElectricityPriceIntervalResource::collection($intervals)->response();
    }

    /**
     * Get price intervals for a specific hour range
     *
     * Obtiene los intervalos de precios para un rango de horas específico.
     *
     * @queryParam start_hour int required Hora de inicio (0-23). Example: 8
     * @queryParam end_hour int required Hora de fin (0-23). Example: 18
     * @queryParam date_from date Fecha de inicio (YYYY-MM-DD). Example: 2024-01-01
     * @queryParam date_to date Fecha de fin (YYYY-MM-DD). Example: 2024-01-31
     * @queryParam price_id int ID del precio de electricidad. Example: 1
     *
     * @response 200 {
     *   "data": [
     *     {
     *       "id": 1,
     *       "date": "2024-01-15",
     *       "start_hour": 8,
     *       "end_hour": 12,
     *       "price_eur": 0.18,
     *       "price_type": "peak"
     *     }
     *   ]
     * }
     */
    public function byHourRange(Request $request): JsonResponse
    {
        $request->validate([
            'start_hour' => 'required|integer|min:0|max:23',
            'end_hour' => 'required|integer|min:0|max:23',
            'date_from' => 'sometimes|date',
            'date_to' => 'sometimes|date',
            'price_id' => 'sometimes|integer|exists:electricity_prices,id'
        ]);

        if ($request->start_hour >= $request->end_hour) {
            return response()->json([
                'message' => 'La hora de fin debe ser mayor que la hora de inicio'
            ], 422);
        }

        $query = ElectricityPriceInterval::where(function ($q) use ($request) {
            $q->whereBetween('start_hour', [$request->start_hour, $request->end_hour - 1])
              ->orWhereBetween('end_hour', [$request->start_hour + 1, $request->end_hour])
              ->orWhere(function ($subQ) use ($request) {
                  $subQ->where('start_hour', '<=', $request->start_hour)
                       ->where('end_hour', '>=', $request->end_hour);
              });
        });

        if ($request->filled('date_from')) {
            $query->whereDate('date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('date', '<=', $request->date_to);
        }

        if ($request->filled('price_id')) {
            $query->where('electricity_price_id', $request->price_id);
        }

        $intervals = $query->with(['electricityPrice'])
            ->orderBy('date', 'asc')
            ->orderBy('start_hour', 'asc')
            ->get();

        return ElectricityPriceIntervalResource::collection($intervals)->response();
    }

    /**
     * Get price statistics for a date range
     *
     * Obtiene estadísticas de precios para un rango de fechas.
     *
     * @queryParam date_from date required Fecha de inicio (YYYY-MM-DD). Example: 2024-01-01
     * @queryParam date_to date required Fecha de fin (YYYY-MM-DD). Example: 2024-01-31
     * @queryParam price_id int ID del precio de electricidad. Example: 1
     *
     * @response 200 {
     *   "data": {
     *     "total_intervals": 744,
     *     "average_price": 0.15,
     *     "min_price": 0.08,
     *     "max_price": 0.25,
     *     "by_type": {
     *       "peak": 248,
     *       "valley": 248,
     *       "flat": 248
     *     },
     *     "price_trend": "stable"
     *   }
     * }
     */
    public function statistics(Request $request): JsonResponse
    {
        $request->validate([
            'date_from' => 'required|date',
            'date_to' => 'required|date|after_or_equal:date_from',
            'price_id' => 'sometimes|integer|exists:electricity_prices,id'
        ]);

        $query = ElectricityPriceInterval::whereDate('date', '>=', $request->date_from)
            ->whereDate('date', '<=', $request->date_to);

        if ($request->filled('price_id')) {
            $query->where('electricity_price_id', $request->price_id);
        }

        $intervals = $query->get();

        $totalIntervals = $intervals->count();
        $averagePrice = $intervals->avg('price_eur');
        $minPrice = $intervals->min('price_eur');
        $maxPrice = $intervals->max('price_eur');

        $byType = $intervals->groupBy('price_type')
            ->map(function ($group) {
                return $group->count();
            });

        // Determinar tendencia de precios
        $firstHalf = $intervals->where('date', '<=', now()->parse($request->date_from)->addDays(ceil($intervals->count() / 2)));
        $secondHalf = $intervals->where('date', '>', now()->parse($request->date_from)->addDays(ceil($intervals->count() / 2)));
        
        $firstHalfAvg = $firstHalf->avg('price_eur');
        $secondHalfAvg = $secondHalf->avg('price_eur');
        
        $priceTrend = 'stable';
        if ($secondHalfAvg > $firstHalfAvg * 1.05) {
            $priceTrend = 'increasing';
        } elseif ($secondHalfAvg < $firstHalfAvg * 0.95) {
            $priceTrend = 'decreasing';
        }

        return response()->json([
            'data' => [
                'total_intervals' => $totalIntervals,
                'average_price' => round($averagePrice, 4),
                'min_price' => round($minPrice, 4),
                'max_price' => round($maxPrice, 4),
                'by_type' => $byType,
                'price_trend' => $priceTrend,
                'date_range' => [
                    'from' => $request->date_from,
                    'to' => $request->date_to
                ]
            ]
        ]);
    }

    /**
     * Determine price type based on hour range
     *
     * Determina el tipo de precio basándose en el rango de horas.
     */
    private function determinePriceType(int $startHour, int $endHour): string
    {
        $midHour = ($startHour + $endHour) / 2;
        
        if ($midHour >= 8 && $midHour <= 12 || $midHour >= 18 && $midHour <= 22) {
            return 'peak';
        } elseif ($midHour >= 0 && $midHour <= 6) {
            return 'valley';
        } else {
            return 'flat';
        }
    }
}
