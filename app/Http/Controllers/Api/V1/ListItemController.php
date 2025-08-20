<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\ListItemResource;
use App\Models\ListItem;
use App\Models\UserList;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

/**
 * @group List Items
 *
 * APIs para la gestión de items dentro de listas personalizadas.
 * Permite agregar, remover y gestionar contenido en listas como
 * "Favoritos", "Seguir más tarde", etc.
 */
class ListItemController extends Controller
{
    /**
     * Display a listing of list items
     *
     * Obtiene una lista de items de listas con opciones de filtrado.
     *
     * @queryParam user_list_id int ID de la lista para filtrar. Example: 1
     * @queryParam listable_type string Tipo de contenido (TopicPost, User, ProjectProposal, etc.). Example: TopicPost
     * @queryParam listable_id int ID del contenido. Example: 1
     * @queryParam added_by int ID del usuario que agregó el item. Example: 1
     * @queryParam sort string Ordenamiento (recent, oldest, alphabetical, relevance). Example: recent
     * @queryParam page int Número de página. Example: 1
     * @queryParam per_page int Cantidad por página (máx 100). Example: 15
     *
     * @apiResourceCollection App\Http\Resources\V1\ListItemResource
     * @apiResourceModel App\Models\ListItem
     */
    public function index(Request $request)
    {
        $query = ListItem::with(['userList', 'listable', 'addedBy'])
            ->when($request->user_list_id, fn($q, $listId) => $q->where('user_list_id', $listId))
            ->when($request->listable_type, fn($q, $type) => $q->where('listable_type', $type))
            ->when($request->listable_id, fn($q, $id) => $q->where('listable_id', $id))
            ->when($request->added_by, fn($q, $userId) => $q->where('added_by', $userId));

        // Aplicar ordenamiento
        switch ($request->get('sort', 'recent')) {
            case 'oldest':
                $query->orderBy('created_at', 'asc');
                break;
            case 'alphabetical':
                $query->orderBy('notes', 'asc');
                break;
            case 'relevance':
                $query->orderBy('relevance_score', 'desc');
                break;
            default: // recent
                $query->orderBy('created_at', 'desc');
        }

        $perPage = min($request->get('per_page', 15), 100);
        $items = $query->paginate($perPage);

        return ListItemResource::collection($items);
    }

    /**
     * Store a new list item
     *
     * Agrega un nuevo item a una lista personalizada.
     *
     * @bodyParam user_list_id int required ID de la lista. Example: 1
     * @bodyParam listable_type string required Tipo de contenido. Example: App\Models\TopicPost
     * @bodyParam listable_id int required ID del contenido. Example: 1
     * @bodyParam notes string Notas sobre el item. Example: Muy útil para mi proyecto
     * @bodyParam priority string Prioridad (low, normal, high, urgent). Default: normal. Example: normal
     * @bodyParam relevance_score number Puntuación de relevancia (0-100). Example: 85
     * @bodyParam tags array Etiquetas del item. Example: ["importante", "referencia"]
     * @bodyParam is_public boolean Si el item es público. Default: false. Example: false
     *
     * @apiResource App\Http\Resources\V1\ListItemResource
     * @apiResourceModel App\Models\ListItem
     *
     * @response 201 {"data": {...}, "message": "Item agregado exitosamente"}
     * @response 409 {"message": "El item ya está en la lista"}
     * @response 422 {"message": "Datos inválidos", "errors": {...}}
     * @authenticated
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_list_id' => 'required|exists:user_lists,id',
            'listable_type' => 'required|string|max:255',
            'listable_id' => 'required|integer',
            'notes' => 'nullable|string|max:1000',
            'priority' => 'in:low,normal,high,urgent',
            'relevance_score' => 'nullable|numeric|min:0|max:100',
            'tags' => 'nullable|array',
            'is_public' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Datos inválidos',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = Auth::guard('sanctum')->user();
        
        // Verificar que la lista pertenece al usuario
        $userList = UserList::findOrFail($request->user_list_id);
        if ($userList->user_id !== $user->id) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        // Verificar que el item no esté ya en la lista
        $existing = ListItem::where('user_list_id', $request->user_list_id)
            ->where('listable_type', $request->listable_type)
            ->where('listable_id', $request->listable_id)
            ->first();

        if ($existing) {
            return response()->json([
                'message' => 'El item ya está en la lista',
                'data' => new ListItemResource($existing)
            ], 409);
        }

        $item = ListItem::create(array_merge($validator->validated(), [
            'added_by' => $user->id,
            'priority' => $request->get('priority', 'normal'),
            'relevance_score' => $request->get('relevance_score', 50),
            'is_public' => $request->get('is_public', false),
        ]));

        $item->load(['userList', 'listable', 'addedBy']);

        // Incrementar contador de items en la lista
        $userList->increment('items_count');

        return response()->json([
            'data' => new ListItemResource($item),
            'message' => 'Item agregado exitosamente'
        ], 201);
    }

    /**
     * Display the specified list item
     *
     * Muestra un item específico de una lista.
     *
     * @urlParam listItem int required ID del item. Example: 1
     *
     * @apiResource App\Http\Resources\V1\ListItemResource
     * @apiResourceModel App\Models\ListItem
     *
     * @response 404 {"message": "Item no encontrado"}
     */
    public function show(ListItem $listItem)
    {
        $listItem->load(['userList', 'listable', 'addedBy']);
        return new ListItemResource($listItem);
    }

    /**
     * Update the specified list item
     *
     * Actualiza un item existente en una lista.
     *
     * @urlParam listItem int required ID del item. Example: 1
     * @bodyParam notes string Nuevas notas. Example: Notas actualizadas
     * @bodyParam priority string Nueva prioridad. Example: high
     * @bodyParam relevance_score number Nueva puntuación de relevancia. Example: 95
     * @bodyParam tags array Nuevas etiquetas. Example: ["actualizado", "importante"]
     * @bodyParam is_public boolean Si es público. Example: true
     *
     * @apiResource App\Http\Resources\V1\ListItemResource
     * @apiResourceModel App\Models\ListItem
     *
     * @response 403 {"message": "No autorizado"}
     * @response 422 {"message": "Datos inválidos", "errors": {...}}
     * @authenticated
     */
    public function update(Request $request, ListItem $listItem)
    {
        $user = Auth::guard('sanctum')->user();

        // Solo el propietario de la lista puede editar items
        if ($listItem->userList->user_id !== $user->id) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        $validator = Validator::make($request->all(), [
            'notes' => 'nullable|string|max:1000',
            'priority' => 'in:low,normal,high,urgent',
            'relevance_score' => 'nullable|numeric|min:0|max:100',
            'tags' => 'nullable|array',
            'is_public' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Datos inválidos',
                'errors' => $validator->errors()
            ], 422);
        }

        $listItem->update($validator->validated());
        $listItem->load(['userList', 'listable', 'addedBy']);

        return new ListItemResource($listItem);
    }

    /**
     * Remove the specified list item
     *
     * Elimina un item de una lista.
     *
     * @urlParam listItem int required ID del item. Example: 1
     *
     * @response 200 {"message": "Item eliminado exitosamente"}
     * @response 403 {"message": "No autorizado"}
     * @authenticated
     */
    public function destroy(ListItem $listItem)
    {
        $user = Auth::guard('sanctum')->user();

        if ($listItem->userList->user_id !== $user->id) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        // Decrementar contador de items en la lista
        $listItem->userList->decrement('items_count');
        
        $listItem->delete();

        return response()->json(['message' => 'Item eliminado exitosamente']);
    }

    /**
     * Get items for a specific list
     *
     * Obtiene todos los items de una lista específica.
     *
     * @urlParam userList int required ID de la lista. Example: 1
     * @queryParam listable_type string Filtrar por tipo de contenido. Example: TopicPost
     * @queryParam priority string Filtrar por prioridad. Example: high
     * @queryParam sort string Ordenamiento. Example: priority
     * @queryParam per_page int Cantidad por página. Example: 15
     *
     * @apiResourceCollection App\Http\Resources\V1\ListItemResource
     * @apiResourceModel App\Models\ListItem
     */
    public function listItems(Request $request, UserList $userList)
    {
        // Verificar permisos de visualización
        $user = Auth::guard('sanctum')->user();
        if ($userList->visibility !== 'public' && $userList->user_id !== $user->id) {
            return response()->json(['message' => 'Lista no encontrada'], 404);
        }

        $query = $userList->items()
            ->with(['userList', 'listable', 'addedBy'])
            ->when($request->listable_type, fn($q, $type) => $q->where('listable_type', $type))
            ->when($request->priority, fn($q, $priority) => $q->where('priority', $priority));

        // Aplicar ordenamiento
        switch ($request->get('sort', 'recent')) {
            case 'priority':
                $query->orderByRaw("FIELD(priority, 'urgent', 'high', 'normal', 'low')");
                break;
            case 'relevance':
                $query->orderBy('relevance_score', 'desc');
                break;
            case 'alphabetical':
                $query->orderBy('notes', 'asc');
                break;
            default: // recent
                $query->orderBy('created_at', 'desc');
        }

        $perPage = min($request->get('per_page', 15), 100);
        $items = $query->paginate($perPage);

        return ListItemResource::collection($items);
    }

    /**
     * Move item to another list
     *
     * Mueve un item de una lista a otra.
     *
     * @urlParam listItem int required ID del item. Example: 1
     * @bodyParam new_list_id int required ID de la nueva lista. Example: 2
     *
     * @response 200 {"message": "Item movido exitosamente", "data": {...}}
     * @response 403 {"message": "No autorizado"}
     * @response 422 {"message": "Datos inválidos", "errors": {...}}
     * @authenticated
     */
    public function move(Request $request, ListItem $listItem)
    {
        $validator = Validator::make($request->all(), [
            'new_list_id' => 'required|exists:user_lists,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Datos inválidos',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = Auth::guard('sanctum')->user();
        
        // Verificar que ambas listas pertenezcan al usuario
        if ($listItem->userList->user_id !== $user->id) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        $newList = UserList::findOrFail($request->new_list_id);
        if ($newList->user_id !== $user->id) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        // Verificar que el item no esté ya en la nueva lista
        $existing = ListItem::where('user_list_id', $request->new_list_id)
            ->where('listable_type', $listItem->listable_type)
            ->where('listable_id', $listItem->listable_id)
            ->first();

        if ($existing) {
            return response()->json([
                'message' => 'El item ya está en la lista de destino'
            ], 422);
        }

        // Actualizar contadores
        $listItem->userList->decrement('items_count');
        $newList->increment('items_count');

        // Mover el item
        $listItem->update(['user_list_id' => $request->new_list_id]);
        $listItem->load(['userList', 'listable', 'addedBy']);

        return response()->json([
            'message' => 'Item movido exitosamente',
            'data' => new ListItemResource($listItem)
        ]);
    }
}
