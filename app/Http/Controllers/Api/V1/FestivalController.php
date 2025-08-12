<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Festival;
use App\Http\Resources\V1\FestivalResource;

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
}
