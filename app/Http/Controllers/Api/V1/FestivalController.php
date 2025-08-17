<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Festival;
use App\Http\Resources\V1\FestivalResource;
use App\Http\Requests\StoreFestivalRequest;
use App\Models\Event;
use App\Models\Artist;
use App\Models\Municipality;
use App\Models\Region;
use App\Models\Province;
use App\Models\AutonomousCommunity;
use App\Http\Resources\V1\EventResource;
use App\Http\Resources\V1\ArtistResource;
use Illuminate\Support\Facades\DB;

/**
 * @OA\Tag(
 *     name="Festivals",
 *     description="Festival management"
 * )
 */
class FestivalController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/v1/festivals",
     *     summary="Get all festivals",
     *     tags={"Festivals"},
     *     @OA\Response(response=200, description="List of festivals")
     * )
     */
    public function index()
    {
        return FestivalResource::collection(Festival::paginate(20));
    }

    /**
     * @OA\Get(
     *     path="/api/v1/festivals/{id}",
     *     summary="Get a festival by ID",
     *     tags={"Festivals"},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Festival found"),
     *     @OA\Response(response=404, description="Festival not found")
     * )
     */
    public function show($id)
    {
        $festival = Festival::findOrFail($id);
        return new FestivalResource($festival);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/festivals",
     *     summary="Create a new festival (public)",
     *     tags={"Festivals"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "slug"},
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="slug", type="string"),
     *             @OA\Property(property="description", type="string"),
     *             @OA\Property(property="month", type="integer"),
     *             @OA\Property(property="usual_days", type="string"),
     *             @OA\Property(property="recurring", type="boolean"),
     *             @OA\Property(property="location_id", type="integer"),
     *             @OA\Property(property="logo_url", type="string"),
     *             @OA\Property(property="color_theme", type="string"),
     *         )
     *     ),
     *     @OA\Response(response=201, description="Festival created"),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function store(StoreFestivalRequest $request)
    {
        $festival = \App\Models\Festival::create($request->validated());
        return (new FestivalResource($festival))->response()->setStatusCode(201);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/festivals/{id}/events",
     *     summary="Get events of a festival",
     *     tags={"Festivals"},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="List of events")
     * )
     */
    public function events($id)
    {
        $festival = Festival::findOrFail($id);
        $events = $festival->events()->paginate(20);
        return EventResource::collection($events);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/festivals/{id}/artists",
     *     summary="Get artists of a festival",
     *     tags={"Festivals"},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="List of artists")
     * )
     */
    public function artists($id)
    {
        $festival = Festival::findOrFail($id);
        $artists = $festival->artists()->paginate(20);
        return ArtistResource::collection($artists);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/festivals/municipality/{idOrSlug}",
     *     summary="Get festivals by municipality",
     *     tags={"Festivals"},
     *     @OA\Parameter(name="idOrSlug", in="path", required=true, @OA\Schema(type="string")),
     *     @OA\Response(response=200, description="List of festivals")
     * )
     */
    public function byMunicipality($idOrSlug)
    {
        $municipality = Municipality::where('id', $idOrSlug)->orWhere('slug', $idOrSlug)->firstOrFail();
        $festivals = Festival::where('location_id', $municipality->id)->paginate(20);
        return FestivalResource::collection($festivals);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/festivals/region/{idOrSlug}",
     *     summary="Get festivals by region",
     *     tags={"Festivals"},
     *     @OA\Parameter(name="idOrSlug", in="path", required=true, @OA\Schema(type="string")),
     *     @OA\Response(response=200, description="List of festivals")
     * )
     */
    public function byRegion($idOrSlug)
    {
        $region = Region::where('id', $idOrSlug)->orWhere('slug', $idOrSlug)->firstOrFail();
        $municipalityIds = $region->municipalities->pluck('id');
        $festivals = Festival::whereIn('location_id', $municipalityIds)->paginate(20);
        return FestivalResource::collection($festivals);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/festivals/province/{idOrSlug}",
     *     summary="Get festivals by province",
     *     tags={"Festivals"},
     *     @OA\Parameter(name="idOrSlug", in="path", required=true, @OA\Schema(type="string")),
     *     @OA\Response(response=200, description="List of festivals")
     * )
     */
    public function byProvince($idOrSlug)
    {
        $province = Province::where('id', $idOrSlug)->orWhere('slug', $idOrSlug)->firstOrFail();
        $municipalityIds = $province->municipalities->pluck('id');
        $festivals = Festival::whereIn('location_id', $municipalityIds)->paginate(20);
        return FestivalResource::collection($festivals);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/festivals/autonomous-community/{idOrSlug}",
     *     summary="Get festivals by autonomous community",
     *     tags={"Festivals"},
     *     @OA\Parameter(name="idOrSlug", in="path", required=true, @OA\Schema(type="string")),
     *     @OA\Response(response=200, description="List of festivals")
     * )
     */
    public function byAutonomousCommunity($idOrSlug)
    {
        $community = AutonomousCommunity::where('id', $idOrSlug)->orWhere('slug', $idOrSlug)->firstOrFail();
        $municipalityIds = $community->municipalities->pluck('id');
        $festivals = Festival::whereIn('location_id', $municipalityIds)->paginate(20);
        return FestivalResource::collection($festivals);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/festivals/today",
     *     summary="Get festivals happening today",
     *     tags={"Festivals"},
     *     @OA\Response(response=200, description="List of festivals")
     * )
     */
    public function today()
    {
        $today = now()->toDateString();
        $festivals = Festival::whereHas('events', function($q) use ($today) {
            $q->whereDate('start_datetime', $today);
        })->paginate(20);
        return FestivalResource::collection($festivals);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/festivals/this-week",
     *     summary="Get festivals happening this week",
     *     tags={"Festivals"},
     *     @OA\Response(response=200, description="List of festivals")
     * )
     */
    public function thisWeek()
    {
        $start = now()->startOfWeek()->toDateString();
        $end = now()->endOfWeek()->toDateString();
        $festivals = Festival::whereHas('events', function($q) use ($start, $end) {
            $q->whereBetween(DB::raw('DATE(start_datetime)'), [$start, $end]);
        })->paginate(20);
        return FestivalResource::collection($festivals);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/festivals/this-month",
     *     summary="Get festivals happening this month",
     *     tags={"Festivals"},
     *     @OA\Response(response=200, description="List of festivals")
     * )
     */
    public function thisMonth()
    {
        $start = now()->startOfMonth()->toDateString();
        $end = now()->endOfMonth()->toDateString();
        $festivals = Festival::whereHas('events', function($q) use ($start, $end) {
            $q->whereBetween(DB::raw('DATE(start_datetime)'), [$start, $end]);
        })->paginate(20);
        return FestivalResource::collection($festivals);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/festivals/this-year",
     *     summary="Get festivals happening this year",
     *     tags={"Festivals"},
     *     @OA\Response(response=200, description="List of festivals")
     * )
     */
    public function thisYear()
    {
        $start = now()->startOfYear()->toDateString();
        $end = now()->endOfYear()->toDateString();
        $festivals = Festival::whereHas('events', function($q) use ($start, $end) {
            $q->whereBetween(DB::raw('DATE(start_datetime)'), [$start, $end]);
        })->paginate(20);
        return FestivalResource::collection($festivals);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/festivals-and-unassigned-events",
     *     summary="Get all festivals and events not assigned to a festival",
     *     tags={"Festivals"},
     *     @OA\Response(response=200, description="Festivals and unassigned events")
     * )
     */
    public function festivalsAndUnassignedEvents()
    {
        $festivals = Festival::paginate(20);
        $events = Event::whereNull('festival_id')->paginate(20);
        return response()->json([
            'festivals' => FestivalResource::collection($festivals),
            'unassigned_events' => EventResource::collection($events),
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/festivals-with-events-and-unassigned",
     *     summary="Get all festivals with their events, and events not assigned to a festival",
     *     tags={"Festivals"},
     *     @OA\Response(response=200, description="Festivals with events and unassigned events")
     * )
     */
    public function festivalsWithEventsAndUnassigned()
    {
        $festivals = Festival::with(['events'])->paginate(20);
        $events = Event::whereNull('festival_id')->paginate(20);
        return response()->json([
            'festivals' => FestivalResource::collection($festivals),
            'unassigned_events' => EventResource::collection($events),
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/festivals/filter/by-location",
     *     summary="Filter festivals by geographical location",
     *     tags={"Festivals"},
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
     *         @OA\Schema(type="number", format="float", example=100.0)
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
     *         description="Festivals filtered by location",
     *         @OA\JsonContent(type="array", @OA\Items())
     *     )
     * )
     */
    public function filterByLocation(Request $request)
    {
        $query = Festival::with(['events.venue', 'events.eventType']);

        // Filtro geográfico por coordenadas y radio
        if ($request->has('latitude') && $request->has('longitude') && $request->has('radius_km')) {
            $lat = (float)$request->latitude;
            $lng = (float)$request->longitude;
            $radiusKm = (float)$request->radius_km;

            // Filtrar festivales que tienen eventos en venues dentro del radio
            $query->whereHas('events.venue', function($q) use ($lat, $lng, $radiusKm) {
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
            $query->whereHas('events', function($q) use ($request) {
                $q->whereDate('start_datetime', '>=', $request->start_date);
            });
        }

        if ($request->has('end_date')) {
            $query->whereHas('events', function($q) use ($request) {
                $q->whereDate('start_datetime', '<=', $request->end_date);
            });
        }

        $festivals = $query->paginate(20);
        
        return FestivalResource::collection($festivals);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/festivals/search",
     *     summary="Advanced festival search",
     *     tags={"Festivals"},
     *     @OA\Parameter(
     *         name="q",
     *         in="query",
     *         description="Search query for festival name or description",
     *         @OA\Schema(type="string", example="música rock")
     *     ),
     *     @OA\Parameter(
     *         name="artist_name",
     *         in="query",
     *         description="Search by artist name",
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
     *         name="min_duration_days",
     *         in="query",
     *         description="Minimum festival duration in days",
     *         @OA\Schema(type="integer", example=3)
     *     ),
     *     @OA\Parameter(
     *         name="sort_by",
     *         in="query",
     *         description="Sort field: start_date, name, duration",
     *         @OA\Schema(type="string", enum={"start_date", "name", "duration"}, example="start_date")
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
        $query = Festival::with(['events.venue', 'events.eventType', 'events.artists']);

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
            $query->whereHas('events.artists', function($q) use ($request) {
                $q->where('name', 'LIKE', '%' . $request->artist_name . '%');
            });
        }

        // Filtros geográficos
        if ($request->has('municipality_slug')) {
            $query->whereHas('events.venue.municipality', function($q) use ($request) {
                $q->where('slug', $request->municipality_slug);
            });
        }

        if ($request->has('province_slug')) {
            $query->whereHas('events.venue.municipality.province', function($q) use ($request) {
                $q->where('slug', $request->province_slug);
            });
        }

        if ($request->has('autonomous_community_slug')) {
            $query->whereHas('events.venue.municipality.autonomousCommunity', function($q) use ($request) {
                $q->where('slug', $request->autonomous_community_slug);
            });
        }

        // Filtros de fecha
        if ($request->has('start_date')) {
            $query->whereDate('start_date', '>=', $request->start_date);
        }

        if ($request->has('end_date')) {
            $query->whereDate('end_date', '<=', $request->end_date);
        }

        // Filtro por duración mínima
        if ($request->has('min_duration_days')) {
            $minDays = (int)$request->min_duration_days;
            $query->whereRaw('DATEDIFF(end_date, start_date) + 1 >= ?', [$minDays]);
        }

        // Ordenamiento
        $sortBy = $request->get('sort_by', 'start_date');
        $sortDirection = $request->get('sort_direction', 'asc');
        
        $allowedSortFields = ['start_date', 'end_date', 'name', 'created_at'];
        if (!in_array($sortBy, $allowedSortFields)) {
            $sortBy = 'start_date';
        }
        
        if (!in_array($sortDirection, ['asc', 'desc'])) {
            $sortDirection = 'asc';
        }

        // Para ordenar por duración, usar cálculo
        if ($sortBy === 'duration') {
            $query->orderByRaw('DATEDIFF(end_date, start_date) ' . $sortDirection);
        } else {
            $query->orderBy($sortBy, $sortDirection);
        }

        $festivals = $query->paginate(20);
        
        return FestivalResource::collection($festivals);
    }
}
