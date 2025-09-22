<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\VenueType;
use App\Http\Resources\V1\VenueTypeResource;
use App\Http\Requests\StoreVenueTypeRequest;
use App\Http\Requests\UpdateVenueTypeRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use OpenApi\Annotations as OA;

/**
 * @group Venue Types
 *
 * APIs para la gestión de tipos de venues del sistema.
 * Permite crear, consultar y gestionar tipos de lugares y espacios.
 */
/**
 * @OA\Tag(
 *     name="Tipos de Venue",
 *     description="APIs para la gestión de Tipos de Venue"
 * )
 */
class VenueTypeController extends Controller
{
    /**
     * Display a listing of venue types
     *
     * Obtiene una lista paginada de todos los tipos de venues.
     *
     * @queryParam page int Número de página. Example: 1
     * @queryParam per_page int Cantidad por página (máx 100). Example: 20
     * @queryParam category string Filtrar por categoría (entertainment, business, cultural). Example: entertainment
     * @queryParam is_active boolean Filtrar por tipos activos. Example: true
     * @queryParam search string Buscar por nombre o descripción. Example: teatro
     *
     * @response 200 {
     *   "data": [
     *     {
     *       "id": 1,
     *       "name": "Teatro",
     *       "slug": "teatro",
     *       "category": "entertainment",
     *       "description": "Lugar para presentaciones teatrales",
     *       "is_active": true,
     *       "icon": "fa-theater-masks"
     *     }
     *   ],
     *   "meta": {...}
     * }
     *
     * @apiResourceCollection App\Http\Resources\V1\VenueTypeResource
     * @apiResourceModel App\Models\VenueType
     */
    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'page' => 'sometimes|integer|min:1',
            'per_page' => 'sometimes|integer|min:1|max:100',
            'category' => 'sometimes|string|in:entertainment,business,cultural,sports,education',
            'is_active' => 'sometimes|boolean',
            'search' => 'sometimes|string|max:255'
        ]);

        $query = VenueType::query();

        if ($request->has('category')) {
            $query->where('category', $request->category);
        }

        if ($request->has('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        if ($request->has('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        $venueTypes = $query->orderBy('category')
                           ->orderBy('name')
                           ->paginate($request->get('per_page', 20));

        return response()->json([
            'data' => VenueTypeResource::collection($venueTypes),
            'meta' => [
                'current_page' => $venueTypes->currentPage(),
                'last_page' => $venueTypes->lastPage(),
                'per_page' => $venueTypes->perPage(),
                'total' => $venueTypes->total(),
            ]
        ]);
    }

    /**
     * Store a newly created venue type
     *
     * Crea un nuevo tipo de venue en el sistema.
     *
     * @bodyParam name string required Nombre del tipo. Example: Teatro
     * @bodyParam slug string Slug único del tipo. Example: teatro
     * @bodyParam category string required Categoría del tipo (entertainment, business, cultural). Example: entertainment
     * @bodyParam description string Descripción del tipo. Example: Lugar para presentaciones teatrales
     * @bodyParam is_active boolean Si el tipo está activo. Example: true
     * @bodyParam icon string Icono del tipo. Example: fa-theater-masks
     * @bodyParam color string Color hexadecimal del tipo. Example: #9C27B0
     *
     * @response 201 {
     *   "data": {
     *     "id": 1,
     *     "name": "Teatro",
     *     "slug": "teatro",
     *     "category": "entertainment",
     *     "description": "Lugar para presentaciones teatrales",
     *     "is_active": true,
     *     "icon": "fa-theater-masks",
     *     "created_at": "2024-01-01T00:00:00.000000Z"
     *   }
     * }
     *
     * @response 422 {
     *   "message": "Los datos proporcionados no son válidos.",
     *   "errors": {...}
     * }
     *
     * @apiResourceModel App\Models\VenueType
     * @authenticated
     */
    public function store(StoreVenueTypeRequest $request): JsonResponse
    {
        $data = $request->validated();
        
        $venueType = VenueType::create($data);

        return response()->json([
            'data' => new VenueTypeResource($venueType)
        ], 201);
    }

    /**
     * Display the specified venue type
     *
     * Obtiene los detalles de un tipo de venue específico.
     *
     * @urlParam venueType integer ID del tipo de venue. Example: 1
     *
     * @response 200 {
     *   "data": {
     *     "id": 1,
     *     "name": "Teatro",
     *     "slug": "teatro",
     *     "category": "entertainment",
     *     "description": "Lugar para presentaciones teatrales",
     *     "is_active": true,
     *     "icon": "fa-theater-masks",
     *     "color": "#9C27B0"
     *   }
     * }
     *
     * @response 404 {
     *   "message": "Tipo de venue no encontrado"
     * }
     *
     * @apiResourceModel App\Models\VenueType
     */
    public function show(VenueType $venueType): JsonResponse
    {
        return response()->json([
            'data' => new VenueTypeResource($venueType)
        ]);
    }

    /**
     * Update the specified venue type
     *
     * Actualiza un tipo de venue existente.
     *
     * @urlParam venueType integer ID del tipo de venue. Example: 1
     * @bodyParam name string Nombre del tipo. Example: Teatro Clásico
     * @bodyParam slug string Slug único del tipo. Example: teatro-clasico
     * @bodyParam category string Categoría del tipo (entertainment, business, cultural). Example: entertainment
     * @bodyParam description string Descripción del tipo. Example: Lugar para presentaciones teatrales clásicas
     * @bodyParam is_active boolean Si el tipo está activo. Example: true
     * @bodyParam icon string Icono del tipo. Example: fa-theater-masks
     * @bodyParam color string Color hexadecimal del tipo. Example: #7B1FA2
     *
     * @response 200 {
     *   "data": {
     *     "id": 1,
     *     "name": "Teatro Clásico",
     *     "slug": "teatro-clasico",
     *     "category": "entertainment",
     *     "description": "Lugar para presentaciones teatrales clásicas",
     *     "is_active": true,
     *     "icon": "fa-theater-masks",
     *     "color": "#7B1FA2",
     *     "updated_at": "2024-01-02T00:00:00.000000Z"
     *   }
     * }
     *
     * @response 404 {
     *   "message": "Tipo de venue no encontrado"
     * }
     *
     * @response 422 {
     *   "message": "Los datos proporcionados no son válidos.",
     *   "errors": {...}
     * }
     *
     * @apiResourceModel App\Models\VenueType
     * @authenticated
     */
    public function update(UpdateVenueTypeRequest $request, VenueType $venueType): JsonResponse
    {
        $data = $request->validated();
        
        $venueType->update($data);

        return response()->json([
            'data' => new VenueTypeResource($venueType)
        ]);
    }

    /**
     * Remove the specified venue type
     *
     * Elimina un tipo de venue del sistema.
     *
     * @urlParam venueType integer ID del tipo de venue. Example: 1
     *
     * @response 204 {
     *   "message": "Tipo de venue eliminado exitosamente"
     * }
     *
     * @response 404 {
     *   "message": "Tipo de venue no encontrado"
     * }
     *
     * @authenticated
     */
    public function destroy(VenueType $venueType): JsonResponse
    {
        $venueType->delete();

        return response()->json([
            'message' => 'Tipo de venue eliminado exitosamente'
        ], 204);
    }
}
