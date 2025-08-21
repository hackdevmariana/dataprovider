<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Font;
use App\Http\Resources\V1\FontResource;
use App\Http\Requests\StoreFontRequest;
use App\Http\Requests\UpdateFontRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

/**
 * @group Fonts
 *
 * APIs para la gestión de fuentes tipográficas del sistema.
 * Permite crear, consultar y gestionar fuentes para personalización.
 */
class FontController extends Controller
{
    /**
     * Display a listing of fonts
     *
     * Obtiene una lista paginada de todas las fuentes.
     *
     * @queryParam page int Número de página. Example: 1
     * @queryParam per_page int Cantidad por página (máx 100). Example: 20
     * @queryParam category string Filtrar por categoría (serif, sans-serif, monospace). Example: sans-serif
     * @queryParam is_active boolean Filtrar por fuentes activas. Example: true
     * @queryParam search string Buscar por nombre o familia. Example: Arial
     *
     * @response 200 {
     *   "data": [
     *     {
     *       "id": 1,
     *       "name": "Arial",
     *       "family": "Arial, sans-serif",
     *       "category": "sans-serif",
     *       "is_active": true,
     *       "is_web_safe": true
     *     }
     *   ],
     *   "meta": {...}
     * }
     *
     * @apiResourceCollection App\Http\Resources\V1\FontResource
     * @apiResourceModel App\Models\Font
     */
    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'page' => 'sometimes|integer|min:1',
            'per_page' => 'sometimes|integer|min:1|max:100',
            'category' => 'sometimes|string|in:serif,sans-serif,monospace',
            'is_active' => 'sometimes|boolean',
            'search' => 'sometimes|string|max:255'
        ]);

        $query = Font::query();

        if ($request->has('category')) {
            $query->where('category', $request->category);
        }

        if ($request->has('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        if ($request->has('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('family', 'like', '%' . $request->search . '%');
            });
        }

        $fonts = $query->orderBy('name')
                      ->paginate($request->get('per_page', 20));

        return response()->json([
            'data' => FontResource::collection($fonts),
            'meta' => [
                'current_page' => $fonts->currentPage(),
                'last_page' => $fonts->lastPage(),
                'per_page' => $fonts->perPage(),
                'total' => $fonts->total(),
            ]
        ]);
    }

    /**
     * Store a newly created font
     *
     * Crea una nueva fuente en el sistema.
     *
     * @bodyParam name string required Nombre de la fuente. Example: Arial
     * @bodyParam family string required Familia de la fuente. Example: Arial, sans-serif
     * @bodyParam category string required Categoría de la fuente (serif, sans-serif, monospace). Example: sans-serif
     * @bodyParam description string Descripción de la fuente. Example: Fuente sans-serif estándar
     * @bodyParam is_active boolean Si la fuente está activa. Example: true
     * @bodyParam is_web_safe boolean Si es una fuente web-safe. Example: true
     * @bodyParam file_path string Ruta al archivo de la fuente. Example: fonts/arial.ttf
     *
     * @response 201 {
     *   "data": {
     *     "id": 1,
     *     "name": "Arial",
     *     "family": "Arial, sans-serif",
     *     "category": "sans-serif",
     *     "is_active": true,
     *     "is_web_safe": true,
     *     "created_at": "2024-01-01T00:00:00.000000Z"
     *   }
     * }
     *
     * @response 422 {
     *   "message": "Los datos proporcionados no son válidos.",
     *   "errors": {...}
     * }
     *
     * @apiResourceModel App\Models\Font
     * @authenticated
     */
    public function store(StoreFontRequest $request): JsonResponse
    {
        $data = $request->validated();
        
        $font = Font::create($data);

        return response()->json([
            'data' => new FontResource($font)
        ], 201);
    }

    /**
     * Display the specified font
     *
     * Obtiene los detalles de una fuente específica.
     *
     * @urlParam font integer ID de la fuente. Example: 1
     *
     * @response 200 {
     *   "data": {
     *     "id": 1,
     *     "name": "Arial",
     *     "family": "Arial, sans-serif",
     *     "category": "sans-serif",
     *     "description": "Fuente sans-serif estándar",
     *     "is_active": true,
     *     "is_web_safe": true,
     *     "file_path": "fonts/arial.ttf"
     *   }
     * }
     *
     * @response 404 {
     *   "message": "Fuente no encontrada"
     * }
     *
     * @apiResourceModel App\Models\Font
     */
    public function show(Font $font): JsonResponse
    {
        return response()->json([
            'data' => new FontResource($font)
        ]);
    }

    /**
     * Update the specified font
     *
     * Actualiza una fuente existente.
     *
     * @urlParam font integer ID de la fuente. Example: 1
     * @bodyParam name string Nombre de la fuente. Example: Arial Bold
     * @bodyParam family string Familia de la fuente. Example: Arial Bold, sans-serif
     * @bodyParam category string Categoría de la fuente (serif, sans-serif, monospace). Example: sans-serif
     * @bodyParam description string Descripción de la fuente. Example: Fuente sans-serif en negrita
     * @bodyParam is_active boolean Si la fuente está activa. Example: true
     * @bodyParam is_web_safe boolean Si es una fuente web-safe. Example: true
     * @bodyParam file_path string Ruta al archivo de la fuente. Example: fonts/arial-bold.ttf
     *
     * @response 200 {
     *   "data": {
     *     "id": 1,
     *     "name": "Arial Bold",
     *     "family": "Arial Bold, sans-serif",
     *     "category": "sans-serif",
     *     "description": "Fuente sans-serif en negrita",
     *     "is_active": true,
     *     "updated_at": "2024-01-02T00:00:00.000000Z"
     *   }
     * }
     *
     * @response 404 {
     *   "message": "Fuente no encontrada"
     * }
     *
     * @response 422 {
     *   "message": "Los datos proporcionados no son válidos.",
     *   "errors": {...}
     * }
     *
     * @apiResourceModel App\Models\Font
     * @authenticated
     */
    public function update(UpdateFontRequest $request, Font $font): JsonResponse
    {
        $data = $request->validated();
        
        $font->update($data);

        return response()->json([
            'data' => new FontResource($font)
        ]);
    }

    /**
     * Remove the specified font
     *
     * Elimina una fuente del sistema.
     *
     * @urlParam font integer ID de la fuente. Example: 1
     *
     * @response 204 {
     *   "message": "Fuente eliminada exitosamente"
     * }
     *
     * @response 404 {
     *   "message": "Fuente no encontrada"
     * }
     *
     * @authenticated
     */
    public function destroy(Font $font): JsonResponse
    {
        $font->delete();

        return response()->json([
            'message' => 'Fuente eliminada exitosamente'
        ], 204);
    }
}
