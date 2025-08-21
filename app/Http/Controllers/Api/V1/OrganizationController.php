<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Organization;
use App\Http\Resources\V1\OrganizationResource;
use App\Http\Requests\StoreOrganizationRequest;
use App\Http\Requests\UpdateOrganizationRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

/**
 * @group Organizations
 *
 * APIs para la gestión de organizaciones del sistema.
 * Permite crear, consultar y gestionar organizaciones.
 */
class OrganizationController extends Controller
{
    /**
     * Display a listing of organizations
     *
     * Obtiene una lista paginada de todas las organizaciones.
     *
     * @queryParam page int Número de página. Example: 1
     * @queryParam per_page int Cantidad por página (máx 100). Example: 20
     * @queryParam type string Filtrar por tipo de organización (company, nonprofit, government). Example: company
     * @queryParam status string Filtrar por estado (active, inactive). Example: active
     * @queryParam search string Buscar por nombre o descripción. Example: tech
     *
     * @response 200 {
     *   "data": [
     *     {
     *       "id": 1,
     *       "name": "Tech Corp",
     *       "slug": "tech-corp",
     *       "type": "company",
     *       "email": "contact@techcorp.com",
     *       "phone": "+1234567890",
     *       "website": "https://techcorp.com",
     *       "status": "active"
     *     }
     *   ],
     *   "meta": {...}
     * }
     *
     * @apiResourceCollection App\Http\Resources\V1\OrganizationResource
     * @apiResourceModel App\Models\Organization
     * @authenticated
     */
    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'page' => 'sometimes|integer|min:1',
            'per_page' => 'sometimes|integer|min:1|max:100',
            'type' => 'sometimes|string|in:company,nonprofit,government',
            'status' => 'sometimes|string|in:active,inactive',
            'search' => 'sometimes|string|max:255'
        ]);

        $query = Organization::query();

        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        if ($request->has('status')) {
            $isActive = $request->status === 'active';
            $query->where('is_active', $isActive);
        }

        if ($request->has('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        $organizations = $query->orderBy('created_at', 'desc')
                              ->paginate($request->get('per_page', 20));

        return response()->json([
            'data' => OrganizationResource::collection($organizations),
            'meta' => [
                'current_page' => $organizations->currentPage(),
                'last_page' => $organizations->lastPage(),
                'per_page' => $organizations->perPage(),
                'total' => $organizations->total(),
            ]
        ]);
    }

    /**
     * Store a newly created organization
     *
     * Crea una nueva organización en el sistema.
     *
     * @bodyParam name string required Nombre de la organización. Example: Tech Corp
     * @bodyParam slug string Slug único de la organización. Example: tech-corp
     * @bodyParam type string required Tipo de organización (company, nonprofit, government). Example: company
     * @bodyParam description string Descripción de la organización. Example: Empresa de tecnología
     * @bodyParam email string Email de contacto. Example: contact@techcorp.com
     * @bodyParam phone string Teléfono de contacto. Example: +1234567890
     * @bodyParam website string Sitio web oficial. Example: https://techcorp.com
     * @bodyParam address string Dirección física. Example: 123 Main St, City
     * @bodyParam logo_url string URL del logo. Example: https://techcorp.com/logo.png
     * @bodyParam is_active boolean Si la organización está activa. Example: true
     *
     * @response 201 {
     *   "data": {
     *     "id": 1,
     *     "name": "Tech Corp",
     *     "slug": "tech-corp",
     *     "type": "company",
     *     "email": "contact@techcorp.com",
     *     "phone": "+1234567890",
     *     "website": "https://techcorp.com",
     *     "status": "active",
     *     "created_at": "2024-01-01T00:00:00.000000Z"
     *   }
     * }
     *
     * @response 422 {
     *   "message": "Los datos proporcionados no son válidos.",
     *   "errors": {...}
     * }
     *
     * @apiResourceModel App\Models\Organization
     * @authenticated
     */
    public function store(StoreOrganizationRequest $request): JsonResponse
    {
        $data = $request->validated();
        
        $organization = Organization::create($data);

        return response()->json([
            'data' => new OrganizationResource($organization)
        ], 201);
    }

    /**
     * Display the specified organization
     *
     * Obtiene los detalles de una organización específica.
     *
     * @urlParam organization integer ID de la organización. Example: 1
     *
     * @response 200 {
     *   "data": {
     *     "id": 1,
     *     "name": "Tech Corp",
     *     "slug": "tech-corp",
     *     "type": "company",
     *     "description": "Empresa de tecnología",
     *     "email": "contact@techcorp.com",
     *     "phone": "+1234567890",
     *     "website": "https://techcorp.com",
     *     "address": "123 Main St, City",
     *     "logo_url": "https://techcorp.com/logo.png",
     *     "status": "active"
     *   }
     * }
     *
     * @response 404 {
     *   "message": "Organización no encontrada"
     * }
     *
     * @apiResourceModel App\Models\Organization
     * @authenticated
     */
    public function show(Organization $organization): JsonResponse
    {
        return response()->json([
            'data' => new OrganizationResource($organization)
        ]);
    }

    /**
     * Update the specified organization
     *
     * Actualiza una organización existente.
     *
     * @urlParam organization integer ID de la organización. Example: 1
     * @bodyParam name string Nombre de la organización. Example: Tech Corp Inc
     * @bodyParam slug string Slug único de la organización. Example: tech-corp-inc
     * @bodyParam type string Tipo de organización (company, nonprofit, government). Example: company
     * @bodyParam description string Descripción de la organización. Example: Empresa de tecnología avanzada
     * @bodyParam email string Email de contacto. Example: info@techcorp.com
     * @bodyParam phone string Teléfono de contacto. Example: +1234567891
     * @bodyParam website string Sitio web oficial. Example: https://techcorp.com
     * @bodyParam address string Dirección física. Example: 456 New St, City
     * @bodyParam logo_url string URL del logo. Example: https://techcorp.com/new-logo.png
     * @bodyParam is_active boolean Si la organización está activa. Example: true
     *
     * @response 200 {
     *   "data": {
     *     "id": 1,
     *     "name": "Tech Corp Inc",
     *     "slug": "tech-corp-inc",
     *     "type": "company",
     *     "email": "info@techcorp.com",
     *     "phone": "+1234567891",
     *     "website": "https://techcorp.com",
     *     "status": "active",
     *     "updated_at": "2024-01-02T00:00:00.000000Z"
     *   }
     * }
     *
     * @response 404 {
     *   "message": "Organización no encontrada"
     * }
     *
     * @response 422 {
     *   "message": "Los datos proporcionados no son válidos.",
     *   "errors": {...}
     * }
     *
     * @apiResourceModel App\Models\Organization
     * @authenticated
     */
    public function update(UpdateOrganizationRequest $request, Organization $organization): JsonResponse
    {
        $data = $request->validated();
        
        $organization->update($data);

        return response()->json([
            'data' => new OrganizationResource($organization)
        ]);
    }

    /**
     * Remove the specified organization
     *
     * Elimina una organización del sistema.
     *
     * @urlParam organization integer ID de la organización. Example: 1
     *
     * @response 204 {
     *   "message": "Organización eliminada exitosamente"
     * }
     *
     * @response 404 {
     *   "message": "Organización no encontrada"
     * }
     *
     * @authenticated
     */
    public function destroy(Organization $organization): JsonResponse
    {
        $organization->delete();

        return response()->json([
            'message' => 'Organización eliminada exitosamente'
        ], 204);
    }
}
