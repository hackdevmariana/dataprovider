<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Platform;
use App\Http\Resources\V1\PlatformResource;
use App\Http\Requests\StorePlatformRequest;
use App\Http\Requests\UpdatePlatformRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
/**
 * @group Platforms
 *
 * APIs para la gestión de plataformas del sistema.
 * Permite crear, consultar y gestionar plataformas tecnológicas.
 */
class PlatformController extends Controller
{
    /**
     * Display a listing of platforms
     *
     * Obtiene una lista paginada de todas las plataformas.
     *
     * @queryParam page int Número de página. Example: 1
     * @queryParam per_page int Cantidad por página (máx 100). Example: 20
     * @queryParam type string Filtrar por tipo (mobile, web, desktop). Example: mobile
     * @queryParam is_active boolean Filtrar por plataformas activas. Example: true
     * @queryParam search string Buscar por nombre o descripción. Example: iOS
     *
     * @response 200 {
     *   "data": [
     *     {
     *       "id": 1,
     *       "name": "iOS",
     *       "type": "mobile",
     *       "version": "17.0",
     *       "is_active": true,
     *       "icon": "fa-apple"
     *     }
     *   ],
     *   "meta": {...}
     * }
     *
     * @apiResourceCollection App\Http\Resources\V1\PlatformResource
     * @apiResourceModel App\Models\Platform
     */
    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'page' => 'sometimes|integer|min:1',
            'per_page' => 'sometimes|integer|min:1|max:100',
            'type' => 'sometimes|string|in:mobile,web,desktop',
            'is_active' => 'sometimes|boolean',
            'search' => 'sometimes|string|max:255'
        ]);

        $query = Platform::query();

        if ($request->has('type')) {
            $query->where('type', $request->type);
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

        $platforms = $query->orderBy('type')
                          ->orderBy('name')
                          ->paginate($request->get('per_page', 20));

        return response()->json([
            'data' => PlatformResource::collection($platforms),
            'meta' => [
                'current_page' => $platforms->currentPage(),
                'last_page' => $platforms->lastPage(),
                'per_page' => $platforms->perPage(),
                'total' => $platforms->total(),
            ]
        ]);
    }

    /**
     * Store a newly created platform
     *
     * Crea una nueva plataforma en el sistema.
     *
     * @bodyParam name string required Nombre de la plataforma. Example: iOS
     * @bodyParam type string required Tipo de plataforma (mobile, web, desktop). Example: mobile
     * @bodyParam version string Versión de la plataforma. Example: 17.0
     * @bodyParam description string Descripción de la plataforma. Example: Sistema operativo móvil de Apple
     * @bodyParam is_active boolean Si la plataforma está activa. Example: true
     * @bodyParam icon string Icono de la plataforma. Example: fa-apple
     * @bodyParam website string Sitio web oficial. Example: https://www.apple.com/ios
     *
     * @response 201 {
     *   "data": {
     *     "id": 1,
     *     "name": "iOS",
     *     "type": "mobile",
     *     "version": "17.0",
     *     "description": "Sistema operativo móvil de Apple",
     *     "is_active": true,
     *     "created_at": "2024-01-01T00:00:00.000000Z"
     *   }
     * }
     *
     * @response 422 {
     *   "message": "Los datos proporcionados no son válidos.",
     *   "errors": {...}
     * }
     *
     * @apiResourceModel App\Models\Platform
     * @authenticated
     */
    public function store(StorePlatformRequest $request): JsonResponse
    {
        $data = $request->validated();
        
        $platform = Platform::create($data);

        return response()->json([
            'data' => new PlatformResource($platform)
        ], 201);
    }

    /**
     * Display the specified platform
     *
     * Obtiene los detalles de una plataforma específica.
     *
     * @urlParam platform integer ID de la plataforma. Example: 1
     *
     * @response 200 {
     *   "data": {
     *     "id": 1,
     *     "name": "iOS",
     *     "type": "mobile",
     *     "version": "17.0",
     *     "description": "Sistema operativo móvil de Apple",
     *     "is_active": true,
     *     "icon": "fa-apple",
     *     "website": "https://www.apple.com/ios"
     *   }
     * }
     *
     * @response 404 {
     *   "message": "Plataforma no encontrada"
     * }
     *
     * @apiResourceModel App\Models\Platform
     */
    public function show(Platform $platform): JsonResponse
    {
        return response()->json([
            'data' => new PlatformResource($platform)
        ]);
    }

    /**
     * Update the specified platform
     *
     * Actualiza una plataforma existente.
     *
     * @urlParam platform integer ID de la plataforma. Example: 1
     * @bodyParam name string Nombre de la plataforma. Example: iOS 17
     * @bodyParam type string Tipo de plataforma (mobile, web, desktop). Example: mobile
     * @bodyParam version string Versión de la plataforma. Example: 17.1
     * @bodyParam description string Descripción de la plataforma. Example: Sistema operativo móvil de Apple v17
     * @bodyParam is_active boolean Si la plataforma está activa. Example: true
     * @bodyParam icon string Icono de la plataforma. Example: fa-apple
     * @bodyParam website string Sitio web oficial. Example: https://www.apple.com/ios
     *
     * @response 200 {
     *   "data": {
     *     "id": 1,
     *     "name": "iOS 17",
     *     "type": "mobile",
     *     "version": "17.1",
     *     "description": "Sistema operativo móvil de Apple v17",
     *     "is_active": true,
     *     "updated_at": "2024-01-02T00:00:00.000000Z"
     *   }
     * }
     *
     * @response 404 {
     *   "message": "Plataforma no encontrada"
     * }
     *
     * @response 422 {
     *   "message": "Los datos proporcionados no son válidos.",
     *   "errors": {...}
     * }
     *
     * @apiResourceModel App\Models\Platform
     * @authenticated
     */
    public function update(UpdatePlatformRequest $request, Platform $platform): JsonResponse
    {
        $data = $request->validated();
        
        $platform->update($data);

        return response()->json([
            'data' => new PlatformResource($platform)
        ]);
    }

    /**
     * Remove the specified platform
     *
     * Elimina una plataforma del sistema.
     *
     * @urlParam platform integer ID de la plataforma. Example: 1
     *
     * @response 204 {
     *   "message": "Plataforma eliminada exitosamente"
     * }
     *
     * @response 404 {
     *   "message": "Plataforma no encontrada"
     * }
     *
     * @authenticated
     */
    public function destroy(Platform $platform): JsonResponse
    {
        $platform->delete();

        return response()->json([
            'message' => 'Plataforma eliminada exitosamente'
        ], 204);
    }
}
