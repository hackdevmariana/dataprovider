<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Achievement;
use App\Http\Resources\V1\AchievementResource;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(
 *     name="Logros",
 *     description="APIs para la gestión de Logros"
 * )
 */
class AchievementController extends Controller
{
    /**
     * Display a listing of achievements
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
     */
    public function show(Achievement $achievement): JsonResponse
    {
        return response()->json([
            'data' => new AchievementResource($achievement)
        ]);
    }
}
