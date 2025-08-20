<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Http\Resources\V1\CountryResource;
use Illuminate\Http\JsonResponse;

/**
 * @group Countries
 *
 * APIs para la gestión de países del sistema.
 * Permite consultar información de países, zonas horarias e idiomas.
 */
class CountryController extends Controller
{
    /**
     * Display a listing of countries
     *
     * Obtiene una lista de todos los países con sus zonas horarias e idiomas.
     *
     * @response 200 {
     *   "data": [
     *     {
     *       "id": 1,
     *       "name": "España",
     *       "slug": "spain",
     *       "iso_alpha2": "ES",
     *       "iso_alpha3": "ESP",
     *       "iso_numeric": "724",
     *       "timezone": {
     *         "id": 1,
     *         "name": "Europe/Madrid"
     *       },
     *       "languages": [
     *         {
     *           "id": 1,
     *           "name": "Español",
     *           "iso_639_1": "es"
     *         }
     *       ]
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
     * @urlParam idOrSlug mixed ID o slug del país. Example: spain
     *
     * @response 200 {
     *   "data": {
     *     "id": 1,
     *     "name": "España",
     *     "slug": "spain",
     *     "iso_alpha2": "ES",
     *     "iso_alpha3": "ESP",
     *     "iso_numeric": "724",
     *     "capital": "Madrid",
     *     "population": 46754778,
     *     "area_km2": 505990,
     *     "timezone": {
     *       "id": 1,
     *       "name": "Europe/Madrid"
     *     },
     *     "languages": [
     *       {
     *         "id": 1,
     *         "name": "Español",
     *         "iso_639_1": "es"
     *       }
     *     ]
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


