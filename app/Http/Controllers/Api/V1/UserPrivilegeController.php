<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\UserPrivilegeResource;
use App\Models\UserPrivilege;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

/**
 * @group User Privileges
 * 
 * API endpoints for managing user privileges in the social system.
 */
class UserPrivilegeController extends Controller
{
    /**
     * Display user privileges
     */
    public function index(Request $request)
    {
        $query = UserPrivilege::with(['user', 'grantor'])
            ->when($request->user_id, fn($q, $userId) => $q->where('user_id', $userId))
            ->when($request->privilege_type, fn($q, $type) => $q->where('privilege_type', $type))
            ->when($request->scope, fn($q, $scope) => $q->where('scope', $scope))
            ->when($request->level, fn($q, $level) => $q->where('level', $level))
            ->when($request->has('is_active'), fn($q) => $q->where('is_active', $request->boolean('is_active')))
            ->orderBy('granted_at', 'desc');

        $perPage = min($request->get('per_page', 15), 100);
        $privileges = $query->paginate($perPage);

        return UserPrivilegeResource::collection($privileges);
    }

    /**
     * Get user's privileges
     */
    public function myPrivileges(Request $request)
    {
        $user = Auth::guard('sanctum')->user();
        
        if (!$user) {
            return response()->json(['message' => 'Usuario no autenticado'], 401);
        }

        $query = $user->privileges()
            ->with(['grantor'])
            ->when($request->privilege_type, fn($q, $type) => $q->where('privilege_type', $type))
            ->when($request->scope, fn($q, $scope) => $q->where('scope', $scope))
            ->when($request->has('is_active'), fn($q) => $q->where('is_active', $request->boolean('is_active')))
            ->orderBy('granted_at', 'desc');

        $privileges = $query->get();

        // Calcular estadísticas
        $stats = [
            'total_privileges' => $privileges->count(),
            'active_privileges' => $privileges->where('is_active', true)->count(),
            'by_type' => $privileges->groupBy('privilege_type')->map->count(),
            'by_scope' => $privileges->groupBy('scope')->map->count(),
            'by_level' => $privileges->groupBy('level')->map->count(),
            'highest_level' => $privileges->max('level') ?? 0,
        ];

        return response()->json([
            'data' => UserPrivilegeResource::collection($privileges),
            'stats' => $stats,
        ]);
    }

    /**
     * Grant a privilege to user
     */
    public function store(Request $request)
    {
        $user = Auth::guard('sanctum')->user();
        
        if (!$user || !$user->hasRole('admin')) {
            return response()->json(['message' => 'No tienes permisos para otorgar privilegios'], 403);
        }

        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'privilege_type' => 'required|in:posting,voting,moderation,verification,administration,content_creation,expert_answers,project_approval',
            'scope' => 'required|in:global,topic,cooperative,project,region',
            'scope_id' => 'nullable|integer|min:1',
            'level' => 'required|integer|min:1|max:5',
            'permissions' => 'nullable|array',
            'limits' => 'nullable|array',
            'reputation_required' => 'nullable|integer|min:0',
            'expires_at' => 'nullable|date|after:today',
            'reason' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Los datos proporcionados no son válidos',
                'errors' => $validator->errors()
            ], 422);
        }

        // Verificar si ya existe un privilegio similar
        $existing = UserPrivilege::where('user_id', $request->user_id)
            ->where('privilege_type', $request->privilege_type)
            ->where('scope', $request->scope)
            ->where('scope_id', $request->scope_id ?? null)
            ->where('is_active', true)
            ->first();

        if ($existing) {
            return response()->json([
                'message' => 'El usuario ya tiene este privilegio activo'
            ], 409);
        }

        $privilege = UserPrivilege::create(array_merge($validator->validated(), [
            'granted_at' => now(),
            'granted_by' => $user->id,
            'is_active' => true,
        ]));

        $privilege->load(['user', 'grantor']);

        return response()->json([
            'data' => new UserPrivilegeResource($privilege),
            'message' => 'Privilegio otorgado exitosamente'
        ], 201);
    }

    /**
     * Display the specified privilege
     */
    public function show(UserPrivilege $userPrivilege)
    {
        $user = Auth::guard('sanctum')->user();
        
        // Solo el usuario propietario, el otorgante o un admin pueden ver los detalles
        if (!$user || (
            $user->id !== $userPrivilege->user_id && 
            $user->id !== $userPrivilege->granted_by && 
            !$user->hasRole('admin')
        )) {
            return response()->json(['message' => 'No tienes permisos para ver este privilegio'], 403);
        }

        $userPrivilege->load(['user', 'grantor']);
        
        return new UserPrivilegeResource($userPrivilege);
    }

    /**
     * Update the specified privilege
     */
    public function update(Request $request, UserPrivilege $userPrivilege)
    {
        $user = Auth::guard('sanctum')->user();
        
        if (!$user || !$user->hasRole('admin')) {
            return response()->json(['message' => 'No tienes permisos para actualizar privilegios'], 403);
        }

        $validator = Validator::make($request->all(), [
            'level' => 'sometimes|integer|min:1|max:5',
            'permissions' => 'sometimes|array',
            'limits' => 'sometimes|array',
            'reputation_required' => 'sometimes|integer|min:0',
            'expires_at' => 'sometimes|nullable|date|after:today',
            'reason' => 'sometimes|string|max:1000',
            'is_active' => 'sometimes|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Los datos proporcionados no son válidos',
                'errors' => $validator->errors()
            ], 422);
        }

        $userPrivilege->update($validator->validated());
        $userPrivilege->load(['user', 'grantor']);

        return response()->json([
            'data' => new UserPrivilegeResource($userPrivilege),
            'message' => 'Privilegio actualizado exitosamente'
        ]);
    }

    /**
     * Revoke a privilege
     */
    public function revoke(UserPrivilege $userPrivilege)
    {
        $user = Auth::guard('sanctum')->user();
        
        if (!$user || !$user->hasRole('admin')) {
            return response()->json(['message' => 'No tienes permisos para revocar privilegios'], 403);
        }

        $userPrivilege->update(['is_active' => false]);
        $userPrivilege->load(['user', 'grantor']);

        return response()->json([
            'data' => new UserPrivilegeResource($userPrivilege),
            'message' => 'Privilegio revocado exitosamente'
        ]);
    }

    /**
     * Activate a privilege
     */
    public function activate(UserPrivilege $userPrivilege)
    {
        $user = Auth::guard('sanctum')->user();
        
        if (!$user || !$user->hasRole('admin')) {
            return response()->json(['message' => 'No tienes permisos para activar privilegios'], 403);
        }

        $userPrivilege->update(['is_active' => true]);
        $userPrivilege->load(['user', 'grantor']);

        return response()->json([
            'data' => new UserPrivilegeResource($userPrivilege),
            'message' => 'Privilegio activado exitosamente'
        ]);
    }

    /**
     * Get privilege statistics
     */
    public function stats(Request $request)
    {
        $query = UserPrivilege::query();

        if ($request->period && $request->period !== 'all') {
            $date = match($request->period) {
                'week' => now()->subWeek(),
                'month' => now()->subMonth(),
                'year' => now()->subYear(),
                default => now()->subMonth()
            };
            $query->where('granted_at', '>=', $date);
        }

        $privileges = $query->get();

        $stats = [
            'total_privileges' => $privileges->count(),
            'active_privileges' => $privileges->where('is_active', true)->count(),
            'by_type' => $privileges->groupBy('privilege_type')->map->count(),
            'by_scope' => $privileges->groupBy('scope')->map->count(),
            'by_level' => $privileges->groupBy('level')->map->count(),
            'users_with_privileges' => $privileges->pluck('user_id')->unique()->count(),
            'expired_privileges' => $privileges->filter(function($p) {
                return $p->expires_at && $p->expires_at->isPast();
            })->count(),
        ];

        return response()->json(['data' => $stats]);
    }

    /**
     * Check if user has specific privilege
     */
    public function checkPrivilege(Request $request)
    {
        $user = Auth::guard('sanctum')->user();
        
        if (!$user) {
            return response()->json(['message' => 'Usuario no autenticado'], 401);
        }

        $validator = Validator::make($request->all(), [
            'privilege_type' => 'required|string',
            'scope' => 'sometimes|string',
            'scope_id' => 'sometimes|integer',
            'min_level' => 'sometimes|integer|min:1|max:5',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Los datos proporcionados no son válidos',
                'errors' => $validator->errors()
            ], 422);
        }

        $query = $user->privileges()
            ->where('privilege_type', $request->privilege_type)
            ->where('is_active', true)
            ->where(function($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
            });

        if ($request->scope) {
            $query->where('scope', $request->scope);
        }

        if ($request->scope_id) {
            $query->where('scope_id', $request->scope_id);
        }

        if ($request->min_level) {
            $query->where('level', '>=', $request->min_level);
        }

        $hasPrivilege = $query->exists();
        $privilege = $query->first();

        return response()->json([
            'has_privilege' => $hasPrivilege,
            'privilege' => $privilege ? new UserPrivilegeResource($privilege) : null,
        ]);
    }

    /**
     * Remove the specified privilege
     */
    public function destroy(UserPrivilege $userPrivilege)
    {
        $user = Auth::guard('sanctum')->user();
        
        if (!$user || !$user->hasRole('admin')) {
            return response()->json(['message' => 'No tienes permisos para eliminar privilegios'], 403);
        }

        $userPrivilege->delete();

        return response()->json(['message' => 'Privilegio eliminado exitosamente']);
    }
}