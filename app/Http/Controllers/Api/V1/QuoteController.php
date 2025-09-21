<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Quote;
use App\Models\QuoteCategory;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;
/**
 * @OA\Tag(name="Quotes")
 */
class QuoteController extends Controller
{
    /**
     * Display a listing of quotes with filtering and pagination.
     * 
     * @OA\Get(
     *     path="/quotes",
     *     summary="Listar citas con filtros y paginación",
     *     description="Obtiene una lista paginada de citas con filtros opcionales",
     *     tags={"Quotes"},
     *     @OA\Parameter(
     *         name="search",
     *         description="Término de búsqueda",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string", maxLength=100)
     *     ),
     *     @OA\Parameter(
     *         name="category",
     *         description="Filtrar por categoría",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string", maxLength=100)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Lista de citas obtenida exitosamente"
     *     )
     * )
     */
    public function index(Request $request): JsonResponse
    {
        $query = Quote::query();

        // Filtros
        if ($request->filled('category')) {
            $query->byCategory($request->category);
        }

        if ($request->filled('language')) {
            $query->byLanguage($request->language);
        }

        if ($request->filled('mood')) {
            $query->byMood($request->mood);
        }

        if ($request->filled('difficulty')) {
            $query->byDifficulty($request->difficulty);
        }

        if ($request->filled('author')) {
            $query->byAuthor($request->author);
        }

        if ($request->filled('verified')) {
            if ($request->boolean('verified')) {
                $query->verified();
            }
        }

        if ($request->filled('popular')) {
            if ($request->boolean('popular')) {
                $query->popular($request->float('popular', 0.6));
            }
        }

        if ($request->filled('search')) {
            $query->search($request->search);
        }

        // Ordenamiento
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        
        if (in_array($sortBy, ['popularity_score', 'usage_count', 'word_count', 'created_at'])) {
            $query->orderBy($sortBy, $sortOrder);
        }

        // Paginación
        $perPage = min($request->get('per_page', 15), 100);
        $quotes = $query->paginate($perPage);

        return response()->json([
            'data' => $quotes->items(),
            'meta' => [
                'current_page' => $quotes->currentPage(),
                'last_page' => $quotes->lastPage(),
                'per_page' => $quotes->perPage(),
                'total' => $quotes->total(),
            ]
        ]);
    }

    /**
     * Store a newly created quote.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'text' => 'required|string|max:2000',
            'author' => 'nullable|string|max:255',
            'source' => 'nullable|string|max:255',
            'language' => 'required|string|max:10',
            'category' => 'nullable|string|max:100',
            'tags' => 'nullable|array',
            'tags.*' => 'string|max:50',
            'mood' => ['nullable', Rule::in(['inspiring', 'motivational', 'philosophical', 'humorous', 'romantic', 'melancholic', 'energetic', 'calm', 'mysterious', 'optimistic', 'pessimistic'])],
            'difficulty_level' => ['nullable', Rule::in(['easy', 'medium', 'hard', 'expert'])],
            'is_verified' => 'boolean',
        ]);

        // Calcular métricas automáticamente
        $validated['word_count'] = str_word_count($validated['text']);
        $validated['character_count'] = strlen($validated['text']);
        $validated['popularity_score'] = 0.1; // Score inicial
        $validated['usage_count'] = 0;

        $quote = Quote::create($validated);

        return response()->json([
            'data' => $quote,
            'message' => 'Cita creada exitosamente'
        ], 201);
    }

    /**
     * Display the specified quote.
     */
    public function show(Quote $quote): JsonResponse
    {
        return response()->json([
            'data' => $quote->load('category', 'collections')
        ]);
    }

    /**
     * Update the specified quote.
     */
    public function update(Request $request, Quote $quote): JsonResponse
    {
        $validated = $request->validate([
            'text' => 'sometimes|required|string|max:2000',
            'author' => 'nullable|string|max:255',
            'source' => 'nullable|string|max:255',
            'language' => 'sometimes|required|string|max:10',
            'category' => 'nullable|string|max:100',
            'tags' => 'nullable|array',
            'tags.*' => 'string|max:50',
            'mood' => ['nullable', Rule::in(['inspiring', 'motivational', 'philosophical', 'humorous', 'romantic', 'melancholic', 'energetic', 'calm', 'mysterious', 'optimistic', 'pessimistic'])],
            'difficulty_level' => ['nullable', Rule::in(['easy', 'medium', 'hard', 'expert'])],
            'is_verified' => 'boolean',
        ]);

        // Recalcular métricas si cambia el texto
        if (isset($validated['text'])) {
            $validated['word_count'] = str_word_count($validated['text']);
            $validated['character_count'] = strlen($validated['text']);
        }

        $quote->update($validated);

        return response()->json([
            'data' => $quote->fresh(),
            'message' => 'Cita actualizada exitosamente'
        ]);
    }

    /**
     * Remove the specified quote.
     */
    public function destroy(Quote $quote): JsonResponse
    {
        $quote->delete();

        return response()->json([
            'message' => 'Cita eliminada exitosamente'
        ]);
    }

    /**
     * Get quotes by category.
     */
    public function byCategory(Request $request, string $category): JsonResponse
    {
        $perPage = min($request->get('per_page', 15), 100);
        $quotes = Quote::byCategory($category)->paginate($perPage);

        return response()->json([
            'data' => $quotes->items(),
            'meta' => [
                'current_page' => $quotes->currentPage(),
                'last_page' => $quotes->lastPage(),
                'per_page' => $quotes->perPage(),
                'total' => $quotes->total(),
            ]
        ]);
    }

    /**
     * Get quotes by mood.
     */
    public function byMood(Request $request, string $mood): JsonResponse
    {
        $perPage = min($request->get('per_page', 15), 100);
        $quotes = Quote::byMood($mood)->paginate($perPage);

        return response()->json([
            'data' => $quotes->items(),
            'meta' => [
                'current_page' => $quotes->currentPage(),
                'last_page' => $quotes->lastPage(),
                'per_page' => $quotes->perPage(),
                'total' => $quotes->total(),
            ]
        ]);
    }

    /**
     * Get popular quotes.
     */
    public function popular(Request $request): JsonResponse
    {
        $perPage = min($request->get('per_page', 15), 100);
        $minScore = $request->get('min_score', 0.6);
        
        $quotes = Quote::popular($minScore)
            ->orderBy('popularity_score', 'desc')
            ->paginate($perPage);

        return response()->json([
            'data' => $quotes->items(),
            'meta' => [
                'current_page' => $quotes->currentPage(),
                'last_page' => $quotes->lastPage(),
                'per_page' => $quotes->perPage(),
                'total' => $quotes->total(),
            ]
        ]);
    }

    /**
     * Get random quote.
     */
    public function random(Request $request): JsonResponse
    {
        $query = Quote::query();

        if ($request->filled('category')) {
            $query->byCategory($request->category);
        }

        if ($request->filled('mood')) {
            $query->byMood($request->mood);
        }

        if ($request->filled('verified')) {
            if ($request->boolean('verified')) {
                $query->verified();
            }
        }

        $quote = $query->inRandomOrder()->first();

        if (!$quote) {
            return response()->json([
                'message' => 'No se encontraron citas con los filtros especificados'
            ], 404);
        }

        // Incrementar contador de uso
        $quote->incrementUsage();

        return response()->json([
            'data' => $quote
        ]);
    }

    /**
     * Search quotes.
     */
    public function search(Request $request): JsonResponse
    {
        $request->validate([
            'q' => 'required|string|min:2|max:100'
        ]);

        $perPage = min($request->get('per_page', 15), 100);
        $quotes = Quote::search($request->q)->paginate($perPage);

        return response()->json([
            'data' => $quotes->items(),
            'meta' => [
                'current_page' => $quotes->currentPage(),
                'last_page' => $quotes->lastPage(),
                'per_page' => $quotes->perPage(),
                'total' => $quotes->total(),
                'search_query' => $request->q,
            ]
        ]);
    }

    /**
     * Get quote statistics.
     */
    public function statistics(): JsonResponse
    {
        $stats = [
            'total_quotes' => Quote::count(),
            'verified_quotes' => Quote::verified()->count(),
            'total_categories' => Quote::distinct('category')->count(),
            'total_languages' => Quote::distinct('language')->count(),
            'most_popular_category' => Quote::selectRaw('category, COUNT(*) as count')
                ->groupBy('category')
                ->orderBy('count', 'desc')
                ->first(),
            'most_popular_mood' => Quote::selectRaw('mood, COUNT(*) as count')
                ->whereNotNull('mood')
                ->groupBy('mood')
                ->orderBy('count', 'desc')
                ->first(),
            'average_word_count' => round(Quote::avg('word_count'), 2),
            'average_character_count' => round(Quote::avg('character_count'), 2),
            'total_usage' => Quote::sum('usage_count'),
        ];

        return response()->json([
            'data' => $stats
        ]);
    }

    /**
     * Increment quote usage.
     */
    public function incrementUsage(Quote $quote): JsonResponse
    {
        $quote->incrementUsage();

        return response()->json([
            'data' => $quote->fresh(['usage_count']),
            'message' => 'Contador de uso incrementado'
        ]);
    }
}