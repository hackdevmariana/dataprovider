<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\UserBookmarkResource;
use App\Models\UserBookmark;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

/**
 * @group User Bookmarks
 *
 * APIs para la gestión de marcadores/favoritos de usuarios.
 * Permite guardar contenido para acceso rápido posterior.
 */
class UserBookmarkController extends Controller
{
    /**
     * Display a listing of bookmarks
     *
     * Obtiene una lista paginada de marcadores con opciones de filtrado.
     *
     * @queryParam user_id int ID del usuario para filtrar marcadores. Example: 1
     * @queryParam bookmarkable_type string Tipo de contenido marcado. Example: App\Models\Topic
     * @queryParam collection_name string Nombre de la colección. Example: favoritos
     * @queryParam visibility string Visibilidad del marcador (private, public, shared). Example: public
     * @queryParam is_favorite boolean Filtrar por marcadores favoritos. Example: true
     * @queryParam page int Número de página. Example: 1
     * @queryParam per_page int Cantidad por página (máx 100). Example: 15
     *
     * @apiResourceCollection App\Http\Resources\V1\UserBookmarkResource
     * @apiResourceModel App\Models\UserBookmark
     */
    public function index(Request $request)
    {
        $query = UserBookmark::with(['user'])
            ->when($request->user_id, fn($q, $userId) => $q->where('user_id', $userId))
            ->when($request->bookmarkable_type, fn($q, $type) => $q->where('bookmarkable_type', $type))
            ->when($request->collection_name, fn($q, $collection) => $q->where('collection_name', $collection))
            ->when($request->visibility, fn($q, $visibility) => $q->where('visibility', $visibility))
            ->when($request->has('is_favorite'), fn($q) => $q->where('is_favorite', $request->boolean('is_favorite')))
            ->orderBy('bookmarked_at', 'desc');

        $perPage = min($request->get('per_page', 15), 100);
        $bookmarks = $query->paginate($perPage);

        return UserBookmarkResource::collection($bookmarks);
    }

    /**
     * Store a new bookmark
     *
     * Crea un nuevo marcador para el usuario autenticado.
     *
     * @bodyParam bookmarkable_type string required Tipo de contenido a marcar. Example: App\Models\Topic
     * @bodyParam bookmarkable_id int required ID del contenido a marcar. Example: 1
     * @bodyParam title string Título personalizado del marcador. Example: Mi tema favorito
     * @bodyParam notes string Notas sobre el marcador. Example: Información muy útil
     * @bodyParam collection_name string Nombre de la colección. Example: favoritos
     * @bodyParam tags array Etiquetas del marcador. Example: ["energía", "solar"]
     * @bodyParam visibility string Visibilidad (private, public, shared). Default: private. Example: private
     * @bodyParam is_favorite boolean Si es marcador favorito. Default: false. Example: false
     *
     * @apiResource App\Http\Resources\V1\UserBookmarkResource
     * @apiResourceModel App\Models\UserBookmark
     *
     * @response 201 {"data": {...}, "message": "Marcador creado exitosamente"}
     * @response 422 {"message": "Datos inválidos", "errors": {...}}
     * @authenticated
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'bookmarkable_type' => 'required|string|max:255',
            'bookmarkable_id' => 'required|integer',
            'title' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:1000',
            'collection_name' => 'nullable|string|max:100',
            'tags' => 'nullable|array',
            'visibility' => 'in:private,public,shared',
            'is_favorite' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Datos inválidos',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = Auth::guard('sanctum')->user();
        
        // Verificar si ya existe el marcador
        $existing = UserBookmark::where('user_id', $user->id)
            ->where('bookmarkable_type', $request->bookmarkable_type)
            ->where('bookmarkable_id', $request->bookmarkable_id)
            ->first();

        if ($existing) {
            return response()->json([
                'message' => 'El contenido ya está marcado',
                'data' => new UserBookmarkResource($existing)
            ], 409);
        }

        $bookmark = UserBookmark::create(array_merge($validator->validated(), [
            'user_id' => $user->id,
            'bookmarked_at' => now(),
        ]));

        $bookmark->load(['user']);

        return response()->json([
            'data' => new UserBookmarkResource($bookmark),
            'message' => 'Marcador creado exitosamente'
        ], 201);
    }

    /**
     * Display the specified bookmark
     *
     * Muestra un marcador específico.
     *
     * @urlParam userBookmark int required ID del marcador. Example: 1
     *
     * @apiResource App\Http\Resources\V1\UserBookmarkResource
     * @apiResourceModel App\Models\UserBookmark
     *
     * @response 404 {"message": "Marcador no encontrado"}
     * @authenticated
     */
    public function show(UserBookmark $userBookmark)
    {
        $user = Auth::guard('sanctum')->user();

        // Solo mostrar marcadores propios o públicos
        if ($userBookmark->visibility !== 'public' && $userBookmark->user_id !== $user->id) {
            return response()->json(['message' => 'Marcador no encontrado'], 404);
        }

        $userBookmark->load(['user']);
        
        // Actualizar último acceso
        if ($userBookmark->user_id === $user->id) {
            $userBookmark->update(['last_accessed_at' => now()]);
        }

        return new UserBookmarkResource($userBookmark);
    }

    /**
     * Update the specified bookmark
     *
     * Actualiza un marcador existente.
     *
     * @urlParam userBookmark int required ID del marcador. Example: 1
     * @bodyParam title string Título personalizado. Example: Nuevo título
     * @bodyParam notes string Notas del marcador. Example: Notas actualizadas
     * @bodyParam collection_name string Colección. Example: nueva-coleccion
     * @bodyParam tags array Etiquetas. Example: ["tag1", "tag2"]
     * @bodyParam visibility string Visibilidad. Example: public
     * @bodyParam is_favorite boolean Si es favorito. Example: true
     *
     * @apiResource App\Http\Resources\V1\UserBookmarkResource
     * @apiResourceModel App\Models\UserBookmark
     *
     * @response 403 {"message": "No autorizado"}
     * @response 422 {"message": "Datos inválidos", "errors": {...}}
     * @authenticated
     */
    public function update(Request $request, UserBookmark $userBookmark)
    {
        $user = Auth::guard('sanctum')->user();

        if ($userBookmark->user_id !== $user->id) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:1000',
            'collection_name' => 'nullable|string|max:100',
            'tags' => 'nullable|array',
            'visibility' => 'in:private,public,shared',
            'is_favorite' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Datos inválidos',
                'errors' => $validator->errors()
            ], 422);
        }

        $userBookmark->update($validator->validated());
        $userBookmark->load(['user']);

        return new UserBookmarkResource($userBookmark);
    }

    /**
     * Remove the specified bookmark
     *
     * Elimina un marcador.
     *
     * @urlParam userBookmark int required ID del marcador. Example: 1
     *
     * @response 200 {"message": "Marcador eliminado exitosamente"}
     * @response 403 {"message": "No autorizado"}
     * @authenticated
     */
    public function destroy(UserBookmark $userBookmark)
    {
        $user = Auth::guard('sanctum')->user();

        if ($userBookmark->user_id !== $user->id) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        $userBookmark->delete();

        return response()->json(['message' => 'Marcador eliminado exitosamente']);
    }

    /**
     * Get user's bookmarks
     *
     * Obtiene todos los marcadores del usuario autenticado.
     *
     * @queryParam collection_name string Filtrar por colección. Example: favoritos
     * @queryParam is_favorite boolean Solo marcadores favoritos. Example: true
     * @queryParam per_page int Cantidad por página. Example: 15
     *
     * @apiResourceCollection App\Http\Resources\V1\UserBookmarkResource
     * @apiResourceModel App\Models\UserBookmark
     * @authenticated
     */
    public function myBookmarks(Request $request)
    {
        $user = Auth::guard('sanctum')->user();

        $query = $user->bookmarks()
            ->when($request->collection_name, fn($q, $collection) => $q->where('collection_name', $collection))
            ->when($request->has('is_favorite'), fn($q) => $q->where('is_favorite', $request->boolean('is_favorite')))
            ->orderBy('bookmarked_at', 'desc');

        $perPage = min($request->get('per_page', 15), 100);
        $bookmarks = $query->paginate($perPage);

        return UserBookmarkResource::collection($bookmarks);
    }

    /**
     * Toggle favorite status
     *
     * Alterna el estado de favorito de un marcador.
     *
     * @urlParam userBookmark int required ID del marcador. Example: 1
     *
     * @response 200 {"message": "Estado de favorito actualizado", "is_favorite": true}
     * @response 403 {"message": "No autorizado"}
     * @authenticated
     */
    public function toggleFavorite(UserBookmark $userBookmark)
    {
        $user = Auth::guard('sanctum')->user();

        if ($userBookmark->user_id !== $user->id) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        $userBookmark->update(['is_favorite' => !$userBookmark->is_favorite]);

        return response()->json([
            'message' => 'Estado de favorito actualizado',
            'is_favorite' => $userBookmark->is_favorite
        ]);
    }
}