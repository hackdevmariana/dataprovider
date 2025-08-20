<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\TopicPostResource;
use App\Models\TopicPost;
use App\Models\Topic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

/**
 * @group Topic Posts
 *
 * APIs para la gestión de posts dentro de temas/comunidades.
 * Permite crear discusiones, preguntas, anuncios y otros tipos de contenido.
 */
class TopicPostController extends Controller
{
    /**
     * Display a listing of posts
     *
     * Obtiene una lista paginada de posts con opciones de filtrado.
     *
     * @queryParam topic_id int ID del tema para filtrar posts. Example: 1
     * @queryParam author_id int ID del autor. Example: 1
     * @queryParam type string Tipo de post (discussion, question, announcement, poll, guide, showcase, news, event). Example: discussion
     * @queryParam status string Estado del post (draft, published, hidden, locked, deleted). Example: published
     * @queryParam is_pinned boolean Filtrar posts fijados. Example: true
     * @queryParam is_featured boolean Filtrar posts destacados. Example: true
     * @queryParam is_solved boolean Filtrar preguntas resueltas. Example: true
     * @queryParam page int Número de página. Example: 1
     * @queryParam per_page int Cantidad por página (máx 100). Example: 15
     *
     * @apiResourceCollection App\Http\Resources\V1\TopicPostResource
     * @apiResourceModel App\Models\TopicPost
     */
    public function index(Request $request)
    {
        $query = TopicPost::with(['topic', 'author'])
            ->when($request->topic_id, fn($q, $topicId) => $q->where('topic_id', $topicId))
            ->when($request->author_id, fn($q, $authorId) => $q->where('author_id', $authorId))
            ->when($request->type, fn($q, $type) => $q->where('type', $type))
            ->when($request->status, fn($q, $status) => $q->where('status', $status))
            ->when($request->has('is_pinned'), fn($q) => $q->where('is_pinned', $request->boolean('is_pinned')))
            ->when($request->has('is_featured'), fn($q) => $q->where('is_featured', $request->boolean('is_featured')))
            ->when($request->has('is_solved'), fn($q) => $q->where('is_solved', $request->boolean('is_solved')))
            ->where('status', '!=', 'deleted')
            ->orderByDesc('is_pinned')
            ->orderByDesc('is_featured')
            ->orderBy('published_at', 'desc');

        $perPage = min($request->get('per_page', 15), 100);
        $posts = $query->paginate($perPage);

        return TopicPostResource::collection($posts);
    }

    /**
     * Store a new post
     *
     * Crea un nuevo post en un tema.
     *
     * @bodyParam topic_id int required ID del tema. Example: 1
     * @bodyParam title string required Título del post. Example: ¿Cómo optimizar paneles solares?
     * @bodyParam content string required Contenido del post. Example: Tengo una instalación de 5kW y quiero optimizar...
     * @bodyParam type string required Tipo de post (discussion, question, announcement, poll, guide, showcase, news, event). Example: question
     * @bodyParam status string Estado inicial (draft, published). Default: draft. Example: published
     * @bodyParam priority string Prioridad (low, normal, high, urgent). Default: normal. Example: normal
     * @bodyParam allows_comments boolean Si permite comentarios. Default: true. Example: true
     * @bodyParam is_anonymous boolean Si es anónimo. Default: false. Example: false
     * @bodyParam notify_followers boolean Si notificar seguidores. Default: true. Example: true
     * @bodyParam tags array Etiquetas del post. Example: ["solar", "optimización"]
     * @bodyParam published_at datetime Fecha de publicación programada. Example: 2024-01-01 12:00:00
     *
     * @apiResource App\Http\Resources\V1\TopicPostResource
     * @apiResourceModel App\Models\TopicPost
     *
     * @response 201 {"data": {...}, "message": "Post creado exitosamente"}
     * @response 422 {"message": "Datos inválidos", "errors": {...}}
     * @authenticated
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'topic_id' => 'required|exists:topics,id',
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'type' => 'required|in:discussion,question,announcement,poll,guide,showcase,news,event',
            'status' => 'in:draft,published',
            'priority' => 'in:low,normal,high,urgent',
            'allows_comments' => 'boolean',
            'is_anonymous' => 'boolean',
            'notify_followers' => 'boolean',
            'tags' => 'nullable|array',
            'published_at' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Datos inválidos',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = Auth::guard('sanctum')->user();
        
        // Verificar que el usuario puede postear en el tema
        $topic = Topic::findOrFail($request->topic_id);
        
        // TODO: Verificar permisos de membresía y roles
        
        $postData = array_merge($validator->validated(), [
            'author_id' => $user->id,
            'status' => $request->get('status', 'draft'),
            'priority' => $request->get('priority', 'normal'),
            'allows_comments' => $request->get('allows_comments', true),
            'is_anonymous' => $request->get('is_anonymous', false),
            'notify_followers' => $request->get('notify_followers', true),
        ]);

        // Si se publica inmediatamente, establecer published_at
        if ($postData['status'] === 'published' && !$request->published_at) {
            $postData['published_at'] = now();
        }

        $post = TopicPost::create($postData);
        $post->load(['topic', 'author']);

        return response()->json([
            'data' => new TopicPostResource($post),
            'message' => 'Post creado exitosamente'
        ], 201);
    }

    /**
     * Display the specified post
     *
     * Muestra un post específico.
     *
     * @urlParam topicPost int required ID del post. Example: 1
     *
     * @apiResource App\Http\Resources\V1\TopicPostResource
     * @apiResourceModel App\Models\TopicPost
     *
     * @response 404 {"message": "Post no encontrado"}
     */
    public function show(TopicPost $topicPost)
    {
        if ($topicPost->status === 'deleted') {
            return response()->json(['message' => 'Post no encontrado'], 404);
        }

        $topicPost->load(['topic', 'author']);
        
        // Incrementar contador de vistas (si el campo existe)
        $topicPost->increment('views_count');

        return new TopicPostResource($topicPost);
    }

    /**
     * Update the specified post
     *
     * Actualiza un post existente.
     *
     * @urlParam topicPost int required ID del post. Example: 1
     * @bodyParam title string Título del post. Example: Nuevo título
     * @bodyParam content string Contenido del post. Example: Contenido actualizado
     * @bodyParam type string Tipo de post. Example: guide
     * @bodyParam status string Estado del post. Example: published
     * @bodyParam priority string Prioridad. Example: high
     * @bodyParam allows_comments boolean Si permite comentarios. Example: false
     * @bodyParam is_solved boolean Si está resuelto (solo para questions). Example: true
     * @bodyParam tags array Etiquetas. Example: ["actualizado", "importante"]
     *
     * @apiResource App\Http\Resources\V1\TopicPostResource
     * @apiResourceModel App\Models\TopicPost
     *
     * @response 403 {"message": "No autorizado"}
     * @response 422 {"message": "Datos inválidos", "errors": {...}}
     * @authenticated
     */
    public function update(Request $request, TopicPost $topicPost)
    {
        $user = Auth::guard('sanctum')->user();

        // Solo el autor o moderadores pueden editar
        if ($topicPost->author_id !== $user->id) {
            // TODO: Verificar si es moderador del tema
            return response()->json(['message' => 'No autorizado'], 403);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'string|max:255',
            'content' => 'string',
            'type' => 'in:discussion,question,announcement,poll,guide,showcase,news,event',
            'status' => 'in:draft,published,hidden,locked',
            'priority' => 'in:low,normal,high,urgent',
            'allows_comments' => 'boolean',
            'is_solved' => 'boolean',
            'tags' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Datos inválidos',
                'errors' => $validator->errors()
            ], 422);
        }

        $updateData = $validator->validated();

        // Si se publica y no tenía published_at, establecerlo
        if (isset($updateData['status']) && $updateData['status'] === 'published' && !$topicPost->published_at) {
            $updateData['published_at'] = now();
        }

        $topicPost->update($updateData);
        $topicPost->load(['topic', 'author']);

        return new TopicPostResource($topicPost);
    }

    /**
     * Remove the specified post
     *
     * Elimina (marca como eliminado) un post.
     *
     * @urlParam topicPost int required ID del post. Example: 1
     *
     * @response 200 {"message": "Post eliminado exitosamente"}
     * @response 403 {"message": "No autorizado"}
     * @authenticated
     */
    public function destroy(TopicPost $topicPost)
    {
        $user = Auth::guard('sanctum')->user();

        // Solo el autor o moderadores pueden eliminar
        if ($topicPost->author_id !== $user->id) {
            // TODO: Verificar si es moderador del tema
            return response()->json(['message' => 'No autorizado'], 403);
        }

        $topicPost->update(['status' => 'deleted']);

        return response()->json(['message' => 'Post eliminado exitosamente']);
    }

    /**
     * Get posts for a specific topic
     *
     * Obtiene todos los posts de un tema específico.
     *
     * @urlParam topic int required ID del tema. Example: 1
     * @queryParam type string Filtrar por tipo de post. Example: question
     * @queryParam status string Filtrar por estado. Example: published
     * @queryParam is_pinned boolean Solo posts fijados. Example: true
     * @queryParam per_page int Cantidad por página. Example: 15
     *
     * @apiResourceCollection App\Http\Resources\V1\TopicPostResource
     * @apiResourceModel App\Models\TopicPost
     */
    public function topicPosts(Request $request, Topic $topic)
    {
        $query = $topic->posts()
            ->with(['topic', 'author'])
            ->when($request->type, fn($q, $type) => $q->where('type', $type))
            ->when($request->status, fn($q, $status) => $q->where('status', $status))
            ->when($request->has('is_pinned'), fn($q) => $q->where('is_pinned', $request->boolean('is_pinned')))
            ->where('status', '!=', 'deleted')
            ->orderByDesc('is_pinned')
            ->orderBy('published_at', 'desc');

        $perPage = min($request->get('per_page', 15), 100);
        $posts = $query->paginate($perPage);

        return TopicPostResource::collection($posts);
    }

    /**
     * Pin/unpin a post
     *
     * Fija o desfija un post en el tema.
     *
     * @urlParam topicPost int required ID del post. Example: 1
     *
     * @response 200 {"message": "Post fijado/desfijado", "is_pinned": true}
     * @response 403 {"message": "No autorizado"}
     * @authenticated
     */
    public function pin(TopicPost $topicPost)
    {
        $user = Auth::guard('sanctum')->user();

        // Solo moderadores pueden fijar posts
        // TODO: Verificar si es moderador del tema
        if ($topicPost->author_id !== $user->id) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        $topicPost->update(['is_pinned' => !$topicPost->is_pinned]);

        return response()->json([
            'message' => $topicPost->is_pinned ? 'Post fijado' : 'Post desfijado',
            'is_pinned' => $topicPost->is_pinned
        ]);
    }
}