<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Color;
use App\Http\Resources\V1\ColorResource;
use App\Http\Requests\StoreColorRequest;
use App\Http\Requests\UpdateColorRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use OpenApi\Annotations as OA;

/**
 * @group Colors
 *
 * APIs para la gestión de colores del sistema.
 * Permite crear, consultar y gestionar colores para personalización.
 */
/**
 * @OA\Tag(
 *     name="Colores",
 *     description="APIs para la gestión de Colores"
 * )
 */
class ColorController extends Controller
{
    /**
     * Display a listing of colors
     *
     * Obtiene una lista paginada de todos los colores.
     *
     * @queryParam page int Número de página. Example: 1
     * @queryParam per_page int Cantidad por página (máx 100). Example: 20
     * @queryParam category string Filtrar por categoría (primary, secondary, accent). Example: primary
     * @queryParam is_active boolean Filtrar por colores activos. Example: true
     * @queryParam search string Buscar por nombre o código. Example: azul
     *
     * @response 200 {
     *   "data": [
     *     {
     *       "id": 1,
     *       "name": "Azul Principal",
     *       "hex_code": "#2196F3",
     *       "rgb_code": "33, 150, 243",
     *       "category": "primary",
     *       "is_active": true
     *     }
     *   ],
     *   "meta": {...}
     * }
     *
     * @apiResourceCollection App\Http\Resources\V1\ColorResource
     * @apiResourceModel App\Models\Color
     */
    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'page' => 'sometimes|integer|min:1',
            'per_page' => 'sometimes|integer|min:1|max:100',
            'category' => 'sometimes|string|in:primary,secondary,accent',
            'is_active' => 'sometimes|boolean',
            'search' => 'sometimes|string|max:255'
        ]);

        $query = Color::query();

        if ($request->has('category')) {
            $query->where('category', $request->category);
        }

        if ($request->has('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        if ($request->has('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('hex_code', 'like', '%' . $request->search . '%');
            });
        }

        $colors = $query->orderBy('category')
                       ->orderBy('name')
                       ->paginate($request->get('per_page', 20));

        return response()->json([
            'data' => ColorResource::collection($colors),
            'meta' => [
                'current_page' => $colors->currentPage(),
                'last_page' => $colors->lastPage(),
                'per_page' => $colors->perPage(),
                'total' => $colors->total(),
            ]
        ]);
    }

    /**
     * Store a newly created color
     *
     * Crea un nuevo color en el sistema.
     *
     * @bodyParam name string required Nombre del color. Example: Azul Principal
     * @bodyParam hex_code string required Código hexadecimal del color. Example: #2196F3
     * @bodyParam rgb_code string Código RGB del color. Example: 33, 150, 243
     * @bodyParam category string required Categoría del color (primary, secondary, accent). Example: primary
     * @bodyParam description string Descripción del color. Example: Color azul principal para la interfaz
     * @bodyParam is_active boolean Si el color está activo. Example: true
     *
     * @response 201 {
     *   "data": {
     *     "id": 1,
     *       "name": "Azul Principal",
     *       "hex_code": "#2196F3",
     *       "rgb_code": "33, 150, 243",
     *       "category": "primary",
     *       "is_active": true,
     *       "created_at": "2024-01-01T00:00:00.000000Z"
     *   }
     * }
     *
     * @response 422 {
     *   "message": "Los datos proporcionados no son válidos.",
     *   "errors": {...}
     * }
     *
     * @apiResourceModel App\Models\Color
     * @authenticated
     */
    public function store(StoreColorRequest $request): JsonResponse
    {
        $data = $request->validated();
        
        $color = Color::create($data);

        return response()->json([
            'data' => new ColorResource($color)
        ], 201);
    }

    /**
     * Display the specified color
     *
     * Obtiene los detalles de un color específico.
     *
     * @urlParam color integer ID del color. Example: 1
     *
     * @response 200 {
     *   "data": {
     *     "id": 1,
     *       "name": "Azul Principal",
     *       "hex_code": "#2196F3",
     *       "rgb_code": "33, 150, 243",
     *       "category": "primary",
     *       "description": "Color azul principal para la interfaz",
     *       "is_active": true
     *   }
     * }
     *
     * @response 404 {
     *   "message": "Color no encontrado"
     * }
     *
     * @apiResourceModel App\Models\Color
     */
    public function show(Color $color): JsonResponse
    {
        return response()->json([
            'data' => new ColorResource($color)
        ]);
    }

    /**
     * Update the specified color
     *
     * Actualiza un color existente.
     *
     * @urlParam color integer ID del color. Example: 1
     * @bodyParam name string Nombre del color. Example: Azul Principal Actualizado
     * @bodyParam hex_code string Código hexadecimal del color. Example: #1976D2
     * @bodyParam rgb_code string Código RGB del color. Example: 25, 118, 210
     * @bodyParam category string Categoría del color (primary, secondary, accent). Example: primary
     * @bodyParam description string Descripción del color. Example: Color azul principal actualizado
     * @bodyParam is_active boolean Si el color está activo. Example: true
     *
     * @response 200 {
     *   "data": {
     *     "id": 1,
     *       "name": "Azul Principal Actualizado",
     *       "hex_code": "#1976D2",
     *       "rgb_code": "25, 118, 210",
     *       "category": "primary",
     *       "updated_at": "2024-01-02T00:00:00.000000Z"
     *   }
     * }
     *
     * @response 404 {
     *   "message": "Color no encontrado"
     * }
     *
     * @response 422 {
     *   "message": "Los datos proporcionados no son válidos.",
     *   "errors": {...}
     * }
     *
     * @apiResourceModel App\Models\Color
     * @authenticated
     */
    public function update(UpdateColorRequest $request, Color $color): JsonResponse
    {
        $data = $request->validated();
        
        $color->update($data);

        return response()->json([
            'data' => new ColorResource($color)
        ]);
    }

    /**
     * Remove the specified color
     *
     * Elimina un color del sistema.
     *
     * @urlParam color integer ID del color. Example: 1
     *
     * @response 204 {
     *   "message": "Color eliminado exitosamente"
     * }
     *
     * @response 404 {
     *   "message": "Color no encontrado"
     * }
     *
     * @authenticated
     */
    public function destroy(Color $color): JsonResponse
    {
        $color->delete();

        return response()->json([
            'message' => 'Color eliminado exitosamente'
        ], 204);
    }
}
