<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\RelationshipType;
use App\Http\Resources\V1\RelationshipTypeResource;
use App\Http\Requests\StoreRelationshipTypeRequest;
use App\Http\Requests\UpdateRelationshipTypeRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

/**
 * @group Relationship Types
 *
 * APIs para la gestión de tipos de relaciones del sistema.
 * Permite crear, consultar y gestionar tipos de relaciones entre entidades.
 */
class RelationshipTypeController extends Controller
{
    /**
     * Display a listing of relationship types
     *
     * Obtiene una lista paginada de todos los tipos de relaciones.
     *
     * @queryParam page int Número de página. Example: 1
     * @queryParam per_page int Cantidad por página (máx 100). Example: 20
     * @queryParam category string Filtrar por categoría (family, professional, social). Example: family
     * @queryParam is_active boolean Filtrar por tipos activos. Example: true
     * @queryParam search string Buscar por nombre o descripción. Example: padre
     *
     * @response 200 {
     *   "data": [
     *     {
     *       "id": 1,
     *       "name": "Padre",
     *       "category": "family",
     *       "description": "Relación padre-hijo",
     *       "is_active": true,
     *       "is_bidirectional": true
     *     }
     *   ],
     *   "meta": {...}
     * }
     *
     * @apiResourceCollection App\Http\Resources\V1\RelationshipTypeResource
     * @apiResourceModel App\Models\RelationshipType
     */
    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'page' => 'sometimes|integer|min:1',
            'per_page' => 'sometimes|integer|min:1|max:100',
            'category' => 'sometimes|string|in:family,professional,social',
            'is_active' => 'sometimes|boolean',
            'search' => 'sometimes|string|max:255'
        ]);

        $query = RelationshipType::query();

        if ($request->has('category')) {
            $query->where('category', $request->category);
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

        $relationshipTypes = $query->orderBy('category')
                                 ->orderBy('name')
                                 ->paginate($request->get('per_page', 20));

        return response()->json([
            'data' => RelationshipTypeResource::collection($relationshipTypes),
            'meta' => [
                'current_page' => $relationshipTypes->currentPage(),
                'last_page' => $relationshipTypes->lastPage(),
                'per_page' => $relationshipTypes->perPage(),
                'total' => $relationshipTypes->total(),
            ]
        ]);
    }

    /**
     * Store a newly created relationship type
     *
     * Crea un nuevo tipo de relación en el sistema.
     *
     * @bodyParam name string required Nombre del tipo de relación. Example: Padre
     * @bodyParam category string required Categoría del tipo (family, professional, social). Example: family
     * @bodyParam description string Descripción del tipo de relación. Example: Relación padre-hijo
     * @bodyParam is_active boolean Si el tipo está activo. Example: true
     * @bodyParam is_bidirectional boolean Si la relación es bidireccional. Example: true
     * @bodyParam reverse_name string Nombre de la relación inversa. Example: Hijo
     *
     * @response 201 {
     *   "data": {
     *     "id": 1,
     *     "name": "Padre",
     *     "category": "family",
     *     "description": "Relación padre-hijo",
     *     "is_active": true,
     *     "is_bidirectional": true,
     *     "created_at": "2024-01-01T00:00:00.000000Z"
     *   }
     * }
     *
     * @response 422 {
     *   "message": "Los datos proporcionados no son válidos.",
     *   "errors": {...}
     * }
     *
     * @apiResourceModel App\Models\RelationshipType
     * @authenticated
     */
    public function store(StoreRelationshipTypeRequest $request): JsonResponse
    {
        $data = $request->validated();
        
        $relationshipType = RelationshipType::create($data);

        return response()->json([
            'data' => new RelationshipTypeResource($relationshipType)
        ], 201);
    }

    /**
     * Display the specified relationship type
     *
     * Obtiene los detalles de un tipo de relación específico.
     *
     * @urlParam relationshipType integer ID del tipo de relación. Example: 1
     *
     * @response 200 {
     *   "data": {
     *     "id": 1,
     *     "name": "Padre",
     *     "category": "family",
     *     "description": "Relación padre-hijo",
     *     "is_active": true,
     *     "is_bidirectional": true,
     *     "reverse_name": "Hijo"
     *   }
     * }
     *
     * @response 404 {
     *   "message": "Tipo de relación no encontrado"
     * }
     *
     * @apiResourceModel App\Models\RelationshipType
     */
    public function show(RelationshipType $relationshipType): JsonResponse
    {
        return response()->json([
            'data' => new RelationshipTypeResource($relationshipType)
        ]);
    }

    /**
     * Update the specified relationship type
     *
     * Actualiza un tipo de relación existente.
     *
     * @urlParam relationshipType integer ID del tipo de relación. Example: 1
     * @bodyParam name string Nombre del tipo de relación. Example: Padre Biológico
     * @bodyParam category string Categoría del tipo (family, professional, social). Example: family
     * @bodyParam description string Descripción del tipo de relación. Example: Relación padre biológico-hijo
     * @bodyParam is_active boolean Si el tipo está activo. Example: true
     * @bodyParam is_bidirectional boolean Si la relación es bidireccional. Example: true
     * @bodyParam reverse_name string Nombre de la relación inversa. Example: Hijo Biológico
     *
     * @response 200 {
     *   "data": {
     *     "id": 1,
     *     "name": "Padre Biológico",
     *     "category": "family",
     *     "description": "Relación padre biológico-hijo",
     *     "is_active": true,
     *     "is_bidirectional": true,
     *     "updated_at": "2024-01-02T00:00:00.000000Z"
     *   }
     * }
     *
     * @response 404 {
     *   "message": "Tipo de relación no encontrado"
     * }
     *
     * @response 422 {
     *   "message": "Los datos proporcionados no son válidos.",
     *   "errors": {...}
     * }
     *
     * @apiResourceModel App\Models\RelationshipType
     * @authenticated
     */
    public function update(UpdateRelationshipTypeRequest $request, RelationshipType $relationshipType): JsonResponse
    {
        $data = $request->validated();
        
        $relationshipType->update($data);

        return response()->json([
            'data' => new RelationshipTypeResource($relationshipType)
        ]);
    }

    /**
     * Remove the specified relationship type
     *
     * Elimina un tipo de relación del sistema.
     *
     * @urlParam relationshipType integer ID del tipo de relación. Example: 1
     *
     * @response 204 {
     *   "message": "Tipo de relación eliminado exitosamente"
     * }
     *
     * @response 404 {
     *   "message": "Tipo de relación no encontrado"
     * }
     *
     * @authenticated
     */
    public function destroy(RelationshipType $relationshipType): JsonResponse
    {
        $relationshipType->delete();

        return response()->json([
            'message' => 'Tipo de relación eliminado exitosamente'
        ], 204);
    }
}
