<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Country;

/**
 * @OA\Tag(
 *     name="Countries",
 *     description="Gestión de países"
 * )
 */
class CountryController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/v1/countries",
     *     summary="Listado de países",
     *     tags={"Countries"},
     *     @OA\Response(
     *         response=200,
     *         description="Lista de países",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Country")
     *         )
     *     )
     * )
     */
    public function index()
    {
        $countries = Country::with(['timezone', 'languages'])->get();
        return response()->json($countries);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/countries/{idOrSlug}",
     *     summary="Mostrar país por ID o slug",
     *     tags={"Countries"},
     *     @OA\Parameter(
     *         name="idOrSlug",
     *         in="path",
     *         required=true,
     *         description="ID o slug del país",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="País encontrado",
     *         @OA\JsonContent(ref="#/components/schemas/Country")
     *     ),
     *     @OA\Response(response=404, description="No encontrado")
     * )
     */
    public function show($idOrSlug)
    {
        $country = Country::with(['timezone', 'languages'])
            ->where('id', $idOrSlug)
            ->orWhere('slug', $idOrSlug)
            ->first();

        if (!$country) {
            return response()->json(['message' => 'País no encontrado'], 404);
        }

        return response()->json($country);
    }
}
