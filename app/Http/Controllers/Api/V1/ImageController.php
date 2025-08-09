<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Image;
use App\Http\Resources\V1\ImageResource;
use App\Http\Requests\StoreImageRequest;
use App\Http\Requests\UpdateImageRequest;

/**
 * @OA\Tag(
 *     name="Images",
 *     description="API Endpoints para gestión de imágenes"
 * )
 */
class ImageController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/v1/images",
     *     tags={"Images"},
     *     summary="Lista paginada de imágenes",
     *     description="Devuelve una lista paginada de imágenes",
     *     @OA\Response(
     *         response=200,
     *         description="Lista paginada de imágenes",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="data", type="array",
     *                 @OA\Items(ref="#/components/schemas/Image")
     *             ),
     *             @OA\Property(property="links", type="object"),
     *             @OA\Property(property="meta", type="object")
     *         )
     *     )
     * )
     */
    public function index()
    {
        $images = Image::paginate(20);
        return ImageResource::collection($images);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/images/{id}",
     *     tags={"Images"},
     *     summary="Mostrar imagen",
     *     description="Devuelve los detalles de una imagen por ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID de la imagen",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Detalles de la imagen",
     *         @OA\JsonContent(ref="#/components/schemas/Image")
     *     ),
     *     @OA\Response(response=404, description="Imagen no encontrada")
     * )
     */
    public function show($id)
    {
        $image = Image::findOrFail($id);
        return new ImageResource($image);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/images",
     *     tags={"Images"},
     *     summary="Crear imagen",
     *     description="Crea una nueva imagen",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"url"},
     *             @OA\Property(property="slug", type="string", example="imagen-ejemplo"),
     *             @OA\Property(property="url", type="string", format="url", example="https://example.com/image.jpg"),
     *             @OA\Property(property="alt_text", type="string", example="Texto alternativo"),
     *             @OA\Property(property="source", type="string", example="Wikimedia"),
     *             @OA\Property(property="width", type="integer", example=800),
     *             @OA\Property(property="height", type="integer", example=600)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Imagen creada",
     *         @OA\JsonContent(ref="#/components/schemas/Image")
     *     ),
     *     @OA\Response(response=422, description="Error de validación")
     * )
     */
    public function store(StoreImageRequest $request)
    {
        $image = Image::create($request->validated());

        return (new ImageResource($image))->response()->setStatusCode(201);
    }

    /**
     * @OA\Put(
     *     path="/api/v1/images/{id}",
     *     tags={"Images"},
     *     summary="Actualizar imagen",
     *     description="Actualiza una imagen existente",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID de la imagen",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="slug", type="string", example="imagen-ejemplo"),
     *             @OA\Property(property="url", type="string", format="url", example="https://example.com/image.jpg"),
     *             @OA\Property(property="alt_text", type="string", example="Texto alternativo"),
     *             @OA\Property(property="source", type="string", example="Wikimedia"),
     *             @OA\Property(property="width", type="integer", example=800),
     *             @OA\Property(property="height", type="integer", example=600)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Imagen actualizada",
     *         @OA\JsonContent(ref="#/components/schemas/Image")
     *     ),
     *     @OA\Response(response=404, description="Imagen no encontrada"),
     *     @OA\Response(response=422, description="Error de validación")
     * )
     */
    public function update(UpdateImageRequest $request, $id)
    {
        $image = Image::findOrFail($id);
        $image->update($request->validated());

        return new ImageResource($image);
    }

    /**
     * @OA\Delete(
     *     path="/api/v1/images/{id}",
     *     tags={"Images"},
     *     summary="Eliminar imagen",
     *     description="Elimina una imagen por ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID de la imagen",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=204, description="Imagen eliminada"),
     *     @OA\Response(response=404, description="Imagen no encontrada")
     * )
     */
    public function destroy($id)
    {
        $image = Image::findOrFail($id);
        $image->delete();

        return response()->json(null, 204);
    }
}


