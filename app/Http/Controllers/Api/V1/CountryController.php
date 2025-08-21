<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Http\Resources\V1\CountryResource;
use Illuminate\Http\JsonResponse;

/**
 * @group Countries
 *
 * APIs para la gestión de países y territorios.
 * Permite consultar información de países, idiomas y zonas horarias.
 */
class CountryController extends Controller
{
    /**
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
