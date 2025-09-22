<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Parish;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use OpenApi\Annotations as OA;

#[OA\Tag(name: "Parishes")]
/**
 * @OA\Tag(
 *     name="Parroquias",
 *     description="APIs para la gestión de Parroquias"
 * )
 */
class ParishController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Parish::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('address', 'like', "%{$search}%");
            });
        }

        if ($request->filled('municipality_id')) {
            $query->where('municipality_id', $request->municipality_id);
        }

        if ($request->filled('diocese')) {
            $query->where('diocese', 'like', "%{$request->diocese}%");
        }

        if ($request->has('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        $sortBy = $request->get('sort_by', 'name');
        $sortOrder = $request->get('sort_order', 'asc');
        $query->orderBy($sortBy, $sortOrder);

        $perPage = min($request->get('per_page', 15), 100);
        $parishes = $query->paginate($perPage);

        return response()->json([
            'data' => $parishes->items(),
            'meta' => [
                'current_page' => $parishes->currentPage(),
                'last_page' => $parishes->lastPage(),
                'per_page' => $parishes->perPage(),
                'total' => $parishes->total(),
            ]
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:500',
            'municipality_id' => 'required|exists:municipalities,id',
            'diocese' => 'required|string|max:255',
            'parish_priest' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'website' => 'nullable|url|max:500',
            'founded_year' => 'nullable|integer|min:1|max:' . date('Y'),
            'is_active' => 'boolean',
        ]);

        $parish = Parish::create($validated);

        return response()->json([
            'data' => $parish,
            'message' => 'Parroquia creada exitosamente'
        ], 201);
    }

    public function show(Parish $parish): JsonResponse
    {
        return response()->json([
            'data' => $parish
        ]);
    }

    public function update(Request $request, Parish $parish): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'address' => 'sometimes|required|string|max:500',
            'municipality_id' => 'sometimes|required|exists:municipalities,id',
            'diocese' => 'sometimes|required|string|max:255',
            'parish_priest' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'website' => 'nullable|url|max:500',
            'founded_year' => 'nullable|integer|min:1|max:' . date('Y'),
            'is_active' => 'boolean',
        ]);

        $parish->update($validated);

        return response()->json([
            'data' => $parish->fresh(),
            'message' => 'Parroquia actualizada exitosamente'
        ]);
    }

    public function destroy(Parish $parish): JsonResponse
    {
        $parish->delete();

        return response()->json([
            'message' => 'Parroquia eliminada exitosamente'
        ]);
    }

    public function byMunicipality(Request $request, int $municipalityId): JsonResponse
    {
        $perPage = min($request->get('per_page', 15), 100);
        $parishes = Parish::where('municipality_id', $municipalityId)
            ->where('is_active', true)
            ->orderBy('name')
            ->paginate($perPage);

        return response()->json([
            'data' => $parishes->items(),
            'meta' => [
                'current_page' => $parishes->currentPage(),
                'last_page' => $parishes->lastPage(),
                'per_page' => $parishes->perPage(),
                'total' => $parishes->total(),
                'municipality_id' => $municipalityId,
            ]
        ]);
    }

    public function statistics(): JsonResponse
    {
        $stats = [
            'total_parishes' => Parish::count(),
            'active_parishes' => Parish::where('is_active', true)->count(),
            'parishes_by_diocese' => Parish::selectRaw('diocese, COUNT(*) as count')
                ->groupBy('diocese')
                ->orderBy('count', 'desc')
                ->get(),
            'parishes_by_municipality' => Parish::selectRaw('municipality_id, COUNT(*) as count')
                ->groupBy('municipality_id')
                ->orderBy('count', 'desc')
                ->limit(10)
                ->get(),
        ];

        return response()->json([
            'data' => $stats
        ]);
    }
}
