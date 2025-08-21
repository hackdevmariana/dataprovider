<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\TagGroup;
use App\Http\Resources\V1\TagGroupResource;
use App\Http\Requests\StoreTagGroupRequest;
use App\Http\Requests\UpdateTagGroupRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

/**
 * @group Tag Groups
 *
 * APIs para la gestión de grupos de etiquetas del sistema.
 * Permite crear, consultar y gestionar grupos para organizar etiquetas.
 */
class TagGroupController extends Controller
{
    /**
     * Display a listing of tag groups
     *
     * Obtiene una lista paginada de todos los grupos de etiquetas.
     *
     * @queryParam page int Número de página. Example: 1
     * @queryParam per_page int Cantidad por página (máx 100). Example: 20
     * @queryParam is_active boolean Filtrar por grupos activos. Example: true
     * @queryParam search string Buscar por nombre o descripción. Example: sostenibilidad
     *
     * @response 200 {
     *   "data": [
     *     {
     *       "id": 1,
     *       "name": "Sostenibilidad",
     *       "slug": "sostenibilidad",
     *       "description": "Grupo de etiquetas relacionadas con sostenibilidad",
     *       "is_active": true,
     *       "tags_count": 15
     *     }
     *   ],
     *   "meta": {...}
     * }
     *
     * @apiResourceCollection App\Http\Resources\V1\TagGroupResource
     * @apiResourceModel App\Models\TagGroup
     */
    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'page' => 'sometimes|integer|min:1',
            'per_page' => 'sometimes|integer|min:1|max:100',
            'is_active' => 'sometimes|boolean',
            'search' => 'sometimes|string|max:255'
        ]);

        $query = TagGroup::withCount('tags');

        if ($request->has('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        if ($request->has('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        $tagGroups = $query->orderBy('name')
                          ->paginate($request->get('per_page', 20));

        return response()->json([
            'data' => TagGroupResource::collection($tagGroups),
            'meta' => [
                'current_page' => $tagGroups->currentPage(),
                'last_page' => $tagGroups->lastPage(),
                'per_page' => $tagGroups->perPage(),
                'total' => $tagGroups->total(),
            ]
        ]);
    }

    /**
     * Store a newly created tag group
     *
     * Crea un nuevo grupo de etiquetas en el sistema.
     *
     * @bodyParam name string required Nombre del grupo. Example: Sostenibilidad
     * @bodyParam slug string Slug único del grupo. Example: sostenibilidad
     * @bodyParam description string Descripción del grupo. Example: Grupo de etiquetas relacionadas con sostenibilidad
     * @bodyParam is_active boolean Si el grupo está activo. Example: true
     * @bodyParam color string Color hexadecimal del grupo. Example: #4CAF50
     * @bodyParam icon string Icono del grupo. Example: fa-leaf
     *
     * @response 201 {
     *   "data": {
     *     "id": 1,
     *     "name": "Sostenibilidad",
     *     "slug": "sostenibilidad",
     *     "description": "Grupo de etiquetas relacionadas con sostenibilidad",
     *     "is_active": true,
     *     "color": "#4CAF50",
     *     "created_at": "2024-01-01T00:00:00.000000Z"
     *   }
     * }
     *
     * @response 422 {
     *   "message": "Los datos proporcionados no son válidos.",
     *   "errors": {...}
     * }
     *
     * @apiResourceModel App\Models\TagGroup
     * @authenticated
     */
    public function store(StoreTagGroupRequest $request): JsonResponse
    {
        $data = $request->validated();
        
        $tagGroup = TagGroup::create($data);

        return response()->json([
            'data' => new TagGroupResource($tagGroup->loadCount('tags'))
        ], 201);
    }

    /**
     * Display the specified tag group
     *
     * Obtiene los detalles de un grupo de etiquetas específico.
     *
     * @urlParam tagGroup integer ID del grupo de etiquetas. Example: 1
     *
     * @response 200 {
     *   "data": {
     *     "id": 1,
     *     "name": "Sostenibilidad",
     *     "slug": "sostenibilidad",
     *     "description": "Grupo de etiquetas relacionadas con sostenibilidad",
     *     "is_active": true,
     *     "color": "#4CAF50",
     *     "icon": "fa-leaf",
     *     "tags_count": 15,
     *     "tags": [...]
     *   }
     * }
     *
     * @response 404 {
     *   "message": "Grupo de etiquetas no encontrado"
     * }
     *
     * @apiResourceModel App\Models\TagGroup
     */
    public function show(TagGroup $tagGroup): JsonResponse
    {
        return response()->json([
            'data' => new TagGroupResource($tagGroup->load(['tags'])->loadCount('tags'))
        ]);
    }

    /**
     * Update the specified tag group
     *
     * Actualiza un grupo de etiquetas existente.
     *
     * @urlParam tagGroup integer ID del grupo de etiquetas. Example: 1
     * @bodyParam name string Nombre del grupo. Example: Sostenibilidad Verde
     * @bodyParam slug string Slug único del grupo. Example: sostenibilidad-verde
     * @bodyParam description string Descripción del grupo. Example: Grupo de etiquetas relacionadas con sostenibilidad verde
     * @bodyParam is_active boolean Si el grupo está activo. Example: true
     * @bodyParam color string Color hexadecimal del grupo. Example: #2E7D32
     * @bodyParam icon string Icono del grupo. Example: fa-seedling
     *
     * @response 200 {
     *   "data": {
     *     "id": 1,
     *     "name": "Sostenibilidad Verde",
     *     "slug": "sostenibilidad-verde",
     *     "description": "Grupo de etiquetas relacionadas con sostenibilidad verde",
     *     "is_active": true,
     *     "color": "#2E7D32",
     *     "updated_at": "2024-01-02T00:00:00.000000Z"
     *   }
     * }
     *
     * @response 404 {
     *   "message": "Grupo de etiquetas no encontrado"
     * }
     *
     * @response 422 {
     *   "message": "Los datos proporcionados no son válidos.",
     *   "errors": {...}
     * }
     *
     * @apiResourceModel App\Models\TagGroup
     * @authenticated
     */
    public function update(UpdateTagGroupRequest $request, TagGroup $tagGroup): JsonResponse
    {
        $data = $request->validated();
        
        $tagGroup->update($data);

        return response()->json([
            'data' => new TagGroupResource($tagGroup->loadCount('tags'))
        ]);
    }

    /**
     * Remove the specified tag group
     *
     * Elimina un grupo de etiquetas del sistema.
     *
     * @urlParam tagGroup integer ID del grupo de etiquetas. Example: 1
     *
     * @response 204 {
     *   "message": "Grupo de etiquetas eliminado exitosamente"
     * }
     *
     * @response 404 {
     *   "message": "Grupo de etiquetas no encontrado"
     * }
     *
     * @response 422 {
     *   "message": "No se puede eliminar el grupo porque tiene etiquetas asociadas",
     *   "errors": {...}
     * }
     *
     * @authenticated
     */
    public function destroy(TagGroup $tagGroup): JsonResponse
    {
        // Verificar si el grupo tiene etiquetas
        if ($tagGroup->tags()->count() > 0) {
            return response()->json([
                'message' => 'No se puede eliminar el grupo porque tiene etiquetas asociadas',
                'errors' => [
                    'tagGroup' => ['El grupo tiene etiquetas asociadas']
                ]
            ], 422);
        }

        $tagGroup->delete();

        return response()->json([
            'message' => 'Grupo de etiquetas eliminado exitosamente'
        ], 204);
    }
}
