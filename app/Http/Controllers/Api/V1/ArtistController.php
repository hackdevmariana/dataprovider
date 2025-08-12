<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Artist;
use App\Http\Resources\V1\ArtistResource;

/**
 * @OA\Tag(
 *     name="Artists",
 *     description="Artist management"
 * )
 */
class ArtistController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/v1/artists",
     *     summary="Get all artists",
     *     tags={"Artists"},
     *     @OA\Response(response=200, description="List of artists")
     * )
     */
    public function index()
    {
        return ArtistResource::collection(Artist::paginate(20));
    }

    /**
     * @OA\Get(
     *     path="/api/v1/artists/{idOrSlug}",
     *     summary="Get an artist by ID or slug",
     *     tags={"Artists"},
     *     @OA\Parameter(name="idOrSlug", in="path", required=true, @OA\Schema(type="string")),
     *     @OA\Response(response=200, description="Artist found"),
     *     @OA\Response(response=404, description="Artist not found")
     * )
     */
    public function show($idOrSlug)
    {
        $artist = Artist::where('id', $idOrSlug)->orWhere('slug', $idOrSlug)->firstOrFail();
        return new ArtistResource($artist);
    }
}
