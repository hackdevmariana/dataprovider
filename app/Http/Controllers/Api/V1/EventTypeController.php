<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\EventType;
use App\Http\Resources\V1\EventTypeResource;
use Illuminate\Http\JsonResponse;
/**
 * @group Event Types
 *
 * APIs para la gestión de tipos de eventos.
 * Permite consultar diferentes categorías y tipos de eventos del sistema.
 */
class EventTypeController extends Controller
{
    /**
     * Display a listing of event types
     *
     * Obtiene una lista de todos los tipos de eventos disponibles.
     *
     * @response 200 {
     *   "data": [
     *     {
     *       "id": 1,
     *       "name": "Conferencia",
     *       "slug": "conferencia",
     *       "description": "Eventos de tipo conferencia",
     *       "category": "educativo"
     *     }
     *   ]
     * }
     *
     * @apiResourceCollection App\Http\Resources\V1\EventTypeResource
     * @apiResourceModel App\Models\EventType
     */
    public function index(): JsonResponse
    {
        $eventTypes = EventType::all();
        
        return response()->json([
            'data' => EventTypeResource::collection($eventTypes)
        ]);
    }

    /**
     * Display the specified event type
     *
     * Obtiene los detalles de un tipo de evento específico por ID o slug.
     *
     * @urlParam idOrSlug integer|string ID o slug del tipo de evento. Example: 1
     *
     * @response 200 {
     *   "data": {
     *     "id": 1,
     *     "name": "Conferencia",
     *     "slug": "conferencia",
     *     "description": "Eventos de tipo conferencia",
     *     "category": "educativo"
     *   }
     * }
     *
     * @response 404 {
     *   "message": "Tipo de evento no encontrado"
     * }
     *
     * @apiResourceModel App\Models\EventType
     */
    public function show($idOrSlug): JsonResponse
    {
        $eventType = EventType::where('slug', $idOrSlug)
            ->orWhere('id', $idOrSlug)
            ->firstOrFail();
            
        return response()->json([
            'data' => new EventTypeResource($eventType)
        ]);
    }
}
