<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Work;
use Illuminate\Http\Request;
use App\Http\Resources\WorkResource;

/**
 * @OA\Tag(
 *     name="Works",
 *     description="API para gestionar trabajos (libros, películas, etc.) de personas famosas"
 * )
 */
class WorkController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/v1/works",
     *     summary="Obtener listado de trabajos",
     *     tags={"Works"},
     *     @OA\Response(
     *         response=200,
     *         description="Listado de trabajos",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Work"))
     *         )
     *     )
     * )
     */
    public function index()
    {
        $works = Work::with(['person', 'language', 'link'])->get();
        return WorkResource::collection($works);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/works/{idOrSlug}",
     *     summary="Mostrar detalles de un trabajo",
     *     tags={"Works"},
     *     @OA\Parameter(
     *         name="idOrSlug",
     *         in="path",
     *         required=true,
     *         description="ID o slug del trabajo",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Trabajo encontrado",
     *         @OA\JsonContent(ref="#/components/schemas/Work")
     *     ),
     *     @OA\Response(response=404, description="Trabajo no encontrado")
     * )
     */
    public function show($idOrSlug)
    {
        $work = Work::with(['person', 'language', 'link'])
            ->where('slug', $idOrSlug)
            ->orWhere('id', $idOrSlug)
            ->firstOrFail();

        return new WorkResource($work);
    }
}
