<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\HistoricalEvent;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Carbon\Carbon;

#[OA\Tag(name: "Historical Events")]
class HistoricalEventController extends Controller
{
    /**
     * Display a listing of historical events.
     */
    public function index(Request $request): JsonResponse
    {
        $query = HistoricalEvent::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('location', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('year_from')) {
            $query->where('year', '>=', $request->year_from);
        }

        if ($request->filled('year_to')) {
            $query->where('year', '<=', $request->year_to);
        }

        if ($request->filled('importance')) {
            $query->where('importance', $request->importance);
        }

        $sortBy = $request->get('sort_by', 'year');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $perPage = min($request->get('per_page', 15), 100);
        $events = $query->paginate($perPage);

        return response()->json([
            'data' => $events->items(),
            'meta' => [
                'current_page' => $events->currentPage(),
                'last_page' => $events->lastPage(),
                'per_page' => $events->perPage(),
                'total' => $events->total(),
            ]
        ]);
    }

    /**
     * Store a newly created historical event.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:2000',
            'year' => 'required|integer|min:1|max:9999',
            'month' => 'nullable|integer|min:1|max:12',
            'day' => 'nullable|integer|min:1|max:31',
            'category' => 'required|string|max:100',
            'importance' => 'required|string|in:low,medium,high,critical',
            'location' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:100',
            'region' => 'nullable|string|max:100',
            'participants' => 'nullable|string|max:1000',
            'outcome' => 'nullable|string|max:1000',
            'sources' => 'nullable|array',
            'tags' => 'nullable|array',
            'is_verified' => 'boolean',
        ]);

        $event = HistoricalEvent::create($validated);

        return response()->json([
            'data' => $event,
            'message' => 'Evento histórico creado exitosamente'
        ], 201);
    }

    /**
     * Display the specified historical event.
     */
    public function show(HistoricalEvent $historicalEvent): JsonResponse
    {
        return response()->json([
            'data' => $historicalEvent
        ]);
    }

    /**
     * Update the specified historical event.
     */
    public function update(Request $request, HistoricalEvent $historicalEvent): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string|max:2000',
            'year' => 'sometimes|required|integer|min:1|max:9999',
            'month' => 'nullable|integer|min:1|max:12',
            'day' => 'nullable|integer|min:1|max:31',
            'category' => 'sometimes|required|string|max:100',
            'importance' => 'sometimes|required|string|in:low,medium,high,critical',
            'location' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:100',
            'region' => 'nullable|string|max:100',
            'participants' => 'nullable|string|max:1000',
            'outcome' => 'nullable|string|max:1000',
            'sources' => 'nullable|array',
            'tags' => 'nullable|array',
            'is_verified' => 'boolean',
        ]);

        $historicalEvent->update($validated);

        return response()->json([
            'data' => $historicalEvent->fresh(),
            'message' => 'Evento histórico actualizado exitosamente'
        ]);
    }

    /**
     * Remove the specified historical event.
     */
    public function destroy(HistoricalEvent $historicalEvent): JsonResponse
    {
        $historicalEvent->delete();

        return response()->json([
            'message' => 'Evento histórico eliminado exitosamente'
        ]);
    }

    /**
     * Get events by year.
     */
    public function byYear(Request $request, int $year): JsonResponse
    {
        $perPage = min($request->get('per_page', 15), 100);
        $events = HistoricalEvent::where('year', $year)
            ->orderBy('month', 'asc')
            ->orderBy('day', 'asc')
            ->paginate($perPage);

        return response()->json([
            'data' => $events->items(),
            'meta' => [
                'current_page' => $events->currentPage(),
                'last_page' => $events->lastPage(),
                'per_page' => $events->perPage(),
                'total' => $events->total(),
                'year' => $year,
            ]
        ]);
    }

    /**
     * Get events by category.
     */
    public function byCategory(Request $request, string $category): JsonResponse
    {
        $perPage = min($request->get('per_page', 15), 100);
        $events = HistoricalEvent::where('category', $category)
            ->orderBy('year', 'desc')
            ->paginate($perPage);

        return response()->json([
            'data' => $events->items(),
            'meta' => [
                'current_page' => $events->currentPage(),
                'last_page' => $events->lastPage(),
                'per_page' => $events->perPage(),
                'total' => $events->total(),
                'category' => $category,
            ]
        ]);
    }

    /**
     * Get events by importance level.
     */
    public function byImportance(Request $request, string $importance): JsonResponse
    {
        $perPage = min($request->get('per_page', 15), 100);
        $events = HistoricalEvent::where('importance', $importance)
            ->orderBy('year', 'desc')
            ->paginate($perPage);

        return response()->json([
            'data' => $events->items(),
            'meta' => [
                'current_page' => $events->currentPage(),
                'last_page' => $events->lastPage(),
                'per_page' => $events->perPage(),
                'total' => $events->total(),
                'importance' => $importance,
            ]
        ]);
    }

    /**
     * Get events by date range.
     */
    public function byDateRange(Request $request): JsonResponse
    {
        $request->validate([
            'start_year' => 'required|integer|min:1|max:9999',
            'end_year' => 'required|integer|min:1|max:9999|gte:start_year',
        ]);

        $perPage = min($request->get('per_page', 15), 100);
        $events = HistoricalEvent::whereBetween('year', [$request->start_year, $request->end_year])
            ->orderBy('year', 'asc')
            ->paginate($perPage);

        return response()->json([
            'data' => $events->items(),
            'meta' => [
                'current_page' => $events->currentPage(),
                'last_page' => $events->lastPage(),
                'per_page' => $events->perPage(),
                'total' => $events->total(),
                'start_year' => $request->start_year,
                'end_year' => $request->end_year,
            ]
        ]);
    }

    /**
     * Get statistics for historical events.
     */
    public function statistics(): JsonResponse
    {
        $stats = [
            'total_events' => HistoricalEvent::count(),
            'verified_events' => HistoricalEvent::where('is_verified', true)->count(),
            'events_by_category' => HistoricalEvent::selectRaw('category, COUNT(*) as count')
                ->groupBy('category')
                ->orderBy('count', 'desc')
                ->get(),
            'events_by_importance' => HistoricalEvent::selectRaw('importance, COUNT(*) as count')
                ->groupBy('importance')
                ->orderBy('count', 'desc')
                ->get(),
            'events_by_century' => HistoricalEvent::selectRaw('FLOOR(year/100) as century, COUNT(*) as count')
                ->groupBy('century')
                ->orderBy('century', 'asc')
                ->get(),
            'oldest_event' => HistoricalEvent::orderBy('year', 'asc')->first(),
            'newest_event' => HistoricalEvent::orderBy('year', 'desc')->first(),
        ];

        return response()->json([
            'data' => $stats
        ]);
    }
}