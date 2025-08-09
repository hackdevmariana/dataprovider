<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Municipality;
use Illuminate\Http\Request;

class MunicipalityController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/v1/municipalities",
     *     summary="Listar todos los municipios",
     *     tags={"Municipalities"},
     *     @OA\Response(
     *         response=200,
     *         description="Lista de municipios",
     *         @OA\JsonContent(type="array", @OA\Items())
     *     )
     * )
     */
    public function index()
    {
        $municipalities = Municipality::with(['province', 'autonomousCommunity', 'country'])->paginate(50);
        return response()->json($municipalities);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/municipalities/province/{slug}",
     *     summary="Listar municipios por provincia",
     *     tags={"Municipalities"},
     *     @OA\Parameter(
     *         name="slug",
     *         in="path",
     *         description="Slug de la provincia",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Municipios encontrados",
     *         @OA\JsonContent(type="array", @OA\Items())
     *     )
     * )
     */
    public function byProvince($slug)
    {
        $municipalities = Municipality::whereHas('province', fn($q) => $q->where('slug', $slug))
            ->with(['province', 'autonomousCommunity', 'country'])
            ->get();

        return response()->json($municipalities);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/municipalities/country/{slug}",
     *     summary="Listar municipios por país",
     *     tags={"Municipalities"},
     *     @OA\Parameter(
     *         name="slug",
     *         in="path",
     *         description="Slug del país",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Municipios encontrados",
     *         @OA\JsonContent(type="array", @OA\Items())
     *     )
     * )
     */
    public function byCountry($slug)
    {
        $municipalities = Municipality::whereHas('country', fn($q) => $q->where('slug', $slug))
            ->with(['province', 'autonomousCommunity', 'country'])
            ->paginate(50);

        return response()->json($municipalities);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/municipalities/{idOrSlug}",
     *     summary="Mostrar detalle de un municipio",
     *     tags={"Municipalities"},
     *     @OA\Parameter(
     *         name="idOrSlug",
     *         in="path",
     *         description="ID o slug del municipio",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Municipio encontrado",
     *         @OA\JsonContent()
     *     ),
     *     @OA\Response(response=404, description="No encontrado")
     * )
     */
    public function show($idOrSlug)
    {
        $municipality = Municipality::with(['province', 'autonomousCommunity', 'country'])
            ->where('id', $idOrSlug)
            ->orWhere('slug', $idOrSlug)
            ->first();

        if (!$municipality) {
            return response()->json(['message' => 'Municipio no encontrado'], 404);
        }

        return response()->json($municipality);
    }
}


