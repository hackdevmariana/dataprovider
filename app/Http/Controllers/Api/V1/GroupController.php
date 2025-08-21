<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Group;
use App\Http\Resources\V1\GroupResource;
use App\Http\Requests\StoreGroupRequest;
use Illuminate\Http\JsonResponse;

/**
 * @group Groups
 *
 * APIs para la gestión de grupos y organizaciones.
 * Permite crear, consultar y gestionar grupos del sistema.
 */
class GroupController extends Controller
{
    /**
     * Display a listing of groups
     *

     * Obtiene una lista paginada de todos los grupos disponibles.
     *
     * @queryParam page int Número de página. Example: 1
     * @queryParam per_page int Cantidad por página (máx 100). Example: 20
     *
     * @response 200 {
     *   "data": [
     *     {
     *       "id": 1,
     *       "name": "Grupo de Desarrollo",
     *       "description": "Grupo de desarrolladores de software",
     *       "slug": "grupo-desarrollo"
     *     }
     *   ],
     *   "meta": {...}
     * }
     *
     * @apiResourceCollection App\Http\Resources\V1\GroupResource
     * @apiResourceModel App\Models\Group
     */
    public function index(): JsonResponse
    {
        $groups = Group::paginate(20);
        
        return response()->json([
            'data' => GroupResource::collection($groups),
            'meta' => [
                'current_page' => $groups->currentPage(),
                'last_page' => $groups->lastPage(),
                'per_page' => $groups->perPage(),
                'total' => $groups->total(),
            ]
        ]);
    }

    /**
     * Display the specified group
     *

     * Obtiene los detalles de un grupo específico por ID.
     *
     * @urlParam id integer ID del grupo. Example: 1
     *
     * @response 200 {
     *   "data": {
     *     "id": 1,
     *       "name": "Grupo de Desarrollo",
     *       "description": "Grupo de desarrolladores de software",
     *       "slug": "grupo-desarrollo"
     *   }
     * }
     *
     * @response 404 {
     *   "message": "Grupo no encontrado"
     * }
     *
     * @apiResourceModel App\Models\Group
     */
    public function show($id): JsonResponse
    {
        $group = Group::findOrFail($id);
        
        return response()->json([
            'data' => new GroupResource($group)
        ]);
    }

    /**
     * Store a newly created group
     *

     * Crea un nuevo grupo en el sistema (público).
     *
     * @bodyParam name string required Nombre del grupo. Example: Grupo de Desarrollo
     * @bodyParam description string Descripción del grupo. Example: Grupo de desarrolladores de software
     * @bodyParam slug string Slug único del grupo. Example: grupo-desarrollo
     * @bodyParam is_active boolean Si el grupo está activo. Example: true
     *
     * @response 201 {
     *   "data": {
     *     "id": 1,
     *       "name": "Grupo de Desarrollo",
     *       "description": "Grupo de desarrolladores de software",
     *       "slug": "grupo-desarrollo"
     *   }
     * }
     *
     * @response 422 {
     *   "message": "Los datos proporcionados no son válidos.",
     *   "errors": {...}
     * }
     *
     * @apiResourceModel App\Models\Group
     */
    public function store(StoreGroupRequest $request): JsonResponse
    {
        $group = Group::create($request->validated());
        
        return response()->json([
            'data' => new GroupResource($group)
        ], 201);
    }
}
