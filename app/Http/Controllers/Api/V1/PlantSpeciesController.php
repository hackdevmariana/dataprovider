<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\PlantSpeciesResource;
use App\Models\PlantSpecies;
use Illuminate\Http\Request;

/**
 * @OA\Tag(
 *     name="Plant Species",
 *     description="API endpoints para catálogo de especies vegetales y reforestación"
 * )
 */
class PlantSpeciesController extends Controller
{
    public function index(Request $request)
    {
        $species = PlantSpecies::with(['nativeRegion', 'image'])
            ->when($request->plant_type, function($query, $type) {
                return $query->where('plant_type', $type);
            })
            ->when($request->verified, function($query) {
                return $query->verified();
            })
            ->orderBy('co2_absorption_kg_per_year', 'desc')
            ->paginate($request->get('per_page', 15));

        return PlantSpeciesResource::collection($species);
    }

    public function show(PlantSpecies $plantSpecies)
    {
        $plantSpecies->load(['nativeRegion', 'image']);
        return new PlantSpeciesResource($plantSpecies);
    }

    public function trees()
    {
        $trees = PlantSpecies::with(['nativeRegion', 'image'])
            ->trees()
            ->orderBy('co2_absorption_kg_per_year', 'desc')
            ->paginate(15);

        return PlantSpeciesResource::collection($trees);
    }

    public function forReforestation()
    {
        $species = PlantSpecies::with(['nativeRegion', 'image'])
            ->forReforestation()
            ->orderBy('co2_absorption_kg_per_year', 'desc')
            ->paginate(15);

        return PlantSpeciesResource::collection($species);
    }

    public function highCO2Absorption(Request $request)
    {
        $minKg = $request->get('min_kg', 20);
        
        $species = PlantSpecies::with(['nativeRegion', 'image'])
            ->highCO2Absorption($minKg)
            ->orderBy('co2_absorption_kg_per_year', 'desc')
            ->paginate(15);

        return PlantSpeciesResource::collection($species);
    }

    public function droughtResistant()
    {
        $species = PlantSpecies::with(['nativeRegion', 'image'])
            ->droughtResistant()
            ->orderBy('co2_absorption_kg_per_year', 'desc')
            ->paginate(15);

        return PlantSpeciesResource::collection($species);
    }

    public function calculateCompensation(Request $request)
    {
        $validated = $request->validate([
            'co2_kg' => 'required|numeric|min:0',
            'years' => 'required|integer|min:1|max:100',
            'species_filter' => 'nullable|in:all,trees,reforestation',
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
                        ->limit(10)
                        ->get();

        $recommendations = [];

        foreach ($species as $plant) {
            $totalAbsorption = $plant->co2_absorption_kg_per_year * $years;
            
            if ($totalAbsorption > 0) {
                $plantsNeeded = ceil($co2ToCompensate / $totalAbsorption);
                $totalCost = $plant->calculateTotalCost($years, $plantsNeeded);

                $recommendations[] = [
                    'species' => new PlantSpeciesResource($plant),
                    'plants_needed' => $plantsNeeded,
                    'total_co2_absorbed' => round($totalAbsorption * $plantsNeeded, 1),
                    'total_cost' => $totalCost,
                    'efficiency_score' => round($plant->co2_efficiency, 2),
                ];
            }
        }

        return response()->json(['data' => ['recommendations' => $recommendations]]);
    }

    public function statistics()
    {
        $stats = [
            'total_species' => PlantSpecies::count(),
            'tree_species' => PlantSpecies::trees()->count(),
            'reforestation_species' => PlantSpecies::forReforestation()->count(),
            'drought_resistant' => PlantSpecies::droughtResistant()->count(),
            'avg_co2_absorption' => round(PlantSpecies::avg('co2_absorption_kg_per_year'), 2),
        ];

        return response()->json(['data' => $stats]);
    }
}