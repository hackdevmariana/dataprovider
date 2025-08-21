<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Timezone;
use App\Http\Resources\V1\TimezoneResource;
use Illuminate\Http\JsonResponse;

/**
 * @group Timezones
 *
 * APIs para la gestión de zonas horarias.
 * Permite consultar información de zonas horarias y países donde se aplican.
 */
class TimezoneController extends Controller
{
    /**
     * Display a listing of timezones
     *
     * Obtiene una lista de todas las zonas horarias disponibles.
     *
     * @response 200 {
     *   "data": [
     *     {
     *       "id": 1,
     *       "name": "Europe/Madrid",
     *       "offset": "+01:00",
     *       "offset_minutes": 60,
     *       "countries": [
     *         {
     *           "id": 1,
     *           "name": "España",
     *           "slug": "espana"
     *         }
     *       ]
     *     }
     *   ]
     * }
     *
     * @apiResourceCollection App\Http\Resources\V1\TimezoneResource
     * @apiResourceModel App\Models\Timezone
     */
    public function index(): JsonResponse
    {
        $timezones = Timezone::with('countries')->get();
        
        return response()->json([
            'data' => TimezoneResource::collection($timezones)
        ]);
    }

    /**
     * Display the specified timezone
     *
     * Obtiene los detalles de una zona horaria específica por ID o nombre.
     *
     * @urlParam idOrName integer|string ID o nombre de la zona horaria. Example: 1
     *
     * @response 200 {
     *   "data": {
     *     "id": 1,
     *       "name": "Europe/Madrid",
     *       "offset": "+01:00",
     *       "offset_minutes": 60,
     *       "countries": [
     *         {
     *           "id": 1,
     *           "name": "España",
     *           "slug": "espana"
     *         }
     *       ]
     *   }
     * }
     *
     * @response 404 {
     *   "message": "Zona horaria no encontrada"
     * }
     *
     * @apiResourceModel App\Models\Timezone
     */
    public function show($idOrName): JsonResponse
    {
        $timezone = Timezone::with('countries')
            ->where('id', $idOrName)
            ->orWhere('name', $idOrName)
            ->firstOrFail();

        return response()->json([
            'data' => new TimezoneResource($timezone)
        ]);
    }
}
