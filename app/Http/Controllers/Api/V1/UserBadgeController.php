<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\UserBadgeResource;
use App\Models\UserBadge;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

/**
 * @group User Badges
 * 
 * API endpoints for managing user badges in the social system.
 * Badges are achievements awarded to users for various accomplishments.
 */
/**
 * @OA\Tag(
 *     name="Insignias de Usuario",
 *     description="APIs para la gestión de Insignias de Usuario"
 * )
 */
class UserBadgeController extends Controller
{
    /**
     * Display user badges
     * 
     * Get a paginated list of user badges with filtering options.
     */
    public function index(Request $request)
    {
        $query = UserBadge::with(['user'])
            ->when($request->user_id, fn($q, $userId) => $q->where('user_id', $userId))
            ->when($request->badge_type, fn($q, $type) => $q->where('badge_type', $type))
            ->when($request->category, fn($q, $category) => $q->where('category', $category))
            ->when($request->has('is_public'), fn($q) => $q->where('is_public', $request->boolean('is_public')))
            ->when($request->has('is_featured'), fn($q) => $q->where('is_featured', $request->boolean('is_featured')))
            ->orderBy('earned_at', 'desc');

        $perPage = min($request->get('per_page', 15), 100);
        $badges = $query->paginate($perPage);

        return UserBadgeResource::collection($badges);
    }

    /**
     * Get user's badges
     * 
     * Get all badges for the authenticated user.
     */
    public function myBadges(Request $request)
    {
        $user = Auth::guard('sanctum')->user();
        
        if (!$user) {
            return response()->json(['message' => 'Usuario no autenticado'], 401);
        }

        $query = $user->badges()
            ->when($request->category, fn($q, $category) => $q->where('category', $category))
            ->when($request->badge_type, fn($q, $type) => $q->where('badge_type', $type))
            ->when($request->has('is_featured'), fn($q) => $q->where('is_featured', $request->boolean('is_featured')))
            ->orderBy('earned_at', 'desc');

        $badges = $query->get();

        // Calcular estadísticas
        $stats = [
            'total_badges' => $badges->count(),
            'total_points' => $badges->sum('points_awarded'),
            'by_type' => $badges->groupBy('badge_type')->map->count(),
            'by_category' => $badges->groupBy('category')->map->count(),
        ];

        return response()->json([
            'data' => UserBadgeResource::collection($badges),
            'stats' => $stats,
        ]);
    }

    /**
     * Display the specified badge
     */
    public function show(UserBadge $userBadge)
    {
        // Solo mostrar badges públicos o del usuario autenticado
        $user = Auth::guard('sanctum')->user();
        
        if (!$userBadge->is_public && (!$user || $user->id !== $userBadge->user_id)) {
            return response()->json(['message' => 'Insignia no encontrada'], 404);
        }

        $userBadge->load(['user']);
        
        return new UserBadgeResource($userBadge);
    }

    /**
     * Award a badge to a user
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:1000',
            'badge_type' => 'required|in:bronze,silver,gold,platinum,diamond',
            'category' => 'required|string|max:100',
            'icon' => 'nullable|url|max:255',
            'color' => 'nullable|string|max:7',
            'points_awarded' => 'nullable|integer|min:0',
            'criteria' => 'nullable|array',
            'metadata' => 'nullable|array',
            'expires_at' => 'nullable|date',
            'is_public' => 'boolean',
            'is_featured' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Los datos proporcionados no son válidos',
                'errors' => $validator->errors()
            ], 422);
        }

        $badge = UserBadge::create(array_merge($validator->validated(), [
            'earned_at' => now(),
            'awarded_by' => Auth::guard('sanctum')->id(),
        ]));

        $badge->load(['user']);

        return response()->json([
            'data' => new UserBadgeResource($badge),
            'message' => 'Insignia otorgada exitosamente'
        ], 201);
    }

    /**
     * Get badge statistics
     */
    public function stats(Request $request)
    {
        $period = $request->get('period', 'all');
        $userId = $request->get('user_id');

        $query = UserBadge::query();

        // Filtrar por período
        if ($period !== 'all') {
            $date = match($period) {
                'week' => now()->subWeek(),
                'month' => now()->subMonth(),
                'year' => now()->subYear(),
                default => now()->subMonth()
            };
            $query->where('earned_at', '>=', $date);
        }

        // Filtrar por usuario específico
        if ($userId) {
            $query->where('user_id', $userId);
        }

        $badges = $query->get();

        $stats = [
            'total_badges' => $badges->count(),
            'total_users_with_badges' => $badges->pluck('user_id')->unique()->count(),
            'by_type' => $badges->groupBy('badge_type')->map->count(),
            'by_category' => $badges->groupBy('category')->map->count(),
        ];

        return response()->json(['data' => $stats]);
    }

    /**
     * Remove the specified badge
     */
    public function destroy(UserBadge $userBadge)
    {
        $user = Auth::guard('sanctum')->user();
        
        // Solo el usuario propietario o un admin puede eliminar la insignia
        if (!$user || ($user->id !== $userBadge->user_id && !$user->hasRole('admin'))) {
            return response()->json(['message' => 'No tienes permisos para eliminar esta insignia'], 403);
        }

        $userBadge->delete();

        return response()->json(['message' => 'Insignia eliminada exitosamente']);
    }
}