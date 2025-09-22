<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\OrganizationFeature;
use App\Http\Resources\V1\OrganizationFeatureResource;
use App\Http\Requests\StoreOrganizationFeatureRequest;
use App\Http\Requests\UpdateOrganizationFeatureRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use OpenApi\Annotations as OA;

/**
 * @group Organization Features
 *
 * APIs para la gestión de características de organizaciones del sistema.
 * Permite crear, consultar y gestionar características específicas de organizaciones.
 */
/**
 * @OA\Tag(
 *     name="Características de Organizaciones",
 *     description="APIs para la gestión de Características de Organizaciones"
 * )
 */
class OrganizationFeatureController extends Controller
{
    /**
     * Display a listing of organization features
     *
     * Obtiene una lista paginada de todas las características de organizaciones.
     *
     * @queryParam page int Número de página. Example: 1
     * @queryParam per_page int Cantidad por página (máx 100). Example: 20
     * @queryParam organization_id integer Filtrar por organización. Example: 1
     * @queryParam feature_type string Filtrar por tipo de característica (sustainability, technology, social). Example: sustainability
     * @queryParam is_active boolean Filtrar por características activas. Example: true
     * @queryParam search string Buscar por nombre o descripción. Example: reciclaje
     *
     * @response 200 {
     *   "data": [
     *     {
     *       "id": 1,
     *       "organization_id": 1,
     *       "name": "Programa de Reciclaje",
     *       "feature_type": "sustainability",
     *       "description": "Sistema integral de reciclaje de residuos",
     *       "is_active": true,
     *       "priority": "high"
     *     }
     *   ],
     *   "meta": {...}
     * }
     *
     * @apiResourceCollection App\Http\Resources\V1\OrganizationFeatureResource
     * @apiResourceModel App\Models\OrganizationFeature
     */
    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'page' => 'sometimes|integer|min:1',
            'per_page' => 'sometimes|integer|min:1|max:100',
            'organization_id' => 'sometimes|integer|exists:organizations,id',
            'feature_type' => 'sometimes|string|in:sustainability,technology,social,environmental',
            'is_active' => 'sometimes|boolean',
            'search' => 'sometimes|string|max:255'
        ]);

        $query = OrganizationFeature::with(['organization']);

        if ($request->has('organization_id')) {
            $query->where('organization_id', $request->organization_id);
        }

        if ($request->has('feature_type')) {
            $query->where('feature_type', $request->feature_type);
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

        $features = $query->orderBy('priority', 'desc')
                         ->orderBy('name')
                         ->paginate($request->get('per_page', 20));

        return response()->json([
            'data' => OrganizationFeatureResource::collection($features),
            'meta' => [
                'current_page' => $features->currentPage(),
                'last_page' => $features->lastPage(),
                'per_page' => $features->perPage(),
                'total' => $features->total(),
            ]
        ]);
    }

    /**
     * Store a newly created organization feature
     *
     * Crea una nueva característica de organización en el sistema.
     *
     * @bodyParam organization_id integer required ID de la organización. Example: 1
     * @bodyParam name string required Nombre de la característica. Example: Programa de Reciclaje
     * @bodyParam feature_type string required Tipo de característica (sustainability, technology, social). Example: sustainability
     * @bodyParam description string Descripción de la característica. Example: Sistema integral de reciclaje de residuos
     * @bodyParam is_active boolean Si la característica está activa. Example: true
     * @bodyParam priority string Prioridad (low, medium, high, critical). Example: high
     * @bodyParam metadata object Metadatos adicionales (JSON). Example: {"implementation_date": "2024-01-01", "budget": 50000}
     *
     * @response 201 {
     *   "data": {
     *     "id": 1,
     *       "organization_id": 1,
     *       "name": "Programa de Reciclaje",
     *       "feature_type": "sustainability",
     *       "description": "Sistema integral de reciclaje de residuos",
     *       "is_active": true,
     *       "priority": "high",
     *       "created_at": "2024-01-01T00:00:00.000000Z"
     *   }
     * }
     *
     * @response 422 {
     *   "message": "Los datos proporcionados no son válidos.",
     *   "errors": {...}
     * }
     *
     * @apiResourceModel App\Models\OrganizationFeature
     * @authenticated
     */
    public function store(StoreOrganizationFeatureRequest $request): JsonResponse
    {
        $data = $request->validated();
        
        $feature = OrganizationFeature::create($data);

        return response()->json([
            'data' => new OrganizationFeatureResource($feature->load('organization'))
        ], 201);
    }

    /**
     * Display the specified organization feature
     *
     * Obtiene los detalles de una característica de organización específica.
     *
     * @urlParam organizationFeature integer ID de la característica. Example: 1
     *
     * @response 200 {
     *   "data": {
     *     "id": 1,
     *       "organization_id": 1,
     *       "name": "Programa de Reciclaje",
     *       "feature_type": "sustainability",
     *       "description": "Sistema integral de reciclaje de residuos",
     *       "is_active": true,
     *       "priority": "high",
     *       "metadata": {
     *         "implementation_date": "2024-01-01",
     *         "budget": 50000
     *       },
     *       "organization": {...}
     *   }
     * }
     *
     * @response 404 {
     *   "message": "Característica de organización no encontrada"
     * }
     *
     * @apiResourceModel App\Models\OrganizationFeature
     */
    public function show(OrganizationFeature $organizationFeature): JsonResponse
    {
        return response()->json([
            'data' => new OrganizationFeatureResource($organizationFeature->load('organization'))
        ]);
    }

    /**
     * Update the specified organization feature
     *
     * Actualiza una característica de organización existente.
     *
     * @urlParam organizationFeature integer ID de la característica. Example: 1
     * @bodyParam name string Nombre de la característica. Example: Programa de Reciclaje Avanzado
     * @bodyParam feature_type string Tipo de característica (sustainability, technology, social). Example: sustainability
     * @bodyParam description string Descripción de la característica. Example: Sistema integral y avanzado de reciclaje de residuos
     * @bodyParam is_active boolean Si la característica está activa. Example: true
     * @bodyParam priority string Prioridad (low, medium, high, critical). Example: critical
     * @bodyParam metadata object Metadatos adicionales (JSON). Example: {"implementation_date": "2024-01-01", "budget": 75000, "efficiency": "95%"}
     *
     * @response 200 {
     *   "data": {
     *     "id": 1,
     *       "name": "Programa de Reciclaje Avanzado",
     *       "feature_type": "sustainability",
     *       "description": "Sistema integral y avanzado de reciclaje de residuos",
     *       "is_active": true,
     *       "priority": "critical",
     *       "metadata": {
     *         "implementation_date": "2024-01-01",
     *         "budget": 75000,
     *         "efficiency": "95%"
     *       },
     *       "updated_at": "2024-01-02T00:00:00.000000Z"
     *   }
     * }
     *
     * @response 404 {
     *   "message": "Característica de organización no encontrada"
     * }
     *
     * @response 422 {
     *   "message": "Los datos proporcionados no son válidos.",
     *   "errors": {...}
     * }
     *
     * @apiResourceModel App\Models\OrganizationFeature
     * @authenticated
     */
    public function update(UpdateOrganizationFeatureRequest $request, OrganizationFeature $organizationFeature): JsonResponse
    {
        $data = $request->validated();
        
        $organizationFeature->update($data);

        return response()->json([
            'data' => new OrganizationFeatureResource($organizationFeature->load('organization'))
        ]);
    }

    /**
     * Remove the specified organization feature
     *
     * Elimina una característica de organización del sistema.
     *
     * @urlParam organizationFeature integer ID de la característica. Example: 1
     *
     * @response 204 {
     *   "message": "Característica de organización eliminada exitosamente"
     * }
     *
     * @response 404 {
     *   "message": "Característica de organización no encontrada"
     * }
     *
     * @authenticated
     */
    public function destroy(OrganizationFeature $organizationFeature): JsonResponse
    {
        $organizationFeature->delete();

        return response()->json([
            'message' => 'Característica de organización eliminada exitosamente'
        ], 204);
    }
}
