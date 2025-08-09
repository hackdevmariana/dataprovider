<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Region;
use App\Models\Province;
use App\Models\AutonomousCommunity;
use App\Models\Country;
use App\Http\Controllers\Controller;
use App\Http\Resources\V1\RegionResource;
use Illuminate\Http\Request;

class RegionController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/v1/regions",
     *     summary="Obtener listado de regiones",
     *     tags={"Regions"},
     *     @OA\Response(
     *         response=200,
     *         description="Lista de regiones",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Region")),
     *             @OA\Property(property="links", type="object"),
     *             @OA\Property(property="meta", type="object")
     *         )
     *     )
     * )
     */
    public function index()
    {
        return RegionResource::collection(Region::with(['province', 'autonomousCommunity', 'country', 'timezone'])->get());
    }

    /**
     * @OA\Get(
     *     path="/api/v1/regions/{idOrSlug}",
     *     summary="Mostrar detalles de una región",
     *     tags={"Regions"},
     *     @OA\Parameter(
     *         name="idOrSlug",
     *         in="path",
     *         required=true,
     *         description="ID o slug de la región",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Región encontrada",
     *         @OA\JsonContent(ref="#/components/schemas/Region")
     *     ),
     *     @OA\Response(response=404, description="Región no encontrada")
     * )
     */
    public function show($idOrSlug)
    {
        $region = Region::with(['province', 'autonomousCommunity', 'country', 'timezone'])
                        ->where('slug', $idOrSlug)
                        ->orWhere('id', $idOrSlug)
                        ->firstOrFail();

        return new RegionResource($region);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/provinces/{slug}/regions",
     *     summary="Obtener regiones por provincia",
     *     tags={"Regions"},
     *     @OA\Parameter(
     *         name="slug",
     *         in="path",
     *         required=true,
     *         description="Slug de la provincia",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Listado de regiones de la provincia",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Region"))
     *         )
     *     ),
     *     @OA\Response(response=404, description="Provincia no encontrada")
     * )
     */
    public function byProvince($slug)
    {
        $province = Province::where('slug', $slug)->firstOrFail();
        $regions = $province->regions()->with(['province', 'autonomousCommunity', 'country', 'timezone'])->get();

        return RegionResource::collection($regions);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/autonomous-communities/{slug}/regions",
     *     summary="Obtener regiones por comunidad autónoma",
     *     tags={"Regions"},
     *     @OA\Parameter(
     *         name="slug",
     *         in="path",
     *         required=true,
     *         description="Slug de la comunidad autónoma",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Listado de regiones de la comunidad autónoma",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Region"))
     *         )
     *     ),
     *     @OA\Response(response=404, description="Comunidad autónoma no encontrada")
     * )
     */
    public function byAutonomousCommunity($slug)
    {
        $community = AutonomousCommunity::where('slug', $slug)->firstOrFail();

        $regions = Region::with(['province', 'autonomousCommunity', 'country', 'timezone'])
                         ->where('autonomous_community_id', $community->id)
                         ->get();

        return RegionResource::collection($regions);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/countries/{slug}/regions",
     *     summary="Obtener regiones por país",
     *     tags={"Regions"},
     *     @OA\Parameter(
     *         name="slug",
     *         in="path",
     *         required=true,
     *         description="Slug del país",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Listado de regiones del país",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Region"))
     *         )
     *     ),
     *     @OA\Response(response=404, description="País no encontrado")
     * )
     */
    public function byCountry($slug)
    {
        $country = Country::where('slug', $slug)->firstOrFail();

        $regions = $country->regions()->with(['province', 'autonomousCommunity', 'country', 'timezone'])->get();

        return RegionResource::collection($regions);
    }
}


