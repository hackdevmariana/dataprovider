<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Http\Resources\V1\EventResource;
use Illuminate\Http\Request;

/**
 * @OA\Tag(
 *     name="Events",
 *     description="API for managing events"
 * )
 */
class EventController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/v1/events",
     *     summary="Get all events",
     *     tags={"Events"},
     *     @OA\Response(
     *         response=200,
     *         description="List of events",
     *         @OA\JsonContent(type="object",
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Event"))
     *         )
     *     )
     * )
     */
    public function index()
    {
        return EventResource::collection(Event::all());
    }

    /**
     * @OA\Get(
     *     path="/api/v1/events/{idOrSlug}",
     *     summary="Get event by ID or slug",
     *     tags={"Events"},
     *     @OA\Parameter(
     *         name="idOrSlug",
     *         in="path",
     *         required=true,
     *         description="ID or slug of the event",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Event found",
     *         @OA\JsonContent(ref="#/components/schemas/Event")
     *     ),
     *     @OA\Response(response=404, description="Event not found")
     * )
     */
    public function show($idOrSlug)
    {
        $event = Event::where('slug', $idOrSlug)
            ->orWhere('id', $idOrSlug)
            ->firstOrFail();
        return new EventResource($event);
    }
}
