<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\PointOfInterest;
use App\Http\Requests\StorePointOfInterestRequest;
use App\Http\Requests\UpdatePointOfInterestRequest;
use App\Services\PointsOfInterestService;
use Illuminate\Http\JsonResponse;
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(
 *     name="Points of Interest",
 *     description="APIs para la gestión de puntos de interés y lugares destacados"
 * )
 */
class PointOfInterestController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/v1/points-of-interest",
     *     summary="Listar puntos de interés",
     *     description="Obtiene una lista paginada de todos los puntos de interés",
     *     tags={"Points of Interest"},
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Número de página",
     *         required=false,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Cantidad por página (máx 100)",
     *         required=false,
     *         @OA\Schema(type="integer", example=50)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Lista de puntos de interés obtenida exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="array", @OA\Items(type="object")),
     *             @OA\Property(property="meta", type="object")
     *         )
     *     )
     * )
     */
    public function index(PointsOfInterestService $service): JsonResponse
    {
        $points = $service->paginate(50);
        
        return response()->json($points);
    }

    /**
     * Display the specified point of interest
     *
     * Obtiene los detalles de un punto de interés por ID o slug.
     *
     * @urlParam idOrSlug integer|string ID o slug del punto de interés. Example: 1
     *
     * @response 200 {
     *   "data": {
     *     "id": 1,
     *       "name": "Parque Central",
     *       "slug": "parque-central",
     *       "description": "Parque principal de la ciudad",
     *       "latitude": 40.4168,
     *       "longitude": -3.7038,
     *       "type": "park"
     *   }
     * }
     *
     * @response 404 {
     *   "message": "Punto de interés no encontrado"
     * }
     *
     * @apiResourceModel App\Models\PointOfInterest
     */
    public function show($idOrSlug, PointsOfInterestService $service): JsonResponse
    {
        $poi = $service->findByIdOrSlug($idOrSlug);

        if (!$poi) {
            return response()->json([
                'message' => 'Punto de interés no encontrado'
            ], 404);
        }

        return response()->json([
            'data' => $poi
        ]);
    }

    /**
     * Store a newly created point of interest
     *
     * Crea un nuevo punto de interés en el sistema.
     *
     * @bodyParam name string required Nombre del punto de interés. Example: Parque Central
     * @bodyParam slug string Slug único del punto de interés. Example: parque-central
     * @bodyParam description string Descripción del punto de interés. Example: Parque principal de la ciudad
     * @bodyParam latitude number Latitud del punto de interés. Example: 40.4168
     * @bodyParam longitude number Longitud del punto de interés. Example: -3.7038
     * @bodyParam type string Tipo de punto de interés. Example: park
     * @bodyParam address string Dirección del punto de interés. Example: Calle Mayor, 1
     * @bodyParam phone string Teléfono de contacto. Example: +34 91 123 4567
     * @bodyParam website string Sitio web oficial. Example: https://example.com
     * @bodyParam opening_hours string Horarios de apertura. Example: L-V 9:00-18:00
     * @bodyParam is_active boolean Si el punto de interés está activo. Example: true
     *
     * @response 201 {
     *   "data": {
     *     "id": 1,
     *       "name": "Parque Central",
     *       "slug": "parque-central",
     *       "description": "Parque principal de la ciudad"
     *   }
     * }
     *
     * @response 422 {
     *   "message": "Los datos proporcionados no son válidos.",
     *   "errors": {...}
     * }
     *
     * @apiResourceModel App\Models\PointOfInterest
     */
    public function store(StorePointOfInterestRequest $request, PointsOfInterestService $service): JsonResponse
    {
        $poi = $service->create($request->validated());

        return response()->json([
            'data' => $poi
        ], 201);
    }

    /**
     * Update the specified point of interest
     *
     * Actualiza un punto de interés existente.
     *
     * @urlParam id integer ID del punto de interés. Example: 1
     * @bodyParam name string Nombre del punto de interés. Example: Parque Central
     * @bodyParam slug string Slug único del punto de interés. Example: parque-central
     * @bodyParam description string Descripción del punto de interés. Example: Parque principal de la ciudad
     * @bodyParam latitude number Latitud del punto de interés. Example: 40.4168
     * @bodyParam longitude number Longitud del punto de interés. Example: -3.7038
     * @bodyParam type string Tipo de punto de interés. Example: park
     * @bodyParam address string Dirección del punto de interés. Example: Calle Mayor, 1
     * @bodyParam phone string Teléfono de contacto. Example: +34 91 123 4567
     * @bodyParam website string Sitio web oficial. Example: https://example.com
     * @bodyParam opening_hours string Horarios de apertura. Example: L-V 9:00-18:00
     * @bodyParam is_active boolean Si el punto de interés está activo. Example: true
     *
     * @response 200 {
     *   "data": {
     *     "id": 1,
     *       "name": "Parque Central",
     *       "slug": "parque-central",
     *       "description": "Parque principal de la ciudad actualizado"
     *   }
     * }
     *
     * @response 404 {
     *   "message": "Punto de interés no encontrado"
     * }
     *
     * @response 422 {
     *   "message": "Los datos proporcionados no son válidos.",
     *   "errors": {...}
     * }
     *
     * @apiResourceModel App\Models\PointOfInterest
     */
    public function update(UpdatePointOfInterestRequest $request, $id, PointsOfInterestService $service): JsonResponse
    {
        $poi = PointOfInterest::find($id);
        if (!$poi) {
            return response()->json([
                'message' => 'Punto de interés no encontrado'
            ], 404);
        }

        $updatedPoi = $service->update($poi, $request->validated());

        return response()->json([
            'data' => $updatedPoi
        ]);
    }

    /**
     * Remove the specified point of interest
     *
     * Elimina un punto de interés del sistema.
     *
     * @urlParam id integer ID del punto de interés. Example: 1
     *
     * @response 204 {
     *   "message": "Punto de interés eliminado exitosamente"
     * }
     *
     * @response 404 {
     *   "message": "Punto de interés no encontrado"
     * }
     */
    public function destroy($id, PointsOfInterestService $service): JsonResponse
    {
        $poi = PointOfInterest::find($id);
        if (!$poi) {
            return response()->json([
                'message' => 'Punto de interés no encontrado'
            ], 404);
        }

        $service->delete($poi);

        return response()->json([
            'message' => 'Punto de interés eliminado exitosamente'
        ], 204);
    }
}
