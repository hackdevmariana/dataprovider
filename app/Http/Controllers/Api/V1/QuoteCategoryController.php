<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\QuoteCategory;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;

class QuoteCategoryController extends Controller
{
    /**
     * Display a listing of quote categories.
     */
    public function index(Request $request): JsonResponse
    {
        $query = QuoteCategory::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $perPage = min($request->get('per_page', 15), 100);
        $categories = $query->orderBy('name')->paginate($perPage);

        return response()->json([
            'data' => $categories->items(),
            'meta' => [
                'current_page' => $categories->currentPage(),
                'last_page' => $categories->lastPage(),
                'per_page' => $categories->perPage(),
                'total' => $categories->total(),
            ]
        ]);
    }

    /**
     * Store a newly created quote category.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:quote_categories,name',
            'description' => 'nullable|string|max:500',
            'color' => 'nullable|string|max:7',
            'icon' => 'nullable|string|max:50',
            'quotes_count' => 'integer|min:0',
            'is_active' => 'boolean',
        ]);

        $category = QuoteCategory::create($validated);

        return response()->json([
            'data' => $category,
            'message' => 'Categoría de cita creada exitosamente'
        ], 201);
    }

    /**
     * Display the specified quote category.
     */
    public function show(QuoteCategory $quoteCategory): JsonResponse
    {
        return response()->json([
            'data' => $quoteCategory
        ]);
    }

    /**
     * Update the specified quote category.
     */
    public function update(Request $request, QuoteCategory $quoteCategory): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:100|unique:quote_categories,name,' . $quoteCategory->id,
            'description' => 'nullable|string|max:500',
            'color' => 'nullable|string|max:7',
            'icon' => 'nullable|string|max:50',
            'quotes_count' => 'integer|min:0',
            'is_active' => 'boolean',
        ]);

        $quoteCategory->update($validated);

        return response()->json([
            'data' => $quoteCategory->fresh(),
            'message' => 'Categoría de cita actualizada exitosamente'
        ]);
    }

    /**
     * Remove the specified quote category.
     */
    public function destroy(QuoteCategory $quoteCategory): JsonResponse
    {
        $quoteCategory->delete();

        return response()->json([
            'message' => 'Categoría de cita eliminada exitosamente'
        ]);
    }

    /**
     * Get statistics for quote categories.
     */
    public function statistics(): JsonResponse
    {
        $stats = [
            'total_categories' => QuoteCategory::count(),
            'active_categories' => QuoteCategory::where('is_active', true)->count(),
            'most_popular_category' => QuoteCategory::orderBy('quotes_count', 'desc')->first(),
            'average_quotes_per_category' => round(QuoteCategory::avg('quotes_count'), 2),
        ];

        return response()->json([
            'data' => $stats
        ]);
    }
}