<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Festival;
use App\Http\Resources\V1\FestivalResource;
use App\Http\Requests\StoreFestivalRequest;

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
}
