<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Http\Resources\V1\CountryResource;
use Illuminate\Http\JsonResponse;

/**
 * @OA\Tag(
 *     name="Countries",
 *     description="API endpoints para gestión de países y territorios"
 * )
 * 
 * @group Countries
 *
 * APIs para la gestión de países y territorios.
 * Permite consultar información de países, idiomas y zonas horarias.
 */
class CountryController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/v1/countries",
     *     summary="List countries",
     *     tags={"Countries"},
     *     @OA\Response(
     *         response=200,
     *         description="List of countries",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="array", @OA\Items(
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="España"),
     *                 @OA\Property(property="slug", type="string", example="espana"),
     *                 @OA\Property(property="code", type="string", example="ES"),
     *                 @OA\Property(property="iso3", type="string", example="ESP"),
     *                 @OA\Property(property="population", type="integer", example=47326687),
     *                 @OA\Property(property="area_km2", type="number", example=505992),
     *                 @OA\Property(property="capital", type="string", example="Madrid")
     *             ))
     *         )
     *     )
     * )
     * 
     * Display a listing of countries
     *
     * Obtiene una lista de países con sus relaciones cargadas.
     *
     * @response 200 {
     *   "data": [
     *     {
     *       "id": 1,
     *       "name": "España",
     *       "slug": "espana",
     *       "iso_alpha2": "ES",
     *       "iso_alpha3": "ESP",
     *       "timezone": {...},
     *       "languages": [...]
     *     }
     *   ]
     * }
     *
     * @apiResourceCollection App\Http\Resources\V1\CountryResource
     * @apiResourceModel App\Models\Country
     */
    public function index(): JsonResponse
    {
        $countries = Country::with(['timezone', 'languages'])->get();
        
        return response()->json([
            'data' => CountryResource::collection($countries)
        ]);
    }

    /**
     * Display the specified country
     *
     * Obtiene los detalles de un país específico por ID o slug.
     *
     * @urlParam idOrSlug integer|string ID o slug del país. Example: 1
     *
     * @response 200 {
     *   "data": {
     *     "id": 1,
     *     "name": "España",
     *     "slug": "espana",
     *     "iso_alpha2": "ES",
     *     "iso_alpha3": "ESP",
     *     "timezone": {...},
     *     "languages": [...]
     *   }
     * }
     *
     * @response 404 {
     *   "message": "País no encontrado"
     * }
     *
     * @apiResourceModel App\Models\Country
     */
    public function show($idOrSlug): JsonResponse
    {
        $country = Country::with(['timezone', 'languages'])
            ->where('id', $idOrSlug)
            ->orWhere('slug', $idOrSlug)
            ->firstOrFail();

        return response()->json([
            'data' => new CountryResource($country)
        ]);
    }
}
