<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\TopicCommentResource;
use App\Models\TopicComment;
use App\Models\TopicPost;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

/**
 * @group Topic Comments
 *
 * APIs para la gestión de comentarios en posts de temas/comunidades.
 * Permite a los usuarios comentar, responder y moderar comentarios.
 */
class TopicCommentController extends Controller
{
    /**
     * Display a listing of comments
     *
     * Obtiene una lista de comentarios con opciones de filtrado.
     *
     * @queryParam topic_post_id int ID del post para filtrar comentarios. Example: 1
     * @queryParam author_id int ID del autor del comentario. Example: 1
     * @queryParam parent_id int ID del comentario padre (para respuestas). Example: 1
     * @queryParam status string Estado del comentario (published, hidden, deleted, flagged). Example: published
     * @queryParam is_verified boolean Filtrar por comentarios verificados. Example: true
     * @queryParam sort string Ordenamiento (newest, oldest, most_liked, most_replied). Example: newest
     * @queryParam page int Número de página. Example: 1
     * @queryParam per_page int Cantidad por página (máx 100). Example: 15
     *
     * @apiResourceCollection App\Http\Resources\V1\TopicCommentResource
     * @apiResourceModel App\Models\TopicComment
     */
    public function index(Request $request)
    {
        $query = TopicComment::with(['topicPost', 'author', 'parent', 'replies'])
            ->when($request->topic_post_id, fn($q, $postId) => $q->where('topic_post_id', $postId))
            ->when($request->author_id, fn($q, $authorId) => $q->where('author_id', $authorId))
            ->when($request->parent_id, fn($q, $parentId) => $q->where('parent_id', $parentId))
            ->when($request->status, fn($q, $status) => $q->where('status', $status))
            ->when($request->has('is_verified'), fn($q) => $q->where('is_verified', $request->boolean('is_verified')))
            ->where('status', '!=', 'deleted');

        // Aplicar ordenamiento
        switch ($request->get('sort', 'newest')) {
            case 'oldest':
                $query->orderBy('created_at', 'asc');
                break;
            case 'most_liked':
                $query->orderBy('likes_count', 'desc')->orderBy('created_at', 'desc');
                break;
            case 'most_replied':
                $query->orderBy('replies_count', 'desc')->orderBy('created_at', 'desc');
                break;
            default: // newest
                $query->orderBy('created_at', 'desc');
        }

        $perPage = min($request->get('per_page', 15), 100);
        $comments = $query->paginate($perPage);

        return TopicCommentResource::collection($comments);
    }

    /**
     * Store a new comment
     *
     * Crea un nuevo comentario en un post.
     *
     * @bodyParam topic_post_id int required ID del post. Example: 1
     * @bodyParam parent_id int ID del comentario padre (para respuestas). Example: 1
     * @bodyParam content string required Contenido del comentario. Example: Excelente post, muy útil
     * @bodyParam is_anonymous boolean Si el comentario es anónimo. Default: false. Example: false
     * @bodyParam notify_author boolean Si notificar al autor del post. Default: true. Example: true
     * @bodyParam notify_parent boolean Si notificar al autor del comentario padre. Default: true. Example: true
     *
     * @apiResource App\Http\Resources\V1\TopicCommentResource
     * @apiResourceModel App\Models\TopicComment
     *
     * @response 201 {"data": {...}, "message": "Comentario creado exitosamente"}
     * @response 422 {"message": "Datos inválidos", "errors": {...}}
     * @authenticated
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'topic_post_id' => 'required|exists:topic_posts,id',
            'parent_id' => 'nullable|exists:topic_comments,id',
            'content' => 'required|string|max:5000',
            'is_anonymous' => 'boolean',
            'notify_author' => 'boolean',
            'notify_parent' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Datos inválidos',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = Auth::guard('sanctum')->user();
        
        // Verificar que el post permite comentarios
        $post = TopicPost::findOrFail($request->topic_post_id);
        if (!$post->allows_comments) {
            return response()->json([
                'message' => 'Este post no permite comentarios'
            ], 422);
        }

        // Verificar que el usuario puede comentar en el tema
        $membership = $post->topic->memberships()
            ->where('user_id', $user->id)
            ->where('status', 'active')
            ->first();

        if (!$membership || !$membership->can_comment) {
            return response()->json([
                'message' => 'No tienes permisos para comentar en este tema'
            ], 403);
        }

        $comment = TopicComment::create(array_merge($validator->validated(), [
            'author_id' => $user->id,
            'status' => 'published',
            'is_verified' => $user->is_verified ?? false,
        ]));

        $comment->load(['topicPost', 'author', 'parent']);

        return response()->json([
            'data' => new TopicCommentResource($comment),
            'message' => 'Comentario creado exitosamente'
        ], 201);
    }

    /**
     * Display the specified comment
     *
     * Muestra un comentario específico con sus respuestas.
     *
     * @urlParam topicComment int required ID del comentario. Example: 1
     *
     * @apiResource App\Http\Resources\V1\TopicCommentResource
     * @apiResourceModel App\Models\TopicComment
     *
     * @response 404 {"message": "Comentario no encontrado"}
     */
    public function show(TopicComment $topicComment)
    {
        if ($topicComment->status === 'deleted') {
            return response()->json(['message' => 'Comentario no encontrado'], 404);
        }

        $topicComment->load(['topicPost', 'author', 'parent', 'replies.author']);
        return new TopicCommentResource($topicComment);
    }

    /**
     * Update the specified comment
     *
     * Actualiza un comentario existente.
     *
     * @urlParam topicComment int required ID del comentario. Example: 1
     * @bodyParam content string Nuevo contenido. Example: Contenido actualizado
     * @bodyParam is_anonymous boolean Si es anónimo. Example: false
     *
     * @apiResource App\Http\Resources\V1\TopicCommentResource
     * @apiResourceModel App\Models\TopicComment
     *
     * @response 403 {"message": "No autorizado"}
     * @response 422 {"message": "Datos inválidos", "errors": {...}}
     * @authenticated
     */
    public function update(Request $request, TopicComment $topicComment)
    {
        $user = Auth::guard('sanctum')->user();

        // Solo el autor puede editar
        if ($topicComment->author_id !== $user->id) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        if ($topicComment->status !== 'published') {
            return response()->json([
                'message' => 'No se puede editar un comentario no publicado'
            ], 422);
        }

        $validator = Validator::make($request->all(), [
            'content' => 'string|max:5000',
            'is_anonymous' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Datos inválidos',
                'errors' => $validator->errors()
            ], 422);
        }

        $topicComment->update(array_merge($validator->validated(), [
            'edited_at' => now(),
        ]));

        $topicComment->load(['topicPost', 'author', 'parent']);
        return new TopicCommentResource($topicComment);
    }

    /**
     * Remove the specified comment
     *
     * Elimina un comentario (marca como eliminado).
     *
     * @urlParam topicComment int required ID del comentario. Example: 1
     *
     * @response 200 {"message": "Comentario eliminado exitosamente"}
     * @response 403 {"message": "No autorizado"}
     * @authenticated
     */
    public function destroy(TopicComment $topicComment)
    {
        $user = Auth::guard('sanctum')->user();

        // Solo el autor o moderadores pueden eliminar
        if ($topicComment->author_id !== $user->id) {
            // TODO: Verificar si es moderador del tema
            return response()->json(['message' => 'No autorizado'], 403);
        }

        $topicComment->update(['status' => 'deleted']);

        return response()->json(['message' => 'Comentario eliminado exitosamente']);
    }

    /**
     * Get comments for a specific post
     *
     * Obtiene todos los comentarios de un post específico.
     *
     * @urlParam topicPost int required ID del post. Example: 1
     * @queryParam parent_id int Filtrar por comentario padre. Example: 1
     * @queryParam sort string Ordenamiento. Example: newest
     * @queryParam per_page int Cantidad por página. Example: 15
     *
     * @apiResourceCollection App\Http\Resources\V1\TopicCommentResource
     * @apiResourceModel App\Models\TopicComment
     */
    public function postComments(Request $request, TopicPost $topicPost)
    {
        $query = $topicPost->comments()
            ->with(['author', 'parent', 'replies.author'])
            ->when($request->parent_id, fn($q, $parentId) => $q->where('parent_id', $parentId))
            ->where('status', '!=', 'deleted');

        // Aplicar ordenamiento
        switch ($request->get('sort', 'newest')) {
            case 'oldest':
                $query->orderBy('created_at', 'asc');
                break;
            case 'most_liked':
                $query->orderBy('likes_count', 'desc')->orderBy('created_at', 'desc');
                break;
            case 'most_replied':
                $query->orderBy('replies_count', 'desc')->orderBy('created_at', 'desc');
                break;
            default: // newest
                $query->orderBy('created_at', 'desc');
        }

        $perPage = min($request->get('per_page', 15), 100);
        $comments = $query->paginate($perPage);

        return TopicCommentResource::collection($comments);
    }
}
