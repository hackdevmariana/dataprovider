<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Profession;
use App\Http\Requests\StoreProfessionRequest;
use App\Http\Resources\V1\ProfessionResource;
use App\Services\ProfessionsService;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;

#[OA\Tag(name: 'Profesiones', description: 'APIs para la gestión de profesiones y oficios')]
class ProfessionController extends Controller
{
    #[OA\Get(
        path: '/api/v1/professions',
        summary: 'Lista de profesiones',
        description: 'Obtiene una lista de todas las profesiones disponibles.',
        tags: ['Profesiones'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Lista de profesiones obtenida exitosamente',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: 'data',
                            type: 'array',
                            items: new OA\Items(ref: '#/components/schemas/Profession')
                        )
                    ]
                )
            )
        ]
    )]
    public function index(ProfessionsService $service): JsonResponse
    {
        $professions = $service->list();
        
        return response()->json([
            'data' => ProfessionResource::collection($professions)
        ]);
    }

    #[OA\Get(
        path: '/api/v1/professions/{idOrSlug}',
        summary: 'Detalle de una profesión',
        description: 'Obtiene los detalles de una profesión específica por ID o slug.',
        tags: ['Profesiones'],
        parameters: [
            new OA\Parameter(
                name: 'idOrSlug',
                description: 'ID o slug de la profesión',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'string', example: '1')
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Profesión obtenida exitosamente',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: 'data',
                            ref: '#/components/schemas/Profession'
                        )
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: 'Profesión no encontrada',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'Profesión no encontrada')
                    ]
                )
            )
        ]
    )]
    /**
     * Display the specified profession
     *
     * Obtiene los detalles de una profesión específica por ID o slug.
     *
     * @urlParam idOrSlug integer|string ID o slug de la profesión. Example: 1
     *
     * @response 200 {
     *   "data": {
     *     "id": 1,
     *       "name": "Ingeniero de Software",
     *       "slug": "ingeniero-de-software",
     *       "category": "Tecnología",
     *       "is_public_facing": true
     *   }
     * }
     *
     * @response 404 {
     *   "message": "Profesión no encontrada"
     * }
     *
     * @apiResourceModel App\Models\Profession
     */
    public function show($idOrSlug, ProfessionsService $service): JsonResponse
    {
        $profession = $service->findByIdOrSlug($idOrSlug);

        return response()->json([
            'data' => new ProfessionResource($profession)
        ]);
    }

    #[OA\Post(
        path: '/api/v1/professions',
        summary: 'Crear nueva profesión',
        description: 'Crea una nueva profesión en el sistema.',
        security: [['sanctum' => []]],
        tags: ['Profesiones'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['name', 'slug', 'category', 'is_public_facing'],
                properties: [
                    new OA\Property(property: 'name', type: 'string', example: 'Ingeniero de Software', description: 'Nombre de la profesión'),
                    new OA\Property(property: 'slug', type: 'string', example: 'ingeniero-de-software', description: 'Slug único de la profesión'),
                    new OA\Property(property: 'category', type: 'string', example: 'Tecnología', description: 'Categoría de la profesión'),
                    new OA\Property(property: 'is_public_facing', type: 'boolean', example: true, description: 'Si la profesión es visible públicamente'),
                    new OA\Property(property: 'description', type: 'string', example: 'Desarrollador de aplicaciones informáticas', description: 'Descripción de la profesión', nullable: true),
                    new OA\Property(property: 'requirements', type: 'string', example: 'Grado en Ingeniería Informática', description: 'Requisitos para ejercer la profesión', nullable: true),
                    new OA\Property(property: 'salary_range', type: 'string', example: '30,000-60,000€', description: 'Rango salarial típico', nullable: true),
                    new OA\Property(property: 'is_active', type: 'boolean', example: true, description: 'Si la profesión está activa', nullable: true)
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'Profesión creada exitosamente',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: 'data',
                            ref: '#/components/schemas/Profession'
                        )
                    ]
                )
            ),
            new OA\Response(
                response: 422,
                description: 'Error de validación en los datos',
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
            ),
            new OA\Response(
                response: 401,
                description: 'No autenticado',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'Unauthenticated.')
                    ]
                )
            )
        ]
    )]
    /**
     * Store a newly created profession
     *
     * Crea una nueva profesión en el sistema.
     *
     * @bodyParam name string required Nombre de la profesión. Example: Ingeniero de Software
     * @bodyParam slug string required Slug único de la profesión. Example: ingeniero-de-software
     * @bodyParam category string required Categoría de la profesión. Example: Tecnología
     * @bodyParam is_public_facing boolean required Si la profesión es visible públicamente. Example: true
     * @bodyParam description string Descripción de la profesión. Example: Desarrollador de aplicaciones informáticas
     * @bodyParam requirements string Requisitos para ejercer la profesión. Example: Grado en Ingeniería Informática
     * @bodyParam salary_range string Rango salarial típico. Example: 30,000-60,000€
     * @bodyParam is_active boolean Si la profesión está activa. Example: true
     *
     * @response 201 {
     *   "data": {
     *     "id": 1,
     *       "name": "Ingeniero de Software",
     *       "slug": "ingeniero-de-software",
     *       "category": "Tecnología",
     *       "is_public_facing": true
     *   }
     * }
     *
     * @response 422 {
     *   "message": "Los datos proporcionados no son válidos.",
     *   "errors": {...}
     * }
     *
     * @apiResourceModel App\Models\Profession
     * @authenticated
     */
    public function store(StoreProfessionRequest $request, ProfessionsService $service): JsonResponse
    {
        $profession = $service->create($request->validated());

        return response()->json([
            'data' => new ProfessionResource($profession)
        ], 201);
    }
}
