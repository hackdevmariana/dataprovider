<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Work;
use Illuminate\Http\Request;
use App\Http\Resources\V1\WorkResource;
use App\Http\Requests\StoreWorkRequest;
use Illuminate\Http\JsonResponse;

/**
 * @group Works
 *
 * APIs para gestionar trabajos (libros, películas, etc.) de personas famosas.
 * Permite crear, consultar y gestionar obras y trabajos del sistema.
 */
class WorkController extends Controller
{
    /**
     * Display a listing of works
     *
     * Obtiene una lista de todos los trabajos disponibles.
     *
     * @response 200 {
     *   "data": [
     *     {
     *       "id": 1,
     *       "title": "El Padrino",
     *       "slug": "el-padrino",
     *       "type": "movie",
     *       "description": "Película de crimen de 1972.",
     *       "release_year": 1972,
     *       "genre": "Crimen, Drama",
     *       "person": {...},
     *       "language": {...},
     *       "link": {...}
     *     }
     *   ]
     * }
     *
     * @apiResourceCollection App\Http\Resources\V1\WorkResource
     * @apiResourceModel App\Models\Work
     */
    public function index(): JsonResponse
    {
        $works = Work::with(['person', 'language', 'link'])->get();
        
        return response()->json([
            'data' => WorkResource::collection($works)
        ]);
    }

    /**
     * Display the specified work
     *
     * Obtiene los detalles de un trabajo específico por ID o slug.
     *
     * @urlParam idOrSlug integer|string ID o slug del trabajo. Example: 1
     *
     * @response 200 {
     *   "data": {
     *     "id": 1,
     *       "title": "El Padrino",
     *       "slug": "el-padrino",
     *       "type": "movie",
     *       "description": "Película de crimen de 1972.",
     *       "release_year": 1972,
     *       "genre": "Crimen, Drama",
     *       "person": {...},
     *       "language": {...},
     *       "link": {...}
     *   }
     * }
     *
     * @response 404 {
     *   "message": "Trabajo no encontrado"
     * }
     *
     * @apiResourceModel App\Models\Work
     */
    public function show($idOrSlug): JsonResponse
    {
        $work = Work::with(['person', 'language', 'link'])
            ->where('slug', $idOrSlug)
            ->orWhere('id', $idOrSlug)
            ->firstOrFail();

        return response()->json([
            'data' => new WorkResource($work)
        ]);
    }

    /**
     * Store a newly created work
     *
     * Crea un nuevo trabajo en el sistema.
     *
     * @bodyParam title string required Título del trabajo. Example: El Padrino
     * @bodyParam slug string required Slug único del trabajo. Example: el-padrino
     * @bodyParam type string required Tipo de trabajo. Example: movie
     * @bodyParam description string Descripción del trabajo. Example: Película de crimen de 1972.
     * @bodyParam release_year integer Año de lanzamiento. Example: 1972
     * @bodyParam person_id integer required ID de la persona. Example: 1
     * @bodyParam genre string Género del trabajo. Example: Crimen, Drama
     * @bodyParam language_id integer ID del idioma. Example: 2
     * @bodyParam link_id integer ID del enlace. Example: 3
     * @bodyParam is_active boolean Si el trabajo está activo. Example: true
     *
     * @response 201 {
     *   "data": {
     *     "id": 1,
     *       "title": "El Padrino",
     *       "slug": "el-padrino",
     *       "type": "movie",
     *       "description": "Película de crimen de 1972.",
     *       "release_year": 1972,
     *       "genre": "Crimen, Drama"
     *   }
     * }
     *
     * @response 422 {
     *   "message": "Los datos proporcionados no son válidos.",
     *   "errors": {...}
     * }
     *
     * @apiResourceModel App\Models\Work
     * @authenticated
     */
    public function store(StoreWorkRequest $request): JsonResponse
    {
        $work = Work::create($request->validated());
        
        return response()->json([
            'data' => new WorkResource($work)
        ], 201);
    }
}
