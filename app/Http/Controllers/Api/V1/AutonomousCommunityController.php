<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\AutonomousCommunity;
use App\Http\Resources\V1\AutonomousCommunityResource;
use Illuminate\Http\JsonResponse;

/**
 * @group Autonomous Communities
 *
 * APIs para la gestión de comunidades autónomas.
 * Permite consultar información de comunidades autónomas, provincias y municipios.
 */
class AutonomousCommunityController extends Controller
{
    /**
     * Display a listing of autonomous communities
     *
     * Obtiene una lista de todas las comunidades autónomas.
     *
     * @response 200 {
     *   "data": [
     *     {
     *       "id": 1,
     *       "name": "Cataluña",
     *       "slug": "cataluna",
     *       "ine_code": "09",
     *       "capital": "Barcelona"
     *     }
     *   ]
     * }
     *
     * @apiResourceCollection App\Http\Resources\V1\AutonomousCommunityResource
     * @apiResourceModel App\Models\AutonomousCommunity
     */
    public function index(): JsonResponse
    {
        $communities = AutonomousCommunity::all();
        
        return response()->json([
            'data' => AutonomousCommunityResource::collection($communities)
        ]);
    }

    /**
     * Display the specified autonomous community
     *
     * Obtiene los detalles de una comunidad autónoma específica.
     *
     * @urlParam slug string Slug de la comunidad autónoma. Example: cataluna
     *
     * @response 200 {
     *   "data": {
     *     "id": 1,
     *     "name": "Cataluña",
     *     "slug": "cataluna",
     *     "ine_code": "09",
     *     "capital": "Barcelona"
     *   }
     * }
     *
     * @response 404 {
     *   "message": "Comunidad autónoma no encontrada"
     * }
     *
     * @apiResourceModel App\Models\AutonomousCommunity
     */
    public function show($slug): JsonResponse
    {
        $community = AutonomousCommunity::where('slug', $slug)->firstOrFail();
        
        return response()->json([
            'data' => new AutonomousCommunityResource($community)
        ]);
    }

    /**
     * Get autonomous communities with provinces
     *
     * Obtiene las comunidades autónomas con sus provincias.
     *
     * @response 200 {
     *   "data": [
     *     {
     *       "id": 1,
     *       "name": "Cataluña",
     *       "slug": "cataluna",
     *       "provinces": [
     *         {
     *           "id": 1,
     *           "name": "Barcelona",
     *           "slug": "barcelona"
     *         }
     *       ]
     *     }
     *   ]
     * }
     *
     * @apiResourceCollection App\Http\Resources\V1\AutonomousCommunityResource
     * @apiResourceModel App\Models\AutonomousCommunity
     */
    public function withProvinces(): JsonResponse
    {
        $communities = AutonomousCommunity::with('provinces')->get();
        
        return response()->json([
            'data' => AutonomousCommunityResource::collection($communities)
        ]);
    }

    /**
     * Get autonomous communities with provinces and municipalities
     *
     * Obtiene las comunidades autónomas con provincias y municipios.
     *
     * @response 200 {
     *   "data": [
     *     {
     *       "id": 1,
     *       "name": "Cataluña",
     *       "slug": "cataluna",
     *       "provinces": [
     *         {
     *           "id": 1,
     *           "name": "Barcelona",
     *           "slug": "barcelona",
     *           "municipalities": [
     *             {
     *               "id": 1,
     *               "name": "Barcelona",
     *               "slug": "barcelona"
     *             }
     *           ]
     *         }
     *       ]
     *     }
     *   ]
     * }
     *
     * @apiResourceCollection App\Http\Resources\V1\AutonomousCommunityResource
     * @apiResourceModel App\Models\AutonomousCommunity
     */
    public function withProvincesAndMunicipalities(): JsonResponse
    {
        $communities = AutonomousCommunity::with('provinces.municipalities')->get();
        
        return response()->json([
            'data' => AutonomousCommunityResource::collection($communities)
        ]);
    }
}
