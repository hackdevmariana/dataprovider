<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Achievement;
use App\Http\Resources\V1\AchievementResource;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;

#[OA\Tag(name: 'Achievements', description: 'Gestión de logros y medallas del sistema de gamificación')]
class AchievementController extends Controller
{
    #[OA\Get(
        path: '/api/v1/achievements',
        summary: 'Obtener lista de logros',
        description: 'Retorna una lista paginada de logros disponibles en el sistema de gamificación. Permite filtrar por tipo, dificultad y si son secretos.',
        tags: ['Achievements'],
        parameters: [
            new OA\Parameter(
                name: 'type',
                description: 'Filtrar por tipo de logro',
                in: 'query',
                required: false,
                schema: new OA\Schema(
                    type: 'string',
                    enum: ['single', 'progressive', 'recurring']
                )
            ),
            new OA\Parameter(
                name: 'difficulty',
                description: 'Filtrar por dificultad del logro',
                in: 'query',
                required: false,
                schema: new OA\Schema(
                    type: 'string',
                    enum: ['bronze', 'silver', 'gold', 'legendary']
                )
            ),
            new OA\Parameter(
                name: 'is_secret',
                description: 'Filtrar por logros secretos',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'boolean')
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Lista de logros obtenida exitosamente',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: 'data',
                            type: 'array',
                            items: new OA\Items(ref: '#/components/schemas/Achievement')
                        ),
                        new OA\Property(
                            property: 'meta',
                            type: 'object',
                            properties: [
                                new OA\Property(property: 'total', type: 'integer', description: 'Total de logros encontrados')
                            ]
                        )
                    ]
                )
            ),
            new OA\Response(
                response: 422,
                description: 'Error de validación en los parámetros',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string'),
                        new OA\Property(
                            property: 'errors',
                            type: 'object',
                            additionalProperties: new OA\AdditionalProperties(
                                type: 'array',
                                items: new OA\Items(type: 'string')
                            )
                        )
                    ]
                )
            )
        ]
    )]
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

    #[OA\Get(
        path: '/api/v1/achievements/{id}',
        summary: 'Obtener un logro específico',
        description: 'Retorna los detalles completos de un logro específico por su ID.',
        tags: ['Achievements'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                description: 'ID único del logro',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer', format: 'int64')
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Logro obtenido exitosamente',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: 'data',
                            ref: '#/components/schemas/Achievement'
                        )
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: 'Logro no encontrado',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'Logro no encontrado')
                    ]
                )
            )
        ]
    )]
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
