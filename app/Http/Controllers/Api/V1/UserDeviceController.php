<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\UserDevice;
use App\Http\Resources\V1\UserDeviceResource;
use App\Http\Requests\StoreUserDeviceRequest;
use App\Http\Requests\UpdateUserDeviceRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use OpenApi\Annotations as OA;

/**
 * @group User Devices
 *
 * APIs para la gestión de dispositivos de usuarios.
 * Permite registrar, consultar y gestionar dispositivos asociados a usuarios.
 */
/**
 * @OA\Tag(
 *     name="Dispositivos de Usuario",
 *     description="APIs para la gestión de Dispositivos de Usuario"
 * )
 */
class UserDeviceController extends Controller
{
    /**
     * Display a listing of user devices
     *
     * Obtiene una lista paginada de dispositivos del usuario autenticado.
     *
     * @queryParam page int Número de página. Example: 1
     * @queryParam per_page int Cantidad por página (máx 100). Example: 20
     * @queryParam device_type string Filtrar por tipo de dispositivo (mobile, tablet, desktop). Example: mobile
     * @queryParam is_active boolean Filtrar por dispositivos activos. Example: true
     *
     * @response 200 {
     *   "data": [
     *     {
     *       "id": 1,
     *       "device_name": "iPhone 13",
     *       "device_type": "mobile",
     *       "device_token": "abc123...",
     *       "platform": "ios",
     *       "is_active": true,
     *       "last_used_at": "2024-01-01T00:00:00.000000Z"
     *     }
     *   ],
     *   "meta": {...}
     * }
     *
     * @apiResourceCollection App\Http\Resources\V1\UserDeviceResource
     * @apiResourceModel App\Models\UserDevice
     * @authenticated
     */
    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'page' => 'sometimes|integer|min:1',
            'per_page' => 'sometimes|integer|min:1|max:100',
            'device_type' => 'sometimes|string|in:mobile,tablet,desktop',
            'is_active' => 'sometimes|boolean'
        ]);

        $query = UserDevice::where('user_id', auth()->id());

        if ($request->has('device_type')) {
            $query->where('device_type', $request->device_type);
        }

        if ($request->has('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        $devices = $query->orderBy('last_used_at', 'desc')
                         ->paginate($request->get('per_page', 20));

        return response()->json([
            'data' => UserDeviceResource::collection($devices),
            'meta' => [
                'current_page' => $devices->currentPage(),
                'last_page' => $devices->lastPage(),
                'per_page' => $devices->perPage(),
                'total' => $devices->total(),
            ]
        ]);
    }

    /**
     * Store a newly created user device
     *
     * Registra un nuevo dispositivo para el usuario autenticado.
     *
     * @bodyParam device_name string required Nombre del dispositivo. Example: iPhone 13
     * @bodyParam device_type string required Tipo de dispositivo (mobile, tablet, desktop). Example: mobile
     * @bodyParam device_token string required Token único del dispositivo. Example: abc123def456
     * @bodyParam platform string required Plataforma del dispositivo (ios, android, web). Example: ios
     * @bodyParam app_version string Versión de la aplicación. Example: 1.0.0
     * @bodyParam os_version string Versión del sistema operativo. Example: iOS 17.0
     * @bodyParam is_active boolean Si el dispositivo está activo. Example: true
     *
     * @response 201 {
     *   "data": {
     *     "id": 1,
     *     "device_name": "iPhone 13",
     *     "device_type": "mobile",
     *     "device_token": "abc123...",
     *     "platform": "ios",
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
     * @apiResourceModel App\Models\UserDevice
     * @authenticated
     */
    public function store(StoreUserDeviceRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['user_id'] = auth()->id();
        $data['last_used_at'] = now();
        
        $device = UserDevice::create($data);

        return response()->json([
            'data' => new UserDeviceResource($device)
        ], 201);
    }

    /**
     * Display the specified user device
     *
     * Obtiene los detalles de un dispositivo específico del usuario autenticado.
     *
     * @urlParam userDevice integer ID del dispositivo. Example: 1
     *
     * @response 200 {
     *   "data": {
     *     "id": 1,
     *     "device_name": "iPhone 13",
     *     "device_type": "mobile",
     *     "device_token": "abc123...",
     *     "platform": "ios",
     *     "is_active": true,
     *     "last_used_at": "2024-01-01T00:00:00.000000Z"
     *   }
     * }
     *
     * @response 404 {
     *   "message": "Dispositivo no encontrado"
     * }
     *
     * @apiResourceModel App\Models\UserDevice
     * @authenticated
     */
    public function show(UserDevice $userDevice): JsonResponse
    {
        // Verificar que el dispositivo pertenece al usuario autenticado
        if ($userDevice->user_id !== auth()->id()) {
            return response()->json([
                'message' => 'Dispositivo no encontrado'
            ], 404);
        }

        return response()->json([
            'data' => new UserDeviceResource($userDevice)
        ]);
    }

    /**
     * Update the specified user device
     *
     * Actualiza un dispositivo existente del usuario autenticado.
     *
     * @urlParam userDevice integer ID del dispositivo. Example: 1
     * @bodyParam device_name string Nombre del dispositivo. Example: iPhone 13 Pro
     * @bodyParam device_type string Tipo de dispositivo (mobile, tablet, desktop). Example: mobile
     * @bodyParam device_token string Token único del dispositivo. Example: abc123def456
     * @bodyParam platform string Plataforma del dispositivo (ios, android, web). Example: ios
     * @bodyParam app_version string Versión de la aplicación. Example: 1.1.0
     * @bodyParam os_version string Versión del sistema operativo. Example: iOS 17.1
     * @bodyParam is_active boolean Si el dispositivo está activo. Example: true
     *
     * @response 200 {
     *   "data": {
     *     "id": 1,
     *     "device_name": "iPhone 13 Pro",
     *     "device_type": "mobile",
     *     "device_token": "abc123...",
     *     "platform": "ios",
     *     "is_active": true,
     *     "updated_at": "2024-01-02T00:00:00.000000Z"
     *   }
     * }
     *
     * @response 404 {
     *   "message": "Dispositivo no encontrado"
     * }
     *
     * @response 422 {
     *   "message": "Los datos proporcionados no son válidos.",
     *   "errors": {...}
     * }
     *
     * @apiResourceModel App\Models\UserDevice
     * @authenticated
     */
    public function update(UpdateUserDeviceRequest $request, UserDevice $userDevice): JsonResponse
    {
        // Verificar que el dispositivo pertenece al usuario autenticado
        if ($userDevice->user_id !== auth()->id()) {
            return response()->json([
                'message' => 'Dispositivo no encontrado'
            ], 404);
        }

        $data = $request->validated();
        $data['last_used_at'] = now();
        
        $userDevice->update($data);

        return response()->json([
            'data' => new UserDeviceResource($userDevice)
        ]);
    }

    /**
     * Remove the specified user device
     *
     * Elimina un dispositivo del usuario autenticado.
     *
     * @urlParam userDevice integer ID del dispositivo. Example: 1
     *
     * @response 204 {
     *   "message": "Dispositivo eliminado exitosamente"
     * }
     *
     * @response 404 {
     *   "message": "Dispositivo no encontrado"
     * }
     *
     * @authenticated
     */
    public function destroy(UserDevice $userDevice): JsonResponse
    {
        // Verificar que el dispositivo pertenece al usuario autenticado
        if ($userDevice->user_id !== auth()->id()) {
            return response()->json([
                'message' => 'Dispositivo no encontrado'
            ], 404);
        }

        $userDevice->delete();

        return response()->json([
            'message' => 'Dispositivo eliminado exitosamente'
        ], 204);
    }
}
