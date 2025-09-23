<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Http\Resources\V1\UserResource;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
/**
 * @group Users
 *
 * APIs para la gestión de usuarios del sistema.
 * Permite crear, consultar, actualizar y eliminar usuarios.
 */
class UserController extends Controller
{
    /**
     * Display a listing of users
     *
     * Obtiene una lista paginada de todos los usuarios.
     *
     * @queryParam page int Número de página. Example: 1
     * @queryParam per_page int Cantidad por página (máx 100). Example: 20
     * @queryParam search string Buscar por nombre o email. Example: juan
     * @queryParam status string Filtrar por estado (active, inactive). Example: active
     *
     * @response 200 {
     *   "data": [
     *     {
     *       "id": 1,
     *       "name": "Juan Pérez",
     *       "email": "juan@example.com",
     *       "email_verified_at": "2024-01-01T00:00:00.000000Z",
     *       "created_at": "2024-01-01T00:00:00.000000Z"
     *     }
     *   ],
     *   "meta": {...}
     * }
     *
     * @apiResourceCollection App\Http\Resources\V1\UserResource
     * @apiResourceModel App\Models\User
     * @authenticated
     */
    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'page' => 'sometimes|integer|min:1',
            'per_page' => 'sometimes|integer|min:1|max:100',
            'search' => 'sometimes|string|max:255',
            'status' => 'sometimes|string|in:active,inactive'
        ]);

        $query = User::query();

        if ($request->has('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->has('status')) {
            $isActive = $request->status === 'active';
            $query->where('is_active', $isActive);
        }

        $users = $query->orderBy('created_at', 'desc')
                      ->paginate($request->get('per_page', 20));

        return response()->json([
            'data' => UserResource::collection($users),
            'meta' => [
                'current_page' => $users->currentPage(),
                'last_page' => $users->lastPage(),
                'per_page' => $users->perPage(),
                'total' => $users->total(),
            ]
        ]);
    }

    /**
     * Store a newly created user
     *
     * Crea un nuevo usuario en el sistema.
     *
     * @bodyParam name string required Nombre completo del usuario. Example: Juan Pérez
     * @bodyParam email string required Email único del usuario. Example: juan@example.com
     * @bodyParam password string required Contraseña del usuario (mín 8 caracteres). Example: password123
     * @bodyParam password_confirmation string required Confirmación de contraseña. Example: password123
     * @bodyParam is_active boolean Si el usuario está activo. Example: true
     *
     * @response 201 {
     *   "data": {
     *     "id": 1,
     *     "name": "Juan Pérez",
     *     "email": "juan@example.com",
     *     "email_verified_at": null,
     *     "created_at": "2024-01-01T00:00:00.000000Z"
     *   }
     * }
     *
     * @response 422 {
     *   "message": "Los datos proporcionados no son válidos.",
     *   "errors": {...}
     * }
     *
     * @apiResourceModel App\Models\User
     * @authenticated
     */
    public function store(StoreUserRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['password'] = bcrypt($data['password']);
        
        $user = User::create($data);

        return response()->json([
            'data' => new UserResource($user)
        ], 201);
    }

    /**
     * Display the specified user
     *
     * Obtiene los detalles de un usuario específico.
     *
     * @urlParam user integer ID del usuario. Example: 1
     *
     * @response 200 {
     *   "data": {
     *     "id": 1,
     *     "name": "Juan Pérez",
     *     "email": "juan@example.com",
     *     "email_verified_at": "2024-01-01T00:00:00.000000Z",
     *     "created_at": "2024-01-01T00:00:00.000000Z"
     *   }
     * }
     *
     * @response 404 {
     *   "message": "Usuario no encontrado"
     * }
     *
     * @apiResourceModel App\Models\User
     * @authenticated
     */
    public function show(User $user): JsonResponse
    {
        return response()->json([
            'data' => new UserResource($user)
        ]);
    }

    /**
     * Update the specified user
     *
     * Actualiza un usuario existente.
     *
     * @urlParam user integer ID del usuario. Example: 1
     * @bodyParam name string Nombre completo del usuario. Example: Juan Carlos Pérez
     * @bodyParam email string Email único del usuario. Example: juan.carlos@example.com
     * @bodyParam password string Contraseña del usuario (mín 8 caracteres). Example: newpassword123
     * @bodyParam password_confirmation string Confirmación de contraseña. Example: newpassword123
     * @bodyParam is_active boolean Si el usuario está activo. Example: true
     *
     * @response 200 {
     *   "data": {
     *     "id": 1,
     *     "name": "Juan Carlos Pérez",
     *     "email": "juan.carlos@example.com",
     *     "email_verified_at": "2024-01-01T00:00:00.000000Z",
     *     "updated_at": "2024-01-02T00:00:00.000000Z"
     *   }
     * }
     *
     * @response 404 {
     *   "message": "Usuario no encontrado"
     * }
     *
     * @response 422 {
     *   "message": "Los datos proporcionados no son válidos.",
     *   "errors": {...}
     * }
     *
     * @apiResourceModel App\Models\User
     * @authenticated
     */
    public function update(UpdateUserRequest $request, User $user): JsonResponse
    {
        $data = $request->validated();
        
        if (isset($data['password'])) {
            $data['password'] = bcrypt($data['password']);
        }
        
        $user->update($data);

        return response()->json([
            'data' => new UserResource($user)
        ]);
    }

    /**
     * Remove the specified user
     *
     * Elimina un usuario del sistema (soft delete).
     *
     * @urlParam user integer ID del usuario. Example: 1
     *
     * @response 204 {
     *   "message": "Usuario eliminado exitosamente"
     * }
     *
     * @response 404 {
     *   "message": "Usuario no encontrado"
     * }
     *
     * @authenticated
     */
    public function destroy(User $user): JsonResponse
    {
        $user->delete();

        return response()->json([
            'message' => 'Usuario eliminado exitosamente'
        ], 204);
    }
}
