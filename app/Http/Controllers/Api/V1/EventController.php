<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Http\Resources\V1\EventResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * @OA\Tag(
 *     name="Events",
 *     description="API for managing events"
 * )
 */
class EventController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/v1/events",
     *     summary="Get all events",
     *     tags={"Events"},
     *     @OA\Response(
     *         response=200,
     *         description="List of events",
     *         @OA\JsonContent(type="object",
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Event"))
     *         )
     *     )
     * )
     */
    public function index()
    {
        return EventResource::collection(Event::paginate(20));
    }

    /**
     * @OA\Get(
     *     path="/api/v1/events/{idOrSlug}",
     *     summary="Get event by ID or slug",
     *     tags={"Events"},
     *     @OA\Parameter(
     *         name="idOrSlug",
     *         in="path",
     *         required=true,
     *         description="ID or slug of the event",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Event found",
     *         @OA\JsonContent(ref="#/components/schemas/Event")
     *     ),
     *     @OA\Response(response=404, description="Event not found")
     * )
     */
    public function show($idOrSlug)
    {
        $event = Event::where('slug', $idOrSlug)
            ->orWhere('id', $idOrSlug)
            ->firstOrFail();
        return new EventResource($event);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/events/filter/by-date-range",
     *     summary="Filter events by date range",
     *     tags={"Events"},
     *     @OA\Parameter(
     *         name="start_date",
     *         in="query",
     *         description="Start date (YYYY-MM-DD)",
     *         @OA\Schema(type="string", format="date", example="2024-01-01")
     *     ),
     *     @OA\Parameter(
     *         name="end_date",
     *         in="query",
     *         description="End date (YYYY-MM-DD)",
     *         @OA\Schema(type="string", format="date", example="2024-12-31")
     *     ),
     *     @OA\Parameter(
     *         name="municipality_id",
     *         in="query",
     *         description="Filter by municipality ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="province_id",
     *         in="query",
     *         description="Filter by province ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Events filtered by date range",
     *         @OA\JsonContent(type="array", @OA\Items())
     *     )
     * )
     */
    public function filterByDateRange(Request $request)
    {
        $query = Event::with(['venue', 'eventType', 'festival', 'artists']);

        // Filtro por fecha de inicio
        if ($request->has('start_date')) {
            $query->whereDate('start_datetime', '>=', $request->start_date);
        }

        // Filtro por fecha de fin
        if ($request->has('end_date')) {
            $query->whereDate('start_datetime', '<=', $request->end_date);
        }

        // Filtro por municipio
        if ($request->has('municipality_id')) {
            $query->whereHas('venue', function($q) use ($request) {
                $q->where('municipality_id', $request->municipality_id);
            });
        }

        // Filtro por provincia
        if ($request->has('province_id')) {
            $query->whereHas('venue.municipality', function($q) use ($request) {
                $q->where('province_id', $request->province_id);
            });
        }

        $events = $query->orderBy('start_datetime', 'asc')->paginate(20);
        
        return EventResource::collection($events);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/events/filter/by-location",
     *     summary="Filter events by geographical location",
     *     tags={"Events"},
     *     @OA\Parameter(
     *         name="latitude",
     *         in="query",
     *         description="Central latitude for search",
     *         @OA\Schema(type="number", format="float", example=40.4168)
     *     ),
     *     @OA\Parameter(
     *         name="longitude",
     *         in="query",
     *         description="Central longitude for search",
     *         @OA\Schema(type="number", format="float", example=-3.7038)
     *     ),
     *     @OA\Parameter(
     *         name="radius_km",
     *         in="query",
     *         description="Search radius in kilometers",
     *         @OA\Schema(type="number", format="float", example=50.0)
     *     ),
     *     @OA\Parameter(
     *         name="start_date",
     *         in="query",
     *         description="Start date filter (YYYY-MM-DD)",
     *         @OA\Schema(type="string", format="date")
     *     ),
     *     @OA\Parameter(
     *         name="end_date",
     *         in="query",
     *         description="End date filter (YYYY-MM-DD)",
     *         @OA\Schema(type="string", format="date")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Events filtered by location",
     *         @OA\JsonContent(type="array", @OA\Items())
     *     )
     * )
     */
    public function filterByLocation(Request $request)
    {
        $query = Event::with(['venue', 'eventType', 'festival', 'artists']);

        // Filtro geográfico por coordenadas y radio
        if ($request->has('latitude') && $request->has('longitude') && $request->has('radius_km')) {
            $lat = (float)$request->latitude;
            $lng = (float)$request->longitude;
            $radiusKm = (float)$request->radius_km;

            // Usando la fórmula de Haversine para calcular distancia
            $query->whereHas('venue', function($q) use ($lat, $lng, $radiusKm) {
                $q->whereNotNull('latitude')
                  ->whereNotNull('longitude')
                  ->whereRaw(
                      '(6371 * acos(cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) + sin(radians(?)) * sin(radians(latitude)))) <= ?',
                      [$lat, $lng, $lat, $radiusKm]
                  );
            });
        }

        // Filtros adicionales de fecha
        if ($request->has('start_date')) {
            $query->whereDate('start_datetime', '>=', $request->start_date);
        }

        if ($request->has('end_date')) {
            $query->whereDate('start_datetime', '<=', $request->end_date);
        }

        $events = $query->orderBy('start_datetime', 'asc')->paginate(20);
        
        return EventResource::collection($events);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/events/filter/by-type",
     *     summary="Filter events by type and other criteria",
     *     tags={"Events"},
     *     @OA\Parameter(
     *         name="event_type_id",
     *         in="query",
     *         description="Event type ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="event_type_slug",
     *         in="query",
     *         description="Event type slug",
     *         @OA\Schema(type="string", example="concierto")
     *     ),
     *     @OA\Parameter(
     *         name="has_festival",
     *         in="query",
     *         description="Filter by events that are part of a festival",
     *         @OA\Schema(type="boolean", example=true)
     *     ),
     *     @OA\Parameter(
     *         name="is_free",
     *         in="query",
     *         description="Filter by free events",
     *         @OA\Schema(type="boolean", example=true)
     *     ),
     *     @OA\Parameter(
     *         name="start_date",
     *         in="query",
     *         description="Start date filter (YYYY-MM-DD)",
     *         @OA\Schema(type="string", format="date")
     *     ),
     *     @OA\Parameter(
     *         name="end_date",
     *         in="query",
     *         description="End date filter (YYYY-MM-DD)",
     *         @OA\Schema(type="string", format="date")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Events filtered by type",
     *         @OA\JsonContent(type="array", @OA\Items())
     *     )
     * )
     */
    public function filterByType(Request $request)
    {
        $query = Event::with(['venue', 'eventType', 'festival', 'artists']);

        // Filtro por tipo de evento (ID)
        if ($request->has('event_type_id')) {
            $query->where('event_type_id', $request->event_type_id);
        }

        // Filtro por tipo de evento (slug)
        if ($request->has('event_type_slug')) {
            $query->whereHas('eventType', function($q) use ($request) {
                $q->where('slug', $request->event_type_slug);
            });
        }

        // Filtro por eventos que forman parte de un festival
        if ($request->has('has_festival')) {
            if ($request->boolean('has_festival')) {
                $query->whereNotNull('festival_id');
            } else {
                $query->whereNull('festival_id');
            }
        }

        // Filtro por eventos gratuitos
        if ($request->has('is_free')) {
            if ($request->boolean('is_free')) {
                $query->where(function($q) {
                    $q->whereNull('price')
                      ->orWhere('price', 0);
                });
            } else {
                $query->where('price', '>', 0);
            }
        }

        // Filtros de fecha
        if ($request->has('start_date')) {
            $query->whereDate('start_datetime', '>=', $request->start_date);
        }

        if ($request->has('end_date')) {
            $query->whereDate('start_datetime', '<=', $request->end_date);
        }

        $events = $query->orderBy('start_datetime', 'asc')->paginate(20);
        
        return EventResource::collection($events);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/events/search",
     *     summary="Advanced event search",
     *     tags={"Events"},
     *     @OA\Parameter(
     *         name="q",
     *         in="query",
     *         description="Search query for event name or description",
     *         @OA\Schema(type="string", example="concierto rock")
     *     ),
     *     @OA\Parameter(
     *         name="artist_name",
     *         in="query",
     *         description="Search by artist name",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="venue_name",
     *         in="query",
     *         description="Search by venue name",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="municipality_slug",
     *         in="query",
     *         description="Municipality slug",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="province_slug",
     *         in="query",
     *         description="Province slug",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="autonomous_community_slug",
     *         in="query",
     *         description="Autonomous community slug",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="start_date",
     *         in="query",
     *         description="Start date filter (YYYY-MM-DD)",
     *         @OA\Schema(type="string", format="date")
     *     ),
     *     @OA\Parameter(
     *         name="end_date",
     *         in="query",
     *         description="End date filter (YYYY-MM-DD)",
     *         @OA\Schema(type="string", format="date")
     *     ),
     *     @OA\Parameter(
     *         name="sort_by",
     *         in="query",
     *         description="Sort field: start_datetime, name, price",
     *         @OA\Schema(type="string", enum={"start_datetime", "name", "price"}, example="start_datetime")
     *     ),
     *     @OA\Parameter(
     *         name="sort_direction",
     *         in="query",
     *         description="Sort direction",
     *         @OA\Schema(type="string", enum={"asc", "desc"}, example="asc")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Search results",
     *         @OA\JsonContent(type="array", @OA\Items())
     *     )
     * )
     */
    public function search(Request $request)
    {
        $query = Event::with(['venue', 'eventType', 'festival', 'artists']);

        // Búsqueda por texto en nombre y descripción
        if ($request->has('q') && !empty($request->q)) {
            $searchTerm = $request->q;
            $query->where(function($q) use ($searchTerm) {
                $q->where('name', 'LIKE', '%' . $searchTerm . '%')
                  ->orWhere('description', 'LIKE', '%' . $searchTerm . '%');
            });
        }

        // Búsqueda por artista
        if ($request->has('artist_name')) {
            $query->whereHas('artists', function($q) use ($request) {
                $q->where('name', 'LIKE', '%' . $request->artist_name . '%');
            });
        }

        // Búsqueda por venue
        if ($request->has('venue_name')) {
            $query->whereHas('venue', function($q) use ($request) {
                $q->where('name', 'LIKE', '%' . $request->venue_name . '%');
            });
        }

        // Filtros geográficos
        if ($request->has('municipality_slug')) {
            $query->whereHas('venue.municipality', function($q) use ($request) {
                $q->where('slug', $request->municipality_slug);
            });
        }

        if ($request->has('province_slug')) {
            $query->whereHas('venue.municipality.province', function($q) use ($request) {
                $q->where('slug', $request->province_slug);
            });
        }

        if ($request->has('autonomous_community_slug')) {
            $query->whereHas('venue.municipality.autonomousCommunity', function($q) use ($request) {
                $q->where('slug', $request->autonomous_community_slug);
            });
        }

        // Filtros de fecha
        if ($request->has('start_date')) {
            $query->whereDate('start_datetime', '>=', $request->start_date);
        }

        if ($request->has('end_date')) {
            $query->whereDate('start_datetime', '<=', $request->end_date);
        }

        // Ordenamiento
        $sortBy = $request->get('sort_by', 'start_datetime');
        $sortDirection = $request->get('sort_direction', 'asc');
        
        $allowedSortFields = ['start_datetime', 'name', 'price', 'created_at'];
        if (!in_array($sortBy, $allowedSortFields)) {
            $sortBy = 'start_datetime';
        }
        
        if (!in_array($sortDirection, ['asc', 'desc'])) {
            $sortDirection = 'asc';
        }

        $query->orderBy($sortBy, $sortDirection);

        $events = $query->paginate(20);
        
        return EventResource::collection($events);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/events/upcoming/{days}",
     *     summary="Get upcoming events in the next N days",
     *     tags={"Events"},
     *     @OA\Parameter(
     *         name="days",
     *         in="path",
     *         description="Number of days to look ahead",
     *         required=true,
     *         @OA\Schema(type="integer", minimum=1, maximum=365, example=7)
     *     ),
     *     @OA\Parameter(
     *         name="municipality_id",
     *         in="query",
     *         description="Filter by municipality",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="event_type_slug",
     *         in="query",
     *         description="Filter by event type",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Upcoming events",
     *         @OA\JsonContent(type="array", @OA\Items())
     *     )
     * )
     */
    public function upcoming(Request $request, $days = 7)
    {
        $days = min(max((int)$days, 1), 365); // Entre 1 y 365 días
        
        $startDate = now()->startOfDay();
        $endDate = now()->addDays($days)->endOfDay();

        $query = Event::with(['venue', 'eventType', 'festival', 'artists'])
            ->whereDate('start_datetime', '>=', $startDate->toDateString())
            ->whereDate('start_datetime', '<=', $endDate->toDateString());

        // Filtros opcionales
        if ($request->has('municipality_id')) {
            $query->whereHas('venue', function($q) use ($request) {
                $q->where('municipality_id', $request->municipality_id);
            });
        }

        if ($request->has('event_type_slug')) {
            $query->whereHas('eventType', function($q) use ($request) {
                $q->where('slug', $request->event_type_slug);
            });
        }

        $events = $query->orderBy('start_datetime', 'asc')->paginate(20);
        
        return EventResource::collection($events);
    }
}
