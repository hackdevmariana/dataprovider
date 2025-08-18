<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\CarbonEquivalenceResource;
use App\Models\CarbonEquivalence;
use App\Models\CarbonCalculation;
use Illuminate\Http\Request;

/**
 * @OA\Tag(
 *     name="Carbon Equivalences",
 *     description="API endpoints para calculadora de huella de carbono"
 * )
 */
class CarbonEquivalenceController extends Controller
{
    public function index(Request $request)
    {
        $equivalences = CarbonEquivalence::query()
            ->when($request->category, function($query, $category) {
                return $query->ofCategory($category);
            })
            ->when($request->verified, function($query) {
                return $query->verified();
            })
            ->orderBy('co2_kg_equivalent')
            ->paginate($request->get('per_page', 15));

        return CarbonEquivalenceResource::collection($equivalences);
    }

    public function show(CarbonEquivalence $carbonEquivalence)
    {
        return new CarbonEquivalenceResource($carbonEquivalence);
    }

    public function energy()
    {
        $equivalences = CarbonEquivalence::energy()
            ->orderBy('co2_kg_equivalent')
            ->paginate(15);

        return CarbonEquivalenceResource::collection($equivalences);
    }

    public function transport()
    {
        $equivalences = CarbonEquivalence::transport()
            ->orderBy('co2_kg_equivalent')
            ->paginate(15);

        return CarbonEquivalenceResource::collection($equivalences);
    }

    public function calculate(Request $request)
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

    public function statistics()
    {
        $stats = [
            'total_equivalences' => CarbonEquivalence::count(),
            'verified_equivalences' => CarbonEquivalence::verified()->count(),
            'categories' => CarbonEquivalence::selectRaw('category, COUNT(*) as count')
                ->groupBy('category')
                ->get(),
            'total_calculations' => CarbonCalculation::count(),
        ];

        return response()->json(['data' => $stats]);
    }

    private function getImpactLevel($co2)
    {
        if ($co2 < 1) return 'bajo';
        elseif ($co2 < 5) return 'medio';
        elseif ($co2 < 10) return 'alto';
        else return 'muy_alto';
    }
}