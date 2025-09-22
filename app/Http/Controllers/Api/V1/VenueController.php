<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Venue;
use App\Http\Resources\V1\VenueResource;
use Illuminate\Http\Request;
use App\Http\Requests\StoreVenueRequest;
use Illuminate\Http\JsonResponse;
use OpenApi\Annotations as OA;

/**
 * @group Venues
 *
 * APIs para la gestión de lugares y recintos.
 * Permite crear, consultar y gestionar lugares del sistema.
 */
/**
 * @OA\Tag(
 *     name="Venues",
 *     description="APIs para la gestión de Venues"
 * )
 */
class VenueController extends Controller
{
    /**
     * Display a listing of venues
     *
     * Obtiene una lista paginada de todos los lugares disponibles.
     *
     * @queryParam page int Número de página. Example: 1
     * @queryParam per_page int Cantidad por página (máx 100). Example: 20
     *
     * @response 200 {
     *   "data": [
     *     {
     *       "id": 1,
     *       "name": "Teatro Principal",
     *       "slug": "teatro-principal",
     *       "address": "Calle Mayor, 1",
     *       "municipality_id": 1,
     *       "latitude": 40.4168,
     *       "longitude": -3.7038,
     *       "capacity": 500,
     *       "venue_type": "theater"
     *     }
     *   ],
     *   "meta": {...}
     * }
     *
     * @apiResourceCollection App\Http\Resources\V1\VenueResource
     * @apiResourceModel App\Models\Venue
     */
    public function index(): JsonResponse
    {
        $venues = Venue::paginate(20);
        
        return response()->json([
            'data' => VenueResource::collection($venues),
            'meta' => [
                'current_page' => $venues->currentPage(),
                'last_page' => $venues->lastPage(),
                'per_page' => $venues->perPage(),
                'total' => $venues->total(),
            ]
        ]);
    }

    /**
     * Display the specified venue
     *
     * Obtiene los detalles de un lugar específico por ID o slug.
     *
     * @urlParam idOrSlug integer|string ID o slug del lugar. Example: 1
     *
     * @response 200 {
     *   "data": {
     *     "id": 1,
     *       "name": "Teatro Principal",
     *       "slug": "teatro-principal",
     *       "address": "Calle Mayor, 1",
     *       "municipality_id": 1,
     *       "latitude": 40.4168,
     *       "longitude": -3.7038,
     *       "capacity": 500,
     *       "venue_type": "theater"
     *   }
     * }
     *
     * @response 404 {
     *   "message": "Lugar no encontrado"
     * }
     *
     * @apiResourceModel App\Models\Venue
     */
    public function show($idOrSlug): JsonResponse
    {
        $venue = Venue::where('id', $idOrSlug)->orWhere('slug', $idOrSlug)->firstOrFail();
        
        return response()->json([
            'data' => new VenueResource($venue)
        ]);
    }

    /**
     * Store a newly created venue
     *
     * Crea un nuevo lugar en el sistema (público).
     *
     * @bodyParam name string required Nombre del lugar. Example: Teatro Principal
     * @bodyParam slug string required Slug único del lugar. Example: teatro-principal
     * @bodyParam address string Dirección del lugar. Example: Calle Mayor, 1
     * @bodyParam municipality_id integer required ID del municipio. Example: 1
     * @bodyParam latitude number Latitud del lugar. Example: 40.4168
     * @bodyParam longitude number Longitud del lugar. Example: -3.7038
     * @bodyParam capacity integer Capacidad del lugar. Example: 500
     * @bodyParam description string Descripción del lugar. Example: Teatro histórico del centro
     * @bodyParam venue_type string Tipo de lugar. Example: theater
     * @bodyParam venue_status string Estado del lugar. Example: active
     * @bodyParam is_verified boolean Si el lugar está verificado. Example: false
     *
     * @response 201 {
     *   "data": {
     *     "id": 1,
     *       "name": "Teatro Principal",
     *       "slug": "teatro-principal",
     *       "address": "Calle Mayor, 1",
     *       "municipality_id": 1,
     *       "capacity": 500,
     *       "venue_type": "theater"
     *   }
     * }
     *
     * @response 422 {
     *   "message": "Los datos proporcionados no son válidos.",
     *   "errors": {...}
     * }
     *
     * @apiResourceModel App\Models\Venue
     */
    public function store(StoreVenueRequest $request): JsonResponse
    {
        $venue = Venue::create($request->validated());
        
        return response()->json([
            'data' => new VenueResource($venue)
        ], 201);
    }
}
