<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Image;
use App\Http\Resources\V1\ImageResource;
use App\Http\Requests\StoreImageRequest;
use App\Http\Requests\UpdateImageRequest;
use App\Services\ImagesService;
use Illuminate\Http\JsonResponse;

/**
 * @group Images
 *
 * APIs para la gestión de imágenes y recursos visuales.
 * Permite crear, consultar y gestionar imágenes del sistema.
 */
class ImageController extends Controller
{
    /**
     * Display a listing of images
     *
     * Devuelve una lista paginada de imágenes.
     *
     * @queryParam page int Número de página. Example: 1
     * @queryParam per_page int Cantidad por página (máx 100). Example: 20
     *
     * @response 200 {
     *   "data": [
     *     {
     *       "id": 1,
     *       "slug": "imagen-ejemplo",
     *       "url": "https://example.com/image.jpg",
     *       "alt_text": "Texto alternativo",
     *       "source": "Wikimedia",
     *       "width": 800,
     *       "height": 600
     *     }
     *   ],
     *   "meta": {...}
     * }
     *
     * @apiResourceCollection App\Http\Resources\V1\ImageResource
     * @apiResourceModel App\Models\Image
     */
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

    /**
     * Display the specified image
     *
     * Devuelve los detalles de una imagen por ID.
     *
     * @urlParam id integer ID de la imagen. Example: 1
     *
     * @response 200 {
     *   "data": {
     *     "id": 1,
     *     "slug": "imagen-ejemplo",
     *     "url": "https://example.com/image.jpg",
     *     "alt_text": "Texto alternativo",
     *     "source": "Wikimedia",
     *     "width": 800,
     *     "height": 600
     *   }
     * }
     *
     * @response 404 {
     *   "message": "Imagen no encontrada"
     * }
     *
     * @apiResourceModel App\Models\Image
     */
    public function show($id, ImagesService $service): JsonResponse
    {
        $image = $service->findById((int) $id);
        
        return response()->json([
            'data' => new ImageResource($image)
        ]);
    }

    /**
     * Store a newly created image
     *
     * Crea una nueva imagen en el sistema.
     *
     * @bodyParam slug string Slug único de la imagen. Example: imagen-ejemplo
     * @bodyParam url string required URL de la imagen. Example: https://example.com/image.jpg
     * @bodyParam alt_text string Texto alternativo de la imagen. Example: Texto alternativo
     * @bodyParam source string Fuente de la imagen. Example: Wikimedia
     * @bodyParam width integer Ancho de la imagen en píxeles. Example: 800
     * @bodyParam height integer Alto de la imagen en píxeles. Example: 600
     *
     * @response 201 {
     *   "data": {
     *     "id": 1,
     *     "slug": "imagen-ejemplo",
     *     "url": "https://example.com/image.jpg",
     *     "alt_text": "Texto alternativo",
     *     "source": "Wikimedia",
     *     "width": 800,
     *     "height": 600
     *   }
     * }
     *
     * @response 422 {
     *   "message": "Los datos proporcionados no son válidos.",
     *   "errors": {...}
     * }
     *
     * @apiResourceModel App\Models\Image
     */
    public function store(StoreImageRequest $request, ImagesService $service): JsonResponse
    {
        $image = $service->create($request->validated());
        
        return response()->json([
            'data' => new ImageResource($image)
        ], 201);
    }

    /**
     * Update the specified image
     *
     * Actualiza una imagen existente.
     *
     * @urlParam id integer ID de la imagen. Example: 1
     * @bodyParam slug string Slug único de la imagen. Example: imagen-ejemplo
     * @bodyParam url string URL de la imagen. Example: https://example.com/image.jpg
     * @bodyParam alt_text string Texto alternativo de la imagen. Example: Texto alternativo
     * @bodyParam source string Fuente de la imagen. Example: Wikimedia
     * @bodyParam width integer Ancho de la imagen en píxeles. Example: 800
     * @bodyParam height integer Alto de la imagen en píxeles. Example: 600
     *
     * @response 200 {
     *   "data": {
     *     "id": 1,
     *     "slug": "imagen-ejemplo",
     *     "url": "https://example.com/image.jpg",
     *     "alt_text": "Texto alternativo actualizado"
     *   }
     * }
     *
     * @response 404 {
     *   "message": "Imagen no encontrada"
     * }
     *
     * @response 422 {
     *   "message": "Los datos proporcionados no son válidos.",
     *   "errors": {...}
     * }
     *
     * @apiResourceModel App\Models\Image
     */
    public function update(UpdateImageRequest $request, $id, ImagesService $service): JsonResponse
    {
        $image = $service->update((int) $id, $request->validated());
        
        return response()->json([
            'data' => new ImageResource($image)
        ]);
    }

    /**
     * Remove the specified image
     *
     * Elimina una imagen del sistema.
     *
     * @urlParam id integer ID de la imagen. Example: 1
     *
     * @response 204 {
     *   "message": "Imagen eliminada exitosamente"
     * }
     *
     * @response 404 {
     *   "message": "Imagen no encontrada"
     * }
     */
    public function destroy($id, ImagesService $service): JsonResponse
    {
        $service->delete((int) $id);
        
        return response()->json([
            'message' => 'Imagen eliminada exitosamente'
        ], 204);
    }
}
