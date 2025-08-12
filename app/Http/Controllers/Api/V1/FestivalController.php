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
        $start = now()->startOfWeek();
        $end = now()->endOfWeek();
        $festivals = Festival::whereHas('events', function($q) use ($start, $end) {
            $q->whereBetween('start_datetime', [$start, $end]);
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
        $start = now()->startOfMonth();
        $end = now()->endOfMonth();
        $festivals = Festival::whereHas('events', function($q) use ($start, $end) {
            $q->whereBetween('start_datetime', [$start, $end]);
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
        $start = now()->startOfYear();
        $end = now()->endOfYear();
        $festivals = Festival::whereHas('events', function($q) use ($start, $end) {
            $q->whereBetween('start_datetime', [$start, $end]);
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
}
