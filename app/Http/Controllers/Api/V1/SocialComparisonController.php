<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\SocialComparisonResource;
use App\Models\SocialComparison;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

/**
 * @group Social Comparisons
 * 
 * API endpoints for managing social comparisons and user performance metrics.
 */
class SocialComparisonController extends Controller
{
    /**
     * Display social comparisons
     */
    public function index(Request $request)
    {
        $query = SocialComparison::with(['user'])
            ->when($request->user_id, fn($q, $userId) => $q->where('user_id', $userId))
            ->when($request->comparison_type, fn($q, $type) => $q->where('comparison_type', $type))
            ->when($request->period, fn($q, $period) => $q->where('period', $period))
            ->when($request->scope, fn($q, $scope) => $q->where('scope', $scope))
            ->when($request->comparison_group, fn($q, $group) => $q->where('comparison_group', $group))
            ->orderBy('comparison_date', 'desc');

        $perPage = min($request->get('per_page', 15), 100);
        $comparisons = $query->paginate($perPage);

        return SocialComparisonResource::collection($comparisons);
    }

    /**
     * Get user's comparisons
     */
    public function myComparisons(Request $request)
    {
        $user = Auth::guard('sanctum')->user();
        
        if (!$user) {
            return response()->json(['message' => 'Usuario no autenticado'], 401);
        }

        $query = $user->socialComparisons()
            ->when($request->comparison_type, fn($q, $type) => $q->where('comparison_type', $type))
            ->when($request->period, fn($q, $period) => $q->where('period', $period))
            ->when($request->scope, fn($q, $scope) => $q->where('scope', $scope))
            ->orderBy('comparison_date', 'desc');

        $comparisons = $query->get();

        // Calcular estadísticas del usuario
        $stats = [
            'total_comparisons' => $comparisons->count(),
            'by_type' => $comparisons->groupBy('comparison_type')->map->count(),
            'by_period' => $comparisons->groupBy('period')->map->count(),
            'latest_comparison' => $comparisons->first(),
            'best_performance' => $this->getBestPerformance($comparisons),
            'performance_trend' => $this->getPerformanceTrend($comparisons),
        ];

        return response()->json([
            'data' => SocialComparisonResource::collection($comparisons),
            'stats' => $stats,
        ]);
    }

    /**
     * Create a new social comparison
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'comparison_type' => 'required|in:energy_savings,carbon_reduction,community_participation,project_contributions,knowledge_sharing',
            'period' => 'required|in:daily,weekly,monthly,yearly,all_time',
            'scope' => 'required|in:personal,cooperative,regional,national,global',
            'user_value' => 'required|numeric|min:0',
            'unit' => 'required|string|max:20',
            'average_value' => 'nullable|numeric|min:0',
            'median_value' => 'nullable|numeric|min:0',
            'best_value' => 'nullable|numeric|min:0',
            'user_rank' => 'nullable|integer|min:1',
            'total_participants' => 'required|integer|min:1',
            'percentile' => 'nullable|numeric|min:0|max:100',
            'comparison_group' => 'required|string|max:100',
            'group_id' => 'nullable|integer',
            'breakdown' => 'nullable|array',
            'comparison_date' => 'required|date',
            'is_public' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Los datos proporcionados no son válidos',
                'errors' => $validator->errors()
            ], 422);
        }

        $comparison = SocialComparison::create($validator->validated());
        $comparison->load(['user']);

        return response()->json([
            'data' => new SocialComparisonResource($comparison),
            'message' => 'Comparación social creada exitosamente'
        ], 201);
    }

    /**
     * Display the specified comparison
     */
    public function show(SocialComparison $socialComparison)
    {
        $user = Auth::guard('sanctum')->user();
        
        // Verificar permisos de visibilidad
        if (!$socialComparison->is_public && (!$user || $user->id !== $socialComparison->user_id)) {
            return response()->json(['message' => 'No tienes permisos para ver esta comparación'], 403);
        }

        $socialComparison->load(['user']);
        
        return new SocialComparisonResource($socialComparison);
    }

    /**
     * Update the specified comparison
     */
    public function update(Request $request, SocialComparison $socialComparison)
    {
        $user = Auth::guard('sanctum')->user();
        
        if (!$user || ($user->id !== $socialComparison->user_id && !$user->hasRole('admin'))) {
            return response()->json(['message' => 'No tienes permisos para actualizar esta comparación'], 403);
        }

        $validator = Validator::make($request->all(), [
            'user_value' => 'sometimes|numeric|min:0',
            'average_value' => 'sometimes|nullable|numeric|min:0',
            'median_value' => 'sometimes|nullable|numeric|min:0',
            'best_value' => 'sometimes|nullable|numeric|min:0',
            'user_rank' => 'sometimes|nullable|integer|min:1',
            'total_participants' => 'sometimes|integer|min:1',
            'percentile' => 'sometimes|nullable|numeric|min:0|max:100',
            'breakdown' => 'sometimes|array',
            'is_public' => 'sometimes|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Los datos proporcionados no son válidos',
                'errors' => $validator->errors()
            ], 422);
        }

        $socialComparison->update($validator->validated());
        $socialComparison->load(['user']);

        return response()->json([
            'data' => new SocialComparisonResource($socialComparison),
            'message' => 'Comparación actualizada exitosamente'
        ]);
    }

    /**
     * Get leaderboard for specific comparison
     */
    public function leaderboard(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'comparison_type' => 'required|in:energy_savings,carbon_reduction,community_participation,project_contributions,knowledge_sharing',
            'period' => 'required|in:daily,weekly,monthly,yearly,all_time',
            'scope' => 'required|in:personal,cooperative,regional,national,global',
            'comparison_group' => 'required|string',
            'group_id' => 'nullable|integer',
            'limit' => 'nullable|integer|min:1|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Los datos proporcionados no son válidos',
                'errors' => $validator->errors()
            ], 422);
        }

        $query = SocialComparison::with(['user'])
            ->where('comparison_type', $request->comparison_type)
            ->where('period', $request->period)
            ->where('scope', $request->scope)
            ->where('comparison_group', $request->comparison_group)
            ->when($request->group_id, fn($q, $groupId) => $q->where('group_id', $groupId))
            ->where('is_public', true)
            ->orderBy('user_rank')
            ->limit($request->get('limit', 50));

        $leaderboard = $query->get();

        return response()->json([
            'data' => SocialComparisonResource::collection($leaderboard),
            'meta' => [
                'comparison_type' => $request->comparison_type,
                'period' => $request->period,
                'scope' => $request->scope,
                'total_participants' => $leaderboard->first()?->total_participants ?? 0,
                'generated_at' => now(),
            ]
        ]);
    }

    /**
     * Get comparison statistics
     */
    public function stats(Request $request)
    {
        $query = SocialComparison::query();

        if ($request->comparison_type) {
            $query->where('comparison_type', $request->comparison_type);
        }

        if ($request->period && $request->period !== 'all') {
            $date = match($request->period) {
                'week' => now()->subWeek(),
                'month' => now()->subMonth(),
                'year' => now()->subYear(),
                default => now()->subMonth()
            };
            $query->where('comparison_date', '>=', $date);
        }

        $comparisons = $query->get();

        $stats = [
            'total_comparisons' => $comparisons->count(),
            'unique_users' => $comparisons->pluck('user_id')->unique()->count(),
            'by_type' => $comparisons->groupBy('comparison_type')->map->count(),
            'by_period' => $comparisons->groupBy('period')->map->count(),
            'by_scope' => $comparisons->groupBy('scope')->map->count(),
            'average_participation' => $comparisons->avg('total_participants'),
            'top_performers' => $this->getTopPerformers($comparisons),
            'performance_distribution' => $this->getPerformanceDistribution($comparisons),
        ];

        return response()->json(['data' => $stats]);
    }

    /**
     * Generate comparison for user
     */
    public function generateComparison(Request $request)
    {
        $user = Auth::guard('sanctum')->user();
        
        if (!$user) {
            return response()->json(['message' => 'Usuario no autenticado'], 401);
        }

        $validator = Validator::make($request->all(), [
            'comparison_type' => 'required|in:energy_savings,carbon_reduction,community_participation,project_contributions,knowledge_sharing',
            'period' => 'required|in:daily,weekly,monthly,yearly,all_time',
            'scope' => 'required|in:personal,cooperative,regional,national,global',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Los datos proporcionados no son válidos',
                'errors' => $validator->errors()
            ], 422);
        }

        // Aquí iría la lógica para calcular la comparación
        $comparisonData = $this->calculateComparison($user, $validator->validated());

        $comparison = SocialComparison::create($comparisonData);
        $comparison->load(['user']);

        return response()->json([
            'data' => new SocialComparisonResource($comparison),
            'message' => 'Comparación generada exitosamente'
        ], 201);
    }

    /**
     * Remove the specified comparison
     */
    public function destroy(SocialComparison $socialComparison)
    {
        $user = Auth::guard('sanctum')->user();
        
        if (!$user || ($user->id !== $socialComparison->user_id && !$user->hasRole('admin'))) {
            return response()->json(['message' => 'No tienes permisos para eliminar esta comparación'], 403);
        }

        $socialComparison->delete();

        return response()->json(['message' => 'Comparación eliminada exitosamente']);
    }

    /**
     * Get best performance from comparisons
     */
    private function getBestPerformance($comparisons)
    {
        return $comparisons->sortBy('user_rank')->first();
    }

    /**
     * Get performance trend
     */
    private function getPerformanceTrend($comparisons)
    {
        $recent = $comparisons->take(5);
        
        if ($recent->count() < 2) {
            return 'insufficient_data';
        }

        $latest = $recent->first();
        $previous = $recent->skip(1)->first();

        if ($latest->user_rank < $previous->user_rank) {
            return 'improving';
        } elseif ($latest->user_rank > $previous->user_rank) {
            return 'declining';
        } else {
            return 'stable';
        }
    }

    /**
     * Get top performers
     */
    private function getTopPerformers($comparisons)
    {
        return $comparisons->sortBy('user_rank')
            ->take(10)
            ->map(function($comparison) {
                return [
                    'user' => $comparison->user->name,
                    'rank' => $comparison->user_rank,
                    'value' => $comparison->user_value,
                    'percentile' => $comparison->percentile,
                ];
            });
    }

    /**
     * Get performance distribution
     */
    private function getPerformanceDistribution($comparisons)
    {
        $distribution = [
            'top_10_percent' => 0,
            'top_25_percent' => 0,
            'top_50_percent' => 0,
            'bottom_50_percent' => 0,
        ];

        foreach ($comparisons as $comparison) {
            if ($comparison->percentile >= 90) {
                $distribution['top_10_percent']++;
            } elseif ($comparison->percentile >= 75) {
                $distribution['top_25_percent']++;
            } elseif ($comparison->percentile >= 50) {
                $distribution['top_50_percent']++;
            } else {
                $distribution['bottom_50_percent']++;
            }
        }

        return $distribution;
    }

    /**
     * Calculate comparison for user
     */
    private function calculateComparison($user, $params)
    {
        // Esta sería la lógica para calcular métricas reales
        // Por ahora devolvemos datos de ejemplo
        return [
            'user_id' => $user->id,
            'comparison_type' => $params['comparison_type'],
            'period' => $params['period'],
            'scope' => $params['scope'],
            'user_value' => 100.0, // Valor calculado del usuario
            'unit' => 'kWh',
            'average_value' => 85.0,
            'median_value' => 80.0,
            'best_value' => 150.0,
            'user_rank' => 15,
            'total_participants' => 100,
            'percentile' => 85.0,
            'comparison_group' => 'municipality',
            'group_id' => 1,
            'comparison_date' => now()->toDateString(),
            'is_public' => true,
        ];
    }
}