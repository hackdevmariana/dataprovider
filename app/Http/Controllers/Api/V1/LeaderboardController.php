<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\LeaderboardResource;
use App\Models\Leaderboard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

/**
 * @group Leaderboards
 * 
 * API endpoints for managing leaderboards and rankings.
 */
/**
 * @OA\Tag(
 *     name="Tablas de Clasificación",
 *     description="APIs para la gestión de Tablas de Clasificación"
 * )
 */
class LeaderboardController extends Controller
{
    /**
     * Display leaderboards
     */
    public function index(Request $request)
    {
        $query = Leaderboard::query()
            ->when($request->type, fn($q, $type) => $q->where('type', $type))
            ->when($request->period, fn($q, $period) => $q->where('period', $period))
            ->when($request->scope, fn($q, $scope) => $q->where('scope', $scope))
            ->when($request->has('is_active'), fn($q) => $q->where('is_active', $request->boolean('is_active')))
            ->when($request->has('is_public'), fn($q) => $q->where('is_public', $request->boolean('is_public')))
            ->orderBy('is_featured', 'desc')
            ->orderBy('created_at', 'desc');

        $perPage = min($request->get('per_page', 15), 100);
        $leaderboards = $query->paginate($perPage);

        return LeaderboardResource::collection($leaderboards);
    }

    /**
     * Get active leaderboards
     */
    public function active(Request $request)
    {
        $query = Leaderboard::where('is_active', true)
            ->where('is_public', true)
            ->when($request->type, fn($q, $type) => $q->where('type', $type))
            ->when($request->scope, fn($q, $scope) => $q->where('scope', $scope))
            ->orderBy('is_featured', 'desc')
            ->orderBy('name');

        $leaderboards = $query->get();

        return response()->json([
            'data' => LeaderboardResource::collection($leaderboards),
            'meta' => [
                'total_active' => $leaderboards->count(),
                'by_type' => $leaderboards->groupBy('type')->map->count(),
                'by_scope' => $leaderboards->groupBy('scope')->map->count(),
            ]
        ]);
    }

    /**
     * Create a new leaderboard
     */
    public function store(Request $request)
    {
        $user = Auth::guard('sanctum')->user();
        
        if (!$user || !$user->hasRole('admin')) {
            return response()->json(['message' => 'No tienes permisos para crear leaderboards'], 403);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:leaderboards,name',
            'type' => 'required|in:energy_savings,reputation,contributions,projects,community_engagement',
            'period' => 'required|in:daily,weekly,monthly,yearly,all_time',
            'scope' => 'required|in:global,cooperative,regional,topic',
            'scope_id' => 'nullable|integer|min:1',
            'max_positions' => 'required|integer|min:10|max:1000',
            'criteria' => 'required|array',
            'rules' => 'nullable|array',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after:start_date',
            'is_active' => 'boolean',
            'is_public' => 'boolean',
            'metadata' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Los datos proporcionados no son válidos',
                'errors' => $validator->errors()
            ], 422);
        }

        $leaderboard = Leaderboard::create($validator->validated());

        return response()->json([
            'data' => new LeaderboardResource($leaderboard),
            'message' => 'Leaderboard creado exitosamente'
        ], 201);
    }

    /**
     * Display the specified leaderboard
     */
    public function show(Leaderboard $leaderboard)
    {
        if (!$leaderboard->is_public) {
            $user = Auth::guard('sanctum')->user();
            if (!$user || !$user->hasRole('admin')) {
                return response()->json(['message' => 'No tienes permisos para ver este leaderboard'], 403);
            }
        }

        return new LeaderboardResource($leaderboard);
    }

    /**
     * Get leaderboard rankings
     */
    public function rankings(Leaderboard $leaderboard, Request $request)
    {
        if (!$leaderboard->is_public) {
            $user = Auth::guard('sanctum')->user();
            if (!$user || !$user->hasRole('admin')) {
                return response()->json(['message' => 'No tienes permisos para ver este leaderboard'], 403);
            }
        }

        $limit = min($request->get('limit', $leaderboard->max_positions), $leaderboard->max_positions);
        
        // Aquí iría la lógica para calcular rankings reales
        $rankings = $this->calculateRankings($leaderboard, $limit);

        return response()->json([
            'data' => $rankings,
            'meta' => [
                'leaderboard' => new LeaderboardResource($leaderboard),
                'total_positions' => count($rankings),
                'last_updated' => $leaderboard->last_calculated_at,
                'next_update' => $this->getNextUpdateTime($leaderboard),
            ]
        ]);
    }

    /**
     * Get user position in leaderboard
     */
    public function userPosition(Leaderboard $leaderboard, Request $request)
    {
        $user = Auth::guard('sanctum')->user();
        
        if (!$user) {
            return response()->json(['message' => 'Usuario no autenticado'], 401);
        }

        if (!$leaderboard->is_public && !$user->hasRole('admin')) {
            return response()->json(['message' => 'No tienes permisos para ver este leaderboard'], 403);
        }

        // Calcular posición del usuario
        $userPosition = $this->getUserPosition($leaderboard, $user);

        return response()->json([
            'data' => $userPosition,
            'meta' => [
                'leaderboard' => [
                    'id' => $leaderboard->id,
                    'name' => $leaderboard->name,
                    'type' => $leaderboard->type,
                ],
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                ]
            ]
        ]);
    }

    /**
     * Update the specified leaderboard
     */
    public function update(Request $request, Leaderboard $leaderboard)
    {
        $user = Auth::guard('sanctum')->user();
        
        if (!$user || !$user->hasRole('admin')) {
            return response()->json(['message' => 'No tienes permisos para actualizar leaderboards'], 403);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255|unique:leaderboards,name,' . $leaderboard->id,
            'max_positions' => 'sometimes|integer|min:10|max:1000',
            'criteria' => 'sometimes|array',
            'rules' => 'sometimes|array',
            'end_date' => 'sometimes|nullable|date|after:start_date',
            'is_active' => 'sometimes|boolean',
            'is_public' => 'sometimes|boolean',
            'metadata' => 'sometimes|nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Los datos proporcionados no son válidos',
                'errors' => $validator->errors()
            ], 422);
        }

        $leaderboard->update($validator->validated());

        return response()->json([
            'data' => new LeaderboardResource($leaderboard),
            'message' => 'Leaderboard actualizado exitosamente'
        ]);
    }

    /**
     * Recalculate leaderboard rankings
     */
    public function recalculate(Leaderboard $leaderboard)
    {
        $user = Auth::guard('sanctum')->user();
        
        if (!$user || !$user->hasRole('admin')) {
            return response()->json(['message' => 'No tienes permisos para recalcular leaderboards'], 403);
        }

        if (!$leaderboard->is_active) {
            return response()->json(['message' => 'No se puede recalcular un leaderboard inactivo'], 400);
        }

        // Aquí iría la lógica de recálculo
        $this->performRecalculation($leaderboard);

        $leaderboard->update(['last_calculated_at' => now()]);

        return response()->json([
            'data' => new LeaderboardResource($leaderboard),
            'message' => 'Leaderboard recalculado exitosamente'
        ]);
    }

    /**
     * Get leaderboard statistics
     */
    public function stats(Request $request)
    {
        $query = Leaderboard::query();

        if ($request->period && $request->period !== 'all') {
            $date = match($request->period) {
                'week' => now()->subWeek(),
                'month' => now()->subMonth(),
                'year' => now()->subYear(),
                default => now()->subMonth()
            };
            $query->where('created_at', '>=', $date);
        }

        $leaderboards = $query->get();

        $stats = [
            'total_leaderboards' => $leaderboards->count(),
            'active_leaderboards' => $leaderboards->where('is_active', true)->count(),
            'public_leaderboards' => $leaderboards->where('is_public', true)->count(),
            'by_type' => $leaderboards->groupBy('type')->map->count(),
            'by_period' => $leaderboards->groupBy('period')->map->count(),
            'by_scope' => $leaderboards->groupBy('scope')->map->count(),
            'featured_leaderboards' => $leaderboards->where('is_featured', true)->count(),
            'recently_updated' => $leaderboards->where('last_calculated_at', '>=', now()->subDay())->count(),
        ];

        return response()->json(['data' => $stats]);
    }

    /**
     * Remove the specified leaderboard
     */
    public function destroy(Leaderboard $leaderboard)
    {
        $user = Auth::guard('sanctum')->user();
        
        if (!$user || !$user->hasRole('admin')) {
            return response()->json(['message' => 'No tienes permisos para eliminar leaderboards'], 403);
        }

        $leaderboard->delete();

        return response()->json(['message' => 'Leaderboard eliminado exitosamente']);
    }

    /**
     * Calculate rankings for leaderboard
     */
    private function calculateRankings(Leaderboard $leaderboard, int $limit): array
    {
        // Esta sería la lógica real para calcular rankings
        // Por ahora devolvemos datos de ejemplo
        $rankings = [];
        
        for ($i = 1; $i <= min($limit, 20); $i++) {
            $rankings[] = [
                'position' => $i,
                'user' => [
                    'id' => $i,
                    'name' => "Usuario {$i}",
                ],
                'value' => 1000 - ($i * 10),
                'change' => rand(-5, 5),
                'trend' => ['up', 'down', 'stable'][rand(0, 2)],
            ];
        }

        return $rankings;
    }

    /**
     * Get user position in leaderboard
     */
    private function getUserPosition(Leaderboard $leaderboard, $user): array
    {
        // Lógica para calcular la posición específica del usuario
        return [
            'position' => rand(1, 100),
            'value' => rand(500, 1000),
            'percentile' => rand(50, 95),
            'change_from_last' => rand(-10, 10),
            'trend' => ['up', 'down', 'stable'][rand(0, 2)],
            'distance_to_next' => rand(5, 50),
            'distance_from_previous' => rand(5, 50),
        ];
    }

    /**
     * Get next update time for leaderboard
     */
    private function getNextUpdateTime(Leaderboard $leaderboard): ?string
    {
        if (!$leaderboard->is_active) {
            return null;
        }

        $nextUpdate = match($leaderboard->period) {
            'daily' => now()->addDay(),
            'weekly' => now()->addWeek(),
            'monthly' => now()->addMonth(),
            'yearly' => now()->addYear(),
            default => null,
        };

        return $nextUpdate?->toISOString();
    }

    /**
     * Perform leaderboard recalculation
     */
    private function performRecalculation(Leaderboard $leaderboard): void
    {
        // Aquí iría la lógica real de recálculo
        // Por ejemplo, consultar datos de usuarios, aplicar criterios, etc.
        
        // Simular tiempo de procesamiento
        sleep(1);
    }
}