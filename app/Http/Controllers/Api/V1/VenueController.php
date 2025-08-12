<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Venue;
use App\Http\Resources\V1\VenueResource;
use Illuminate\Http\Request;
use App\Http\Requests\StoreVenueRequest;

/**
 * @OA\Tag(
 *     name="Venues",
 *     description="Venue management"
 * )
 */
class VenueController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/v1/venues",
     *     summary="Get all venues",
     *     tags={"Venues"},
     *     @OA\Response(response=200, description="List of venues")
     * )
     */
    public function index()
    {
        return VenueResource::collection(Venue::paginate(20));
    }

    /**
     * @OA\Get(
     *     path="/api/v1/venues/{idOrSlug}",
     *     summary="Get a venue by ID or slug",
     *     tags={"Venues"},
     *     @OA\Parameter(name="idOrSlug", in="path", required=true, @OA\Schema(type="string")),
     *     @OA\Response(response=200, description="Venue found"),
     *     @OA\Response(response=404, description="Venue not found")
     * )
     */
    public function show($idOrSlug)
    {
        $venue = Venue::where('id', $idOrSlug)->orWhere('slug', $idOrSlug)->firstOrFail();
        return new VenueResource($venue);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/venues",
     *     summary="Create a new venue (public)",
     *     tags={"Venues"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "slug", "municipality_id"},
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="slug", type="string"),
     *             @OA\Property(property="address", type="string"),
     *             @OA\Property(property="municipality_id", type="integer"),
     *             @OA\Property(property="latitude", type="number"),
     *             @OA\Property(property="longitude", type="number"),
     *             @OA\Property(property="capacity", type="integer"),
     *             @OA\Property(property="description", type="string"),
     *             @OA\Property(property="venue_type", type="string"),
     *             @OA\Property(property="venue_status", type="string"),
     *             @OA\Property(property="is_verified", type="boolean"),
     *         )
     *     ),
     *     @OA\Response(response=201, description="Venue created"),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function store(StoreVenueRequest $request)
    {
        $venue = \App\Models\Venue::create($request->validated());
        return (new VenueResource($venue))->response()->setStatusCode(201);
    }
}
