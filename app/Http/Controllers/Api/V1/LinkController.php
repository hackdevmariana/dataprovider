<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Link;
use Illuminate\Http\Request;
use App\Http\Resources\V1\LinkResource;
use App\Http\Requests\StoreLinkRequest;
use Illuminate\Http\JsonResponse;

/**
 * @group Links
 *
 * APIs para gestionar enlaces relacionados a otros modelos.
 * Permite crear, consultar y gestionar enlaces del sistema.
 */
class LinkController extends Controller
{
    /**
     * Display a listing of links
     *
     * Obtiene una lista de todos los enlaces disponibles.
     *
     * @response 200 {
     *   "data": [
     *     {
     *       "id": 1,
     *       "url": "https://example.com",
     *       "label": "Sitio oficial",
     *       "type": "external",
     *       "is_primary": true,
     *       "opens_in_new_tab": true
     *     }
     *   ]
     * }
     *
     * @apiResourceCollection App\Http\Resources\V1\LinkResource
     * @apiResourceModel App\Models\Link
     */
    public function index(): JsonResponse
    {
        $links = Link::all();
        
        return response()->json([
            'data' => LinkResource::collection($links)
        ]);
    }

    /**
     * Display the specified link
     *
     * Obtiene los detalles de un enlace específico por ID.
     *
     * @urlParam id integer ID del enlace. Example: 1
     *
     * @response 200 {
     *   "data": {
     *     "id": 1,
     *     "url": "https://example.com",
     *     "label": "Sitio oficial",
     *     "type": "external",
     *     "is_primary": true,
     *     "opens_in_new_tab": true
     *   }
     * }
     *
     * @response 404 {
     *   "message": "Enlace no encontrado"
     * }
     *
     * @apiResourceModel App\Models\Link
     */
    public function show($id): JsonResponse
    {
        $link = Link::findOrFail($id);
        
        return response()->json([
            'data' => new LinkResource($link)
        ]);
    }

    /**
     * Store a newly created link
     *
     * Crea un nuevo enlace en el sistema.
     *
     * @bodyParam url string required URL del enlace. Example: https://example.com
     * @bodyParam label string Etiqueta del enlace. Example: Sitio oficial
     * @bodyParam related_type string required Tipo de modelo relacionado. Example: App\Models\Work
     * @bodyParam related_id integer required ID del modelo relacionado. Example: 1
     * @bodyParam type string Tipo de enlace. Example: external
     * @bodyParam is_primary boolean Si es el enlace principal. Example: true
     * @bodyParam opens_in_new_tab boolean Si se abre en nueva pestaña. Example: true
     *
     * @response 201 {
     *   "data": {
     *     "id": 1,
     *     "url": "https://example.com",
     *     "label": "Sitio oficial",
     *     "type": "external",
     *     "is_primary": true,
     *     "opens_in_new_tab": true
     *   }
     * }
     *
     * @response 422 {
     *   "message": "Los datos proporcionados no son válidos.",
     *   "errors": {...}
     * }
     *
     * @apiResourceModel App\Models\Link
     * @authenticated
     */
    public function store(StoreLinkRequest $request): JsonResponse
    {
        $link = Link::create($request->validated());
        
        return response()->json([
            'data' => new LinkResource($link)
        ], 201);
    }
}
