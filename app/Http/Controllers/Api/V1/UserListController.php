<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\UserListResource;
use App\Models\UserList;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

/**
 * @OA\Tag(
 *     name="User Lists",
 *     description="Sistema de listas personalizadas estilo Twitter/Discord"
 * )
 */
class UserListController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/v1/user-lists",
     *     summary="Listar listas de usuarios",
     *     tags={"User Lists"},
     *     @OA\Parameter(
     *         name="type",
     *         in="query",
     *         description="Filtrar por tipo de lista",
     *         @OA\Schema(type="string", enum={"mixed", "users", "posts", "projects", "companies", "resources", "events", "custom"})
     *     ),
     *     @OA\Parameter(
     *         name="visibility",
     *         in="query",
     *         description="Filtrar por visibilidad",
     *         @OA\Schema(type="string", enum={"public", "private", "followers", "collaborative"})
     *     ),
     *     @OA\Parameter(
     *         name="featured",
     *         in="query",
     *         description="Solo listas destacadas",
     *         @OA\Schema(type="boolean")
     *     ),
     *     @OA\Response(response=200, description="Lista de listas de usuarios")
     * )
     */
    public function index(Request $request)
    {
        $query = UserList::where('is_active', true);

        // Solo mostrar listas públicas si no es el propietario
        if (!auth()->check()) {
            $query->where('visibility', 'public');
        } else {
            $user = auth()->user();
            $query->where(function ($q) use ($user) {
                $q->where('visibility', 'public')
                  ->orWhere('user_id', $user->id)
                  ->orWhere(function ($subQ) use ($user) {
                      $subQ->where('visibility', 'followers')
                           ->whereHas('user.followers', function ($followerQ) use ($user) {
                               $followerQ->where('follower_id', $user->id);
                           });
                  })
                  ->orWhere(function ($subQ) use ($user) {
                      $subQ->where('visibility', 'collaborative')
                           ->whereJsonContains('collaborator_ids', $user->id);
                  });
            });
        }

        if ($request->has('type')) {
            $query->where('list_type', $request->type);
        }

        if ($request->has('visibility')) {
            $query->where('visibility', $request->visibility);
        }

        if ($request->boolean('featured')) {
            $query->where('is_featured', true);
        }

        $lists = $query->with(['user'])
                      ->orderBy('engagement_score', 'desc')
                      ->paginate(20);

        return UserListResource::collection($lists);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/user-lists",
     *     summary="Crear nueva lista",
     *     tags={"User Lists"},
     *     security={{"sanctum": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "list_type"},
     *             @OA\Property(property="name", type="string", example="Instaladores de confianza"),
     *             @OA\Property(property="description", type="string", example="Lista de instaladores verificados"),
     *             @OA\Property(property="list_type", type="string", enum={"mixed", "users", "posts", "projects", "companies", "resources", "events", "custom"}),
     *             @OA\Property(property="visibility", type="string", enum={"private", "public", "followers", "collaborative"}, default="private"),
     *             @OA\Property(property="icon", type="string", example="users"),
     *             @OA\Property(property="color", type="string", example="#3B82F6")
     *         )
     *     ),
     *     @OA\Response(response=201, description="Lista creada"),
     *     @OA\Response(response=422, description="Error de validación")
     * )
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'list_type' => 'required|in:mixed,users,posts,projects,companies,resources,events,custom',
            'visibility' => 'nullable|in:private,public,followers,collaborative',
            'icon' => 'nullable|string|max:50',
            'color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'allowed_content_types' => 'nullable|array',
            'collaborator_ids' => 'nullable|array',
            'collaborator_ids.*' => 'exists:users,id',
        ]);

        $list = UserList::create([
            'user_id' => auth()->id(),
            'name' => $request->name,
            'description' => $request->description,
            'list_type' => $request->list_type,
            'visibility' => $request->visibility ?? 'private',
            'icon' => $request->icon,
            'color' => $request->color ?? '#3B82F6',
            'allowed_content_types' => $request->allowed_content_types,
            'collaborator_ids' => $request->collaborator_ids,
        ]);

        return response()->json([
            'message' => 'Lista creada exitosamente',
            'data' => new UserListResource($list),
        ], 201);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/user-lists/{userList}",
     *     summary="Obtener lista específica",
     *     tags={"User Lists"},
     *     @OA\Parameter(
     *         name="userList",
     *         in="path",
     *         required=true,
     *         description="ID de la lista",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Detalles de la lista"),
     *     @OA\Response(response=404, description="Lista no encontrada")
     * )
     */
    public function show(UserList $userList)
    {
        // Verificar permisos de visualización
        if (!$userList->canView(auth()->user())) {
            abort(403, 'No tienes permisos para ver esta lista');
        }

        $userList->incrementViews();
        $userList->load(['user', 'items.listable']);

        return new UserListResource($userList);
    }

    /**
     * @OA\Put(
     *     path="/api/v1/user-lists/{userList}",
     *     summary="Actualizar lista",
     *     tags={"User Lists"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(
     *         name="userList",
     *         in="path",
     *         required=true,
     *         description="ID de la lista",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="description", type="string"),
     *             @OA\Property(property="visibility", type="string", enum={"private", "public", "followers", "collaborative"}),
     *             @OA\Property(property="icon", type="string"),
     *             @OA\Property(property="color", type="string")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Lista actualizada"),
     *     @OA\Response(response=403, description="Sin permisos"),
     *     @OA\Response(response=422, description="Error de validación")
     * )
     */
    public function update(Request $request, UserList $userList): JsonResponse
    {
        if (!$userList->canEdit(auth()->user())) {
            abort(403, 'No tienes permisos para editar esta lista');
        }

        $request->validate([
            'name' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:1000',
            'visibility' => 'nullable|in:private,public,followers,collaborative',
            'icon' => 'nullable|string|max:50',
            'color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'collaborator_ids' => 'nullable|array',
            'collaborator_ids.*' => 'exists:users,id',
        ]);

        $userList->update($request->only([
            'name', 'description', 'visibility', 'icon', 'color', 'collaborator_ids'
        ]));

        return response()->json([
            'message' => 'Lista actualizada exitosamente',
            'data' => new UserListResource($userList),
        ]);
    }

    /**
     * @OA\Delete(
     *     path="/api/v1/user-lists/{userList}",
     *     summary="Eliminar lista",
     *     tags={"User Lists"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(
     *         name="userList",
     *         in="path",
     *         required=true,
     *         description="ID de la lista",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Lista eliminada"),
     *     @OA\Response(response=403, description="Sin permisos")
     * )
     */
    public function destroy(UserList $userList): JsonResponse
    {
        if ($userList->user_id !== auth()->id()) {
            abort(403, 'Solo el propietario puede eliminar la lista');
        }

        $userList->delete();

        return response()->json([
            'message' => 'Lista eliminada exitosamente',
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/user-lists/featured",
     *     summary="Obtener listas destacadas",
     *     tags={"User Lists"},
     *     @OA\Parameter(
     *         name="limit",
     *         in="query",
     *         description="Número de listas a retornar",
     *         @OA\Schema(type="integer", default=10)
     *     ),
     *     @OA\Response(response=200, description="Listas destacadas")
     * )
     */
    public function featured(Request $request)
    {
        $limit = $request->integer('limit', 10);
        $lists = UserList::getFeatured($limit);
        
        return UserListResource::collection($lists);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/user-lists/search",
     *     summary="Buscar listas",
     *     tags={"User Lists"},
     *     @OA\Parameter(
     *         name="q",
     *         in="query",
     *         required=true,
     *         description="Término de búsqueda",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="limit",
     *         in="query",
     *         description="Número de resultados",
     *         @OA\Schema(type="integer", default=20)
     *     ),
     *     @OA\Response(response=200, description="Resultados de búsqueda")
     * )
     */
    public function search(Request $request)
    {
        $request->validate([
            'q' => 'required|string|min:2',
            'limit' => 'integer|min:1|max:50',
        ]);

        $term = $request->q;
        $limit = $request->integer('limit', 20);
        
        $lists = UserList::search($term, $limit);
        
        return UserListResource::collection($lists);
    }
}
