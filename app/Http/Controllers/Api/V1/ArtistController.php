<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Artist;
use App\Http\Resources\V1\ArtistResource;
use App\Http\Requests\StoreArtistRequest;

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

    /**
     * @OA\Post(
     *     path="/api/v1/artists",
     *     summary="Create a new artist (public)",
     *     tags={"Artists"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "slug"},
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="slug", type="string"),
     *             @OA\Property(property="description", type="string"),
     *             @OA\Property(property="birth_date", type="string", format="date"),
     *             @OA\Property(property="genre", type="string"),
     *             @OA\Property(property="person_id", type="integer"),
     *             @OA\Property(property="stage_name", type="string"),
     *             @OA\Property(property="group_name", type="string"),
     *             @OA\Property(property="active_years_start", type="integer"),
     *             @OA\Property(property="active_years_end", type="integer"),
     *             @OA\Property(property="bio", type="string"),
     *             @OA\Property(property="photo", type="string"),
     *             @OA\Property(property="social_links", type="array", @OA\Items(type="string")),
     *             @OA\Property(property="language_id", type="integer"),
     *         )
     *     ),
     *     @OA\Response(response=201, description="Artist created"),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function store(StoreArtistRequest $request)
    {
        $artist = \App\Models\Artist::create($request->validated());
        return (new ArtistResource($artist))->response()->setStatusCode(201);
    }
}
