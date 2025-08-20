<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\TopicMembershipResource;
use App\Models\TopicMembership;
use App\Models\Topic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

/**
 * @group Topic Memberships
 *
 * APIs para la gestión de membresías en temas/comunidades.
 * Permite a los usuarios unirse, salir y gestionar su participación en temas.
 */
class TopicMembershipController extends Controller
{
    /**
     * Display a listing of memberships
     *
     * Obtiene una lista de membresías con opciones de filtrado.
     *
     * @queryParam topic_id int ID del tema para filtrar. Example: 1
     * @queryParam user_id int ID del usuario. Example: 1
     * @queryParam role string Rol del miembro (member, moderator, admin, owner). Example: member
     * @queryParam status string Estado de la membresía (active, pending, suspended, banned, left). Example: active
     * @queryParam is_verified boolean Filtrar por miembros verificados. Example: true
     * @queryParam page int Número de página. Example: 1
     * @queryParam per_page int Cantidad por página (máx 100). Example: 15
     *
     * @apiResourceCollection App\Http\Resources\V1\TopicMembershipResource
     * @apiResourceModel App\Models\TopicMembership
     */
    public function index(Request $request)
    {
        $query = TopicMembership::with(['topic', 'user'])
            ->when($request->topic_id, fn($q, $topicId) => $q->where('topic_id', $topicId))
            ->when($request->user_id, fn($q, $userId) => $q->where('user_id', $userId))
            ->when($request->role, fn($q, $role) => $q->where('role', $role))
            ->when($request->status, fn($q, $status) => $q->where('status', $status))
            ->when($request->has('is_verified'), fn($q) => $q->where('is_verified', $request->boolean('is_verified')))
            ->orderBy('joined_at', 'desc');

        $perPage = min($request->get('per_page', 15), 100);
        $memberships = $query->paginate($perPage);

        return TopicMembershipResource::collection($memberships);
    }

    /**
     * Store a new membership
     *
     * Crea una nueva membresía o solicitud de unión a un tema.
     *
     * @bodyParam topic_id int required ID del tema. Example: 1
     * @bodyParam role string Rol inicial (member, moderator, admin, owner). Default: member. Example: member
     * @bodyParam status string Estado inicial (active, pending). Default: pending. Example: pending
     * @bodyParam is_verified boolean Si es verificado. Default: false. Example: false
     * @bodyParam can_post boolean Si puede publicar. Default: true. Example: true
     * @bodyParam can_comment boolean Si puede comentar. Default: true. Example: true
     * @bodyParam can_moderate boolean Si puede moderar. Default: false. Example: false
     * @bodyParam receives_notifications boolean Si recibe notificaciones. Default: true. Example: true
     *
     * @apiResource App\Http\Resources\V1\TopicMembershipResource
     * @apiResourceModel App\Models\TopicMembership
     *
     * @response 201 {"data": {...}, "message": "Membresía creada exitosamente"}
     * @response 409 {"message": "Ya eres miembro de este tema"}
     * @response 422 {"message": "Datos inválidos", "errors": {...}}
     * @authenticated
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'topic_id' => 'required|exists:topics,id',
            'role' => 'in:member,moderator,admin,owner',
            'status' => 'in:active,pending',
            'is_verified' => 'boolean',
            'can_post' => 'boolean',
            'can_comment' => 'boolean',
            'can_moderate' => 'boolean',
            'receives_notifications' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Datos inválidos',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = Auth::guard('sanctum')->user();
        
        // Verificar si ya es miembro
        $existingMembership = TopicMembership::where('user_id', $user->id)
            ->where('topic_id', $request->topic_id)
            ->first();

        if ($existingMembership) {
            return response()->json([
                'message' => 'Ya eres miembro de este tema',
                'data' => new TopicMembershipResource($existingMembership)
            ], 409);
        }

        $membership = TopicMembership::create(array_merge($validator->validated(), [
            'user_id' => $user->id,
            'role' => $request->get('role', 'member'),
            'status' => $request->get('status', 'pending'),
            'is_verified' => $request->get('is_verified', false),
            'can_post' => $request->get('can_post', true),
            'can_comment' => $request->get('can_comment', true),
            'can_moderate' => $request->get('can_moderate', false),
            'receives_notifications' => $request->get('receives_notifications', true),
            'joined_at' => now(),
        ]));

        $membership->load(['topic', 'user']);

        return response()->json([
            'data' => new TopicMembershipResource($membership),
            'message' => 'Membresía creada exitosamente'
        ], 201);
    }

    /**
     * Display the specified membership
     *
     * Muestra una membresía específica.
     *
     * @urlParam topicMembership int required ID de la membresía. Example: 1
     *
     * @apiResource App\Http\Resources\V1\TopicMembershipResource
     * @apiResourceModel App\Models\TopicMembership
     *
     * @response 404 {"message": "Membresía no encontrada"}
     */
    public function show(TopicMembership $topicMembership)
    {
        $topicMembership->load(['topic', 'user']);
        return new TopicMembershipResource($topicMembership);
    }

    /**
     * Update the specified membership
     *
     * Actualiza una membresía existente.
     *
     * @urlParam topicMembership int required ID de la membresía. Example: 1
     * @bodyParam role string Nuevo rol. Example: moderator
     * @bodyParam status string Nuevo estado. Example: active
     * @bodyParam can_post boolean Si puede publicar. Example: true
     * @bodyParam can_comment boolean Si puede comentar. Example: true
     * @bodyParam can_moderate boolean Si puede moderar. Example: false
     * @bodyParam receives_notifications boolean Si recibe notificaciones. Example: true
     *
     * @apiResource App\Http\Resources\V1\TopicMembershipResource
     * @apiResourceModel App\Models\TopicMembership
     *
     * @response 403 {"message": "No autorizado"}
     * @response 422 {"message": "Datos inválidos", "errors": {...}}
     * @authenticated
     */
    public function update(Request $request, TopicMembership $topicMembership)
    {
        $user = Auth::guard('sanctum')->user();

        // Solo el usuario propietario o moderadores pueden actualizar
        if ($topicMembership->user_id !== $user->id) {
            // TODO: Verificar si es moderador del tema
            return response()->json(['message' => 'No autorizado'], 403);
        }

        $validator = Validator::make($request->all(), [
            'role' => 'in:member,moderator,admin,owner',
            'status' => 'in:active,pending,suspended,banned,left',
            'can_post' => 'boolean',
            'can_comment' => 'boolean',
            'can_moderate' => 'boolean',
            'receives_notifications' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Datos inválidos',
                'errors' => $validator->errors()
            ], 422);
        }

        $topicMembership->update($validator->validated());
        $topicMembership->load(['topic', 'user']);

        return new TopicMembershipResource($topicMembership);
    }

    /**
     * Remove the specified membership
     *
     * Elimina una membresía (marca como "left").
     *
     * @urlParam topicMembership int required ID de la membresía. Example: 1
     *
     * @response 200 {"message": "Membresía eliminada exitosamente"}
     * @response 403 {"message": "No autorizado"}
     * @authenticated
     */
    public function destroy(TopicMembership $topicMembership)
    {
        $user = Auth::guard('sanctum')->user();

        if ($topicMembership->user_id !== $user->id) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        $topicMembership->update(['status' => 'left']);

        return response()->json(['message' => 'Membresía eliminada exitosamente']);
    }

    /**
     * Get memberships for a specific topic
     *
     * Obtiene todas las membresías de un tema específico.
     *
     * @urlParam topic int required ID del tema. Example: 1
     * @queryParam role string Filtrar por rol. Example: member
     * @queryParam status string Filtrar por estado. Example: active
     * @queryParam per_page int Cantidad por página. Example: 15
     *
     * @apiResourceCollection App\Http\Resources\V1\TopicMembershipResource
     * @apiResourceModel App\Models\TopicMembership
     */
    public function topicMemberships(Request $request, Topic $topic)
    {
        $query = $topic->memberships()
            ->with(['topic', 'user'])
            ->when($request->role, fn($q, $role) => $q->where('role', $role))
            ->when($request->status, fn($q, $status) => $q->where('status', $status))
            ->orderBy('joined_at', 'desc');

        $perPage = min($request->get('per_page', 15), 100);
        $memberships = $query->paginate($perPage);

        return TopicMembershipResource::collection($memberships);
    }

    /**
     * Join a topic
     *
     * Método simplificado para unirse a un tema.
     *
     * @urlParam topic int required ID del tema. Example: 1
     *
     * @response 200 {"message": "Te has unido al tema", "data": {...}}
     * @response 409 {"message": "Ya eres miembro de este tema"}
     * @authenticated
     */
    public function join(Topic $topic)
    {
        $user = Auth::guard('sanctum')->user();
        
        // Verificar si ya es miembro
        $existingMembership = $topic->memberships()
            ->where('user_id', $user->id)
            ->first();

        if ($existingMembership) {
            return response()->json([
                'message' => 'Ya eres miembro de este tema',
                'data' => new TopicMembershipResource($existingMembership)
            ], 409);
        }

        // Crear membresía
        $membership = TopicMembership::create([
            'user_id' => $user->id,
            'topic_id' => $topic->id,
            'role' => 'member',
            'status' => 'active',
            'joined_at' => now(),
        ]);

        $membership->load(['topic', 'user']);

        return response()->json([
            'message' => 'Te has unido al tema',
            'data' => new TopicMembershipResource($membership)
        ]);
    }

    /**
     * Leave a topic
     *
     * Método simplificado para salir de un tema.
     *
     * @urlParam topic int required ID del tema. Example: 1
     *
     * @response 200 {"message": "Has salido del tema"}
     * @response 404 {"message": "No eres miembro de este tema"}
     * @authenticated
     */
    public function leave(Topic $topic)
    {
        $user = Auth::guard('sanctum')->user();
        
        $membership = $topic->memberships()
            ->where('user_id', $user->id)
            ->first();

        if (!$membership) {
            return response()->json(['message' => 'No eres miembro de este tema'], 404);
        }

        $membership->update(['status' => 'left']);

        return response()->json(['message' => 'Has salido del tema']);
    }
}
