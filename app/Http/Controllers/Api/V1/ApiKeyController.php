<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\ApiKey;
use App\Http\Resources\V1\ApiKeyResource;
use App\Http\Requests\StoreApiKeyRequest;
use App\Http\Requests\UpdateApiKeyRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

/**
 * @group API Keys
 *
 * APIs para la gestión de claves de API del sistema.
 * Permite crear, consultar y gestionar claves de acceso a la API.
 */
class ApiKeyController extends Controller
{
    /**
     * Display a listing of API keys
     *
     * Obtiene una lista paginada de todas las claves de API del usuario autenticado.
     *
     * @queryParam page int Número de página. Example: 1
     * @queryParam per_page int Cantidad por página (máx 100). Example: 20
     * @queryParam is_active boolean Filtrar por claves activas. Example: true
     * @queryParam search string Buscar por nombre o descripción. Example: producción
     *
     * @response 200 {
     *   "data": [
     *     {
     *       "id": 1,
     *       "name": "Clave de Producción",
     *       "key": "ak_1234567890abcdef...",
     *       "description": "Clave para aplicación de producción",
     *       "is_active": true,
     *       "last_used_at": "2024-01-01T00:00:00.000000Z",
     *       "expires_at": "2025-01-01T00:00:00.000000Z"
     *     }
     *   ],
     *   "meta": {...}
     * }
     *
     * @apiResourceCollection App\Http\Resources\V1\ApiKeyResource
     * @apiResourceModel App\Models\ApiKey
     * @authenticated
     */
    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'page' => 'sometimes|integer|min:1',
            'per_page' => 'sometimes|integer|min:1|max:100',
            'is_active' => 'sometimes|boolean',
            'search' => 'sometimes|string|max:255'
        ]);

        $query = ApiKey::where('user_id', auth()->id());

        if ($request->has('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        if ($request->has('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        $apiKeys = $query->orderBy('created_at', 'desc')
                         ->paginate($request->get('per_page', 20));

        return response()->json([
            'data' => ApiKeyResource::collection($apiKeys),
            'meta' => [
                'current_page' => $apiKeys->currentPage(),
                'last_page' => $apiKeys->lastPage(),
                'per_page' => $apiKeys->perPage(),
                'total' => $apiKeys->total(),
            ]
        ]);
    }

    /**
     * Store a newly created API key
     *
     * Crea una nueva clave de API para el usuario autenticado.
     *
     * @bodyParam name string required Nombre de la clave. Example: Clave de Producción
     * @bodyParam description string Descripción de la clave. Example: Clave para aplicación de producción
     * @bodyParam expires_at string Fecha de expiración (ISO 8601). Example: 2025-01-01T00:00:00Z
     * @bodyParam permissions array Permisos de la clave. Example: ["read", "write"]
     * @bodyParam is_active boolean Si la clave está activa. Example: true
     *
     * @response 201 {
     *   "data": {
     *     "id": 1,
     *     "name": "Clave de Producción",
     *     "key": "ak_1234567890abcdef...",
     *     "description": "Clave para aplicación de producción",
     *     "is_active": true,
     *     "expires_at": "2025-01-01T00:00:00.000000Z",
     *     "created_at": "2024-01-01T00:00:00.000000Z"
     *   }
     * }
     *
     * @response 422 {
     *   "message": "Los datos proporcionados no son válidos.",
     *   "errors": {...}
     * }
     *
     * @apiResourceModel App\Models\ApiKey
     * @authenticated
     */
    public function store(StoreApiKeyRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['user_id'] = auth()->id();
        $data['key'] = 'ak_' . Str::random(32);
        
        $apiKey = ApiKey::create($data);

        return response()->json([
            'data' => new ApiKeyResource($apiKey)
        ], 201);
    }

    /**
     * Display the specified API key
     *
     * Obtiene los detalles de una clave de API específica del usuario autenticado.
     *
     * @urlParam apiKey integer ID de la clave de API. Example: 1
     *
     * @response 200 {
     *   "data": {
     *     "id": 1,
     *     "name": "Clave de Producción",
     *     "key": "ak_1234567890abcdef...",
     *     "description": "Clave para aplicación de producción",
     *     "is_active": true,
     *     "last_used_at": "2024-01-01T00:00:00.000000Z",
     *     "expires_at": "2025-01-01T00:00:00.000000Z",
     *     "permissions": ["read", "write"]
     *   }
     * }
     *
     * @response 404 {
     *   "message": "Clave de API no encontrada"
     * }
     *
     * @apiResourceModel App\Models\ApiKey
     * @authenticated
     */
    public function show(ApiKey $apiKey): JsonResponse
    {
        // Verificar que la clave pertenece al usuario autenticado
        if ($apiKey->user_id !== auth()->id()) {
            return response()->json([
                'message' => 'Clave de API no encontrada'
            ], 404);
        }

        return response()->json([
            'data' => new ApiKeyResource($apiKey)
        ]);
    }

    /**
     * Update the specified API key
     *
     * Actualiza una clave de API existente del usuario autenticado.
     *
     * @urlParam apiKey integer ID de la clave de API. Example: 1
     * @bodyParam name string Nombre de la clave. Example: Clave de Producción Actualizada
     * @bodyParam description string Descripción de la clave. Example: Clave actualizada para aplicación de producción
     * @bodyParam expires_at string Fecha de expiración (ISO 8601). Example: 2026-01-01T00:00:00Z
     * @bodyParam permissions array Permisos de la clave. Example: ["read", "write", "delete"]
     * @bodyParam is_active boolean Si la clave está activa. Example: true
     *
     * @response 200 {
     *   "data": {
     *     "id": 1,
     *     "name": "Clave de Producción Actualizada",
     *     "description": "Clave actualizada para aplicación de producción",
     *     "is_active": true,
     *     "expires_at": "2026-01-01T00:00:00.000000Z",
     *     "updated_at": "2024-01-02T00:00:00.000000Z"
     *   }
     * }
     *
     * @response 404 {
     *   "message": "Clave de API no encontrada"
     * }
     *
     * @response 422 {
     *   "message": "Los datos proporcionados no son válidos.",
     *   "errors": {...}
     * }
     *
     * @apiResourceModel App\Models\ApiKey
     * @authenticated
     */
    public function update(UpdateApiKeyRequest $request, ApiKey $apiKey): JsonResponse
    {
        // Verificar que la clave pertenece al usuario autenticado
        if ($apiKey->user_id !== auth()->id()) {
            return response()->json([
                'message' => 'Clave de API no encontrada'
            ], 404);
        }

        $data = $request->validated();
        
        $apiKey->update($data);

        return response()->json([
            'data' => new ApiKeyResource($apiKey)
        ]);
    }

    /**
     * Remove the specified API key
     *
     * Elimina una clave de API del usuario autenticado.
     *
     * @urlParam apiKey integer ID de la clave de API. Example: 1
     *
     * @response 204 {
     *   "message": "Clave de API eliminada exitosamente"
     * }
     *
     * @response 404 {
     *   "message": "Clave de API no encontrada"
     * }
     *
     * @authenticated
     */
    public function destroy(ApiKey $apiKey): JsonResponse
    {
        // Verificar que la clave pertenece al usuario autenticado
        if ($apiKey->user_id !== auth()->id()) {
            return response()->json([
                'message' => 'Clave de API no encontrada'
            ], 404);
        }

        $apiKey->delete();

        return response()->json([
            'message' => 'Clave de API eliminada exitosamente'
        ], 204);
    }
}
