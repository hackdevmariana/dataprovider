<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\CarbonCalculationResource;
use App\Models\CarbonCalculation;
use App\Models\EmissionFactor;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

/**
 * @group Carbon Calculations
 *
 * APIs para el cálculo y seguimiento de la huella de carbono.
 * Permite a los usuarios calcular emisiones de CO2 equivalentes
 * basándose en diferentes actividades y consumos energéticos.
 */
class CarbonCalculationController extends Controller
{
    /**
     * Display a listing of carbon calculations
     *
     * Obtiene una lista de cálculos de carbono con opciones de filtrado.
     *
     * @queryParam user_id int ID del usuario que realizó el cálculo. Example: 1
     * @queryParam calculation_type string Tipo de cálculo (energy, transport, waste, food, lifestyle). Example: energy
     * @queryParam date_from date Fecha de inicio (YYYY-MM-DD). Example: 2024-01-01
     * @queryParam date_to date Fecha de fin (YYYY-MM-DD). Example: 2024-12-31
     * @queryParam min_co2_kg int Emisiones mínimas en kg CO2. Example: 100
     * @queryParam max_co2_kg int Emisiones máximas en kg CO2. Example: 1000
     * @queryParam is_verified boolean Solo cálculos verificados. Example: true
     * @queryParam sort string Ordenamiento (recent, oldest, co2_desc, co2_asc). Example: recent
     * @queryParam page int Número de página. Example: 1
     * @queryParam per_page int Cantidad por página (máx 100). Example: 15
     *
     * @apiResourceCollection App\Http\Resources\V1\CarbonCalculationResource
     * @apiResourceModel App\Models\CarbonCalculation
     */
    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'user_id' => 'sometimes|integer|exists:users,id',
            'calculation_type' => 'sometimes|string|in:energy,transport,waste,food,lifestyle',
            'date_from' => 'sometimes|date',
            'date_to' => 'sometimes|date',
            'min_co2_kg' => 'sometimes|numeric|min:0',
            'max_co2_kg' => 'sometimes|numeric|min:0',
            'is_verified' => 'sometimes|boolean',
            'sort' => 'sometimes|string|in:recent,oldest,co2_desc,co2_asc',
            'page' => 'sometimes|integer|min:1',
            'per_page' => 'sometimes|integer|min:1|max:100'
        ]);

        $query = CarbonCalculation::with(['user', 'emissionFactor']);

        // Filtros
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('calculation_type')) {
            $query->where('calculation_type', $request->calculation_type);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        if ($request->filled('min_co2_kg')) {
            $query->where('co2_kg', '>=', $request->min_co2_kg);
        }

        if ($request->filled('max_co2_kg')) {
            $query->where('co2_kg', '<=', $request->max_co2_kg);
        }

        if ($request->filled('is_verified')) {
            $query->where('is_verified', $request->boolean('is_verified'));
        }

        // Ordenamiento
        $sort = $request->get('sort', 'recent');
        switch ($sort) {
            case 'oldest':
                $query->orderBy('created_at', 'asc');
                break;
            case 'co2_desc':
                $query->orderBy('co2_kg', 'desc');
                break;
            case 'co2_asc':
                $query->orderBy('co2_kg', 'asc');
                break;
            default: // recent
                $query->orderBy('created_at', 'desc');
        }

        $perPage = min($request->get('per_page', 15), 100);
        $calculations = $query->paginate($perPage);

        return CarbonCalculationResource::collection($calculations)->response();
    }

    /**
     * Store a newly created carbon calculation
     *
     * Crea un nuevo cálculo de carbono basado en los parámetros proporcionados.
     *
     * @bodyParam calculation_type string required Tipo de cálculo. Example: energy
     * @bodyParam activity_name string required Nombre de la actividad. Example: Consumo eléctrico mensual
     * @bodyParam quantity number required Cantidad de la actividad. Example: 300
     * @bodyParam unit string required Unidad de medida. Example: kWh
     * @bodyParam emission_factor_id int ID del factor de emisión. Example: 1
     * @bodyParam custom_emission_factor number Factor de emisión personalizado (kg CO2/unit). Example: 0.5
     * @bodyParam description text Descripción adicional del cálculo. Example: Consumo en mi vivienda durante enero
     * @bodyParam calculation_date date Fecha del cálculo (YYYY-MM-DD). Example: 2024-01-15
     * @bodyParam tags array Etiquetas para categorizar el cálculo. Example: ["hogar", "energía"]
     *
     * @response 201 {
     *   "data": {
     *     "id": 1,
     *     "user_id": 1,
     *     "calculation_type": "energy",
     *     "activity_name": "Consumo eléctrico mensual",
     *     "quantity": 300,
     *     "unit": "kWh",
     *     "co2_kg": 150.0,
     *     "created_at": "2024-01-15T10:00:00.000000Z"
     *   },
     *   "message": "Cálculo de carbono creado exitosamente"
     * }
     *
     * @response 422 {
     *   "message": "Debe proporcionar un factor de emisión válido",
     *   "errors": {
     *     "emission_factor_id": ["El factor de emisión es requerido o debe ser válido"]
     *   }
     * }
     *
     * @authenticated
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'calculation_type' => 'required|string|in:energy,transport,waste,food,lifestyle',
            'activity_name' => 'required|string|max:255',
            'quantity' => 'required|numeric|min:0.001',
            'unit' => 'required|string|max:50',
            'emission_factor_id' => 'sometimes|integer|exists:emission_factors,id',
            'custom_emission_factor' => 'sometimes|numeric|min:0.001',
            'description' => 'sometimes|string|max:1000',
            'calculation_date' => 'sometimes|date',
            'tags' => 'sometimes|array',
            'tags.*' => 'string|max:50'
        ]);

        // Verificar que se proporcione un factor de emisión
        if (!$request->filled('emission_factor_id') && !$request->filled('custom_emission_factor')) {
            throw ValidationException::withMessages([
                'emission_factor_id' => ['Debe proporcionar un factor de emisión válido']
            ]);
        }

        $userId = Auth::guard('sanctum')->user()->id;
        $emissionFactor = null;
        $co2Kg = 0;

        if ($request->filled('emission_factor_id')) {
            $emissionFactor = EmissionFactor::findOrFail($request->emission_factor_id);
            $co2Kg = $request->quantity * $emissionFactor->factor_value;
        } else {
            $co2Kg = $request->quantity * $request->custom_emission_factor;
        }

        $calculation = CarbonCalculation::create([
            'user_id' => $userId,
            'calculation_type' => $request->calculation_type,
            'activity_name' => $request->activity_name,
            'quantity' => $request->quantity,
            'unit' => $request->unit,
            'emission_factor_id' => $request->emission_factor_id,
            'custom_emission_factor' => $request->custom_emission_factor,
            'co2_kg' => $co2Kg,
            'description' => $request->description,
            'calculation_date' => $request->calculation_date ?? now(),
            'tags' => $request->tags ?? [],
            'is_verified' => false,
            'verification_method' => null
        ]);

        return (new CarbonCalculationResource($calculation))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Display the specified carbon calculation
     *
     * Obtiene los detalles de un cálculo de carbono específico.
     *
     * @urlParam carbonCalculation int required ID del cálculo. Example: 1
     *
     * @response 200 {
     *   "data": {
     *     "id": 1,
     *     "user_id": 1,
     *     "calculation_type": "energy",
     *     "activity_name": "Consumo eléctrico mensual",
     *     "quantity": 300,
     *     "unit": "kWh",
     *     "co2_kg": 150.0,
     *     "description": "Consumo en mi vivienda durante enero",
     *     "created_at": "2024-01-15T10:00:00.000000Z"
     *   }
     * }
     *
     * @response 404 {
     *   "message": "Cálculo de carbono no encontrado"
     * }
     */
    public function show(CarbonCalculation $carbonCalculation): JsonResponse
    {
        $carbonCalculation->load(['user', 'emissionFactor']);
        return (new CarbonCalculationResource($carbonCalculation))->response();
    }

    /**
     * Update the specified carbon calculation
     *
     * Actualiza un cálculo de carbono existente. Solo el autor puede modificarlo.
     *
     * @urlParam carbonCalculation int required ID del cálculo. Example: 1
     * @bodyParam activity_name string Nombre de la actividad. Example: Consumo eléctrico mensual actualizado
     * @bodyParam quantity number Cantidad de la actividad. Example: 350
     * @bodyParam description text Descripción adicional del cálculo. Example: Consumo actualizado en mi vivienda
     * @bodyParam tags array Etiquetas para categorizar el cálculo. Example: ["hogar", "energía", "actualizado"]
     *
     * @response 200 {
     *   "data": {
     *     "id": 1,
     *     "activity_name": "Consumo eléctrico mensual actualizado",
     *     "quantity": 350,
     *     "co2_kg": 175.0,
     *     "updated_at": "2024-01-15T11:00:00.000000Z"
     *   },
     *   "message": "Cálculo de carbono actualizado exitosamente"
     * }
     *
     * @response 403 {
     *   "message": "No tienes permiso para modificar este cálculo"
     * }
     *
     * @authenticated
     */
    public function update(Request $request, CarbonCalculation $carbonCalculation): JsonResponse
    {
        // Verificar permisos
        if ($carbonCalculation->user_id !== Auth::guard('sanctum')->user()->id) {
            return response()->json([
                'message' => 'No tienes permiso para modificar este cálculo'
            ], 403);
        }

        $request->validate([
            'activity_name' => 'sometimes|string|max:255',
            'quantity' => 'sometimes|numeric|min:0.001',
            'description' => 'sometimes|string|max:1000',
            'tags' => 'sometimes|array',
            'tags.*' => 'string|max:50'
        ]);

        // Si se actualiza la cantidad, recalcular CO2
        if ($request->filled('quantity')) {
            $emissionFactor = $carbonCalculation->emissionFactor;
            if ($emissionFactor) {
                $co2Kg = $request->quantity * $emissionFactor->factor_value;
            } else {
                $co2Kg = $request->quantity * ($carbonCalculation->custom_emission_factor ?? 0);
            }
            $request->merge(['co2_kg' => $co2Kg]);
        }

        $carbonCalculation->update($request->only([
            'activity_name', 'quantity', 'description', 'tags', 'co2_kg'
        ]));

        return (new CarbonCalculationResource($carbonCalculation))
            ->response()
            ->setStatusCode(200);
    }

    /**
     * Remove the specified carbon calculation
     *
     * Elimina un cálculo de carbono. Solo el autor puede eliminarlo.
     *
     * @urlParam carbonCalculation int required ID del cálculo. Example: 1
     *
     * @response 200 {
     *   "message": "Cálculo de carbono eliminado exitosamente"
     * }
     *
     * @response 403 {
     *   "message": "No tienes permiso para eliminar este cálculo"
     * }
     *
     * @authenticated
     */
    public function destroy(CarbonCalculation $carbonCalculation): JsonResponse
    {
        // Verificar permisos
        if ($carbonCalculation->user_id !== Auth::guard('sanctum')->user()->id) {
            return response()->json([
                'message' => 'No tienes permiso para eliminar este cálculo'
            ], 403);
        }

        $carbonCalculation->delete();

        return response()->json([
            'message' => 'Cálculo de carbono eliminado exitosamente'
        ]);
    }

    /**
     * Get user's carbon footprint summary
     *
     * Obtiene un resumen de la huella de carbono del usuario.
     *
     * @queryParam period string Período de tiempo (month, quarter, year, all). Example: year
     * @queryParam calculation_type string Filtrar por tipo de cálculo. Example: energy
     *
     * @response 200 {
     *   "data": {
     *     "total_co2_kg": 1250.5,
     *     "average_monthly": 104.2,
     *     "by_type": {
     *       "energy": 800.0,
     *       "transport": 300.0,
     *       "waste": 150.5
     *     },
     *     "comparison": {
     *       "national_average": 1200.0,
     *       "difference": 50.5
     *     }
     *   }
     * }
     *
     * @authenticated
     */
    public function footprintSummary(Request $request): JsonResponse
    {
        $request->validate([
            'period' => 'sometimes|string|in:month,quarter,year,all',
            'calculation_type' => 'sometimes|string|in:energy,transport,waste,food,lifestyle'
        ]);

        $userId = Auth::guard('sanctum')->user()->id;
        $query = CarbonCalculation::where('user_id', $userId);

        // Filtrar por período
        $period = $request->get('period', 'year');
        switch ($period) {
            case 'month':
                $query->whereDate('created_at', '>=', now()->startOfMonth());
                break;
            case 'quarter':
                $query->whereDate('created_at', '>=', now()->startOfQuarter());
                break;
            case 'year':
                $query->whereDate('created_at', '>=', now()->startOfYear());
                break;
            // 'all' no aplica filtro de fecha
        }

        if ($request->filled('calculation_type')) {
            $query->where('calculation_type', $request->calculation_type);
        }

        $calculations = $query->get();

        $totalCo2 = $calculations->sum('co2_kg');
        $byType = $calculations->groupBy('calculation_type')
            ->map(function ($group) {
                return $group->sum('co2_kg');
            });

        $monthsCount = $period === 'all' ? 12 : ($period === 'month' ? 1 : ($period === 'quarter' ? 3 : 12));
        $averageMonthly = $totalCo2 / $monthsCount;

        // Comparación con promedio nacional (ejemplo)
        $nationalAverage = 1200.0; // kg CO2/año
        $difference = $totalCo2 - $nationalAverage;

        return response()->json([
            'data' => [
                'total_co2_kg' => round($totalCo2, 2),
                'average_monthly' => round($averageMonthly, 2),
                'by_type' => $byType->map(function ($value) {
                    return round($value, 2);
                }),
                'comparison' => [
                    'national_average' => $nationalAverage,
                    'difference' => round($difference, 2),
                    'is_below_average' => $difference < 0
                ],
                'period' => $period,
                'total_calculations' => $calculations->count()
            ]
        ]);
    }
}
