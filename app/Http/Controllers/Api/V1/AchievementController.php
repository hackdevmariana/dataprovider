<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Achievement;
use App\Http\Resources\V1\AchievementResource;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

/**
 * @group Achievements
 *
 * APIs para la gestión de logros y achievements del sistema.
 * Permite consultar logros disponibles y sus detalles.
 */
class AchievementController extends Controller
{
    /**
     * Display a listing of achievements
     *
     * Obtiene una lista de logros activos con opciones de filtrado.
     *
     * @queryParam type string Filtrar por tipo de logro (single, progressive, recurring). Example: single
     * @queryParam difficulty string Filtrar por nivel de dificultad (bronze, silver, gold, legendary). Example: gold
     * @queryParam is_secret boolean Filtrar por logros secretos. Example: false
     *
     * @response 200 {
     *   "data": [
     *     {
     *       "id": 1,
     *       "name": "Primer Paso",
     *       "description": "Completa tu primera instalación energética",
     *       "type": "single",
     *       "difficulty": "bronze",
     *       "points": 10,
     *       "is_secret": false
     *     }
     *   ],
     *   "meta": {
     *     "total": 25
     *   }
     * }
     *
     * @apiResourceCollection App\Http\Resources\V1\AchievementResource
     * @apiResourceModel App\Models\Achievement
     */
    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'type' => 'sometimes|string|in:single,progressive,recurring',
            'difficulty' => 'sometimes|string|in:bronze,silver,gold,legendary',
            'is_secret' => 'sometimes|boolean'
        ]);

        $query = Achievement::where('is_active', true);

        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        if ($request->has('difficulty')) {
            $query->where('difficulty', $request->difficulty);
        }

        if ($request->has('is_secret')) {
            $query->where('is_secret', $request->boolean('is_secret'));
        }

        $achievements = $query->orderBy('difficulty')
                             ->orderBy('points')
                             ->get();

        return response()->json([
            'data' => AchievementResource::collection($achievements),
            'meta' => [
                'total' => $achievements->count()
            ]
        ]);
    }

    /**
     * Display the specified achievement
     *
     * Obtiene los detalles de un logro específico.
     *
     * @urlParam achievement int ID del logro. Example: 1
     *
     * @response 200 {
     *   "data": {
     *     "id": 1,
     *       "name": "Primer Paso",
     *       "description": "Completa tu primera instalación energética",
     *       "type": "single",
     *       "difficulty": "bronze",
     *       "points": 10,
     *       "is_secret": false,
     *       "criteria": {
     *         "installations_count": 1
     *       }
     *   }
     * }
     *
     * @response 404 {
     *   "message": "Logro no encontrado"
     * }
     *
     * @apiResourceModel App\Models\Achievement
     */
    public function show(Achievement $achievement): JsonResponse
    {
        return response()->json([
            'data' => new AchievementResource($achievement)
        ]);
    }
}
