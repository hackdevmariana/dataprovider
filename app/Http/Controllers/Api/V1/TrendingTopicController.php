<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(
 *     name="Temas Trending",
 *     description="APIs para la gestión de Temas Trending"
 * )
 */
class TrendingTopicController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        return response()->json([
            "data" => [],
            "message" => "Endpoint en desarrollo"
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        return response()->json([
            "message" => "Endpoint en desarrollo"
        ], 501);
    }

    public function show(string $id): JsonResponse
    {
        return response()->json([
            "data" => null,
            "message" => "Endpoint en desarrollo"
        ]);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        return response()->json([
            "message" => "Endpoint en desarrollo"
        ], 501);
    }

    public function destroy(string $id): JsonResponse
    {
        return response()->json([
            "message" => "Endpoint en desarrollo"
        ], 501);
    }
}