<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Link;
use Illuminate\Http\Request;
use App\Http\Resources\V1\LinkResource;
use App\Http\Requests\StoreLinkRequest;

/**
 * @OA\Tag(
 *     name="Links",
 *     description="API para gestionar enlaces relacionados a otros modelos"
 * )
 */
class LinkController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/v1/links",
     *     summary="Obtener todos los enlaces",
     *     tags={"Links"},
     *     @OA\Response(
     *         response=200,
     *         description="Listado de enlaces",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Link"))
     *         )
     *     )
     * )
     */
    public function index()
    {
        return LinkResource::collection(Link::all());
    }

    /**
     * @OA\Get(
     *     path="/api/v1/links/{id}",
     *     summary="Obtener detalles de un enlace",
     *     tags={"Links"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID del enlace",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Enlace encontrado",
     *         @OA\JsonContent(ref="#/components/schemas/Link")
     *     ),
     *     @OA\Response(response=404, description="Enlace no encontrado")
     * )
     */
    public function show($id)
    {
        $link = Link::findOrFail($id);
        return new LinkResource($link);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/links",
     *     summary="Crear un nuevo enlace",
     *     tags={"Links"},
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"url", "related_type", "related_id"},
     *             @OA\Property(property="url", type="string", example="https://example.com"),
     *             @OA\Property(property="label", type="string", example="Sitio oficial"),
     *             @OA\Property(property="related_type", type="string", example="App\\Models\\Work"),
     *             @OA\Property(property="related_id", type="integer", example=1),
     *             @OA\Property(property="type", type="string", example="external"),
     *             @OA\Property(property="is_primary", type="boolean", example=true),
     *             @OA\Property(property="opens_in_new_tab", type="boolean", example=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Enlace creado",
     *         @OA\JsonContent(ref="#/components/schemas/Link")
     *     ),
     *     @OA\Response(response=422, description="Datos inválidos")
     * )
     */
    public function store(StoreLinkRequest $request)
    {
        $link = Link::create($request->validated());
        return new LinkResource($link);
    }
}


