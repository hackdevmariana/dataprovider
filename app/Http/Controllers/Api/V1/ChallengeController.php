<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Challenge;
use App\Http\Resources\V1\ChallengeResource;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

/**
 * @group Challenges
 *
 * APIs para la gestión de desafíos y retos del sistema.
 * Permite consultar y gestionar desafíos individuales, comunitarios y cooperativos.
 */
class ChallengeController extends Controller
{
    /**
     * Display a listing of challenges
     *
     * Obtiene una lista de desafíos activos con opciones de filtrado.
     *
     * @queryParam type string Filtrar por tipo de desafío (individual, community, cooperative). Example: individual
     * @queryParam category string Filtrar por categoría (energy_saving, solar_production, sustainability, community). Example: energy_saving
     * @queryParam difficulty string Filtrar por nivel de dificultad (easy, medium, hard, expert). Example: medium
     * @queryParam is_active boolean Filtrar por estado activo. Example: true
     * @queryParam is_featured boolean Filtrar por desafíos destacados. Example: true
     *
     * @response 200 {
     *   "data": [
     *     {
     *       "id": 1,
     *       "name": "Ahorro Energético Mensual",
     *       "type": "individual",
     *       "category": "energy_saving",
     *       "difficulty": "medium",
     *       "description": "Reduce tu consumo energético en un 20% este mes"
     *     }
     *   ],
     *   "meta": {
     *     "total": 15
     *   }
     * }
     *
     * @apiResourceCollection App\Http\Resources\V1\ChallengeResource
     * @apiResourceModel App\Models\Challenge
     */
    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'type' => 'sometimes|string|in:individual,community,cooperative',
            'category' => 'sometimes|string|in:energy_saving,solar_production,sustainability,community',
            'difficulty' => 'sometimes|string|in:easy,medium,hard,expert',
            'is_active' => 'sometimes|boolean',
            'is_featured' => 'sometimes|boolean'
        ]);

        $query = Challenge::where('is_active', true);

        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        if ($request->has('category')) {
            $query->where('category', $request->category);
        }

        if ($request->has('difficulty')) {
            $query->where('difficulty', $request->difficulty);
        }

        if ($request->has('is_featured')) {
            $query->where('is_featured', $request->boolean('is_featured'));
        }

        $challenges = $query->orderBy('sort_order')
                           ->orderBy('start_date')
                           ->get();

        return response()->json([
            'data' => ChallengeResource::collection($challenges),
            'meta' => [
                'total' => $challenges->count()
            ]
        ]);
    }

    /**
     * Display the specified challenge
     *
     * Obtiene los detalles de un desafío específico.
     *
     * @urlParam challenge integer ID del desafío. Example: 1
     *
     * @response 200 {
     *   "data": {
     *     "id": 1,
     *     "name": "Ahorro Energético Mensual",
     *     "type": "individual",
     *     "category": "energy_saving",
     *     "difficulty": "medium",
     *     "description": "Reduce tu consumo energético en un 20% este mes",
     *     "start_date": "2024-01-01",
     *     "end_date": "2024-01-31"
     *   }
     * }
     *
     * @response 404 {
     *   "message": "Desafío no encontrado"
     * }
     *
     * @apiResourceModel App\Models\Challenge
     */
    public function show(Challenge $challenge): JsonResponse
    {
        return response()->json([
            'data' => new ChallengeResource($challenge)
        ]);
    }

    /**
     * Get active challenges for user
     *
     * Obtiene los desafíos activos para un usuario específico.
     *
     * @queryParam user_id integer ID del usuario. Example: 1
     * @queryParam status string Estado del desafío (not_started, in_progress, completed, failed). Example: in_progress
     *
     * @response 200 {
     *   "data": [
     *     {
     *       "id": 1,
     *       "name": "Ahorro Energético Mensual",
     *       "status": "in_progress",
     *       "progress_percentage": 65
     *     }
     *   ]
     * }
     *
     * @apiResourceCollection App\Http\Resources\V1\ChallengeResource
     * @apiResourceModel App\Models\Challenge
     */
    public function userChallenges(Request $request): JsonResponse
    {
        $request->validate([
            'user_id' => 'required|integer|exists:users,id',
            'status' => 'sometimes|string|in:not_started,in_progress,completed,failed'
        ]);

        $query = Challenge::where('is_active', true);

        if ($request->has('status')) {
            $query->whereHas('userChallenges', function ($q) use ($request) {
                $q->where('user_id', $request->user_id)
                  ->where('status', $request->status);
            });
        }

        $challenges = $query->orderBy('sort_order')->get();

        return response()->json([
            'data' => ChallengeResource::collection($challenges)
        ]);
    }

    /**
     * Get challenge statistics
     *
     * Obtiene estadísticas generales de los desafíos.
     *
     * @response 200 {
     *   "data": {
     *     "total_challenges": 25,
     *     "active_challenges": 18,
     *     "completed_challenges": 150,
     *     "by_type": {
     *       "individual": 10,
     *       "community": 8,
     *       "cooperative": 7
     *     },
     *     "by_category": {
     *       "energy_saving": 12,
     *       "solar_production": 8,
     *       "sustainability": 5
     *     }
     *   }
     * }
     */
    public function statistics(): JsonResponse
    {
        $totalChallenges = Challenge::count();
        $activeChallenges = Challenge::where('is_active', true)->count();
        $completedChallenges = Challenge::whereHas('userChallenges', function ($q) {
            $q->where('status', 'completed');
        })->count();

        $byType = Challenge::select('type')
            ->selectRaw('count(*) as count')
            ->groupBy('type')
            ->pluck('count', 'type')
            ->toArray();

        $byCategory = Challenge::select('category')
            ->selectRaw('count(*) as count')
            ->groupBy('category')
            ->pluck('count', 'category')
            ->toArray();

        return response()->json([
            'data' => [
                'total_challenges' => $totalChallenges,
                'active_challenges' => $activeChallenges,
                'completed_challenges' => $completedChallenges,
                'by_type' => $byType,
                'by_category' => $byCategory
            ]
        ]);
    }
}
