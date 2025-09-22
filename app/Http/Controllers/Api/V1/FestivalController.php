<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Festival;
use App\Http\Resources\V1\FestivalResource;
use App\Http\Requests\StoreFestivalRequest;
use App\Models\Event;
use App\Models\Artist;
use App\Models\Municipality;
use App\Models\Region;
use App\Models\Province;
use App\Models\AutonomousCommunity;
use App\Http\Resources\V1\EventResource;
use App\Http\Resources\V1\ArtistResource;
use Illuminate\Http\JsonResponse;
use OpenApi\Annotations as OA;
use Illuminate\Support\Facades\DB;

/**
 * @group Festivals
 *
 * APIs para la gestión de festivales y eventos culturales.
 * Permite crear, consultar y gestionar festivales del sistema.
 */
/**
 * @OA\Tag(
 *     name="Festivales",
 *     description="APIs para la gestión de Festivales"
 * )
 */
class FestivalController extends Controller
{
    /**
     * Display a listing of festivals
     *
     * Obtiene una lista paginada de todos los festivales.
     *
     * @queryParam page int Número de página. Example: 1
     * @queryParam per_page int Cantidad por página (máx 100). Example: 20
     *
     * @response 200 {
     *   "data": [
     *     {
     *       "id": 1,
     *       "name": "Festival de Música",
     *       "slug": "festival-musica",
     *       "description": "Festival anual de música",
     *       "month": 7,
     *       "usual_days": "15-20",
     *       "recurring": true
     *     }
     *   ],
     *   "meta": {...}
     * }
     *
     * @apiResourceCollection App\Http\Resources\V1\FestivalResource
     * @apiResourceModel App\Models\Festival
     */
    public function index(): JsonResponse
    {
        $festivals = Festival::paginate(20);
        
        return response()->json([
            'data' => FestivalResource::collection($festivals),
            'meta' => [
                'current_page' => $festivals->currentPage(),
                'last_page' => $festivals->lastPage(),
                'per_page' => $festivals->perPage(),
                'total' => $festivals->total(),
            ]
        ]);
    }

    /**
     * Display the specified festival
     *
     * Obtiene los detalles de un festival específico por ID.
     *
     * @urlParam id integer ID del festival. Example: 1
     *
     * @response 200 {
     *   "data": {
     *     "id": 1,
     *     "name": "Festival de Música",
     *     "slug": "festival-musica",
     *     "description": "Festival anual de música",
     *     "month": 7,
     *     "usual_days": "15-20",
     *     "recurring": true
     *   }
     * }
     *
     * @response 404 {
     *   "message": "Festival no encontrado"
     * }
     *
     * @apiResourceModel App\Models\Festival
     */
    public function show($id): JsonResponse
    {
        $festival = Festival::findOrFail($id);
        
        return response()->json([
            'data' => new FestivalResource($festival)
        ]);
    }

    /**
     * Store a newly created festival
     *
     * Crea un nuevo festival en el sistema (público).
     *
     * @bodyParam name string required Nombre del festival. Example: Festival de Música
     * @bodyParam slug string required Slug único del festival. Example: festival-musica
     * @bodyParam description string Descripción del festival. Example: Festival anual de música
     * @bodyParam month integer Mes del festival (1-12). Example: 7
     * @bodyParam usual_days string Días usuales del festival. Example: 15-20
     * @bodyParam recurring boolean Si el festival es recurrente. Example: true
     * @bodyParam location_id integer ID de la ubicación. Example: 1
     * @bodyParam logo_url string URL del logo del festival. Example: https://example.com/logo.png
     * @bodyParam color_theme string Tema de colores del festival. Example: #FF5733
     *
     * @response 201 {
     *   "data": {
     *     "id": 1,
     *     "name": "Festival de Música",
     *     "slug": "festival-musica",
     *     "description": "Festival anual de música"
     *   }
     * }
     *
     * @response 422 {
     *   "message": "Los datos proporcionados no son válidos.",
     *   "errors": {...}
     * }
     *
     * @apiResourceModel App\Models\Festival
     */
    public function store(StoreFestivalRequest $request): JsonResponse
    {
        $festival = Festival::create($request->validated());
        
        return response()->json([
            'data' => new FestivalResource($festival)
        ], 201);
    }

    /**
     * Get events of a festival
     *
     * Obtiene los eventos asociados a un festival específico.
     *
     * @urlParam id integer ID del festival. Example: 1
     * @queryParam page int Número de página. Example: 1
     * @queryParam per_page int Cantidad por página (máx 100). Example: 20
     *
     * @response 200 {
     *   "data": [
     *     {
     *       "id": 1,
     *       "title": "Concierto Principal",
     *       "description": "Concierto de apertura del festival"
     *     }
     *   ],
     *   "meta": {...}
     * }
     *
     * @apiResourceCollection App\Http\Resources\V1\EventResource
     * @apiResourceModel App\Models\Event
     */
    public function events($id): JsonResponse
    {
        $festival = Festival::findOrFail($id);
        $events = $festival->events()->paginate(20);
        
        return response()->json([
            'data' => EventResource::collection($events),
            'meta' => [
                'current_page' => $events->currentPage(),
                'last_page' => $events->lastPage(),
                'per_page' => $events->perPage(),
                'total' => $events->total(),
            ]
        ]);
    }
}
