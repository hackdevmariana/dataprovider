<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\NewsSource;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use OpenApi\Annotations as OA;

#[OA\Tag(name: "News Sources")]
/**
 * @OA\Tag(
 *     name="Fuentes de Noticias",
 *     description="APIs para la gestión de Fuentes de Noticias"
 * )
 */
class NewsSourceController extends Controller
{
    /**
     * Display a listing of news sources.
     */
    public function index(Request $request): JsonResponse
    {
        $query = NewsSource::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('url', 'like', "%{$search}%");
            });
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('language')) {
            $query->where('language', $request->language);
        }

        if ($request->has('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        $sortBy = $request->get('sort_by', 'name');
        $sortOrder = $request->get('sort_order', 'asc');
        $query->orderBy($sortBy, $sortOrder);

        $perPage = min($request->get('per_page', 15), 100);
        $sources = $query->paginate($perPage);

        return response()->json([
            'data' => $sources->items(),
            'meta' => [
                'current_page' => $sources->currentPage(),
                'last_page' => $sources->lastPage(),
                'per_page' => $sources->perPage(),
                'total' => $sources->total(),
            ]
        ]);
    }

    /**
     * Store a newly created news source.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'url' => 'required|url|max:500',
            'type' => 'required|string|max:50',
            'language' => 'required|string|max:10',
            'country' => 'nullable|string|max:100',
            'region' => 'nullable|string|max:100',
            'is_active' => 'boolean',
            'credibility_score' => 'nullable|numeric|between:0,10',
            'last_scraped_at' => 'nullable|date',
        ]);

        $source = NewsSource::create($validated);

        return response()->json([
            'data' => $source,
            'message' => 'Fuente de noticias creada exitosamente'
        ], 201);
    }

    /**
     * Display the specified news source.
     */
    public function show(NewsSource $newsSource): JsonResponse
    {
        return response()->json([
            'data' => $newsSource
        ]);
    }

    /**
     * Update the specified news source.
     */
    public function update(Request $request, NewsSource $newsSource): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'url' => 'sometimes|required|url|max:500',
            'type' => 'sometimes|required|string|max:50',
            'language' => 'sometimes|required|string|max:10',
            'country' => 'nullable|string|max:100',
            'region' => 'nullable|string|max:100',
            'is_active' => 'boolean',
            'credibility_score' => 'nullable|numeric|between:0,10',
            'last_scraped_at' => 'nullable|date',
        ]);

        $newsSource->update($validated);

        return response()->json([
            'data' => $newsSource->fresh(),
            'message' => 'Fuente de noticias actualizada exitosamente'
        ]);
    }

    /**
     * Remove the specified news source.
     */
    public function destroy(NewsSource $newsSource): JsonResponse
    {
        $newsSource->delete();

        return response()->json([
            'message' => 'Fuente de noticias eliminada exitosamente'
        ]);
    }

    /**
     * Get news sources by type.
     */
    public function byType(Request $request, string $type): JsonResponse
    {
        $perPage = min($request->get('per_page', 15), 100);
        $sources = NewsSource::where('type', $type)
            ->where('is_active', true)
            ->orderBy('name')
            ->paginate($perPage);

        return response()->json([
            'data' => $sources->items(),
            'meta' => [
                'current_page' => $sources->currentPage(),
                'last_page' => $sources->lastPage(),
                'per_page' => $sources->perPage(),
                'total' => $sources->total(),
                'type' => $type,
            ]
        ]);
    }

    /**
     * Get news sources by language.
     */
    public function byLanguage(Request $request, string $language): JsonResponse
    {
        $perPage = min($request->get('per_page', 15), 100);
        $sources = NewsSource::where('language', $language)
            ->where('is_active', true)
            ->orderBy('name')
            ->paginate($perPage);

        return response()->json([
            'data' => $sources->items(),
            'meta' => [
                'current_page' => $sources->currentPage(),
                'last_page' => $sources->lastPage(),
                'per_page' => $sources->perPage(),
                'total' => $sources->total(),
                'language' => $language,
            ]
        ]);
    }

    /**
     * Get statistics for news sources.
     */
    public function statistics(): JsonResponse
    {
        $stats = [
            'total_sources' => NewsSource::count(),
            'active_sources' => NewsSource::where('is_active', true)->count(),
            'sources_by_type' => NewsSource::selectRaw('type, COUNT(*) as count')
                ->groupBy('type')
                ->orderBy('count', 'desc')
                ->get(),
            'sources_by_language' => NewsSource::selectRaw('language, COUNT(*) as count')
                ->groupBy('language')
                ->orderBy('count', 'desc')
                ->get(),
            'average_credibility' => round(NewsSource::avg('credibility_score'), 2),
            'high_credibility_sources' => NewsSource::where('credibility_score', '>=', 8)->count(),
        ];

        return response()->json([
            'data' => $stats
        ]);
    }
}