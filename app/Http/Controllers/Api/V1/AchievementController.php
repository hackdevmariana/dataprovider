<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Achievement;
use App\Http\Resources\V1\AchievementResource;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class AchievementController extends Controller
{
    /**
     * Display a listing of achievements.
     * 
     * @OA\Get(
     *     path="/api/v1/achievements",
     *     summary="Get all achievements",
     *     tags={"Achievements"},
     *     @OA\Parameter(
     *         name="type",
     *         in="query",
     *         description="Filter by achievement type",
     *         @OA\Schema(type="string", enum={"single", "progressive", "recurring"})
     *     ),
     *     @OA\Parameter(
     *         name="difficulty",
     *         in="query", 
     *         description="Filter by difficulty level",
     *         @OA\Schema(type="string", enum={"bronze", "silver", "gold", "legendary"})
     *     ),
     *     @OA\Response(response=200, description="Success")
     * )
     */
    public function index(Request $request): JsonResponse
    {
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
     * Display the specified achievement.
     * 
     * @OA\Get(
     *     path="/api/v1/achievements/{id}",
     *     summary="Get achievement by ID",
     *     tags={"Achievements"},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Success"),
     *     @OA\Response(response=404, description="Achievement not found")
     * )
     */
    public function show(Achievement $achievement): JsonResponse
    {
        return response()->json([
            'data' => new AchievementResource($achievement)
        ]);
    }
}
