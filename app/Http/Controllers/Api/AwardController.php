<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Award;
use App\Http\Resources\AwardResource;

/**
 * @OA\Tag(
 *     name="Awards",
 *     description="Gestión de premios"
 * )
 */
class AwardController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/v1/awards",
     *     summary="Listado de premios",
     *     tags={"Awards"},
     *     @OA\Response(
     *         response=200,
     *         description="Lista de premios",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Award")
     *         )
     *     )
     * )
     */
    public function index()
    {
        $awards = Award::with('awardWinners')->get();
        return AwardResource::collection($awards);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/awards/{idOrSlug}",
     *     summary="Mostrar premio por ID o slug",
     *     tags={"Awards"},
     *     @OA\Parameter(
     *         name="idOrSlug",
     *         in="path",
     *         required=true,
     *         description="ID o slug del premio",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Premio encontrado",
     *         @OA\JsonContent(ref="#/components/schemas/Award")
     *     ),
     *     @OA\Response(response=404, description="No encontrado")
     * )
     */
    public function show($idOrSlug)
    {
        $award = Award::with('awardWinners')
            ->where('id', $idOrSlug)
            ->orWhere('slug', $idOrSlug)
            ->firstOrFail();

        return new AwardResource($award);
    }
}
