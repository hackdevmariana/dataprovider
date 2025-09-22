<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\PlantSpeciesResource;
use App\Models\PlantSpecies;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use OpenApi\Annotations as OA;

/**
 * @group Plant Species
 *
 * APIs para el catálogo de especies vegetales y reforestación.
 * Permite consultar y gestionar especies de plantas para compensación de CO2.
 */
/**
 * @OA\Tag(
 *     name="Especies Vegetales",
 *     description="APIs para la gestión de Especies Vegetales"
 * )
 */
class PlantSpeciesController extends Controller
{
    /**
     * Display a listing of plant species
     *
     * Obtiene una lista paginada de especies vegetales con filtros.
     *
     * @queryParam plant_type string Filtrar por tipo de planta. Example: tree
     * @queryParam verified boolean Filtrar por especies verificadas. Example: true
     * @queryParam per_page int Cantidad por página (máx 100). Example: 15
     * @queryParam page int Número de página. Example: 1
     *
     * @response 200 {
     *   "data": [
     *     {
     *       "id": 1,
     *       "name": "Roble",
     *       "scientific_name": "Quercus robur",
     *       "plant_type": "tree",
     *       "co2_absorption_kg_per_year": 25.5,
     *       "native_region": {...},
     *       "image": {...}
     *     }
     *   ],
     *   "meta": {...}
     * }
     *
     * @apiResourceCollection App\Http\Resources\V1\PlantSpeciesResource
     * @apiResourceModel App\Models\PlantSpecies
     */
    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'plant_type' => 'sometimes|string|max:100',
            'verified' => 'sometimes|boolean',
            'per_page' => 'sometimes|integer|min:1|max:100',
            'page' => 'sometimes|integer|min:1'
        ]);

        $species = PlantSpecies::with(['nativeRegion', 'image'])
            ->when($request->plant_type, function($query, $type) {
                return $query->where('plant_type', $type);
            })
            ->when($request->verified, function($query) {
                return $query->verified();
            })
            ->orderBy('co2_absorption_kg_per_year', 'desc')
            ->paginate($request->get('per_page', 15));

        return response()->json([
            'data' => PlantSpeciesResource::collection($species),
            'meta' => [
                'current_page' => $species->currentPage(),
                'last_page' => $species->lastPage(),
                'per_page' => $species->perPage(),
                'total' => $species->total(),
            ]
        ]);
    }

    /**
     * Display the specified plant species
     *
     * Obtiene los detalles de una especie vegetal específica.
     *
     * @urlParam plantSpecies integer ID de la especie vegetal. Example: 1
     *
     * @response 200 {
     *   "data": {
     *     "id": 1,
     *       "name": "Roble",
     *       "scientific_name": "Quercus robur",
     *       "plant_type": "tree",
     *       "co2_absorption_kg_per_year": 25.5,
     *       "native_region": {...},
     *       "image": {...}
     *   }
     * }
     *
     * @apiResourceModel App\Models\PlantSpecies
     */
    public function show(PlantSpecies $plantSpecies): JsonResponse
    {
        $plantSpecies->load(['nativeRegion', 'image']);
        
        return response()->json([
            'data' => new PlantSpeciesResource($plantSpecies)
        ]);
    }

    /**
     * Get tree species
     *
     * Obtiene una lista de especies de árboles.
     *
     * @queryParam page int Número de página. Example: 1
     * @queryParam per_page int Cantidad por página (máx 100). Example: 15
     *
     * @response 200 {
     *   "data": [
     *     {
     *       "id": 1,
     *       "name": "Roble",
     *       "scientific_name": "Quercus robur",
     *       "plant_type": "tree",
     *       "co2_absorption_kg_per_year": 25.5
     *     }
     *   ],
     *   "meta": {...}
     * }
     *
     * @apiResourceCollection App\Http\Resources\V1\PlantSpeciesResource
     * @apiResourceModel App\Models\PlantSpecies
     */
    public function trees(Request $request): JsonResponse
    {
        $request->validate([
            'page' => 'sometimes|integer|min:1',
            'per_page' => 'sometimes|integer|min:1|max:100'
        ]);

        $perPage = min($request->get('per_page', 15), 100);
        $trees = PlantSpecies::with(['nativeRegion', 'image'])
            ->trees()
            ->orderBy('co2_absorption_kg_per_year', 'desc')
            ->paginate($perPage);

        return response()->json([
            'data' => PlantSpeciesResource::collection($trees),
            'meta' => [
                'current_page' => $trees->currentPage(),
                'last_page' => $trees->lastPage(),
                'per_page' => $trees->perPage(),
                'total' => $trees->total(),
            ]
        ]);
    }

    /**
     * Get species for reforestation
     *
     * Obtiene especies recomendadas para reforestación.
     *
     * @queryParam page int Número de página. Example: 1
     * @queryParam per_page int Cantidad por página (máx 100). Example: 15
     *
     * @response 200 {
     *   "data": [
     *     {
     *       "id": 1,
     *       "name": "Roble",
     *       "scientific_name": "Quercus robur",
     *       "co2_absorption_kg_per_year": 25.5
     *     }
     *   ],
     *   "meta": {...}
     * }
     *
     * @apiResourceCollection App\Http\Resources\V1\PlantSpeciesResource
     * @apiResourceModel App\Models\PlantSpecies
     */
    public function forReforestation(Request $request): JsonResponse
    {
        $request->validate([
            'page' => 'sometimes|integer|min:1',
            'per_page' => 'sometimes|integer|min:1|max:100'
        ]);

        $perPage = min($request->get('per_page', 15), 100);
        $species = PlantSpecies::with(['nativeRegion', 'image'])
            ->forReforestation()
            ->orderBy('co2_absorption_kg_per_year', 'desc')
            ->paginate($perPage);

        return response()->json([
            'data' => PlantSpeciesResource::collection($species),
            'meta' => [
                'current_page' => $species->currentPage(),
                'last_page' => $species->lastPage(),
                'per_page' => $species->perPage(),
                'total' => $species->total(),
            ]
        ]);
    }

    /**
     * Get species with high CO2 absorption
     *
     * Obtiene especies con alta absorción de CO2.
     *
     * @queryParam min_kg number Absorción mínima de CO2 en kg/año. Example: 20
     * @queryParam page int Número de página. Example: 1
     * @queryParam per_page int Cantidad por página (máx 100). Example: 15
     *
     * @response 200 {
     *   "data": [
     *     {
     *       "id": 1,
     *       "name": "Roble",
     *       "co2_absorption_kg_per_year": 25.5
     *     }
     *   ],
     *   "meta": {...}
     * }
     *
     * @apiResourceCollection App\Http\Resources\V1\PlantSpeciesResource
     * @apiResourceModel App\Models\PlantSpecies
     */
    public function highCO2Absorption(Request $request): JsonResponse
    {
        $request->validate([
            'min_kg' => 'sometimes|numeric|min:0',
            'page' => 'sometimes|integer|min:1',
            'per_page' => 'sometimes|integer|min:1|max:100'
        ]);

        $minKg = $request->get('min_kg', 20);
        $perPage = min($request->get('per_page', 15), 100);
        
        $species = PlantSpecies::with(['nativeRegion', 'image'])
            ->highCO2Absorption($minKg)
            ->orderBy('co2_absorption_kg_per_year', 'desc')
            ->paginate($perPage);

        return response()->json([
            'data' => PlantSpeciesResource::collection($species),
            'meta' => [
                'current_page' => $species->currentPage(),
                'last_page' => $species->lastPage(),
                'per_page' => $species->perPage(),
                'total' => $species->total(),
            ]
        ]);
    }

    /**
     * Get drought resistant species
     *
     * Obtiene especies resistentes a la sequía.
     *
     * @queryParam page int Número de página. Example: 1
     * @queryParam per_page int Cantidad por página (máx 100). Example: 15
     *
     * @response 200 {
     *   "data": [
     *     {
     *       "id": 1,
     *       "name": "Roble",
     *       "co2_absorption_kg_per_year": 25.5
     *     }
     *   ],
     *   "meta": {...}
     * }
     *
     * @apiResourceCollection App\Http\Resources\V1\PlantSpeciesResource
     * @apiResourceModel App\Models\PlantSpecies
     */
    public function droughtResistant(Request $request): JsonResponse
    {
        $request->validate([
            'page' => 'sometimes|integer|min:1',
            'per_page' => 'sometimes|integer|min:1|max:100'
        ]);

        $perPage = min($request->get('per_page', 15), 100);
        $species = PlantSpecies::with(['nativeRegion', 'image'])
            ->droughtResistant()
            ->orderBy('co2_absorption_kg_per_year', 'desc')
            ->paginate($perPage);

        return response()->json([
            'data' => PlantSpeciesResource::collection($species),
            'meta' => [
                'current_page' => $species->currentPage(),
                'last_page' => $species->lastPage(),
                'per_page' => $species->perPage(),
                'total' => $species->total(),
            ]
        ]);
    }

    /**
     * Calculate CO2 compensation
     *
     * Calcula la compensación de CO2 basada en especies vegetales.
     *
     * @bodyParam co2_kg number required Cantidad de CO2 a compensar en kg. Example: 1000
     * @bodyParam years integer required Años para la compensación (1-100). Example: 10
     * @bodyParam species_filter string Filtrar por tipo de especie (all, trees, reforestation). Example: all
     *
     * @response 200 {
     *   "data": {
     *     "co2_to_compensate": 1000,
     *     "years": 10,
     *     "species_needed": 40,
     *     "recommended_species": [...]
     *   }
     * }
     *
     * @response 422 {
     *   "message": "Los datos proporcionados no son válidos.",
     *   "errors": {...}
     * }
     */
    public function calculateCompensation(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'co2_kg' => 'required|numeric|min:0',
            'years' => 'required|integer|min:1|max:100',
            'species_filter' => 'nullable|string|in:all,trees,reforestation',
        ]);

        $co2ToCompensate = $validated['co2_kg'];
        $years = $validated['years'];

        $query = PlantSpecies::with(['nativeRegion', 'image']);

        if (($validated['species_filter'] ?? 'all') === 'trees') {
            $query->trees();
        } elseif (($validated['species_filter'] ?? 'all') === 'reforestation') {
            $query->forReforestation();
        }

        $species = $query->orderBy('co2_absorption_kg_per_year', 'desc')
            ->get();

        $totalAbsorption = $species->sum('co2_absorption_kg_per_year') * $years;
        $speciesNeeded = ceil($co2ToCompensate / ($totalAbsorption / $species->count()));

        return response()->json([
            'data' => [
                'co2_to_compensate' => $co2ToCompensate,
                'years' => $years,
                'species_needed' => $speciesNeeded,
                'recommended_species' => PlantSpeciesResource::collection($species->take(10))
            ]
        ]);
    }
}
