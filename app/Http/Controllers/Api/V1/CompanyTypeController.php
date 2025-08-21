<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\CompanyType;
use App\Http\Resources\V1\CompanyTypeResource;
use App\Http\Requests\StoreCompanyTypeRequest;
use App\Http\Requests\UpdateCompanyTypeRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

/**
 * @group Company Types
 *
 * APIs para la gestión de tipos de empresas del sistema.
 * Permite crear, consultar y gestionar tipos de empresas.
 */
class CompanyTypeController extends Controller
{
    /**
     * Display a listing of company types
     *
     * Obtiene una lista paginada de todos los tipos de empresas.
     *
     * @queryParam page int Número de página. Example: 1
     * @queryParam per_page int Cantidad por página (máx 100). Example: 20
     * @queryParam category string Filtrar por categoría (technology, finance, healthcare). Example: technology
     * @queryParam is_active boolean Filtrar por tipos activos. Example: true
     * @queryParam search string Buscar por nombre o descripción. Example: startup
     *
     * @response 200 {
     *   "data": [
     *     {
     *       "id": 1,
     *       "name": "Startup",
     *       "slug": "startup",
     *       "category": "technology",
     *       "description": "Empresa tecnológica en fase inicial",
     *       "is_active": true,
     *       "icon": "fa-rocket"
     *     }
     *   ],
     *   "meta": {...}
     * }
     *
     * @apiResourceCollection App\Http\Resources\V1\CompanyTypeResource
     * @apiResourceModel App\Models\CompanyType
     */
    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'page' => 'sometimes|integer|min:1',
            'per_page' => 'sometimes|integer|min:1|max:100',
            'category' => 'sometimes|string|in:technology,finance,healthcare,retail,manufacturing',
            'is_active' => 'sometimes|boolean',
            'search' => 'sometimes|string|max:255'
        ]);

        $query = CompanyType::query();

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

        $companyTypes = $query->orderBy('category')
                             ->orderBy('name')
                             ->paginate($request->get('per_page', 20));

        return response()->json([
            'data' => CompanyTypeResource::collection($companyTypes),
            'meta' => [
                'current_page' => $companyTypes->currentPage(),
                'last_page' => $companyTypes->lastPage(),
                'per_page' => $companyTypes->perPage(),
                'total' => $companyTypes->total(),
            ]
        ]);
    }

    /**
     * Store a newly created company type
     *
     * Crea un nuevo tipo de empresa en el sistema.
     *
     * @bodyParam name string required Nombre del tipo. Example: Startup
     * @bodyParam slug string Slug único del tipo. Example: startup
     * @bodyParam category string required Categoría del tipo (technology, finance, healthcare). Example: technology
     * @bodyParam description string Descripción del tipo. Example: Empresa tecnológica en fase inicial
     * @bodyParam is_active boolean Si el tipo está activo. Example: true
     * @bodyParam icon string Icono del tipo. Example: fa-rocket
     * @bodyParam color string Color hexadecimal del tipo. Example: #FF5722
     *
     * @response 201 {
     *   "data": {
     *     "id": 1,
     *     "name": "Startup",
     *     "slug": "startup",
     *     "category": "technology",
     *     "description": "Empresa tecnológica en fase inicial",
     *     "is_active": true,
     *     "icon": "fa-rocket",
     *     "created_at": "2024-01-01T00:00:00.000000Z"
     *   }
     * }
     *
     * @response 422 {
     *   "message": "Los datos proporcionados no son válidos.",
     *   "errors": {...}
     * }
     *
     * @apiResourceModel App\Models\CompanyType
     * @authenticated
     */
    public function store(StoreCompanyTypeRequest $request): JsonResponse
    {
        $data = $request->validated();
        
        $companyType = CompanyType::create($data);

        return response()->json([
            'data' => new CompanyTypeResource($companyType)
        ], 201);
    }

    /**
     * Display the specified company type
     *
     * Obtiene los detalles de un tipo de empresa específico.
     *
     * @urlParam companyType integer ID del tipo de empresa. Example: 1
     *
     * @response 200 {
     *   "data": {
     *     "id": 1,
     *     "name": "Startup",
     *     "slug": "startup",
     *     "category": "technology",
     *     "description": "Empresa tecnológica en fase inicial",
     *     "is_active": true,
     *     "icon": "fa-rocket",
     *     "color": "#FF5722"
     *   }
     * }
     *
     * @response 404 {
     *   "message": "Tipo de empresa no encontrado"
     * }
     *
     * @apiResourceModel App\Models\CompanyType
     */
    public function show(CompanyType $companyType): JsonResponse
    {
        return response()->json([
            'data' => new CompanyTypeResource($companyType)
        ]);
    }

    /**
     * Update the specified company type
     *
     * Actualiza un tipo de empresa existente.
     *
     * @urlParam companyType integer ID del tipo de empresa. Example: 1
     * @bodyParam name string Nombre del tipo. Example: Startup Tecnológica
     * @bodyParam slug string Slug único del tipo. Example: startup-tecnologica
     * @bodyParam category string Categoría del tipo (technology, finance, healthcare). Example: technology
     * @bodyParam description string Descripción del tipo. Example: Empresa tecnológica innovadora en fase inicial
     * @bodyParam is_active boolean Si el tipo está activo. Example: true
     * @bodyParam icon string Icono del tipo. Example: fa-rocket
     * @bodyParam color string Color hexadecimal del tipo. Example: #E64A19
     *
     * @response 200 {
     *   "data": {
     *     "id": 1,
     *     "name": "Startup Tecnológica",
     *     "slug": "startup-tecnologica",
     *     "category": "technology",
     *     "description": "Empresa tecnológica innovadora en fase inicial",
     *     "is_active": true,
     *     "icon": "fa-rocket",
     *     "color": "#E64A19",
     *     "updated_at": "2024-01-02T00:00:00.000000Z"
     *   }
     * }
     *
     * @response 404 {
     *   "message": "Tipo de empresa no encontrado"
     * }
     *
     * @response 422 {
     *   "message": "Los datos proporcionados no son válidos.",
     *   "errors": {...}
     * }
     *
     * @apiResourceModel App\Models\CompanyType
     * @authenticated
     */
    public function update(UpdateCompanyTypeRequest $request, CompanyType $companyType): JsonResponse
    {
        $data = $request->validated();
        
        $companyType->update($data);

        return response()->json([
            'data' => new CompanyTypeResource($companyType)
        ]);
    }

    /**
     * Remove the specified company type
     *
     * Elimina un tipo de empresa del sistema.
     *
     * @urlParam companyType integer ID del tipo de empresa. Example: 1
     *
     * @response 204 {
     *   "message": "Tipo de empresa eliminado exitosamente"
     * }
     *
     * @response 404 {
     *   "message": "Tipo de empresa no encontrado"
     * }
     *
     * @authenticated
     */
    public function destroy(CompanyType $companyType): JsonResponse
    {
        $companyType->delete();

        return response()->json([
            'message' => 'Tipo de empresa eliminado exitosamente'
        ], 204);
    }
}
