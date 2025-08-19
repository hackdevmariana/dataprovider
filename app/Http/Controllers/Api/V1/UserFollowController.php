<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\UserFollowResource;
use App\Http\Resources\V1\UserResource;
use App\Models\UserFollow;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

/**
 * @tags User Following
 * @group Seguimiento de Usuarios
 * 
 * API endpoints para gestionar el seguimiento entre usuarios y configurar feeds personalizados.
 */
class UserFollowController extends Controller
{
    /**
     * Seguir a un usuario
     * 
     * Permite al usuario autenticado seguir a otro usuario con configuraciones personalizadas.
     * 
     * @urlParam user integer required ID del usuario a seguir. Example: 2
     * @bodyParam follow_type string Tipo de seguimiento. Example: general
     * @bodyParam notification_frequency string Frecuencia de notificaciones. Example: instant
     * @bodyParam show_in_main_feed boolean Mostrar en feed principal. Example: true
     * @bodyParam prioritize_in_feed boolean Priorizar en feed. Example: false
     * @bodyParam feed_weight integer Peso en algoritmo de feed (0-200). Example: 100
     * @bodyParam follow_reason string Razón del seguimiento. Example: Experto en energía solar
     * @bodyParam interests array Intereses específicos en este usuario. Example: ["energia_solar", "eficiencia"]
     * @bodyParam tags array Etiquetas personalizadas. Example: ["experto", "local"]
     * 
     * @response 201 {
     *   "data": {
     *     "id": 1,
     *     "follower": {"id": 1, "name": "Seguidor"},
     *     "following": {"id": 2, "name": "Seguido"},
     *     "follow_type": "general",
     *     "notification_frequency": "instant",
     *     "is_mutual": false,
     *     "status": "active",
     *     "followed_at": "2025-01-15T10:30:00Z"
     *   },
     *   "message": "Ahora sigues a este usuario"
     * }
     * 
     * @response 422 {
     *   "message": "Ya sigues a este usuario",
     *   "errors": {"following_id": ["Seguimiento duplicado"]}
     * }
     */
    public function follow(Request $request, User $user): JsonResponse
    {
        $follower = Auth::guard('sanctum')->user();

        // Verificar que no se siga a sí mismo
        if ($follower->id === $user->id) {
            return response()->json([
                'message' => 'No puedes seguirte a ti mismo'
            ], 422);
        }

        $request->validate([
            'follow_type' => [
                'nullable',
                Rule::in(['general', 'expertise', 'projects', 'achievements', 'energy_activity', 'installations', 'investments', 'content', 'community'])
            ],
            'notification_frequency' => [
                'nullable',
                Rule::in(['instant', 'daily_digest', 'weekly_digest', 'monthly_digest', 'never'])
            ],
            'show_in_main_feed' => 'boolean',
            'prioritize_in_feed' => 'boolean',
            'feed_weight' => 'integer|min:0|max:200',
            'follow_reason' => 'nullable|string|max:500',
            'interests' => 'nullable|array',
            'interests.*' => 'string|max:100',
            'tags' => 'nullable|array',
            'tags.*' => 'string|max:50',
        ]);

        // Verificar si ya existe el seguimiento
        $existingFollow = UserFollow::where([
            'follower_id' => $follower->id,
            'following_id' => $user->id,
        ])->first();

        if ($existingFollow) {
            if ($existingFollow->status === 'active') {
                return response()->json([
                    'message' => 'Ya sigues a este usuario',
                    'errors' => ['following_id' => ['Seguimiento duplicado']]
                ], 422);
            } else {
                // Reactivar seguimiento existente
                $existingFollow->update([
                    'status' => 'active',
                    'follow_type' => $request->get('follow_type', 'general'),
                    'notification_frequency' => $request->get('notification_frequency', 'instant'),
                    'show_in_main_feed' => $request->boolean('show_in_main_feed', true),
                    'prioritize_in_feed' => $request->boolean('prioritize_in_feed', false),
                    'feed_weight' => $request->integer('feed_weight', 100),
                    'follow_reason' => $request->follow_reason,
                    'interests' => $request->interests,
                    'tags' => $request->tags,
                    'followed_at' => now(),
                    'status_changed_at' => now(),
                ]);

                return response()->json([
                    'data' => new UserFollowResource($existingFollow),
                    'message' => 'Seguimiento reactivado exitosamente'
                ], 200);
            }
        }

        // Crear nuevo seguimiento
        $follow = UserFollow::create([
            'follower_id' => $follower->id,
            'following_id' => $user->id,
            'follow_type' => $request->get('follow_type', 'general'),
            'notification_frequency' => $request->get('notification_frequency', 'instant'),
            'show_in_main_feed' => $request->boolean('show_in_main_feed', true),
            'prioritize_in_feed' => $request->boolean('prioritize_in_feed', false),
            'feed_weight' => $request->integer('feed_weight', 100),
            'follow_reason' => $request->follow_reason,
            'interests' => $request->interests,
            'tags' => $request->tags,
            'followed_at' => now(),
            'status' => 'active',
        ]);

        $follow->load(['follower', 'following']);

        return response()->json([
            'data' => new UserFollowResource($follow),
            'message' => 'Ahora sigues a este usuario'
        ], 201);
    }

    /**
     * Dejar de seguir a un usuario
     * 
     * @urlParam user integer required ID del usuario a dejar de seguir. Example: 2
     * 
     * @response 200 {
     *   "message": "Ya no sigues a este usuario"
     * }
     * 
     * @response 404 {
     *   "message": "No sigues a este usuario"
     * }
     */
    public function unfollow(User $user): JsonResponse
    {
        $follower = Auth::guard('sanctum')->user();

        $follow = UserFollow::where([
            'follower_id' => $follower->id,
            'following_id' => $user->id,
            'status' => 'active',
        ])->first();

        if (!$follow) {
            return response()->json([
                'message' => 'No sigues a este usuario'
            ], 404);
        }

        $follow->update(['status' => 'withdrawn']);

        return response()->json([
            'message' => 'Ya no sigues a este usuario'
        ]);
    }

    /**
     * Usuarios que sigue el usuario autenticado
     * 
     * @queryParam type string Filtrar por tipo de seguimiento. Example: general
     * @queryParam mutual boolean Solo seguimientos mutuos. Example: true
     * @queryParam limit integer Número de seguimientos a retornar. Example: 20
     * @queryParam offset integer Número de seguimientos a saltar. Example: 0
     * 
     * @response 200 {
     *   "data": [
     *     {
     *       "id": 1,
     *       "following": {
     *         "id": 2,
     *         "name": "Usuario Seguido",
     *         "email": "usuario@example.com"
     *       },
     *       "follow_type": "general",
     *       "is_mutual": true,
     *       "engagement_score": 85.5,
     *       "followed_at": "2025-01-10T08:00:00Z"
     *     }
     *   ]
     * }
     */
    public function following(Request $request): JsonResponse
    {
        $user = Auth::guard('sanctum')->user();
        $limit = min($request->integer('limit', 20), 50);
        $offset = $request->integer('offset', 0);

        $query = $user->followingRelationships()->active();

        if ($request->filled('type')) {
            $query->ofType($request->type);
        }

        if ($request->boolean('mutual')) {
            $query->mutual();
        }

        $follows = $query->with(['following'])
                        ->orderByDesc('engagement_score')
                        ->orderByDesc('followed_at')
                        ->offset($offset)
                        ->limit($limit)
                        ->get();

        return response()->json([
            'data' => UserFollowResource::collection($follows),
            'meta' => [
                'count' => $follows->count(),
                'total_following' => $user->activeFollowing()->count(),
                'mutual_follows' => $user->mutualFollows()->count(),
            ]
        ]);
    }

    /**
     * Seguidores del usuario autenticado
     * 
     * @queryParam limit integer Número de seguidores a retornar. Example: 20
     * @queryParam offset integer Número de seguidores a saltar. Example: 0
     * 
     * @response 200 {
     *   "data": [
     *     {
     *       "id": 1,
     *       "follower": {
     *         "id": 3,
     *         "name": "Seguidor",
     *         "email": "seguidor@example.com"
     *       },
     *       "follow_type": "expertise",
     *       "is_mutual": false,
     *       "followed_at": "2025-01-12T14:00:00Z"
     *     }
     *   ]
     * }
     */
    public function followers(Request $request): JsonResponse
    {
        $user = Auth::guard('sanctum')->user();
        $limit = min($request->integer('limit', 20), 50);
        $offset = $request->integer('offset', 0);

        $follows = $user->followerRelationships()
                       ->active()
                       ->with(['follower'])
                       ->orderByDesc('followed_at')
                       ->offset($offset)
                       ->limit($limit)
                       ->get();

        return response()->json([
            'data' => UserFollowResource::collection($follows),
            'meta' => [
                'count' => $follows->count(),
                'total_followers' => $user->activeFollowers()->count(),
                'mutual_follows' => $user->mutualFollows()->count(),
            ]
        ]);
    }

    /**
     * Usuarios sugeridos para seguir
     * 
     * Obtiene usuarios recomendados basado en actividad, intereses comunes y conexiones mutuas.
     * 
     * @queryParam limit integer Número de sugerencias a retornar. Example: 10
     * @queryParam interests array Filtrar por intereses específicos. Example: ["energia_solar"]
     * @queryParam location string Coordenadas lat,lng para usuarios cercanos. Example: 40.4168,-3.7038
     * @queryParam radius integer Radio en km para búsqueda geográfica. Example: 50
     * 
     * @response 200 {
     *   "data": [
     *     {
     *       "user": {
     *         "id": 4,
     *         "name": "Usuario Sugerido",
     *         "email": "sugerido@example.com"
     *       },
     *       "suggestion_score": 85.5,
     *       "suggestion_reasons": [
     *         "Intereses comunes en energía solar",
     *         "Miembro de la misma cooperativa",
     *         "Alta actividad reciente"
     *       ],
     *       "mutual_connections": 3,
     *       "activity_score": 120
     *     }
     *   ]
     * }
     */
    public function suggestions(Request $request): JsonResponse
    {
        $user = Auth::guard('sanctum')->user();
        $limit = min($request->integer('limit', 10), 20);

        // Obtener usuarios ya seguidos para excluir
        $alreadyFollowing = $user->activeFollowing()->pluck('users.id')->toArray();
        $alreadyFollowing[] = $user->id; // Excluir a sí mismo

        $query = User::whereNotIn('id', $alreadyFollowing);

        // Calcular score de sugerencia
        $query->selectRaw('
            users.*,
            (
                -- Score base por actividad reciente
                (SELECT COUNT(*) FROM activity_feeds WHERE user_id = users.id AND created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)) * 2 +
                
                -- Score por seguidores (popularidad)
                (SELECT COUNT(*) FROM user_follows WHERE following_id = users.id AND status = "active") * 0.5 +
                
                -- Score por cooperativas comunes
                (SELECT COUNT(*) FROM cooperative_user_members cum1 
                 JOIN cooperative_user_members cum2 ON cum1.cooperative_id = cum2.cooperative_id 
                 WHERE cum1.user_id = users.id AND cum2.user_id = ? AND cum1.is_active = 1 AND cum2.is_active = 1) * 10 +
                
                -- Score por engagement alto
                (SELECT COALESCE(AVG(engagement_score), 0) FROM activity_feeds WHERE user_id = users.id) * 0.1
                
            ) as suggestion_score
        ', [$user->id]);

        // Filtros adicionales
        if ($request->filled('interests')) {
            // Aquí podrías implementar filtrado por intereses si tienes un campo de perfil
        }

        if ($request->filled('location') && $request->filled('radius')) {
            [$lat, $lng] = explode(',', $request->string('location'));
            $radius = $request->integer('radius', 50);
            
            // Filtrar por ubicación si tienes datos de ubicación de usuarios
            // Esto requeriría campos de latitud/longitud en la tabla users
        }

        $suggestions = $query->having('suggestion_score', '>', 0)
                           ->orderByDesc('suggestion_score')
                           ->limit($limit)
                           ->get();

        // Enriquecer con información adicional
        $enrichedSuggestions = $suggestions->map(function ($suggestedUser) use ($user) {
            $mutualConnections = $this->getMutualConnectionsCount($user, $suggestedUser);
            $suggestionReasons = $this->getSuggestionReasons($user, $suggestedUser);
            
            return [
                'user' => new UserResource($suggestedUser),
                'suggestion_score' => round($suggestedUser->suggestion_score, 1),
                'suggestion_reasons' => $suggestionReasons,
                'mutual_connections' => $mutualConnections,
                'activity_score' => $suggestedUser->activityFeeds()->sum('engagement_score'),
                'followers_count' => $suggestedUser->activeFollowers()->count(),
                'following_count' => $suggestedUser->activeFollowing()->count(),
            ];
        });

        return response()->json([
            'data' => $enrichedSuggestions,
            'meta' => [
                'count' => $enrichedSuggestions->count(),
                'algorithm_version' => 'v1.0',
            ]
        ]);
    }

    /**
     * Configurar seguimiento existente
     * 
     * @urlParam userFollow integer required ID del seguimiento a configurar. Example: 1
     * @bodyParam notification_frequency string Frecuencia de notificaciones. Example: daily_digest
     * @bodyParam show_in_main_feed boolean Mostrar en feed principal. Example: false
     * @bodyParam prioritize_in_feed boolean Priorizar en feed. Example: true
     * @bodyParam feed_weight integer Peso en algoritmo de feed (0-200). Example: 150
     * 
     * @response 200 {
     *   "data": {
     *     "id": 1,
     *     "notification_frequency": "daily_digest",
     *     "show_in_main_feed": false,
     *     "prioritize_in_feed": true,
     *     "feed_weight": 150
     *   },
     *   "message": "Configuración actualizada exitosamente"
     * }
     */
    public function configure(Request $request, UserFollow $userFollow): JsonResponse
    {
        $user = Auth::guard('sanctum')->user();

        // Verificar que el usuario es el dueño del seguimiento
        if ($userFollow->follower_id !== $user->id) {
            return response()->json([
                'message' => 'No tienes permisos para configurar este seguimiento'
            ], 403);
        }

        $request->validate([
            'notification_frequency' => [
                'nullable',
                Rule::in(['instant', 'daily_digest', 'weekly_digest', 'monthly_digest', 'never'])
            ],
            'show_in_main_feed' => 'boolean',
            'prioritize_in_feed' => 'boolean',
            'feed_weight' => 'integer|min:0|max:200',
            'interests' => 'nullable|array',
            'tags' => 'nullable|array',
        ]);

        $userFollow->update($request->only([
            'notification_frequency',
            'show_in_main_feed',
            'prioritize_in_feed',
            'feed_weight',
            'interests',
            'tags',
        ]));

        return response()->json([
            'data' => new UserFollowResource($userFollow),
            'message' => 'Configuración actualizada exitosamente'
        ]);
    }

    /**
     * Estadísticas de seguimiento
     * 
     * @response 200 {
     *   "data": {
     *     "following_count": 25,
     *     "followers_count": 18,
     *     "mutual_follows_count": 12,
     *     "high_engagement_follows": 8,
     *     "notification_settings": {
     *       "instant": 10,
     *       "daily_digest": 8,
     *       "weekly_digest": 5,
     *       "never": 2
     *     }
     *   }
     * }
     */
    public function stats(Request $request): JsonResponse
    {
        $user = Auth::guard('sanctum')->user();

        $stats = [
            'following_count' => $user->activeFollowing()->count(),
            'followers_count' => $user->activeFollowers()->count(),
            'mutual_follows_count' => $user->mutualFollows()->count(),
            'high_engagement_follows' => $user->followingRelationships()
                ->active()->highEngagement(50)->count(),
        ];

        // Distribución de configuraciones de notificación
        $notificationSettings = $user->followingRelationships()
            ->active()
            ->select('notification_frequency', DB::raw('count(*) as count'))
            ->groupBy('notification_frequency')
            ->get()
            ->pluck('count', 'notification_frequency')
            ->toArray();

        $stats['notification_settings'] = $notificationSettings;

        // Tipos de seguimiento
        $followTypes = $user->followingRelationships()
            ->active()
            ->select('follow_type', DB::raw('count(*) as count'))
            ->groupBy('follow_type')
            ->get()
            ->pluck('count', 'follow_type')
            ->toArray();

        $stats['follow_types'] = $followTypes;

        return response()->json(['data' => $stats]);
    }

    // Métodos auxiliares privados

    /**
     * Obtener número de conexiones mutuas entre dos usuarios
     */
    private function getMutualConnectionsCount(User $user1, User $user2): int
    {
        return $user1->activeFollowing()
                    ->whereIn('users.id', $user2->activeFollowing()->pluck('users.id'))
                    ->count();
    }

    /**
     * Obtener razones de sugerencia para un usuario
     */
    private function getSuggestionReasons(User $user, User $suggestedUser): array
    {
        $reasons = [];

        // Cooperativas comunes
        $commonCooperatives = $user->cooperatives()
            ->whereIn('cooperatives.id', $suggestedUser->cooperatives()->pluck('cooperatives.id'))
            ->count();
        
        if ($commonCooperatives > 0) {
            $reasons[] = "Miembro de {$commonCooperatives} cooperativa(s) común(es)";
        }

        // Alta actividad reciente
        $recentActivities = $suggestedUser->activityFeeds()
            ->where('created_at', '>=', now()->subDays(7))
            ->count();
        
        if ($recentActivities >= 3) {
            $reasons[] = "Alta actividad reciente ({$recentActivities} actividades esta semana)";
        }

        // Conexiones mutuas
        $mutualConnections = $this->getMutualConnectionsCount($user, $suggestedUser);
        if ($mutualConnections > 0) {
            $reasons[] = "{$mutualConnections} conexión(es) mutua(s)";
        }

        // Experto verificado
        if ($suggestedUser->activityFeeds()->where('activity_type', 'expert_verified')->exists()) {
            $reasons[] = "Experto verificado";
        }

        return $reasons;
    }
}
