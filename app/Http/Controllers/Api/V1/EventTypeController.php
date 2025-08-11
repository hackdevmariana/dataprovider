<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\EventType;
use App\Http\Resources\V1\EventTypeResource;
use Illuminate\Http\Request;

/**
 * @OA\Tag(
 *     name="EventTypes",
 *     description="API for managing event types"
 * )
 */
class EventTypeController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/v1/event-types",
     *     summary="Get all event types",
     *     tags={"EventTypes"},
     *     @OA\Response(
     *         response=200,
     *         description="List of event types",
     *         @OA\JsonContent(type="object",
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/EventType"))
     *         )
     *     )
     * )
     */
    public function index()
    {
        return EventTypeResource::collection(EventType::all());
    }

    /**
     * @OA\Get(
     *     path="/api/v1/event-types/{idOrSlug}",
     *     summary="Get event type by ID or slug",
     *     tags={"EventTypes"},
     *     @OA\Parameter(
     *         name="idOrSlug",
     *         in="path",
     *         required=true,
     *         description="ID or slug of the event type",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Event type found",
     *         @OA\JsonContent(ref="#/components/schemas/EventType")
     *     ),
     *     @OA\Response(response=404, description="Event type not found")
     * )
     */
    public function show($idOrSlug)
    {
        $eventType = EventType::where('slug', $idOrSlug)
            ->orWhere('id', $idOrSlug)
            ->firstOrFail();
        return new EventTypeResource($eventType);
    }
}
