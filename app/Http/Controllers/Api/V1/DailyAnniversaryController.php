<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\DailyAnniversary;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Carbon\Carbon;

class DailyAnniversaryController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = DailyAnniversary::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('years_ago_from')) {
            $query->where('years_ago', '>=', $request->years_ago_from);
        }

        if ($request->filled('years_ago_to')) {
            $query->where('years_ago', '<=', $request->years_ago_to);
        }

        $sortBy = $request->get('sort_by', 'years_ago');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $perPage = min($request->get('per_page', 15), 100);
        $anniversaries = $query->paginate($perPage);

        return response()->json([
            'data' => $anniversaries->items(),
            'meta' => [
                'current_page' => $anniversaries->currentPage(),
                'last_page' => $anniversaries->lastPage(),
                'per_page' => $anniversaries->perPage(),
                'total' => $anniversaries->total(),
            ]
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'years_ago' => 'required|integer|min:1|max:9999',
            'original_date' => 'required|date',
            'category' => 'required|string|max:100',
            'type' => 'required|string|max:100',
            'related_people' => 'nullable|array',
            'related_places' => 'nullable|array',
            'significance' => 'nullable|string|max:500',
            'is_milestone' => 'boolean',
        ]);

        $anniversary = DailyAnniversary::create($validated);

        return response()->json([
            'data' => $anniversary,
            'message' => 'Aniversario diario creado exitosamente'
        ], 201);
    }

    public function show(DailyAnniversary $dailyAnniversary): JsonResponse
    {
        return response()->json([
            'data' => $dailyAnniversary
        ]);
    }

    public function update(Request $request, DailyAnniversary $dailyAnniversary): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'years_ago' => 'sometimes|required|integer|min:1|max:9999',
            'original_date' => 'sometimes|required|date',
            'category' => 'sometimes|required|string|max:100',
            'type' => 'sometimes|required|string|max:100',
            'related_people' => 'nullable|array',
            'related_places' => 'nullable|array',
            'significance' => 'nullable|string|max:500',
            'is_milestone' => 'boolean',
        ]);

        $dailyAnniversary->update($validated);

        return response()->json([
            'data' => $dailyAnniversary->fresh(),
            'message' => 'Aniversario diario actualizado exitosamente'
        ]);
    }

    public function destroy(DailyAnniversary $dailyAnniversary): JsonResponse
    {
        $dailyAnniversary->delete();

        return response()->json([
            'message' => 'Aniversario diario eliminado exitosamente'
        ]);
    }

    public function today(): JsonResponse
    {
        $today = Carbon::today();
        $anniversaries = DailyAnniversary::whereDate('original_date', $today)
            ->orderBy('years_ago', 'desc')
            ->get();

        return response()->json([
            'data' => $anniversaries,
            'date' => $today->format('Y-m-d'),
            'count' => $anniversaries->count()
        ]);
    }

    public function statistics(): JsonResponse
    {
        $stats = [
            'total_anniversaries' => DailyAnniversary::count(),
            'milestone_anniversaries' => DailyAnniversary::where('is_milestone', true)->count(),
            'anniversaries_by_category' => DailyAnniversary::selectRaw('category, COUNT(*) as count')
                ->groupBy('category')
                ->orderBy('count', 'desc')
                ->get(),
            'anniversaries_by_type' => DailyAnniversary::selectRaw('type, COUNT(*) as count')
                ->groupBy('type')
                ->orderBy('count', 'desc')
                ->get(),
            'oldest_anniversary' => DailyAnniversary::orderBy('years_ago', 'desc')->first(),
            'newest_anniversary' => DailyAnniversary::orderBy('years_ago', 'asc')->first(),
        ];

        return response()->json([
            'data' => $stats
        ]);
    }
}
