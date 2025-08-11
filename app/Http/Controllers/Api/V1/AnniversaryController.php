<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Anniversary;
use App\Http\Resources\V1\AnniversaryResource;
use Illuminate\Http\Request;

/**
 * @OA\Tag(
 *     name="Anniversaries",
 *     description="API for managing anniversaries (efemérides)"
 * )
 */
class AnniversaryController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/v1/anniversaries",
     *     summary="Get all anniversaries",
     *     tags={"Anniversaries"},
     *     @OA\Response(
     *         response=200,
     *         description="List of anniversaries",
     *         @OA\JsonContent(type="object",
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Anniversary"))
     *         )
     *     )
     * )
     */
    public function index()
    {
        return AnniversaryResource::collection(Anniversary::all());
    }

    /**
     * @OA\Get(
     *     path="/api/v1/anniversaries/{idOrSlug}",
     *     summary="Get anniversary by ID or slug",
     *     tags={"Anniversaries"},
     *     @OA\Parameter(
     *         name="idOrSlug",
     *         in="path",
     *         required=true,
     *         description="ID or slug of the anniversary",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Anniversary found",
     *         @OA\JsonContent(ref="#/components/schemas/Anniversary")
     *     ),
     *     @OA\Response(response=404, description="Anniversary not found")
     * )
     */
    public function show($idOrSlug)
    {
        $anniversary = Anniversary::where('slug', $idOrSlug)
            ->orWhere('id', $idOrSlug)
            ->firstOrFail();
        return new AnniversaryResource($anniversary);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/anniversaries/day/{month}/{day}",
     *     summary="Get anniversaries by month and day",
     *     tags={"Anniversaries"},
     *     @OA\Parameter(
     *         name="month",
     *         in="path",
     *         required=true,
     *         description="Month (1-12)",
     *         @OA\Schema(type="integer", example=4)
     *     ),
     *     @OA\Parameter(
     *         name="day",
     *         in="path",
     *         required=true,
     *         description="Day (1-31)",
     *         @OA\Schema(type="integer", example=23)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Anniversaries for the given day",
     *         @OA\JsonContent(type="object",
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Anniversary"))
     *         )
     *     )
     * )
     */
    public function byDay($month, $day)
    {
        $anniversaries = Anniversary::where('month', $month)->where('day', $day)->get();
        return AnniversaryResource::collection($anniversaries);
    }
}
