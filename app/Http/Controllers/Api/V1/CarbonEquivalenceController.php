<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\CarbonEquivalenceResource;
use App\Models\CarbonEquivalence;
use App\Models\CarbonCalculation;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use OpenApi\Annotations as OA;

/**
 * @group Carbon Equivalences
 *
 * APIs para la calculadora de huella de carbono.
 * Permite consultar equivalencias y calcular impactos ambientales.
 */
/**
 * @OA\Tag(
 *     name="Equivalencias de Carbono",
 *     description="APIs para la gestión de Equivalencias de Carbono"
 * )
 */
class CarbonEquivalenceController extends Controller
{
    /**
     * Display a listing of carbon equivalences
     *
     * Obtiene una lista paginada de equivalencias de carbono.
     *
     * @queryParam category string Filtrar por categoría. Example: energy
     * @queryParam verified boolean Filtrar por equivalencias verificadas. Example: true
     * @queryParam per_page int Cantidad por página (máx 100). Example: 15
     * @queryParam page int Número de página. Example: 1
     *
     * @response 200 {
     *   "data": [
     *     {
     *       "id": 1,
     *       "name": "Electricidad",
     *       "category": "energy",
     *       "co2_kg_equivalent": 0.5,
     *       "unit": "kWh"
     *     }
     *   ],
     *   "meta": {...}
     * }
     *
     * @apiResourceCollection App\Http\Resources\V1\CarbonEquivalenceResource
     * @apiResourceModel App\Models\CarbonEquivalence
     */
    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'category' => 'sometimes|string|max:100',
            'verified' => 'sometimes|boolean',
            'per_page' => 'sometimes|integer|min:1|max:100',
            'page' => 'sometimes|integer|min:1'
        ]);

        $equivalences = CarbonEquivalence::query()
            ->when($request->category, function($query, $category) {
                return $query->ofCategory($category);
            })
            ->when($request->verified, function($query) {
                return $query->verified();
            })
            ->orderBy('co2_kg_equivalent')
            ->paginate($request->get('per_page', 15));

        return response()->json([
            'data' => CarbonEquivalenceResource::collection($equivalences),
            'meta' => [
                'current_page' => $equivalences->currentPage(),
                'last_page' => $equivalences->lastPage(),
                'per_page' => $equivalences->perPage(),
                'total' => $equivalences->total(),
            ]
        ]);
    }

    /**
     * Display the specified carbon equivalence
     *

     * Obtiene los detalles de una equivalencia de carbono específica.
     *
     * @urlParam carbonEquivalence integer ID de la equivalencia. Example: 1
     *
     * @response 200 {
     *   "data": {
     *     "id": 1,
     *       "name": "Electricidad",
     *       "category": "energy",
     *       "co2_kg_equivalent": 0.5,
     *       "unit": "kWh"
     *   }
     * }
     *
     * @apiResourceModel App\Models\CarbonEquivalence
     */
    public function show(CarbonEquivalence $carbonEquivalence): JsonResponse
    {
        return response()->json([
            'data' => new CarbonEquivalenceResource($carbonEquivalence)
        ]);
    }

    /**
     * Get energy equivalences
     *

     * Obtiene equivalencias relacionadas con energía.
     *
     * @queryParam page int Número de página. Example: 1
     * @queryParam per_page int Cantidad por página (máx 100). Example: 15
     *
     * @response 200 {
     *   "data": [
     *     {
     *       "id": 1,
     *       "name": "Electricidad",
     *       "category": "energy",
     *       "co2_kg_equivalent": 0.5,
     *       "unit": "kWh"
     *     }
     *   ],
     *   "meta": {...}
     * }
     *
     * @apiResourceCollection App\Http\Resources\V1\CarbonEquivalenceResource
     * @apiResourceModel App\Models\CarbonEquivalence
     */
    public function energy(Request $request): JsonResponse
    {
        $request->validate([
            'page' => 'sometimes|integer|min:1',
            'per_page' => 'sometimes|integer|min:1|max:100'
        ]);

        $perPage = min($request->get('per_page', 15), 100);
        $equivalences = CarbonEquivalence::energy()
            ->orderBy('co2_kg_equivalent')
            ->paginate($perPage);

        return response()->json([
            'data' => CarbonEquivalenceResource::collection($equivalences),
            'meta' => [
                'current_page' => $equivalences->currentPage(),
                'last_page' => $equivalences->lastPage(),
                'per_page' => $equivalences->perPage(),
                'total' => $equivalences->total(),
            ]
        ]);
    }

    /**
     * Get transport equivalences
     *

     * Obtiene equivalencias relacionadas con transporte.
     *
     * @queryParam page int Número de página. Example: 1
     * @queryParam per_page int Cantidad por página (máx 100). Example: 15
     *
     * @response 200 {
     *   "data": [
     *     {
     *       "id": 2,
     *       "name": "Coche",
     *       "category": "transport",
     *       "co2_kg_equivalent": 0.2,
     *       "unit": "km"
     *     }
     *   ],
     *   "meta": {...}
     * }
     *
     * @apiResourceCollection App\Http\Resources\V1\CarbonEquivalenceResource
     * @apiResourceModel App\Models\CarbonEquivalence
     */
    public function transport(Request $request): JsonResponse
    {
        $request->validate([
            'page' => 'sometimes|integer|min:1',
            'per_page' => 'sometimes|integer|min:1|max:100'
        ]);

        $perPage = min($request->get('per_page', 15), 100);
        $equivalences = CarbonEquivalence::transport()
            ->orderBy('co2_kg_equivalent')
            ->paginate($perPage);

        return response()->json([
            'data' => CarbonEquivalenceResource::collection($equivalences),
            'meta' => [
                'current_page' => $equivalences->currentPage(),
                'last_page' => $equivalences->lastPage(),
                'per_page' => $equivalences->perPage(),
                'total' => $equivalences->total(),
            ]
        ]);
    }

    /**
     * Calculate carbon footprint
     *

     * Calcula la huella de carbono basada en una equivalencia.
     *
     * @bodyParam equivalence_id integer required ID de la equivalencia. Example: 1
     * @bodyParam quantity number required Cantidad a calcular. Example: 100
     * @bodyParam context string Contexto del cálculo. Example: Consumo mensual
     * @bodyParam save_calculation boolean Si guardar el cálculo. Example: true
     *
     * @response 200 {
     *   "data": {
     *     "equivalence": {...},
     *     "quantity": 100,
     *     "co2_result": 50,
     *     "impact_level": "medium",
     *     "compensation_recommendations": {
     *       "trees_needed": 3,
     *       "planting_cost_eur": 6
     *     }
     *   }
     * }
     *
     * @response 422 {
     *   "message": "Los datos proporcionados no son válidos.",
     *   "errors": {...}
     * }
     *
     * @authenticated
     */
    public function calculate(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'equivalence_id' => 'required|exists:carbon_equivalences,id',
            'quantity' => 'required|numeric|min:0',
            'context' => 'nullable|string|max:255',
            'save_calculation' => 'boolean',
        ]);

        $equivalence = CarbonEquivalence::findOrFail($validated['equivalence_id']);
        $co2Result = $equivalence->calculateCO2($validated['quantity']);

        if ($validated['save_calculation'] ?? false) {
            CarbonCalculation::create([
                'user_id' => auth()->id(),
                'carbon_equivalence_id' => $equivalence->id,
                'quantity' => $validated['quantity'],
                'co2_result' => $co2Result,
                'context' => $validated['context'] ?? null,
                'session_id' => $request->session()->getId(),
            ]);
        }

        return response()->json([
            'data' => [
                'equivalence' => new CarbonEquivalenceResource($equivalence),
                'quantity' => $validated['quantity'],
                'co2_result' => $co2Result,
                'impact_level' => $this->getImpactLevel($co2Result),
                'compensation_recommendations' => [
                    'trees_needed' => ceil($co2Result / 22),
                    'planting_cost_eur' => ceil($co2Result / 22) * 2,
                ],
            ]
        ]);
    }

    /**
     * Get carbon equivalence statistics
     *

     * Obtiene estadísticas de equivalencias de carbono.
     *
     * @response 200 {
     *   "data": {
     *     "total_equivalences": 50,
     *     "verified_equivalences": 45,
     *     "categories": [
     *       {
     *         "category": "energy",
     *         "count": 20
     *       }
     *     ]
     *   }
     * }
     */
    public function statistics(): JsonResponse
    {
        $stats = [
            'total_equivalences' => CarbonEquivalence::count(),
            'verified_equivalences' => CarbonEquivalence::verified()->count(),
            'categories' => CarbonEquivalence::selectRaw('category, COUNT(*) as count')
                ->groupBy('category')
                ->get()
        ];

        return response()->json([
            'data' => $stats
        ]);
    }

    /**
     * Get impact level based on CO2 result
     *
     * @param float $co2Result
     * @return string
     */
    private function getImpactLevel(float $co2Result): string
    {
        if ($co2Result < 10) return 'low';
        if ($co2Result < 50) return 'medium';
        if ($co2Result < 100) return 'high';
        return 'very_high';
    }
}
