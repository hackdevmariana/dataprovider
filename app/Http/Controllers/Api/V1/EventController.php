<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Http\Resources\V1\EventResource;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

/**
 * @group Events
 *
 * APIs para la gestión de eventos y actividades.
 * Permite consultar, filtrar y gestionar eventos de todo tipo.
 */
class EventController extends Controller
{
    /**
     * Display a listing of events
     *
     * Obtiene una lista de eventos con paginación.
     *
     * @queryParam page int Número de página. Example: 1
     * @queryParam per_page int Cantidad por página (máx 100). Example: 20
     *
     * @response 200 {
     *   "data": [
     *     {
     *       "id": 1,
     *       "title": "Feria de Energía Renovable",
     *       "slug": "feria-energia-renovable",
     *       "start_datetime": "2024-09-15T10:00:00Z",
     *       "end_datetime": "2024-09-15T18:00:00Z"
     *     }
     *   ],
     *   "meta": {...}
     * }
     *
     * @apiResourceCollection App\Http\Resources\V1\EventResource
     * @apiResourceModel App\Models\Event
     */
    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'page' => 'sometimes|integer|min:1',
            'per_page' => 'sometimes|integer|min:1|max:100'
        ]);

        $perPage = min($request->get('per_page', 20), 100);
        $events = Event::paginate($perPage);

        return response()->json([
            'data' => EventResource::collection($events),
            'meta' => [
                'current_page' => $events->currentPage(),
                'last_page' => $events->lastPage(),
                'per_page' => $events->perPage(),
                'total' => $events->total(),
            ]
        ]);
    }

    /**
     * Display the specified event
     *
     * Obtiene los detalles de un evento específico por ID o slug.
     *
     * @urlParam idOrSlug integer|string ID o slug del evento. Example: 1
     *
     * @response 200 {
     *   "data": {
     *     "id": 1,
     *     "title": "Feria de Energía Renovable",
     *     "slug": "feria-energia-renovable",
     *     "start_datetime": "2024-09-15T10:00:00Z",
     *     "end_datetime": "2024-09-15T18:00:00Z"
     *   }
     * }
     *
     * @response 404 {
     *   "message": "Evento no encontrado"
     * }
     *
     * @apiResourceModel App\Models\Event
     */
    public function show($idOrSlug): JsonResponse
    {
        $event = Event::where('slug', $idOrSlug)
            ->orWhere('id', $idOrSlug)
            ->firstOrFail();
            
        return response()->json([
            'data' => new EventResource($event)
        ]);
    }

    /**
     * Filter events by date range
     *
     * Filtra eventos por rango de fechas y otros criterios.
     *
     * @queryParam start_date string Fecha de inicio (YYYY-MM-DD). Example: 2024-01-01
     * @queryParam end_date string Fecha de fin (YYYY-MM-DD). Example: 2024-12-31
     * @queryParam municipality_id int Filtrar por ID del municipio. Example: 1
     * @queryParam province_id int Filtrar por ID de la provincia. Example: 1
     * @queryParam page int Número de página. Example: 1
     * @queryParam per_page int Cantidad por página (máx 100). Example: 20
     *
     * @response 200 {
     *   "data": [
     *     {
     *       "id": 1,
     *       "title": "Feria de Energía Renovable",
     *       "start_datetime": "2024-09-15T10:00:00Z"
     *     }
     *   ],
     *   "meta": {...}
     * }
     *
     * @apiResourceCollection App\Http\Resources\V1\EventResource
     * @apiResourceModel App\Models\Event
     */
    public function filterByDateRange(Request $request): JsonResponse
    {
        $request->validate([
            'start_date' => 'sometimes|date',
            'end_date' => 'sometimes|date|after_or_equal:start_date',
            'municipality_id' => 'sometimes|integer|exists:municipalities,id',
            'province_id' => 'sometimes|integer|exists:provinces,id',
            'page' => 'sometimes|integer|min:1',
            'per_page' => 'sometimes|integer|min:1|max:100'
        ]);

        $query = Event::query();

        if ($request->has('start_date')) {
            $query->whereDate('start_datetime', '>=', $request->start_date);
        }

        if ($request->has('end_date')) {
            $query->whereDate('end_datetime', '<=', $request->end_date);
        }

        if ($request->has('municipality_id')) {
            $query->where('municipality_id', $request->municipality_id);
        }

        if ($request->has('province_id')) {
            $query->whereHas('municipality', function ($q) use ($request) {
                $q->where('province_id', $request->province_id);
            });
        }

        $perPage = min($request->get('per_page', 20), 100);
        $events = $query->orderBy('start_datetime')->paginate($perPage);

        return response()->json([
            'data' => EventResource::collection($events),
            'meta' => [
                'current_page' => $events->currentPage(),
                'last_page' => $events->lastPage(),
                'per_page' => $events->perPage(),
                'total' => $events->total(),
            ]
        ]);
    }

    /**
     * Get upcoming events
     *
     * Obtiene eventos próximos a partir de la fecha actual.
     *
     * @queryParam days int Número de días hacia adelante. Example: 30
     * @queryParam limit int Límite de eventos a retornar. Example: 10
     *
     * @response 200 {
     *   "data": [
     *     {
     *       "id": 1,
     *       "title": "Feria de Energía Renovable",
     *       "start_datetime": "2024-09-15T10:00:00Z",
     *       "days_until": 5
     *     }
     *   ]
     * }
     *
     * @apiResourceCollection App\Http\Resources\V1\EventResource
     * @apiResourceModel App\Models\Event
     */
    public function upcoming(Request $request): JsonResponse
    {
        $request->validate([
            'days' => 'sometimes|integer|min:1|max:365',
            'limit' => 'sometimes|integer|min:1|max:100'
        ]);

        $days = $request->get('days', 30);
        $limit = min($request->get('limit', 10), 100);

        $events = Event::where('start_datetime', '>=', now())
            ->where('start_datetime', '<=', now()->addDays($days))
            ->orderBy('start_datetime')
            ->limit($limit)
            ->get();

        return response()->json([
            'data' => EventResource::collection($events)
        ]);
    }

    /**
     * Get events by category
     *
     * Obtiene eventos filtrados por categoría.
     *
     * @queryParam category string Categoría del evento. Example: energy
     * @queryParam page int Número de página. Example: 1
     * @queryParam per_page int Cantidad por página (máx 100). Example: 20
     *
     * @response 200 {
     *   "data": [
     *     {
     *       "id": 1,
     *       "title": "Feria de Energía Renovable",
     *       "category": "energy"
     *     }
     *   ],
     *   "meta": {...}
     * }
     *
     * @apiResourceCollection App\Http\Resources\V1\EventResource
     * @apiResourceModel App\Models\Event
     */
    public function byCategory(Request $request): JsonResponse
    {
        $request->validate([
            'category' => 'required|string|max:100',
            'page' => 'sometimes|integer|min:1',
            'per_page' => 'sometimes|integer|min:1|max:100'
        ]);

        $perPage = min($request->get('per_page', 20), 100);
        $events = Event::where('category', $request->category)
            ->orderBy('start_datetime')
            ->paginate($perPage);

        return response()->json([
            'data' => EventResource::collection($events),
            'meta' => [
                'current_page' => $events->currentPage(),
                'last_page' => $events->lastPage(),
                'per_page' => $events->perPage(),
                'total' => $events->total(),
            ]
        ]);
    }

    /**
     * Search events
     *
     * Busca eventos por título y descripción.
     *
     * @queryParam q string Término de búsqueda. Example: energía
     * @queryParam page int Número de página. Example: 1
     * @queryParam per_page int Cantidad por página (máx 100). Example: 20
     *
     * @response 200 {
     *   "data": [
     *     {
     *       "id": 1,
     *       "title": "Feria de Energía Renovable",
     *       "description": "Evento sobre energías renovables"
     *     }
     *   ],
     *   "meta": {...}
     * }
     *
     * @apiResourceCollection App\Http\Resources\V1\EventResource
     * @apiResourceModel App\Models\Event
     */
    public function search(Request $request): JsonResponse
    {
        $request->validate([
            'q' => 'required|string|max:255',
            'page' => 'sometimes|integer|min:1',
            'per_page' => 'sometimes|integer|min:1|max:100'
        ]);

        $perPage = min($request->get('per_page', 20), 100);
        $searchTerm = $request->q;

        $events = Event::where('title', 'LIKE', "%{$searchTerm}%")
            ->orWhere('description', 'LIKE', "%{$searchTerm}%")
            ->orderBy('start_datetime')
            ->paginate($perPage);

        return response()->json([
            'data' => EventResource::collection($events),
            'meta' => [
                'current_page' => $events->currentPage(),
                'last_page' => $events->lastPage(),
                'per_page' => $events->perPage(),
                'total' => $events->total(),
            ]
        ]);
    }

    /**
     * Get event statistics
     *
     * Obtiene estadísticas generales de eventos.
     *
     * @response 200 {
     *   "data": {
     *     "total_events": 150,
     *     "upcoming_events": 25,
     *     "past_events": 125,
     *     "events_this_month": 8,
     *     "events_by_category": {
     *       "energy": 45,
     *       "environment": 30,
     *       "technology": 25
     *     }
     *   }
     * }
     */
    public function statistics(): JsonResponse
    {
        $totalEvents = Event::count();
        $upcomingEvents = Event::where('start_datetime', '>=', now())->count();
        $pastEvents = Event::where('start_datetime', '<', now())->count();
        $eventsThisMonth = Event::whereMonth('start_datetime', now()->month)
            ->whereYear('start_datetime', now()->year)
            ->count();

        $eventsByCategory = Event::select('category', DB::raw('count(*) as count'))
            ->groupBy('category')
            ->pluck('count', 'category')
            ->toArray();

        return response()->json([
            'data' => [
                'total_events' => $totalEvents,
                'upcoming_events' => $upcomingEvents,
                'past_events' => $pastEvents,
                'events_this_month' => $eventsThisMonth,
                'events_by_category' => $eventsByCategory
            ]
        ]);
    }
}
