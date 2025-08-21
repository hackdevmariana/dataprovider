<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Alias;
use App\Http\Resources\V1\AliasResource;
use App\Http\Requests\StoreAliasRequest;
use App\Http\Requests\UpdateAliasRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

/**
 * @group Aliases
 *
 * APIs para la gestión de alias del sistema.
 * Permite crear, consultar y gestionar alias para entidades.
 */
class AliasController extends Controller
{
    /**
     * Display a listing of aliases
     *
     * Obtiene una lista paginada de todos los alias.
     *
     * @queryParam page int Número de página. Example: 1
     * @queryParam per_page int Cantidad por página (máx 100). Example: 20
     * @queryParam aliasable_type string Filtrar por tipo de entidad. Example: Person
     * @queryParam aliasable_id integer Filtrar por ID de entidad. Example: 1
     * @queryParam is_active boolean Filtrar por alias activos. Example: true
     * @queryParam search string Buscar por nombre o alias. Example: juan
     *
     * @response 200 {
     *   "data": [
     *     {
     *       "id": 1,
     *       "name": "Juan Pérez",
     *       "alias": "JP",
     *       "aliasable_type": "Person",
     *       "aliasable_id": 1,
     *       "is_active": true,
     *       "description": "Alias común para Juan Pérez"
     *     }
     *   ],
     *   "meta": {...}
     * }
     *
     * @apiResourceCollection App\Http\Resources\V1\AliasResource
     * @apiResourceModel App\Models\Alias
     */
    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'page' => 'sometimes|integer|min:1',
            'per_page' => 'sometimes|integer|min:1|max:100',
            'aliasable_type' => 'sometimes|string|max:255',
            'aliasable_id' => 'sometimes|integer',
            'is_active' => 'sometimes|boolean',
            'search' => 'sometimes|string|max:255'
        ]);

        $query = Alias::with(['aliasable']);

        if ($request->has('aliasable_type')) {
            $query->where('aliasable_type', $request->aliasable_type);
        }

        if ($request->has('aliasable_id')) {
            $query->where('aliasable_id', $request->aliasable_id);
        }

        if ($request->has('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        if ($request->has('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('alias', 'like', '%' . $request->search . '%');
            });
        }

        $aliases = $query->orderBy('aliasable_type')
                        ->orderBy('name')
                        ->paginate($request->get('per_page', 20));

        return response()->json([
            'data' => AliasResource::collection($aliases),
            'meta' => [
                'current_page' => $aliases->currentPage(),
                'last_page' => $aliases->lastPage(),
                'per_page' => $aliases->perPage(),
                'total' => $aliases->total(),
            ]
        ]);
    }

    /**
     * Store a newly created alias
     *
     * Crea un nuevo alias en el sistema.
     *
     * @bodyParam name string required Nombre completo. Example: Juan Pérez
     * @bodyParam alias string required Alias. Example: JP
     * @bodyParam aliasable_type string required Tipo de entidad. Example: Person
     * @bodyParam aliasable_id integer required ID de la entidad. Example: 1
     * @bodyParam description string Descripción del alias. Example: Alias común para Juan Pérez
     * @bodyParam is_active boolean Si el alias está activo. Example: true
     * @bodyParam metadata object Metadatos adicionales (JSON). Example: {"context": "work", "preferred": true}
     *
     * @response 201 {
     *   "data": {
     *     "id": 1,
     *       "name": "Juan Pérez",
     *       "alias": "JP",
     *       "aliasable_type": "Person",
     *       "aliasable_id": 1,
     *       "description": "Alias común para Juan Pérez",
     *       "is_active": true,
     *       "created_at": "2024-01-01T00:00:00.000000Z"
     *   }
     * }
     *
     * @response 422 {
     *   "message": "Los datos proporcionados no son válidos.",
     *   "errors": {...}
     * }
     *
     * @apiResourceModel App\Models\Alias
     * @authenticated
     */
    public function store(StoreAliasRequest $request): JsonResponse
    {
        $data = $request->validated();
        
        $alias = Alias::create($data);

        return response()->json([
            'data' => new AliasResource($alias->load('aliasable'))
        ], 201);
    }

    /**
     * Display the specified alias
     *
     * Obtiene los detalles de un alias específico.
     *
     * @urlParam alias integer ID del alias. Example: 1
     *
     * @response 200 {
     *   "data": {
     *     "id": 1,
     *       "name": "Juan Pérez",
     *       "alias": "JP",
     *       "aliasable_type": "Person",
     *       "aliasable_id": 1,
     *       "description": "Alias común para Juan Pérez",
     *       "is_active": true,
     *       "metadata": {
     *         "context": "work",
     *         "preferred": true
     *       },
     *       "aliasable": {...}
     *   }
     * }
     *
     * @response 404 {
     *   "message": "Alias no encontrado"
     * }
     *
     * @apiResourceModel App\Models\Alias
     */
    public function show(Alias $alias): JsonResponse
    {
        return response()->json([
            'data' => new AliasResource($alias->load('aliasable'))
        ]);
    }

    /**
     * Update the specified alias
     *
     * Actualiza un alias existente.
     *
     * @urlParam alias integer ID del alias. Example: 1
     * @bodyParam name string Nombre completo. Example: Juan Carlos Pérez
     * @bodyParam alias string Alias. Example: JCP
     * @bodyParam aliasable_type string Tipo de entidad. Example: Person
     * @bodyParam aliasable_id integer ID de la entidad. Example: 1
     * @bodyParam description string Descripción del alias. Example: Alias actualizado para Juan Carlos Pérez
     * @bodyParam is_active boolean Si el alias está activo. Example: true
     * @bodyParam metadata object Metadatos adicionales (JSON). Example: {"context": "personal", "preferred": true}
     *
     * @response 200 {
     *   "data": {
     *     "id": 1,
     *       "name": "Juan Carlos Pérez",
     *       "alias": "JCP",
     *       "aliasable_type": "Person",
     *       "aliasable_id": 1,
     *       "description": "Alias actualizado para Juan Carlos Pérez",
     *       "is_active": true,
     *       "updated_at": "2024-01-02T00:00:00.000000Z"
     *   }
     * }
     *
     * @response 404 {
     *   "message": "Alias no encontrado"
     * }
     *
     * @response 422 {
     *   "message": "Los datos proporcionados no son válidos.",
     *   "errors": {...}
     * }
     *
     * @apiResourceModel App\Models\Alias
     * @authenticated
     */
    public function update(UpdateAliasRequest $request, Alias $alias): JsonResponse
    {
        $data = $request->validated();
        
        $alias->update($data);

        return response()->json([
            'data' => new AliasResource($alias->load('aliasable'))
        ]);
    }

    /**
     * Remove the specified alias
     *
     * Elimina un alias del sistema.
     *
     * @urlParam alias integer ID del alias. Example: 1
     *
     * @response 204 {
     *   "message": "Alias eliminado exitosamente"
     * }
     *
     * @response 404 {
     *   "message": "Alias no encontrado"
     * }
     *
     * @authenticated
     */
    public function destroy(Alias $alias): JsonResponse
    {
        $alias->delete();

        return response()->json([
            'message' => 'Alias eliminado exitosamente'
        ], 204);
    }
}
