<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\UserListResource;
use App\Models\UserList;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

/**
 * @group User Lists
 *
 * APIs para la gestión de listas personalizadas de usuarios.
 * Permite crear listas como "Favoritos", "Seguir más tarde", etc.
 * Similar a las listas de Twitter o Discord.
 */
class UserListController extends Controller
{
    /**
     * Display a listing of user lists
     *
     * Obtiene una lista de listas del usuario autenticado.
     *
     * @queryParam type string Tipo de lista (favorites, following, custom, shared). Example: favorites
     * @queryParam visibility string Visibilidad (private, public, shared). Example: public
     * @queryParam is_featured boolean Filtrar listas destacadas. Example: true
     * @queryParam sort string Ordenamiento (recent, alphabetical, items_count, followers_count). Example: recent
     * @queryParam page int Número de página. Example: 1
     * @queryParam per_page int Cantidad por página (máx 100). Example: 15
     *
     * @apiResourceCollection App\Http\Resources\V1\UserListResource
     * @apiResourceModel App\Models\UserList
     * @authenticated
     */
    public function index(Request $request)
    {
        $user = Auth::guard('sanctum')->user();
        
        $query = $user->lists()
            ->when($request->type, fn($q, $type) => $q->where('type', $type))
            ->when($request->visibility, fn($q, $visibility) => $q->where('visibility', $visibility))
            ->when($request->has('is_featured'), fn($q) => $q->where('is_featured', $request->boolean('is_featured')));

        // Aplicar ordenamiento
        switch ($request->get('sort', 'recent')) {
            case 'alphabetical':
                $query->orderBy('name', 'asc');
                break;
            case 'items_count':
                $query->orderBy('items_count', 'desc');
                break;
            case 'followers_count':
                $query->orderBy('followers_count', 'desc');
                break;
            default: // recent
                $query->orderBy('updated_at', 'desc');
        }

        $perPage = min($request->get('per_page', 15), 100);
        $lists = $query->paginate($perPage);

        return UserListResource::collection($lists);
    }

    /**
     * Store a new user list
     *
     * Crea una nueva lista personalizada.
     *
     * @bodyParam name string required Nombre de la lista. Example: Mis Favoritos
     * @bodyParam description string Descripción de la lista. Example: Lista de contenido favorito
     * @bodyParam type string Tipo de lista (favorites, following, custom, shared). Default: custom. Example: custom
     * @bodyParam visibility string Visibilidad (private, public, shared). Default: private. Example: private
     * @bodyParam color string Color de la lista (hex). Example: #ff6b6b
     * @bodyParam icon string Icono de la lista. Example: star
     * @bodyParam is_featured boolean Si es destacada. Default: false. Example: false
     * @bodyParam allows_contributions boolean Si permite contribuciones. Default: false. Example: false
     * @bodyParam tags array Etiquetas de la lista. Example: ["favoritos", "importante"]
     *
     * @apiResource App\Http\Resources\V1\UserListResource
     * @apiResourceModel App\Models\UserList
     *
     * @response 201 {"data": {...}, "message": "Lista creada exitosamente"}
     * @response 422 {"message": "Datos inválidos", "errors": {...}}
     * @authenticated
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'type' => 'in:favorites,following,custom,shared',
            'visibility' => 'in:private,public,shared',
            'color' => 'nullable|string|max:7|regex:/^#[0-9A-Fa-f]{6}$/',
            'icon' => 'nullable|string|max:100',
            'is_featured' => 'boolean',
            'allows_contributions' => 'boolean',
            'tags' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Datos inválidos',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = Auth::guard('sanctum')->user();

        $list = UserList::create(array_merge($validator->validated(), [
            'user_id' => $user->id,
            'type' => $request->get('type', 'custom'),
            'visibility' => $request->get('visibility', 'private'),
            'is_featured' => $request->get('is_featured', false),
            'allows_contributions' => $request->get('allows_contributions', false),
            'items_count' => 0,
            'followers_count' => 0,
        ]));

        $list->load(['user']);
        return new UserListResource($list);
    }

    /**
     * Display the specified user list
     *
     * Muestra una lista específica con sus items.
     *
     * @urlParam userList int required ID de la lista. Example: 1
     *
     * @apiResource App\Http\Resources\V1\UserListResource
     * @apiResourceModel App\Models\UserList
     *
     * @response 404 {"message": "Lista no encontrada"}
     */
    public function show(UserList $userList)
    {
        // Solo mostrar listas públicas o propias
        $user = Auth::guard('sanctum')->user();
        if ($userList->visibility !== 'public' && $userList->user_id !== $user->id) {
            return response()->json(['message' => 'Lista no encontrada'], 404);
        }

        $userList->load(['user', 'items']);
        return new UserListResource($userList);
    }

    /**
     * Update the specified user list
     *
     * Actualiza una lista existente.
     *
     * @urlParam userList int required ID de la lista. Example: 1
     * @bodyParam name string Nuevo nombre. Example: Nuevo nombre
     * @bodyParam description string Nueva descripción. Example: Nueva descripción
     * @bodyParam visibility string Nueva visibilidad. Example: public
     * @bodyParam color string Nuevo color. Example: #4ecdc4
     * @bodyParam icon string Nuevo icono. Example: heart
     * @bodyParam is_featured boolean Si es destacada. Example: true
     * @bodyParam allows_contributions boolean Si permite contribuciones. Example: true
     * @bodyParam tags array Nuevas etiquetas. Example: ["actualizado", "nuevo"]
     *
     * @apiResource App\Http\Resources\V1\UserListResource
     * @apiResourceModel App\Models\UserList
     *
     * @response 403 {"message": "No autorizado"}
     * @response 422 {"message": "Datos inválidos", "errors": {...}}
     * @authenticated
     */
    public function update(Request $request, UserList $userList)
    {
        $user = Auth::guard('sanctum')->user();

        if ($userList->user_id !== $user->id) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'string|max:255',
            'description' => 'nullable|string|max:1000',
            'visibility' => 'in:private,public,shared',
            'color' => 'nullable|string|max:7|regex:/^#[0-9A-Fa-f]{6}$/',
            'icon' => 'nullable|string|max:100',
            'is_featured' => 'boolean',
            'allows_contributions' => 'boolean',
            'tags' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Datos inválidos',
                'errors' => $validator->errors()
            ], 422);
        }

        $userList->update($validator->validated());
        $userList->load(['user']);

        return new UserListResource($userList);
    }

    /**
     * Remove the specified user list
     *
     * Elimina una lista y todos sus items.
     *
     * @urlParam userList int required ID de la lista. Example: 1
     *
     * @response 200 {"message": "Lista eliminada exitosamente"}
     * @response 403 {"message": "No autorizado"}
     * @authenticated
     */
    public function destroy(UserList $userList)
    {
        $user = Auth::guard('sanctum')->user();

        if ($userList->user_id !== $user->id) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        // Eliminar items de la lista primero
        $userList->items()->delete();
        
        // Eliminar la lista
        $userList->delete();

        return response()->json(['message' => 'Lista eliminada exitosamente']);
    }

    /**
     * Follow a user list
     *
     * Seguir una lista pública de otro usuario.
     *
     * @urlParam userList int required ID de la lista. Example: 1
     *
     * @response 200 {"message": "Lista seguida exitosamente"}
     * @response 403 {"message": "No puedes seguir tu propia lista"}
     * @response 404 {"message": "Lista no encontrada"}
     * @authenticated
     */
    public function follow(UserList $userList)
    {
        $user = Auth::guard('sanctum')->user();

        if ($userList->user_id === $user->id) {
            return response()->json(['message' => 'No puedes seguir tu propia lista'], 403);
        }

        if ($userList->visibility !== 'public') {
            return response()->json(['message' => 'Lista no encontrada'], 404);
        }

        // Verificar si ya sigue la lista
        if ($userList->followers()->where('user_id', $user->id)->exists()) {
            return response()->json(['message' => 'Ya sigues esta lista']);
        }

        // Agregar seguidor
        $userList->followers()->attach($user->id);
        $userList->increment('followers_count');

        return response()->json(['message' => 'Lista seguida exitosamente']);
    }

    /**
     * Unfollow a user list
     *
     * Dejar de seguir una lista.
     *
     * @urlParam userList int required ID de la lista. Example: 1
     *
     * @response 200 {"message": "Lista dejada de seguir"}
     * @response 404 {"message": "No sigues esta lista"}
     * @authenticated
     */
    public function unfollow(UserList $userList)
    {
        $user = Auth::guard('sanctum')->user();

        if (!$userList->followers()->where('user_id', $user->id)->exists()) {
            return response()->json(['message' => 'No sigues esta lista'], 404);
        }

        // Remover seguidor
        $userList->followers()->detach($user->id);
        $userList->decrement('followers_count');

        return response()->json(['message' => 'Lista dejada de seguir']);
    }
}
