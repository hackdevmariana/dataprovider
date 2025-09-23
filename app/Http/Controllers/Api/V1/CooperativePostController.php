<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\CooperativePostResource;
use App\Models\CooperativePost;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

/**
 * @group Cooperative Posts
 * 
 * API endpoints for managing cooperative posts and announcements.
 */
class CooperativePostController extends Controller
{
    /**
     * Display cooperative posts
     */
    public function index(Request $request)
    {
        $query = CooperativePost::with(['cooperative', 'author'])
            ->when($request->cooperative_id, fn($q, $coopId) => $q->where('cooperative_id', $coopId))
            ->when($request->author_id, fn($q, $authorId) => $q->where('author_id', $authorId))
            ->when($request->post_type, fn($q, $type) => $q->where('post_type', $type))
            ->when($request->status, fn($q, $status) => $q->where('status', $status))
            ->when($request->visibility, fn($q, $visibility) => $q->where('visibility', $visibility))
            ->when($request->has('is_pinned'), fn($q) => $q->where('is_pinned', $request->boolean('is_pinned')))
            ->when($request->has('is_featured'), fn($q) => $q->where('is_featured', $request->boolean('is_featured')))
            ->orderBy('is_pinned', 'desc')
            ->orderBy('published_at', 'desc');

        // Filtrar por visibilidad según el usuario
        $user = Auth::guard('sanctum')->user();
        if (!$user) {
            $query->where('visibility', 'public');
        } else {
            // Los usuarios pueden ver posts públicos y de sus cooperativas
            $userCooperativeIds = $user->cooperatives()->pluck('cooperatives.id');
            $query->where(function($q) use ($userCooperativeIds) {
                $q->where('visibility', 'public')
                  ->orWhere(function($subQ) use ($userCooperativeIds) {
                      $subQ->where('visibility', 'members_only')
                           ->whereIn('cooperative_id', $userCooperativeIds);
                  });
                
                // Solo admins pueden ver posts de junta
                if (auth()->user() && auth()->user()->hasRole('admin')) {
                    $q->orWhere('visibility', 'board_only');
                }
            });
        }

        $perPage = min($request->get('per_page', 15), 100);
        $posts = $query->paginate($perPage);

        return CooperativePostResource::collection($posts);
    }

    /**
     * Create a new cooperative post
     */
    public function store(Request $request)
    {
        $user = Auth::guard('sanctum')->user();
        
        if (!$user) {
            return response()->json(['message' => 'Usuario no autenticado'], 401);
        }

        $validator = Validator::make($request->all(), [
            'cooperative_id' => 'required|exists:cooperatives,id',
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'post_type' => 'required|in:announcement,news,event,discussion,update',
            'visibility' => 'required|in:public,members_only,board_only',
            'comments_enabled' => 'boolean',
            'is_pinned' => 'boolean',
            'is_featured' => 'boolean',
            'published_at' => 'nullable|date',
            'pinned_until' => 'nullable|date|after:today',
            'attachments' => 'nullable|array',
            'metadata' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Los datos proporcionados no son válidos',
                'errors' => $validator->errors()
            ], 422);
        }

        // Verificar permisos en la cooperativa
        $cooperative = \App\Models\Cooperative::find($request->cooperative_id);
        if (!$user->cooperatives()->where('cooperatives.id', $cooperative->id)->exists() && !$user->hasRole('admin')) {
            return response()->json(['message' => 'No tienes permisos para publicar en esta cooperativa'], 403);
        }

        $post = CooperativePost::create(array_merge($validator->validated(), [
            'author_id' => $user->id,
            'status' => $request->published_at ? 'published' : 'draft',
            'published_at' => $request->published_at ?? now(),
        ]));

        $post->load(['cooperative', 'author']);

        return response()->json([
            'data' => new CooperativePostResource($post),
            'message' => 'Post creado exitosamente'
        ], 201);
    }

    /**
     * Display the specified post
     */
    public function show(CooperativePost $cooperativePost)
    {
        $user = Auth::guard('sanctum')->user();
        
        // Verificar permisos de visibilidad
        if ($cooperativePost->visibility === 'members_only') {
            if (!$user || !$user->cooperatives()->where('cooperatives.id', $cooperativePost->cooperative_id)->exists()) {
                return response()->json(['message' => 'No tienes permisos para ver este post'], 403);
            }
        } elseif ($cooperativePost->visibility === 'board_only') {
            if (!$user || !$user->hasRole('admin')) {
                return response()->json(['message' => 'No tienes permisos para ver este post'], 403);
            }
        }

        // Incrementar contador de vistas
        $cooperativePost->increment('views_count');

        $cooperativePost->load(['cooperative', 'author']);
        
        return new CooperativePostResource($cooperativePost);
    }

    /**
     * Update the specified post
     */
    public function update(Request $request, CooperativePost $cooperativePost)
    {
        $user = Auth::guard('sanctum')->user();
        
        // Solo el autor o un admin pueden editar
        if (!$user || ($user->id !== $cooperativePost->author_id && !$user->hasRole('admin'))) {
            return response()->json(['message' => 'No tienes permisos para editar este post'], 403);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'sometimes|string|max:255',
            'content' => 'sometimes|string',
            'post_type' => 'sometimes|in:announcement,news,event,discussion,update',
            'visibility' => 'sometimes|in:public,members_only,board_only',
            'comments_enabled' => 'sometimes|boolean',
            'is_pinned' => 'sometimes|boolean',
            'is_featured' => 'sometimes|boolean',
            'pinned_until' => 'sometimes|nullable|date|after:today',
            'attachments' => 'sometimes|array',
            'metadata' => 'sometimes|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Los datos proporcionados no son válidos',
                'errors' => $validator->errors()
            ], 422);
        }

        $cooperativePost->update($validator->validated());
        $cooperativePost->load(['cooperative', 'author']);

        return response()->json([
            'data' => new CooperativePostResource($cooperativePost),
            'message' => 'Post actualizado exitosamente'
        ]);
    }

    /**
     * Publish a draft post
     */
    public function publish(CooperativePost $cooperativePost)
    {
        $user = Auth::guard('sanctum')->user();
        
        if (!$user || ($user->id !== $cooperativePost->author_id && !$user->hasRole('admin'))) {
            return response()->json(['message' => 'No tienes permisos para publicar este post'], 403);
        }

        if ($cooperativePost->status !== 'draft') {
            return response()->json(['message' => 'Solo se pueden publicar borradores'], 400);
        }

        $cooperativePost->update([
            'status' => 'published',
            'published_at' => now(),
        ]);

        $cooperativePost->load(['cooperative', 'author']);

        return response()->json([
            'data' => new CooperativePostResource($cooperativePost),
            'message' => 'Post publicado exitosamente'
        ]);
    }

    /**
     * Archive a post
     */
    public function archive(CooperativePost $cooperativePost)
    {
        $user = Auth::guard('sanctum')->user();
        
        if (!$user || ($user->id !== $cooperativePost->author_id && !$user->hasRole('admin'))) {
            return response()->json(['message' => 'No tienes permisos para archivar este post'], 403);
        }

        $cooperativePost->update(['status' => 'archived']);
        $cooperativePost->load(['cooperative', 'author']);

        return response()->json([
            'data' => new CooperativePostResource($cooperativePost),
            'message' => 'Post archivado exitosamente'
        ]);
    }

    /**
     * Pin/unpin a post
     */
    public function togglePin(CooperativePost $cooperativePost, Request $request)
    {
        $user = Auth::guard('sanctum')->user();
        
        if (!$user || !$user->hasRole('admin')) {
            return response()->json(['message' => 'No tienes permisos para fijar posts'], 403);
        }

        $validator = Validator::make($request->all(), [
            'pinned_until' => 'nullable|date|after:today',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Los datos proporcionados no son válidos',
                'errors' => $validator->errors()
            ], 422);
        }

        $cooperativePost->update([
            'is_pinned' => !$cooperativePost->is_pinned,
            'pinned_until' => $request->pinned_until,
        ]);

        $cooperativePost->load(['cooperative', 'author']);

        $action = $cooperativePost->is_pinned ? 'fijado' : 'desfijado';

        return response()->json([
            'data' => new CooperativePostResource($cooperativePost),
            'message' => "Post {$action} exitosamente"
        ]);
    }

    /**
     * Get posts statistics
     */
    public function stats(Request $request)
    {
        $query = CooperativePost::query();

        if ($request->cooperative_id) {
            $query->where('cooperative_id', $request->cooperative_id);
        }

        if ($request->period && $request->period !== 'all') {
            $date = match($request->period) {
                'week' => now()->subWeek(),
                'month' => now()->subMonth(),
                'year' => now()->subYear(),
                default => now()->subMonth()
            };
            $query->where('published_at', '>=', $date);
        }

        $posts = $query->get();

        $stats = [
            'total_posts' => $posts->count(),
            'by_status' => $posts->groupBy('status')->map->count(),
            'by_type' => $posts->groupBy('post_type')->map->count(),
            'by_visibility' => $posts->groupBy('visibility')->map->count(),
            'total_views' => $posts->sum('views_count'),
            'total_likes' => $posts->sum('likes_count'),
            'pinned_posts' => $posts->where('is_pinned', true)->count(),
            'featured_posts' => $posts->where('is_featured', true)->count(),
            'most_viewed' => CooperativePostResource::collection(
                $posts->sortByDesc('views_count')->take(5)
            ),
            'most_liked' => CooperativePostResource::collection(
                $posts->sortByDesc('likes_count')->take(5)
            ),
        ];

        return response()->json(['data' => $stats]);
    }

    /**
     * Remove the specified post
     */
    public function destroy(CooperativePost $cooperativePost)
    {
        $user = Auth::guard('sanctum')->user();
        
        if (!$user || ($user->id !== $cooperativePost->author_id && !$user->hasRole('admin'))) {
            return response()->json(['message' => 'No tienes permisos para eliminar este post'], 403);
        }

        $cooperativePost->delete();

        return response()->json(['message' => 'Post eliminado exitosamente']);
    }
}