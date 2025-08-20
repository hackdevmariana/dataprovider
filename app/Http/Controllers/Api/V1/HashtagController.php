<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\HashtagResource;
use App\Models\Hashtag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * @group Hashtags
 *
 * APIs para la gestión del sistema de hashtags.
 * Permite crear, buscar y gestionar hashtags para categorizar
 * y organizar contenido en la plataforma.
 */
class HashtagController extends Controller
{
    /**
     * Display a listing of hashtags
     *
     * Obtiene una lista de hashtags con opciones de filtrado y búsqueda.
     *
     * @queryParam search string Término de búsqueda en nombre o descripción. Example: energia
     * @queryParam category string Categoría del hashtag. Example: technology
     * @queryParam language string Idioma del hashtag. Example: es
     * @queryParam is_trending boolean Filtrar hashtags trending. Example: true
     * @queryParam is_featured boolean Filtrar hashtags destacados. Example: true
     * @queryParam min_posts_count int Cantidad mínima de posts. Example: 10
     * @queryParam sort string Ordenamiento (popular, recent, alphabetical, posts_count). Example: popular
     * @queryParam page int Número de página. Example: 1
     * @queryParam per_page int Cantidad por página (máx 100). Example: 15
     *
     * @apiResourceCollection App\Http\Resources\V1\HashtagResource
     * @apiResourceModel App\Models\Hashtag
     */
    public function index(Request $request)
    {
        $query = Hashtag::with(['category', 'language'])
            ->when($request->search, fn($q, $search) => $q->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            }))
            ->when($request->category, fn($q, $category) => $q->where('category', $category))
            ->when($request->language, fn($q, $language) => $q->where('language', $language))
            ->when($request->has('is_trending'), fn($q) => $q->where('is_trending', $request->boolean('is_trending')))
            ->when($request->has('is_featured'), fn($q) => $q->where('is_featured', $request->boolean('is_featured')))
            ->when($request->min_posts_count, fn($q, $count) => $q->where('posts_count', '>=', $count));

        // Aplicar ordenamiento
        switch ($request->get('sort', 'popular')) {
            case 'recent':
                $query->orderBy('created_at', 'desc');
                break;
            case 'alphabetical':
                $query->orderBy('name', 'asc');
                break;
            case 'posts_count':
                $query->orderBy('posts_count', 'desc');
                break;
            default: // popular
                $query->orderBy('popularity_score', 'desc')->orderBy('posts_count', 'desc');
        }

        $perPage = min($request->get('per_page', 15), 100);
        $hashtags = $query->paginate($perPage);

        return HashtagResource::collection($hashtags);
    }

    /**
     * Store a new hashtag
     *
     * Crea un nuevo hashtag en la plataforma.
     *
     * @bodyParam name string required Nombre del hashtag (sin #). Example: energias-renovables
     * @bodyParam display_name string Nombre de visualización. Example: Energías Renovables
     * @bodyParam description string Descripción del hashtag. Example: Contenido sobre energías renovables
     * @bodyParam category string Categoría del hashtag. Example: technology
     * @bodyParam language string Idioma del hashtag. Example: es
     * @bodyParam color string Color del hashtag (hex). Example: #00ff00
     * @bodyParam icon string Icono del hashtag. Example: solar-panel
     * @bodyParam is_trending boolean Si es trending. Default: false. Example: false
     * @bodyParam is_featured boolean Si es destacado. Default: false. Example: false
     * @bodyParam metadata array Metadatos adicionales. Example: {"energy_type": "solar"}
     *
     * @apiResource App\Http\Resources\V1\HashtagResource
     * @apiResourceModel App\Models\Hashtag
     *
     * @response 201 {"data": {...}, "message": "Hashtag creado exitosamente"}
     * @response 409 {"message": "El hashtag ya existe"}
     * @response 422 {"message": "Datos inválidos", "errors": {...}}
     * @authenticated
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100|unique:hashtags,name',
            'display_name' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:1000',
            'category' => 'nullable|string|max:100',
            'language' => 'nullable|string|max:10',
            'color' => 'nullable|string|max:7|regex:/^#[0-9A-Fa-f]{6}$/',
            'icon' => 'nullable|string|max:100',
            'is_trending' => 'boolean',
            'is_featured' => 'boolean',
            'metadata' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Datos inválidos',
                'errors' => $validator->errors()
            ], 422);
        }

        // Normalizar el nombre del hashtag
        $name = strtolower(trim($request->name));
        $name = preg_replace('/[^a-z0-9-]/', '-', $name);
        $name = preg_replace('/-+/', '-', $name);
        $name = trim($name, '-');

        // Verificar si ya existe
        $existingHashtag = Hashtag::where('name', $name)->first();
        if ($existingHashtag) {
            return response()->json([
                'message' => 'El hashtag ya existe',
                'data' => new HashtagResource($existingHashtag)
            ], 409);
        }

        $hashtag = Hashtag::create(array_merge($validator->validated(), [
            'name' => $name,
            'display_name' => $request->get('display_name', ucwords(str_replace('-', ' ', $name))),
            'is_trending' => $request->get('is_trending', false),
            'is_featured' => $request->get('is_featured', false),
            'posts_count' => 0,
            'popularity_score' => 0,
        ]));

        $hashtag->load(['category', 'language']);

        return response()->json([
            'data' => new HashtagResource($hashtag),
            'message' => 'Hashtag creado exitosamente'
        ], 201);
    }

    /**
     * Display the specified hashtag
     *
     * Muestra un hashtag específico con estadísticas.
     *
     * @urlParam hashtag int required ID del hashtag. Example: 1
     *
     * @apiResource App\Http\Resources\V1\HashtagResource
     * @apiResourceModel App\Models\Hashtag
     *
     * @response 404 {"message": "Hashtag no encontrado"}
     */
    public function show(Hashtag $hashtag)
    {
        $hashtag->load(['category', 'language']);
        return new HashtagResource($hashtag);
    }

    /**
     * Update the specified hashtag
     *
     * Actualiza un hashtag existente.
     *
     * @urlParam hashtag int required ID del hashtag. Example: 1
     * @bodyParam display_name string Nuevo nombre de visualización. Example: Nuevo nombre
     * @bodyParam description string Nueva descripción. Example: Nueva descripción
     * @bodyParam category string Nueva categoría. Example: new-category
     * @bodyParam color string Nuevo color. Example: #ff0000
     * @bodyParam icon string Nuevo icono. Example: new-icon
     * @bodyParam is_trending boolean Si es trending. Example: true
     * @bodyParam is_featured boolean Si es destacado. Example: true
     * @bodyParam metadata array Metadatos actualizados. Example: {"updated": true}
     *
     * @apiResource App\Http\Resources\V1\HashtagResource
     * @apiResourceModel App\Models\Hashtag
     *
     * @response 403 {"message": "No autorizado"}
     * @response 422 {"message": "Datos inválidos", "errors": {...}}
     * @authenticated
     */
    public function update(Request $request, Hashtag $hashtag)
    {
        // Solo administradores pueden editar hashtags
        $user = auth()->guard('sanctum')->user();
        if (!$user || !$user->hasRole('admin')) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        $validator = Validator::make($request->all(), [
            'display_name' => 'string|max:255',
            'description' => 'nullable|string|max:1000',
            'category' => 'nullable|string|max:100',
            'color' => 'nullable|string|max:7|regex:/^#[0-9A-Fa-f]{6}$/',
            'icon' => 'nullable|string|max:100',
            'is_trending' => 'boolean',
            'is_featured' => 'boolean',
            'metadata' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Datos inválidos',
                'errors' => $validator->errors()
            ], 422);
        }

        $hashtag->update($validator->validated());
        $hashtag->load(['category', 'language']);

        return new HashtagResource($hashtag);
    }

    /**
     * Remove the specified hashtag
     *
     * Elimina un hashtag (solo si no tiene posts asociados).
     *
     * @urlParam hashtag int required ID del hashtag. Example: 1
     *
     * @response 200 {"message": "Hashtag eliminado exitosamente"}
     * @response 403 {"message": "No autorizado"}
     * @response 422 {"message": "No se puede eliminar un hashtag con posts"}
     * @authenticated
     */
    public function destroy(Hashtag $hashtag)
    {
        // Solo administradores pueden eliminar hashtags
        $user = auth()->guard('sanctum')->user();
        if (!$user || !$user->hasRole('admin')) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        // Verificar que no tenga posts asociados
        if ($hashtag->posts_count > 0) {
            return response()->json([
                'message' => 'No se puede eliminar un hashtag con posts asociados'
            ], 422);
        }

        $hashtag->delete();

        return response()->json(['message' => 'Hashtag eliminado exitosamente']);
    }

    /**
     * Search hashtags
     *
     * Búsqueda avanzada de hashtags con autocompletado.
     *
     * @queryParam q string required Término de búsqueda. Example: energia
     * @queryParam limit int Cantidad máxima de resultados. Example: 10
     * @queryParam category string Filtrar por categoría. Example: technology
     * @queryParam trending boolean Solo hashtags trending. Example: true
     *
     * @response 200 {"data": [{"id": 1, "name": "energia-solar", "display_name": "Energía Solar"}]}
     */
    public function search(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'q' => 'required|string|min:2|max:100',
            'limit' => 'integer|min:1|max:50',
            'category' => 'nullable|string|max:100',
            'trending' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Datos inválidos',
                'errors' => $validator->errors()
            ], 422);
        }

        $query = Hashtag::select(['id', 'name', 'display_name', 'posts_count', 'popularity_score'])
            ->when($request->category, fn($q, $category) => $q->where('category', $category))
            ->when($request->trending, fn($q) => $q->where('is_trending', true))
            ->where(function($q) use ($request) {
                $q->where('name', 'like', "%{$request->q}%")
                  ->orWhere('display_name', 'like', "%{$request->q}%");
            })
            ->orderBy('popularity_score', 'desc')
            ->orderBy('posts_count', 'desc')
            ->limit($request->get('limit', 10));

        $hashtags = $query->get();

        return response()->json(['data' => $hashtags]);
    }

    /**
     * Get trending hashtags
     *
     * Obtiene los hashtags más populares y trending.
     *
     * @queryParam limit int Cantidad de hashtags. Example: 10
     * @queryParam category string Filtrar por categoría. Example: technology
     * @queryParam period string Período (day, week, month). Example: week
     *
     * @apiResourceCollection App\Http\Resources\V1\HashtagResource
     * @apiResourceModel App\Models\Hashtag
     */
    public function trending(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'limit' => 'integer|min:1|max:50',
            'category' => 'nullable|string|max:100',
            'period' => 'in:day,week,month',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Datos inválidos',
                'errors' => $validator->errors()
            ], 422);
        }

        $query = Hashtag::with(['category', 'language'])
            ->where('is_trending', true)
            ->when($request->category, fn($q, $category) => $q->where('category', $category))
            ->orderBy('popularity_score', 'desc')
            ->orderBy('posts_count', 'desc')
            ->limit($request->get('limit', 10));

        $hashtags = $query->get();

        return HashtagResource::collection($hashtags);
    }
}
