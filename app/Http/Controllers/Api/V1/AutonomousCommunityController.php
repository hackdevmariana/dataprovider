<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\AutonomousCommunity;
use Illuminate\Http\Request;
use App\Http\Resources\V1\AutonomousCommunityResource;

class AutonomousCommunityController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/v1/autonomous-communities",
     *     summary="Obtener listado de comunidades autónomas",
     *     tags={"Autonomous Communities"},
     *     @OA\Response(
     *         response=200,
     *         description="Lista de comunidades autónomas",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/AutonomousCommunity")),
     *             @OA\Property(property="links", type="object"),
     *             @OA\Property(property="meta", type="object")
     *         )
     *     )
     * )
     */
    public function index()
    {
        return AutonomousCommunityResource::collection(AutonomousCommunity::all());
    }

    /**
     * @OA\Get(
     *     path="/api/v1/autonomous-communities/{slug}",
     *     summary="Mostrar detalles de una comunidad autónoma",
     *     tags={"Autonomous Communities"},
     *     @OA\Parameter(
     *         name="slug",
     *         in="path",
     *         required=true,
     *         description="Slug de la comunidad autónoma",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Comunidad autónoma encontrada",
     *         @OA\JsonContent(ref="#/components/schemas/AutonomousCommunity")
     *     ),
     *     @OA\Response(response=404, description="Comunidad autónoma no encontrada")
     * )
     */
    public function show($slug)
    {
        $community = AutonomousCommunity::where('slug', $slug)->firstOrFail();
        return new AutonomousCommunityResource($community);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/autonomous-communities/with-provinces",
     *     summary="Obtener comunidades autónomas con sus provincias",
     *     tags={"Autonomous Communities"},
     *     @OA\Response(
     *         response=200,
     *         description="Listado de comunidades autónomas con provincias",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/AutonomousCommunityWithProvinces"))
     *         )
     *     )
     * )
     */
    public function withProvinces()
    {
        $communities = AutonomousCommunity::with('provinces')->get();
        return AutonomousCommunityResource::collection($communities);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/autonomous-communities/with-provinces-and-municipalities",
     *     summary="Obtener comunidades autónomas con provincias y municipios",
     *     tags={"Autonomous Communities"},
     *     @OA\Response(
     *         response=200,
     *         description="Listado de comunidades autónomas con provincias y municipios",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/AutonomousCommunityWithProvincesAndMunicipalities"))
     *         )
     *     )
     * )
     */
    public function withProvincesAndMunicipalities()
    {
        $communities = AutonomousCommunity::with('provinces.municipalities')->get();
        return AutonomousCommunityResource::collection($communities);
    }
}


