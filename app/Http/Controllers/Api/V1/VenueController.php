<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Venue;
use App\Http\Resources\V1\VenueResource;
use Illuminate\Http\Request;

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
}
