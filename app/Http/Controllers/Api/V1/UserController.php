<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

/**
 * @group Users
 *
 * APIs para la gestión de usuarios de la plataforma.
 * Permite gestionar perfiles de usuario, autenticación,
 * roles y permisos del sistema.
 */
class UserController extends Controller
{
    /**
     * Display a listing of users
     *
     * Obtiene una lista de usuarios con opciones de filtrado.
     * Solo administradores pueden acceder a esta funcionalidad.
     *
     * @queryParam name string Filtrar por nombre. Example: Juan
     * @queryParam email string Filtrar por email. Example: juan@example.com
     * @queryParam role string Filtrar por rol (user, member, expert, admin, super_admin). Example: member
     * @queryParam status string Filtrar por estado (active, inactive, suspended, banned). Example: active
     * @queryParam cooperative_id int Filtrar por cooperativa. Example: 1
     * @queryParam is_verified boolean Filtrar por usuarios verificados. Example: true
     * @queryParam date_from date Fecha de registro desde (YYYY-MM-DD). Example: 2024-01-01
     * @queryParam date_to date Fecha de registro hasta (YYYY-MM-DD). Example: 2024-12-31
     * @queryParam sort string Ordenamiento (recent, name_asc, name_desc, email_asc, email_desc). Example: recent
     * @queryParam page int Número de página. Example: 1
     * @queryParam per_page int Cantidad por página (máx 100). Example: 15
     *
     * @apiResourceCollection App\Http\Resources\V1\UserResource
     * @apiResourceModel App\Models\User
     *
     * @response 403 {
     *   "message": "No tienes permisos para listar usuarios"
     * }
     */
    public function index(Request $request): JsonResponse
    {
        // Verificar permisos de administrador
        if (!Auth::user()->hasRole(['admin', 'super_admin'])) {
            return response()->json([
                'message' => 'No tienes permisos para listar usuarios'
            ], 403);
        }

        $query = User::with(['cooperative', 'roles', 'permissions']);

        // Filtros
        if ($request->filled('name')) {
            $query->where('name', 'like', '%' . $request->name . '%');
        }

        if ($request->filled('email')) {
            $query->where('email', 'like', '%' . $request->email . '%');
        }

        if ($request->filled('role')) {
            $query->whereHas('roles', function ($q) use ($request) {
                $q->where('name', $request->role);
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('cooperative_id')) {
            $query->where('cooperative_id', $request->cooperative_id);
        }

        if ($request->has('is_verified')) {
            $query->where('is_verified', $request->boolean('is_verified'));
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Ordenamiento
        $sort = $request->get('sort', 'recent');
        switch ($sort) {
            case 'name_asc':
                $query->orderBy('name', 'asc');
                break;
            case 'name_desc':
                $query->orderBy('name', 'desc');
                break;
            case 'email_asc':
                $query->orderBy('email', 'asc');
                break;
            case 'email_desc':
                $query->orderBy('email', 'desc');
                break;
            default:
                $query->latest();
        }

        $perPage = min($request->get('per_page', 15), 100);
        $users = $query->paginate($perPage);

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
     * Crea un nuevo usuario. Solo administradores pueden crear usuarios.
     *
     * @bodyParam name string required Nombre completo del usuario. Example: Juan Pérez
     * @bodyParam email string required Email único del usuario. Example: juan@example.com
     * @bodyParam password string required Contraseña (mín 8 caracteres). Example: password123
     * @bodyParam password_confirmation string required Confirmación de contraseña. Example: password123
     * @bodyParam phone string Teléfono del usuario. Example: +34 600 123 456
     * @bodyParam cooperative_id int ID de la cooperativa. Example: 1
     * @bodyParam roles array Roles del usuario. Example: ["member", "expert"]
     * @bodyParam status string Estado del usuario (active, inactive, suspended). Example: active
     * @bodyParam is_verified boolean Si el usuario está verificado. Example: false
     * @bodyParam profile_data array Datos adicionales del perfil. Example: {"bio": "Experto en energía solar", "location": "Madrid"}
     *
     * @response 201 {
     *   "data": {
     *     "id": 1,
     *     "name": "Juan Pérez",
     *     "email": "juan@example.com",
     *     "status": "active",
     *     "is_verified": false,
     *     "created_at": "2024-01-15T10:00:00.000000Z"
     *   },
     *   "message": "Usuario creado exitosamente"
     * }
     *
     * @response 422 {
     *   "message": "Los datos proporcionados no son válidos",
     *   "errors": {
     *     "email": ["El email ya está en uso"],
     *     "password": ["La contraseña debe tener al menos 8 caracteres"]
     *   }
     * }
     *
     * @authenticated
     */
    public function store(Request $request): JsonResponse
    {
        // Verificar permisos de administrador
        if (!Auth::user()->hasRole(['admin', 'super_admin'])) {
            return response()->json([
                'message' => 'No tienes permisos para crear usuarios'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'nullable|string|max:20',
            'cooperative_id' => 'nullable|exists:cooperatives,id',
            'roles' => 'nullable|array',
            'roles.*' => 'string|exists:roles,name',
            'status' => 'nullable|in:active,inactive,suspended',
            'is_verified' => 'boolean',
            'profile_data' => 'nullable|array'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Los datos proporcionados no son válidos',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
            'cooperative_id' => $request->cooperative_id,
            'status' => $request->status ?? 'active',
            'is_verified' => $request->boolean('is_verified', false),
            'profile_data' => $request->profile_data ?? []
        ]);

        // Asignar roles si se especifican
        if ($request->filled('roles')) {
            $user->assignRole($request->roles);
        } else {
            $user->assignRole('user');
        }

        return response()->json([
            'data' => new UserResource($user),
            'message' => 'Usuario creado exitosamente'
        ], 201);
    }

    /**
     * Display the specified user
     *
     * Obtiene los detalles de un usuario específico.
     * Los usuarios solo pueden ver su propio perfil o perfiles públicos.
     *
     * @urlParam user int required ID del usuario. Example: 1
     *
     * @response 200 {
     *   "data": {
     *     "id": 1,
     *     "name": "Juan Pérez",
     *     "email": "juan@example.com",
     *     "status": "active",
     *     "is_verified": true,
     *     "cooperative": {
     *       "id": 1,
     *       "name": "Cooperativa Solar Madrid"
     *     },
     *     "roles": ["member", "expert"]
     *   }
     * }
     *
     * @response 403 {
     *   "message": "No tienes permisos para ver este usuario"
     * }
     *
     * @response 404 {
     *   "message": "Usuario no encontrado"
     * }
     */
    public function show(User $user): JsonResponse
    {
        // Verificar permisos: usuario puede ver su propio perfil o administradores pueden ver cualquier perfil
        if (Auth::id() !== $user->id && !Auth::user()->hasRole(['admin', 'super_admin'])) {
            return response()->json([
                'message' => 'No tienes permisos para ver este usuario'
            ], 403);
        }

        return response()->json([
            'data' => new UserResource($user->load(['cooperative', 'roles', 'permissions']))
        ]);
    }

    /**
     * Update the specified user
     *
     * Actualiza un usuario existente. Los usuarios solo pueden editar su propio perfil.
     *
     * @urlParam user int required ID del usuario. Example: 1
     * @bodyParam name string Nombre completo del usuario. Example: Juan Carlos Pérez
     * @bodyParam phone string Teléfono del usuario. Example: +34 600 123 456
     * @bodyParam cooperative_id int ID de la cooperativa. Example: 1
     * @bodyParam status string Estado del usuario (solo administradores). Example: active
     * @bodyParam is_verified boolean Si el usuario está verificado (solo administradores). Example: true
     * @bodyParam roles array Roles del usuario (solo administradores). Example: ["member", "expert"]
     * @bodyParam profile_data array Datos adicionales del perfil. Example: {"bio": "Experto en energía solar", "location": "Madrid", "specialties": ["solar", "wind"]}
     *
     * @response 200 {
     *   "data": {
     *     "id": 1,
     *     "name": "Juan Carlos Pérez",
     *     "phone": "+34 600 123 456",
     *     "profile_data": {"bio": "Experto en energía solar", "location": "Madrid"},
     *     "updated_at": "2024-01-15T11:00:00.000000Z"
     *   },
     *   "message": "Usuario actualizado exitosamente"
     * }
     *
     * @response 403 {
     *   "message": "No tienes permisos para editar este usuario"
     * }
     *
     * @response 422 {
     *   "message": "Los datos proporcionados no son válidos"
     * }
     *
     * @authenticated
     */
    public function update(Request $request, User $user): JsonResponse
    {
        // Verificar permisos: usuario puede editar su propio perfil o administradores pueden editar cualquier perfil
        if (Auth::id() !== $user->id && !Auth::user()->hasRole(['admin', 'super_admin'])) {
            return response()->json([
                'message' => 'No tienes permisos para editar este usuario'
            ], 403);
        }

        $isAdmin = Auth::user()->hasRole(['admin', 'super_admin']);
        $isOwnProfile = Auth::id() === $user->id;

        $rules = [
            'name' => 'string|max:255',
            'phone' => 'nullable|string|max:20',
            'cooperative_id' => 'nullable|exists:cooperatives,id',
            'profile_data' => 'nullable|array'
        ];

        // Solo administradores pueden cambiar estos campos
        if ($isAdmin) {
            $rules['status'] = 'nullable|in:active,inactive,suspended,banned';
            $rules['is_verified'] = 'boolean';
            $rules['roles'] = 'nullable|array';
            $rules['roles.*'] = 'string|exists:roles,name';
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Los datos proporcionados no son válidos',
                'errors' => $validator->errors()
            ], 422);
        }

        $updateData = $request->only(['name', 'phone', 'cooperative_id', 'profile_data']);

        // Solo administradores pueden cambiar estos campos
        if ($isAdmin) {
            $updateData = array_merge($updateData, $request->only(['status', 'is_verified']));
        }

        $user->update($updateData);

        // Actualizar roles si es administrador
        if ($isAdmin && $request->filled('roles')) {
            $user->syncRoles($request->roles);
        }

        return response()->json([
            'data' => new UserResource($user->load(['cooperative', 'roles', 'permissions'])),
            'message' => 'Usuario actualizado exitosamente'
        ]);
    }

    /**
     * Remove the specified user
     *
     * Elimina un usuario. Solo administradores pueden eliminar usuarios.
     *
     * @urlParam user int required ID del usuario. Example: 1
     *
     * @response 200 {
     *   "message": "Usuario eliminado exitosamente"
     * }
     *
     * @response 403 {
     *   "message": "No tienes permisos para eliminar usuarios"
     * }
     *
     * @response 422 {
     *   "message": "No se puede eliminar el usuario"
     * }
     *
     * @authenticated
     */
    public function destroy(User $user): JsonResponse
    {
        // Verificar permisos de administrador
        if (!Auth::user()->hasRole(['admin', 'super_admin'])) {
            return response()->json([
                'message' => 'No tienes permisos para eliminar usuarios'
            ], 403);
        }

        // No permitir eliminar el propio usuario
        if (Auth::id() === $user->id) {
            return response()->json([
                'message' => 'No puedes eliminar tu propio usuario'
            ], 422);
        }

        // No permitir eliminar super administradores
        if ($user->hasRole('super_admin')) {
            return response()->json([
                'message' => 'No se pueden eliminar super administradores'
            ], 422);
        }

        $user->delete();

        return response()->json([
            'message' => 'Usuario eliminado exitosamente'
        ]);
    }

    /**
     * Get current user profile
     *
     * Obtiene el perfil del usuario autenticado.
     *
     * @response 200 {
     *   "data": {
     *     "id": 1,
     *     "name": "Juan Pérez",
     *     "email": "juan@example.com",
     *     "status": "active",
     *     "is_verified": true,
     *     "cooperative": {
     *       "id": 1,
     *       "name": "Cooperativa Solar Madrid"
     *     },
     *     "roles": ["member", "expert"],
     *     "permissions": ["read_posts", "create_posts"]
     *   }
     * }
     *
     * @authenticated
     */
    public function profile(): JsonResponse
    {
        $user = Auth::user()->load(['cooperative', 'roles', 'permissions']);

        return response()->json([
            'data' => new UserResource($user)
        ]);
    }

    /**
     * Update current user profile
     *
     * Actualiza el perfil del usuario autenticado.
     *
     * @bodyParam name string Nombre completo del usuario. Example: Juan Carlos Pérez
     * @bodyParam phone string Teléfono del usuario. Example: +34 600 123 456
     * @bodyParam profile_data array Datos adicionales del perfil. Example: {"bio": "Experto en energía solar", "location": "Madrid"}
     *
     * @response 200 {
     *   "data": {
     *     "id": 1,
     *     "name": "Juan Carlos Pérez",
     *     "phone": "+34 600 123 456",
     *     "profile_data": {"bio": "Experto en energía solar", "location": "Madrid"},
     *     "updated_at": "2024-01-15T11:00:00.000000Z"
     *   },
     *   "message": "Perfil actualizado exitosamente"
     * }
     *
     * @response 422 {
     *   "message": "Los datos proporcionados no son válidos"
     * }
     *
     * @authenticated
     */
    public function updateProfile(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'string|max:255',
            'phone' => 'nullable|string|max:20',
            'profile_data' => 'nullable|array'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Los datos proporcionados no son válidos',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = Auth::user();
        $user->update($request->only(['name', 'phone', 'profile_data']));

        return response()->json([
            'data' => new UserResource($user->load(['cooperative', 'roles', 'permissions'])),
            'message' => 'Perfil actualizado exitosamente'
        ]);
    }

    /**
     * Change user password
     *
     * Cambia la contraseña del usuario autenticado.
     *
     * @bodyParam current_password string required Contraseña actual. Example: oldpassword123
     * @bodyParam new_password string required Nueva contraseña (mín 8 caracteres). Example: newpassword123
     * @bodyParam new_password_confirmation string required Confirmación de nueva contraseña. Example: newpassword123
     *
     * @response 200 {
     *   "message": "Contraseña cambiada exitosamente"
     * }
     *
     * @response 422 {
     *   "message": "Los datos proporcionados no son válidos",
     *   "errors": {
     *     "current_password": ["La contraseña actual es incorrecta"],
     *     "new_password": ["La nueva contraseña debe tener al menos 8 caracteres"]
     *   }
     * }
     *
     * @authenticated
     */
    public function changePassword(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:8|confirmed'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Los datos proporcionados no son válidos',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = Auth::user();

        // Verificar contraseña actual
        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'message' => 'La contraseña actual es incorrecta'
            ], 422);
        }

        // Cambiar contraseña
        $user->update([
            'password' => Hash::make($request->new_password)
        ]);

        return response()->json([
            'message' => 'Contraseña cambiada exitosamente'
        ]);
    }
}
