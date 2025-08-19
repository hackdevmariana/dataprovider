<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Challenge;
use App\Http\Resources\V1\ChallengeResource;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ChallengeController extends Controller
{
    /**
     * Display a listing of challenges.
     * 
     * @OA\Get(
     *     path="/api/v1/challenges",
     *     summary="Get all challenges",
     *     tags={"Challenges"},
     *     @OA\Parameter(
     *         name="type",
     *         in="query",
     *         description="Filter by challenge type",
     *         @OA\Schema(type="string", enum={"individual", "community", "cooperative"})
     *     ),
     *     @OA\Parameter(
     *         name="category",
     *         in="query",
     *         description="Filter by challenge category",
     *         @OA\Schema(type="string", enum={"energy_saving", "solar_production", "sustainability", "community"})
     *     ),
     *     @OA\Parameter(
     *         name="difficulty",
     *         in="query",
     *         description="Filter by difficulty level",
     *         @OA\Schema(type="string", enum={"easy", "medium", "hard", "expert"})
     *     ),
     *     @OA\Parameter(
     *         name="is_active",
     *         in="query",
     *         description="Filter by active status",
     *         @OA\Schema(type="boolean")
     *     ),
     *     @OA\Response(response=200, description="Success")
     * )
     */
    public function index(Request $request): JsonResponse
    {
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
     * Display the specified challenge.
     * 
     * @OA\Get(
     *     path="/api/v1/challenges/{id}",
     *     summary="Get challenge by ID",
     *     tags={"Challenges"},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Success"),
     *     @OA\Response(response=404, description="Challenge not found")
     * )
     */
    public function show(Challenge $challenge): JsonResponse
    {
        return response()->json([
            'data' => new ChallengeResource($challenge)
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Challenge $challenge)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Challenge $challenge)
    {
        //
    }
}
