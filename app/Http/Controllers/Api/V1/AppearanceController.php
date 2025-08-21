<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Appearance;
use App\Http\Resources\V1\AppearanceResource;
use App\Http\Requests\StoreAppearanceRequest;
use App\Http\Requests\UpdateAppearanceRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

/**
 * @group Appearances
 *
 * APIs para la gestión de apariencias del sistema.
 * Permite crear, consultar y gestionar configuraciones de apariencia.
 */
class AppearanceController extends Controller
{
    /**
     * Display a listing of appearances
     *
     * Obtiene una lista paginada de todas las apariencias.
     *
     * @queryParam page int Número de página. Example: 1
     * @queryParam per_page int Cantidad por página (máx 100). Example: 20
     * @queryParam theme string Filtrar por tema (light, dark, custom). Example: light
     * @queryParam is_active boolean Filtrar por apariencias activas. Example: true
     * @queryParam search string Buscar por nombre o descripción. Example: moderno
     *
     * @response 200 {
     *   "data": [
     *     {
     *       "id": 1,
     *       "name": "Tema Moderno",
     *       "theme": "light",
     *       "description": "Tema claro con diseño moderno",
     *       "is_active": true,
     *       "primary_color": "#2196F3"
     *     }
     *   ],
     *   "meta": {...}
     * }
     *
     * @apiResourceCollection App\Http\Resources\V1\AppearanceResource
     * @apiResourceModel App\Models\Appearance
     */
    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'page' => 'sometimes|integer|min:1',
            'per_page' => 'sometimes|integer|min:1|max:100',
            'theme' => 'sometimes|string|in:light,dark,custom',
            'is_active' => 'sometimes|boolean',
            'search' => 'sometimes|string|max:255'
        ]);

        $query = Appearance::query();

        if ($request->has('theme')) {
            $query->where('theme', $request->theme);
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

        $appearances = $query->orderBy('theme')
                            ->orderBy('name')
                            ->paginate($request->get('per_page', 20));

        return response()->json([
            'data' => AppearanceResource::collection($appearances),
            'meta' => [
                'current_page' => $appearances->currentPage(),
                'last_page' => $appearances->lastPage(),
                'per_page' => $appearances->perPage(),
                'total' => $appearances->total(),
            ]
        ]);
    }

    /**
     * Store a newly created appearance
     *
     * Crea una nueva apariencia en el sistema.
     *
     * @bodyParam name string required Nombre de la apariencia. Example: Tema Moderno
     * @bodyParam theme string required Tema (light, dark, custom). Example: light
     * @bodyParam description string Descripción de la apariencia. Example: Tema claro con diseño moderno
     * @bodyParam primary_color string Color primario hexadecimal. Example: #2196F3
     * @bodyParam secondary_color string Color secundario hexadecimal. Example: #FFC107
     * @bodyParam accent_color string Color de acento hexadecimal. Example: #4CAF50
     * @bodyParam background_color string Color de fondo hexadecimal. Example: #FFFFFF
     * @bodyParam text_color string Color del texto hexadecimal. Example: #212121
     * @bodyParam is_active boolean Si la apariencia está activa. Example: true
     * @bodyParam metadata object Metadatos adicionales (JSON). Example: {"font_family": "Roboto", "border_radius": "8px"}
     *
     * @response 201 {
     *   "data": {
     *     "id": 1,
     *     "name": "Tema Moderno",
     *     "theme": "light",
     *     "description": "Tema claro con diseño moderno",
     *     "primary_color": "#2196F3",
     *     "is_active": true,
     *     "created_at": "2024-01-01T00:00:00.000000Z"
     *   }
     * }
     *
     * @response 422 {
     *   "message": "Los datos proporcionados no son válidos.",
     *   "errors": {...}
     * }
     *
     * @apiResourceModel App\Models\Appearance
     * @authenticated
     */
    public function store(StoreAppearanceRequest $request): JsonResponse
    {
        $data = $request->validated();
        
        $appearance = Appearance::create($data);

        return response()->json([
            'data' => new AppearanceResource($appearance)
        ], 201);
    }

    /**
     * Display the specified appearance
     *
     * Obtiene los detalles de una apariencia específica.
     *
     * @urlParam appearance integer ID de la apariencia. Example: 1
     *
     * @response 200 {
     *   "data": {
     *     "id": 1,
     *     "name": "Tema Moderno",
     *     "theme": "light",
     *     "description": "Tema claro con diseño moderno",
     *     "primary_color": "#2196F3",
     *     "secondary_color": "#FFC107",
     *     "accent_color": "#4CAF50",
     *     "background_color": "#FFFFFF",
     *     "text_color": "#212121",
     *     "is_active": true,
     *     "metadata": {
     *       "font_family": "Roboto",
     *       "border_radius": "8px"
     *     }
     *   }
     * }
     *
     * @response 404 {
     *   "message": "Apariencia no encontrada"
     * }
     *
     * @apiResourceModel App\Models\Appearance
     */
    public function show(Appearance $appearance): JsonResponse
    {
        return response()->json([
            'data' => new AppearanceResource($appearance)
        ]);
    }

    /**
     * Update the specified appearance
     *
     * Actualiza una apariencia existente.
     *
     * @urlParam appearance integer ID de la apariencia. Example: 1
     * @bodyParam name string Nombre de la apariencia. Example: Tema Moderno Actualizado
     * @bodyParam theme string Tema (light, dark, custom). Example: light
     * @bodyParam description string Descripción de la apariencia. Example: Tema claro con diseño moderno actualizado
     * @bodyParam primary_color string Color primario hexadecimal. Example: #1976D2
     * @bodyParam secondary_color string Color secundario hexadecimal. Example: #FF9800
     * @bodyParam accent_color string Color de acento hexadecimal. Example: #388E3C
     * @bodyParam background_color string Color de fondo hexadecimal. Example: #FAFAFA
     * @bodyParam text_color string Color del texto hexadecimal. Example: #000000
     * @bodyParam is_active boolean Si la apariencia está activa. Example: true
     * @bodyParam metadata object Metadatos adicionales (JSON). Example: {"font_family": "Inter", "border_radius": "12px"}
     *
     * @response 200 {
     *   "data": {
     *     "id": 1,
     *     "name": "Tema Moderno Actualizado",
     *     "theme": "light",
     *     "description": "Tema claro con diseño moderno actualizado",
     *     "primary_color": "#1976D2",
     *     "secondary_color": "#FF9800",
     *     "accent_color": "#388E3C",
     *     "background_color": "#FAFAFA",
     *     "text_color": "#000000",
     *     "is_active": true,
     *     "updated_at": "2024-01-02T00:00:00.000000Z"
     *   }
     * }
     *
     * @response 404 {
     *   "message": "Apariencia no encontrada"
     * }
     *
     * @response 422 {
     *   "message": "Los datos proporcionados no son válidos.",
     *   "errors": {...}
     * }
     *
     * @apiResourceModel App\Models\Appearance
     * @authenticated
     */
    public function update(UpdateAppearanceRequest $request, Appearance $appearance): JsonResponse
    {
        $data = $request->validated();
        
        $appearance->update($data);

        return response()->json([
            'data' => new AppearanceResource($appearance)
        ]);
    }

    /**
     * Remove the specified appearance
     *
     * Elimina una apariencia del sistema.
     *
     * @urlParam appearance integer ID de la apariencia. Example: 1
     *
     * @response 204 {
     *   "message": "Apariencia eliminada exitosamente"
     * }
     *
     * @response 404 {
     *   "message": "Apariencia no encontrada"
     * }
     *
     * @authenticated
     */
    public function destroy(Appearance $appearance): JsonResponse
    {
        $appearance->delete();

        return response()->json([
            'message' => 'Apariencia eliminada exitosamente'
        ], 204);
    }
}
