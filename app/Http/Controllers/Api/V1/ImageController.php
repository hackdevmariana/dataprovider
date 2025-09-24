<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Image;
use App\Http\Resources\V1\ImageResource;
use App\Http\Requests\StoreImageRequest;
use App\Http\Requests\UpdateImageRequest;
use App\Services\ImagesService;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;

#[OA\Tag(name: 'Images', description: 'APIs para la gestión de imágenes y recursos visuales')]
class ImageController extends Controller
{
    #[OA\Get(
        path: '/api/v1/images',
        summary: 'Lista de imágenes',
        description: 'Devuelve una lista paginada de imágenes.',
        tags: ['Images'],
        parameters: [
            new OA\Parameter(
                name: 'page',
                description: 'Número de página',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'integer', minimum: 1, default: 1)
            ),
            new OA\Parameter(
                name: 'per_page',
                description: 'Cantidad por página (máx 100)',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'integer', minimum: 1, maximum: 100, default: 20)
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Lista de imágenes obtenida exitosamente',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: 'data',
                            type: 'array',
                            items: new OA\Items(ref: '#/components/schemas/Image')
                        ),
                        new OA\Property(
                            property: 'meta',
                            type: 'object',
                            properties: [
                                new OA\Property(property: 'current_page', type: 'integer'),
                                new OA\Property(property: 'last_page', type: 'integer'),
                                new OA\Property(property: 'per_page', type: 'integer'),
                                new OA\Property(property: 'total', type: 'integer')
                            ]
                        )
                    ]
                )
            )
        ]
    )]
    public function index(ImagesService $service): JsonResponse
    {
        $images = $service->paginate(20);
        
        return response()->json([
            'data' => ImageResource::collection($images),
            'meta' => [
                'current_page' => $images->currentPage(),
                'last_page' => $images->lastPage(),
                'per_page' => $images->perPage(),
                'total' => $images->total(),
            ]
        ]);
    }

    #[OA\Get(
        path: '/api/v1/images/{id}',
        summary: 'Detalle de una imagen',
        description: 'Devuelve los detalles de una imagen por ID.',
        tags: ['Images'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                description: 'ID de la imagen',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer', format: 'int64', example: 1)
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Imagen obtenida exitosamente',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: 'data',
                            ref: '#/components/schemas/Image'
                        )
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: 'Imagen no encontrada',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'Imagen no encontrada')
                    ]
                )
            )
        ]
    )]
    public function show(ImagesService $service, $id): JsonResponse
    {
        $image = $service->findById((int) $id);
        
        return response()->json([
            'data' => new ImageResource($image)
        ]);
    }

    #[OA\Post(
        path: '/api/v1/images',
        summary: 'Crear nueva imagen',
        description: 'Crea una nueva imagen en el sistema.',
        tags: ['Images'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['url'],
                properties: [
                    new OA\Property(property: 'slug', type: 'string', example: 'imagen-ejemplo', description: 'Slug único de la imagen', nullable: true),
                    new OA\Property(property: 'url', type: 'string', format: 'url', example: 'https://example.com/image.jpg', description: 'URL de la imagen'),
                    new OA\Property(property: 'alt_text', type: 'string', example: 'Texto alternativo', description: 'Texto alternativo de la imagen', nullable: true),
                    new OA\Property(property: 'source', type: 'string', example: 'Wikimedia', description: 'Fuente de la imagen', nullable: true),
                    new OA\Property(property: 'width', type: 'integer', example: 800, description: 'Ancho de la imagen en píxeles', nullable: true),
                    new OA\Property(property: 'height', type: 'integer', example: 600, description: 'Alto de la imagen en píxeles', nullable: true)
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'Imagen creada exitosamente',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: 'data',
                            ref: '#/components/schemas/Image'
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
            )
        ]
    )]
    public function store(StoreImageRequest $request, ImagesService $service): JsonResponse
    {
        $image = $service->create($request->validated());
        
        return response()->json([
            'data' => new ImageResource($image)
        ], 201);
    }

    #[OA\Put(
        path: '/api/v1/images/{id}',
        summary: 'Actualizar imagen',
        description: 'Actualiza una imagen existente.',
        tags: ['Images'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                description: 'ID de la imagen',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer', format: 'int64')
            )
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'slug', type: 'string', example: 'imagen-ejemplo', description: 'Slug único de la imagen', nullable: true),
                    new OA\Property(property: 'url', type: 'string', format: 'url', example: 'https://example.com/image.jpg', description: 'URL de la imagen', nullable: true),
                    new OA\Property(property: 'alt_text', type: 'string', example: 'Texto alternativo actualizado', description: 'Texto alternativo de la imagen', nullable: true),
                    new OA\Property(property: 'source', type: 'string', example: 'Wikimedia', description: 'Fuente de la imagen', nullable: true),
                    new OA\Property(property: 'width', type: 'integer', example: 800, description: 'Ancho de la imagen en píxeles', nullable: true),
                    new OA\Property(property: 'height', type: 'integer', example: 600, description: 'Alto de la imagen en píxeles', nullable: true)
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Imagen actualizada exitosamente',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: 'data',
                            ref: '#/components/schemas/Image'
                        )
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: 'Imagen no encontrada',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'Imagen no encontrada')
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
            )
        ]
    )]
    public function update(UpdateImageRequest $request, ImagesService $service, $id): JsonResponse
    {
        $image = $service->findById((int) $id);
        $image = $service->update($image, $request->validated());
        
        return response()->json([
            'data' => new ImageResource($image)
        ]);
    }

    #[OA\Delete(
        path: '/api/v1/images/{id}',
        summary: 'Eliminar imagen',
        description: 'Elimina una imagen del sistema.',
        tags: ['Images'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                description: 'ID de la imagen',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer', format: 'int64')
            )
        ],
        responses: [
            new OA\Response(
                response: 204,
                description: 'Imagen eliminada exitosamente',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'Imagen eliminada exitosamente')
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: 'Imagen no encontrada',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'Imagen no encontrada')
                    ]
                )
            )
        ]
    )]
    public function destroy(ImagesService $service, $id): JsonResponse
    {
        $image = $service->findById((int) $id);
        $service->delete($image);
        
        return response()->json([
            'message' => 'Imagen eliminada exitosamente'
        ], 204);
    }
}
