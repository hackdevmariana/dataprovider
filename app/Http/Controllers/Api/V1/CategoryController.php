<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Http\Resources\V1\CategoryResource;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

/**
 * @group Categories
 *
 * APIs para la gestión de categorías del sistema.
 * Permite crear, consultar y gestionar categorías para organizar contenido.
 */
class CategoryController extends Controller
{
    /**
     * Display a listing of categories
     *
     * Obtiene una lista paginada de todas las categorías.
     *
     * @queryParam page int Número de página. Example: 1
     * @queryParam per_page int Cantidad por página (máx 100). Example: 20
     * @queryParam parent_id integer Filtrar por categoría padre. Example: 1
     * @queryParam is_active boolean Filtrar por categorías activas. Example: true
     * @queryParam search string Buscar por nombre o descripción. Example: tecnología
     *
     * @response 200 {
     *   "data": [
     *     {
     *       "id": 1,
     *       "name": "Tecnología",
     *       "slug": "tecnologia",
     *       "description": "Categoría para contenido tecnológico",
     *       "parent_id": null,
     *       "is_active": true,
     *       "sort_order": 1,
     *       "children_count": 5
     *     }
     *   ],
     *   "meta": {...}
     * }
     *
     * @apiResourceCollection App\Http\Resources\V1\CategoryResource
     * @apiResourceModel App\Models\Category
     */
    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'page' => 'sometimes|integer|min:1',
            'per_page' => 'sometimes|integer|min:1|max:100',
            'parent_id' => 'sometimes|integer|exists:categories,id',
            'is_active' => 'sometimes|boolean',
            'search' => 'sometimes|string|max:255'
        ]);

        $query = Category::with(['parent', 'children']);

        if ($request->has('parent_id')) {
            $query->where('parent_id', $request->parent_id);
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

        $categories = $query->orderBy('sort_order')
                           ->orderBy('name')
                           ->paginate($request->get('per_page', 20));

        return response()->json([
            'data' => CategoryResource::collection($categories),
            'meta' => [
                'current_page' => $categories->currentPage(),
                'last_page' => $categories->lastPage(),
                'per_page' => $categories->perPage(),
                'total' => $categories->total(),
            ]
        ]);
    }

    /**
     * Store a newly created category
     *
     * Crea una nueva categoría en el sistema.
     *
     * @bodyParam name string required Nombre de la categoría. Example: Tecnología
     * @bodyParam slug string Slug único de la categoría. Example: tecnologia
     * @bodyParam description string Descripción de la categoría. Example: Categoría para contenido tecnológico
     * @bodyParam parent_id integer ID de la categoría padre. Example: null
     * @bodyParam is_active boolean Si la categoría está activa. Example: true
     * @bodyParam sort_order integer Orden de clasificación. Example: 1
     * @bodyParam color string Color hexadecimal de la categoría. Example: #FF5733
     * @bodyParam icon string Icono de la categoría. Example: fa-laptop
     *
     * @response 201 {
     *   "data": {
     *     "id": 1,
     *     "name": "Tecnología",
     *     "slug": "tecnologia",
     *     "description": "Categoría para contenido tecnológico",
     *     "parent_id": null,
     *     "is_active": true,
     *     "sort_order": 1,
     *     "created_at": "2024-01-01T00:00:00.000000Z"
     *   }
     * }
     *
     * @response 422 {
     *   "message": "Los datos proporcionados no son válidos.",
     *   "errors": {...}
     * }
     *
     * @apiResourceModel App\Models\Category
     * @authenticated
     */
    public function store(StoreCategoryRequest $request): JsonResponse
    {
        $data = $request->validated();
        
        $category = Category::create($data);

        return response()->json([
            'data' => new CategoryResource($category->load(['parent', 'children']))
        ], 201);
    }

    /**
     * Display the specified category
     *
     * Obtiene los detalles de una categoría específica.
     *
     * @urlParam category integer ID de la categoría. Example: 1
     *
     * @response 200 {
     *   "data": {
     *     "id": 1,
     *     "name": "Tecnología",
     *     "slug": "tecnologia",
     *     "description": "Categoría para contenido tecnológico",
     *     "parent_id": null,
     *     "is_active": true,
     *     "sort_order": 1,
     *     "color": "#FF5733",
     *     "icon": "fa-laptop",
     *     "parent": null,
     *     "children": [...]
     *   }
     * }
     *
     * @response 404 {
     *   "message": "Categoría no encontrada"
     * }
     *
     * @apiResourceModel App\Models\Category
     */
    public function show(Category $category): JsonResponse
    {
        return response()->json([
            'data' => new CategoryResource($category->load(['parent', 'children']))
        ]);
    }

    /**
     * Update the specified category
     *
     * Actualiza una categoría existente.
     *
     * @urlParam category integer ID de la categoría. Example: 1
     * @bodyParam name string Nombre de la categoría. Example: Tecnología Avanzada
     * @bodyParam slug string Slug único de la categoría. Example: tecnologia-avanzada
     * @bodyParam description string Descripción de la categoría. Example: Categoría para contenido tecnológico avanzado
     * @bodyParam parent_id integer ID de la categoría padre. Example: null
     * @bodyParam is_active boolean Si la categoría está activa. Example: true
     * @bodyParam sort_order integer Orden de clasificación. Example: 2
     * @bodyParam color string Color hexadecimal de la categoría. Example: #33FF57
     * @bodyParam icon string Icono de la categoría. Example: fa-microchip
     *
     * @response 200 {
     *   "data": {
     *     "id": 1,
     *     "name": "Tecnología Avanzada",
     *     "slug": "tecnologia-avanzada",
     *     "description": "Categoría para contenido tecnológico avanzado",
     *     "parent_id": null,
     *     "is_active": true,
     *     "sort_order": 2,
     *     "updated_at": "2024-01-02T00:00:00.000000Z"
     *   }
     * }
     *
     * @response 404 {
     *   "message": "Categoría no encontrada"
     * }
     *
     * @response 422 {
     *   "message": "Los datos proporcionados no son válidos.",
     *   "errors": {...}
     * }
     *
     * @apiResourceModel App\Models\Category
     * @authenticated
     */
    public function update(UpdateCategoryRequest $request, Category $category): JsonResponse
    {
        $data = $request->validated();
        
        $category->update($data);

        return response()->json([
            'data' => new CategoryResource($category->load(['parent', 'children']))
        ]);
    }

    /**
     * Remove the specified category
     *
     * Elimina una categoría del sistema.
     *
     * @urlParam category integer ID de la categoría. Example: 1
     *
     * @response 204 {
     *   "message": "Categoría eliminada exitosamente"
     * }
     *
     * @response 404 {
     *   "message": "Categoría no encontrada"
     * }
     *
     * @response 422 {
     *   "message": "No se puede eliminar la categoría porque tiene elementos asociados",
     *   "errors": {...}
     * }
     *
     * @authenticated
     */
    public function destroy(Category $category): JsonResponse
    {
        // Verificar si la categoría tiene hijos
        if ($category->children()->count() > 0) {
            return response()->json([
                'message' => 'No se puede eliminar la categoría porque tiene subcategorías',
                'errors' => [
                    'category' => ['La categoría tiene subcategorías asociadas']
                ]
            ], 422);
        }

        $category->delete();

        return response()->json([
            'message' => 'Categoría eliminada exitosamente'
        ], 204);
    }
}
