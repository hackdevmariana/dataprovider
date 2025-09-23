<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Tag;
use App\Http\Resources\V1\TagResource;
use App\Http\Requests\StoreTagRequest;
use App\Http\Requests\UpdateTagRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
/**
 * @group Tags
 *
 * APIs para la gestión de etiquetas del sistema.
 * Permite crear, consultar y gestionar etiquetas para categorizar contenido.
 */
class TagController extends Controller
{
    /**
     * Display a listing of tags
     *
     * Obtiene una lista paginada de todas las etiquetas.
     *
     * @queryParam page int Número de página. Example: 1
     * @queryParam per_page int Cantidad por página (máx 100). Example: 20
     * @queryParam category_id integer Filtrar por categoría. Example: 1
     * @queryParam is_active boolean Filtrar por etiquetas activas. Example: true
     * @queryParam search string Buscar por nombre o descripción. Example: sostenibilidad
     * @queryParam popular boolean Filtrar solo etiquetas populares. Example: true
     *
     * @response 200 {
     *   "data": [
     *     {
     *       "id": 1,
     *       "name": "Sostenibilidad",
     *       "slug": "sostenibilidad",
     *       "description": "Etiqueta para contenido sostenible",
     *       "category_id": 1,
     *       "is_active": true,
     *       "usage_count": 25,
     *       "color": "#4CAF50"
     *     }
     *   ],
     *   "meta": {...}
     * }
     *
     * @apiResourceCollection App\Http\Resources\V1\TagResource
     * @apiResourceModel App\Models\Tag
     */
    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'page' => 'sometimes|integer|min:1',
            'per_page' => 'sometimes|integer|min:1|max:100',
            'category_id' => 'sometimes|integer|exists:categories,id',
            'is_active' => 'sometimes|boolean',
            'search' => 'sometimes|string|max:255',
            'popular' => 'sometimes|boolean'
        ]);

        $query = Tag::with(['category']);

        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->has('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        if ($request->has('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->boolean('popular')) {
            $query->orderBy('usage_count', 'desc');
        } else {
            $query->orderBy('name');
        }

        $tags = $query->paginate($request->get('per_page', 20));

        return response()->json([
            'data' => TagResource::collection($tags),
            'meta' => [
                'current_page' => $tags->currentPage(),
                'last_page' => $tags->lastPage(),
                'per_page' => $tags->perPage(),
                'total' => $tags->total(),
            ]
        ]);
    }

    /**
     * Store a newly created tag
     *
     * Crea una nueva etiqueta en el sistema.
     *
     * @bodyParam name string required Nombre de la etiqueta. Example: Sostenibilidad
     * @bodyParam slug string Slug único de la etiqueta. Example: sostenibilidad
     * @bodyParam description string Descripción de la etiqueta. Example: Etiqueta para contenido sostenible
     * @bodyParam category_id integer ID de la categoría. Example: 1
     * @bodyParam is_active boolean Si la etiqueta está activa. Example: true
     * @bodyParam color string Color hexadecimal de la etiqueta. Example: #4CAF50
     * @bodyParam icon string Icono de la etiqueta. Example: fa-leaf
     *
     * @response 201 {
     *   "data": {
     *     "id": 1,
     *     "name": "Sostenibilidad",
     *     "slug": "sostenibilidad",
     *     "description": "Etiqueta para contenido sostenible",
     *     "category_id": 1,
     *     "is_active": true,
     *     "usage_count": 0,
     *     "created_at": "2024-01-01T00:00:00.000000Z"
     *   }
     * }
     *
     * @response 422 {
     *   "message": "Los datos proporcionados no son válidos.",
     *   "errors": {...}
     * }
     *
     * @apiResourceModel App\Models\Tag
     * @authenticated
     */
    public function store(StoreTagRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['usage_count'] = 0;
        
        $tag = Tag::create($data);

        return response()->json([
            'data' => new TagResource($tag->load('category'))
        ], 201);
    }

    /**
     * Display the specified tag
     *
     * Obtiene los detalles de una etiqueta específica.
     *
     * @urlParam tag integer ID de la etiqueta. Example: 1
     *
     * @response 200 {
     *   "data": {
     *     "id": 1,
     *       "name": "Sostenibilidad",
     *       "slug": "sostenibilidad",
     *       "description": "Etiqueta para contenido sostenible",
     *       "category_id": 1,
     *       "is_active": true,
     *       "usage_count": 25,
     *       "color": "#4CAF50",
     *       "icon": "fa-leaf",
     *       "category": {...}
     *   }
     * }
     *
     * @response 404 {
     *   "message": "Etiqueta no encontrada"
     * }
     *
     * @apiResourceModel App\Models\Tag
     */
    public function show(Tag $tag): JsonResponse
    {
        return response()->json([
            'data' => new TagResource($tag->load('category'))
        ]);
    }

    /**
     * Update the specified tag
     *
     * Actualiza una etiqueta existente.
     *
     * @urlParam tag integer ID de la etiqueta. Example: 1
     * @bodyParam name string Nombre de la etiqueta. Example: Sostenibilidad Verde
     * @bodyParam slug string Slug único de la etiqueta. Example: sostenibilidad-verde
     * @bodyParam description string Descripción de la etiqueta. Example: Etiqueta para contenido de sostenibilidad verde
     * @bodyParam category_id integer ID de la categoría. Example: 1
     * @bodyParam is_active boolean Si la etiqueta está activa. Example: true
     * @bodyParam color string Color hexadecimal de la etiqueta. Example: #2E7D32
     * @bodyParam icon string Icono de la etiqueta. Example: fa-seedling
     *
     * @response 200 {
     *   "data": {
     *     "id": 1,
     *     "name": "Sostenibilidad Verde",
     *     "slug": "sostenibilidad-verde",
     *     "description": "Etiqueta para contenido de sostenibilidad verde",
     *     "category_id": 1,
     *     "is_active": true,
     *     "updated_at": "2024-01-02T00:00:00.000000Z"
     *   }
     * }
     *
     * @response 404 {
     *   "message": "Etiqueta no encontrada"
     * }
     *
     * @response 422 {
     *   "message": "Los datos proporcionados no son válidos.",
     *   "errors": {...}
     * }
     *
     * @apiResourceModel App\Models\Tag
     * @authenticated
     */
    public function update(UpdateTagRequest $request, Tag $tag): JsonResponse
    {
        $data = $request->validated();
        
        $tag->update($data);

        return response()->json([
            'data' => new TagResource($tag->load('category'))
        ]);
    }

    /**
     * Remove the specified tag
     *
     * Elimina una etiqueta del sistema.
     *
     * @urlParam tag integer ID de la etiqueta. Example: 1
     *
     * @response 204 {
     *   "message": "Etiqueta eliminada exitosamente"
     * }
     *
     * @response 404 {
     *   "message": "Etiqueta no encontrada"
     * }
     *
     * @response 422 {
     *   "message": "No se puede eliminar la etiqueta porque está en uso",
     *   "errors": {...}
     * }
     *
     * @authenticated
     */
    public function destroy(Tag $tag): JsonResponse
    {
        // Verificar si la etiqueta está en uso
        if ($tag->usage_count > 0) {
            return response()->json([
                'message' => 'No se puede eliminar la etiqueta porque está en uso',
                'errors' => [
                    'tag' => ['La etiqueta tiene elementos asociados']
                ]
            ], 422);
        }

        $tag->delete();

        return response()->json([
            'message' => 'Etiqueta eliminada exitosamente'
        ], 204);
    }
}
