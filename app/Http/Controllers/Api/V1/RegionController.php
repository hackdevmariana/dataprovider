<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Region;
use App\Models\Province;
use App\Models\AutonomousCommunity;
use App\Models\Country;
use App\Http\Controllers\Controller;
use App\Http\Resources\V1\RegionResource;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
/**
 * @group Regions
 *
 * APIs para la gestión de regiones y divisiones geográficas.
 * Permite consultar información de regiones, provincias y comunidades autónomas.
 */
class RegionController extends Controller
{
    /**
     * Display a listing of regions
     *
     * Obtiene una lista de todas las regiones disponibles.
     *
     * @response 200 {
     *   "data": [
     *     {
     *       "id": 1,
     *       "name": "Madrid",
     *       "slug": "madrid",
     *       "province": {...},
     *       "autonomous_community": {...},
     *       "country": {...},
     *       "timezone": {...}
     *     }
     *   ]
     * }
     *
     * @apiResourceCollection App\Http\Resources\V1\RegionResource
     * @apiResourceModel App\Models\Region
     */
    public function index(): JsonResponse
    {
        $regions = Region::with(['province', 'autonomousCommunity', 'country', 'timezone'])->get();
        
        return response()->json([
            'data' => RegionResource::collection($regions)
        ]);
    }

    /**
     * Display the specified region
     *
     * Obtiene los detalles de una región específica por ID o slug.
     *
     * @urlParam idOrSlug integer|string ID o slug de la región. Example: 1
     *
     * @response 200 {
     *   "data": {
     *     "id": 1,
     *       "name": "Madrid",
     *       "slug": "madrid",
     *       "province": {...},
     *       "autonomous_community": {...},
     *       "country": {...},
     *       "timezone": {...}
     *   }
     * }
     *
     * @response 404 {
     *   "message": "Región no encontrada"
     * }
     *
     * @apiResourceModel App\Models\Region
     */
    public function show($idOrSlug): JsonResponse
    {
        $region = Region::with(['province', 'autonomousCommunity', 'country', 'timezone'])
                        ->where('slug', $idOrSlug)
                        ->orWhere('id', $idOrSlug)
                        ->firstOrFail();

        return response()->json([
            'data' => new RegionResource($region)
        ]);
    }

    /**
     * Get regions by province
     *
     * Obtiene las regiones de una provincia específica.
     *
     * @urlParam slug string Slug de la provincia. Example: madrid
     *
     * @response 200 {
     *   "data": [
     *     {
     *       "id": 1,
     *       "name": "Madrid",
     *       "slug": "madrid",
     *       "province": {...},
     *       "autonomous_community": {...},
     *       "country": {...},
     *       "timezone": {...}
     *     }
     *   ]
     * }
     *
     * @response 404 {
     *   "message": "Provincia no encontrada"
     * }
     *
     * @apiResourceCollection App\Http\Resources\V1\RegionResource
     * @apiResourceModel App\Models\Region
     */
    public function byProvince($slug): JsonResponse
    {
        $province = Province::where('slug', $slug)->firstOrFail();
        $regions = $province->regions()->with(['province', 'autonomousCommunity', 'country', 'timezone'])->get();

        return response()->json([
            'data' => RegionResource::collection($regions)
        ]);
    }

    /**
     * Get regions by autonomous community
     *
     * Obtiene las regiones de una comunidad autónoma específica.
     *
     * @urlParam slug string Slug de la comunidad autónoma. Example: madrid
     *
     * @response 200 {
     *   "data": [
     *     {
     *       "id": 1,
     *       "name": "Madrid",
     *       "slug": "madrid",
     *       "province": {...},
     *       "autonomous_community": {...},
     *       "country": {...},
     *       "timezone": {...}
     *     }
     *   ]
     * }
     *
     * @response 404 {
     *   "message": "Comunidad autónoma no encontrada"
     * }
     *
     * @apiResourceCollection App\Http\Resources\V1\RegionResource
     * @apiResourceModel App\Models\Region
     */
    public function byAutonomousCommunity($slug): JsonResponse
    {
        $autonomousCommunity = AutonomousCommunity::where('slug', $slug)->firstOrFail();
        $regions = $autonomousCommunity->regions()->with(['province', 'autonomousCommunity', 'country', 'timezone'])->get();

        return response()->json([
            'data' => RegionResource::collection($regions)
        ]);
    }

    /**
     * Get regions by country
     *
     * Obtiene las regiones de un país específico.
     *
     * @urlParam slug string Slug del país. Example: espana
     *
     * @response 200 {
     *   "data": [
     *     {
     *       "id": 1,
     *       "name": "Madrid",
     *       "slug": "madrid",
     *       "province": {...},
     *       "autonomous_community": {...},
     *       "country": {...},
     *       "timezone": {...}
     *     }
     *   ]
     * }
     *
     * @response 404 {
     *   "message": "País no encontrado"
     * }
     *
     * @apiResourceCollection App\Http\Resources\V1\RegionResource
     * @apiResourceModel App\Models\Region
     */
    public function byCountry($slug): JsonResponse
    {
        $country = Country::where('slug', $slug)->firstOrFail();
        $regions = $country->regions()->with(['province', 'autonomousCommunity', 'country', 'timezone'])->get();

        return response()->json([
            'data' => RegionResource::collection($regions)
        ]);
    }
}
