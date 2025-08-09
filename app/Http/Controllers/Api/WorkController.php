<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Work;
use Illuminate\Http\Request;
use App\Http\Resources\V1\WorkResource;
use App\Http\Requests\StoreWorkRequest;

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

    /**
     * @OA\Post(
     *     path="/api/v1/works",
     *     summary="Crear un nuevo trabajo",
     *     tags={"Works"},
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"title", "slug", "type", "person_id"},
     *             @OA\Property(property="title", type="string", example="El Padrino"),
     *             @OA\Property(property="slug", type="string", example="el-padrino"),
     *             @OA\Property(property="type", type="string", example="movie"),
     *             @OA\Property(property="description", type="string", example="Película de crimen de 1972."),
     *             @OA\Property(property="release_year", type="integer", example=1972),
     *             @OA\Property(property="person_id", type="integer", example=1),
     *             @OA\Property(property="genre", type="string", example="Crimen, Drama"),
     *             @OA\Property(property="language_id", type="integer", example=2),
     *             @OA\Property(property="link_id", type="integer", example=3)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Trabajo creado exitosamente",
     *         @OA\JsonContent(ref="#/components/schemas/Work")
     *     ),
     *     @OA\Response(response=422, description="Datos inválidos")
     * )
     */
    public function store(StoreWorkRequest $request)
    {
        $work = Work::create($request->validated());
        $work->load(['person', 'language', 'link']);

        return new WorkResource($work);
    }
}
