<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\TopicFollowingResource;
use App\Models\TopicFollowing;
use App\Models\Topic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

/**
 * @group Topic Following
 * 
 * API endpoints for managing topic following relationships.
 */
class TopicFollowingController extends Controller
{
    /**
     * Display user's topic followings
     */
    public function index(Request $request)
    {
        $user = Auth::guard('sanctum')->user();
        
        if (!$user) {
            return response()->json(['message' => 'Usuario no autenticado'], 401);
        }

        $query = $user->topicFollowings()
            ->with(['topic'])
            ->when($request->follow_type, fn($q, $type) => $q->where('follow_type', $type))
            ->when($request->has('notifications_enabled'), fn($q) => $q->where('notifications_enabled', $request->boolean('notifications_enabled')))
            ->orderBy('followed_at', 'desc');

        $followings = $query->paginate($request->get('per_page', 15));

        return TopicFollowingResource::collection($followings);
    }

    /**
     * Follow a topic
     */
    public function store(Request $request)
    {
        $user = Auth::guard('sanctum')->user();
        
        if (!$user) {
            return response()->json(['message' => 'Usuario no autenticado'], 401);
        }

        $validator = Validator::make($request->all(), [
            'topic_id' => 'required|exists:topics,id',
            'follow_type' => 'required|in:following,watching,ignoring',
            'notifications_enabled' => 'boolean',
            'notification_preferences' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Los datos proporcionados no son válidos',
                'errors' => $validator->errors()
            ], 422);
        }

        // Verificar si ya sigue el tema
        $existing = TopicFollowing::where('user_id', $user->id)
            ->where('topic_id', $request->topic_id)
            ->first();

        if ($existing) {
            // Actualizar seguimiento existente
            $existing->update($validator->validated());
            $following = $existing;
        } else {
            // Crear nuevo seguimiento
            $following = TopicFollowing::create(array_merge($validator->validated(), [
                'user_id' => $user->id,
                'followed_at' => now(),
            ]));
        }

        $following->load(['topic']);

        return response()->json([
            'data' => new TopicFollowingResource($following),
            'message' => 'Seguimiento de tema actualizado exitosamente'
        ], $existing ? 200 : 201);
    }

    /**
     * Display the specified following
     */
    public function show(TopicFollowing $topicFollowing)
    {
        $user = Auth::guard('sanctum')->user();
        
        if (!$user || $user->id !== $topicFollowing->user_id) {
            return response()->json(['message' => 'No tienes permisos para ver este seguimiento'], 403);
        }

        $topicFollowing->load(['topic']);
        
        return new TopicFollowingResource($topicFollowing);
    }

    /**
     * Update the specified following
     */
    public function update(Request $request, TopicFollowing $topicFollowing)
    {
        $user = Auth::guard('sanctum')->user();
        
        if (!$user || $user->id !== $topicFollowing->user_id) {
            return response()->json(['message' => 'No tienes permisos para actualizar este seguimiento'], 403);
        }

        $validator = Validator::make($request->all(), [
            'follow_type' => 'required|in:following,watching,ignoring',
            'notifications_enabled' => 'boolean',
            'notification_preferences' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Los datos proporcionados no son válidos',
                'errors' => $validator->errors()
            ], 422);
        }

        $topicFollowing->update($validator->validated());
        $topicFollowing->load(['topic']);

        return response()->json([
            'data' => new TopicFollowingResource($topicFollowing),
            'message' => 'Seguimiento actualizado exitosamente'
        ]);
    }

    /**
     * Unfollow a topic
     */
    public function destroy(TopicFollowing $topicFollowing)
    {
        $user = Auth::guard('sanctum')->user();
        
        if (!$user || $user->id !== $topicFollowing->user_id) {
            return response()->json(['message' => 'No tienes permisos para eliminar este seguimiento'], 403);
        }

        $topicFollowing->delete();

        return response()->json(['message' => 'Has dejado de seguir el tema exitosamente']);
    }

    /**
     * Get following statistics for user
     */
    public function stats(Request $request)
    {
        $user = Auth::guard('sanctum')->user();
        
        if (!$user) {
            return response()->json(['message' => 'Usuario no autenticado'], 401);
        }

        $followings = $user->topicFollowings()->with(['topic'])->get();

        $stats = [
            'total_followings' => $followings->count(),
            'by_type' => $followings->groupBy('follow_type')->map->count(),
            'with_notifications' => $followings->where('notifications_enabled', true)->count(),
            'most_visited_topics' => $followings->sortByDesc('visit_count')->take(5)->map(function($following) {
                return [
                    'topic' => $following->topic->name,
                    'visits' => $following->visit_count,
                    'last_visited' => $following->last_visited_at,
                ];
            })->values(),
        ];

        return response()->json(['data' => $stats]);
    }

    /**
     * Mark topic as visited
     */
    public function markVisited(Request $request, Topic $topic)
    {
        $user = Auth::guard('sanctum')->user();
        
        if (!$user) {
            return response()->json(['message' => 'Usuario no autenticado'], 401);
        }

        $following = TopicFollowing::where('user_id', $user->id)
            ->where('topic_id', $topic->id)
            ->first();

        if ($following) {
            $following->update([
                'last_visited_at' => now(),
                'visit_count' => $following->visit_count + 1,
            ]);
        }

        return response()->json(['message' => 'Visita registrada']);
    }
}