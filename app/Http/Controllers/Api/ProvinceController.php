<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Province;
use Illuminate\Http\Request;

class ProvinceController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/v1/provinces",
     *     summary="Obtener listado de provincias",
     *     tags={"Provinces"},
     *     @OA\Response(
     *         response=200,
     *         description="Lista paginada de provincias",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="data", type="array", @OA\Items(
     *                 type="object",
     *                 @OA\Property(property="id", type="integer"),
     *                 @OA\Property(property="name", type="string"),
     *                 @OA\Property(property="slug", type="string"),
     *                 @OA\Property(property="ine_code", type="string"),
     *                 @OA\Property(property="latitude", type="number", format="float"),
     *                 @OA\Property(property="longitude", type="number", format="float"),
     *                 @OA\Property(property="area_km2", type="number", format="float"),
     *                 @OA\Property(property="altitude_m", type="integer"),
     *                 @OA\Property(property="autonomous_community", type="object",
     *                     @OA\Property(property="name", type="string")
     *                 ),
     *                 @OA\Property(property="country", type="object",
     *                     @OA\Property(property="name", type="string")
     *                 )
     *             )),
     *             @OA\Property(property="links", type="object"),
     *             @OA\Property(property="meta", type="object")
     *         )
     *     )
     * )
     */
    public function index()
    {
        $provinces = Province::with(['autonomousCommunity', 'country'])->paginate(20);
        return response()->json($provinces);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/provinces/{idOrSlug}",
     *     summary="Mostrar detalles de una provincia",
     *     tags={"Provinces"},
     *     @OA\Parameter(
     *         name="idOrSlug",
     *         in="path",
     *         required=true,
     *         description="ID o slug de la provincia",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Provincia encontrada",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="integer"),
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="slug", type="string"),
     *             @OA\Property(property="ine_code", type="string"),
     *             @OA\Property(property="latitude", type="number", format="float"),
     *             @OA\Property(property="longitude", type="number", format="float"),
     *             @OA\Property(property="area_km2", type="number", format="float"),
     *             @OA\Property(property="altitude_m", type="integer"),
     *             @OA\Property(property="autonomous_community", type="object",
     *                 @OA\Property(property="name", type="string")
     *             ),
     *             @OA\Property(property="country", type="object",
     *                 @OA\Property(property="name", type="string")
     *             )
     *         )
     *     ),
     *     @OA\Response(response=404, description="Provincia no encontrada")
     * )
     */
    public function show($idOrSlug)
    {
        $province = Province::with(['autonomousCommunity', 'country'])
            ->where('id', $idOrSlug)
            ->orWhere('slug', $idOrSlug)
            ->first();

        if (!$province) {
            return response()->json(['message' => 'Provincia no encontrada'], 404);
        }

        return response()->json($province);
    }
}
