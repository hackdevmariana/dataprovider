<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\SocialInteractionResource;
use App\Models\SocialInteraction;
use App\Models\ActivityFeed;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

/**
 * @tags Social Interactions
 * @group Interacciones Sociales
 * 
 * API endpoints para gestionar las interacciones sociales (likes, shares, bookmarks, etc.)
 */
class SocialInteractionController extends Controller
{
    /**
     * Crear una nueva interacción social
     * 
     * Permite al usuario interactuar con contenido (like, love, share, bookmark, etc.)
     * 
     * @bodyParam interactable_type string required Tipo del objeto (App\Models\ActivityFeed, etc.). Example: App\Models\ActivityFeed
     * @bodyParam interactable_id integer required ID del objeto. Example: 1
     * @bodyParam interaction_type string required Tipo de interacción. Example: like
     * @bodyParam interaction_note string Nota opcional sobre la interacción. Example: ¡Excelente trabajo!
     * @bodyParam is_public boolean Si la interacción es pública. Example: true
     * @bodyParam notify_author boolean Si notificar al autor del contenido. Example: true
     * 
     * @response 201 {
     *   "data": {
     *     "id": 1,
     *     "user": {"id": 1, "name": "Usuario"},
     *     "interaction_type": "like",
     *     "interaction_note": "¡Excelente trabajo!",
     *     "is_public": true,
     *     "engagement_weight": 1,
     *     "created_at": "2025-01-15T10:30:00Z"
     *   },
     *   "message": "Interacción registrada exitosamente"
     * }
     * 
     * @response 422 {
     *   "message": "Ya has interactuado con este contenido de esta forma",
     *   "errors": {"interaction_type": ["Interacción duplicada"]}
     * }
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'interactable_type' => 'required|string',
            'interactable_id' => 'required|integer',
            'interaction_type' => [
                'required',
                Rule::in(['like', 'love', 'wow', 'celebrate', 'support', 'share', 'bookmark', 'follow', 'subscribe', 'report', 'hide', 'block'])
            ],
            'interaction_note' => 'nullable|string|max:500',
            'is_public' => 'boolean',
            'notify_author' => 'boolean',
        ]);

        $user = Auth::guard('sanctum')->user();
        
        if (!$user) {
            return response()->json([
                'message' => 'Usuario no autenticado'
            ], 401);
        }

        // Verificar si ya existe esta interacción
        $existingInteraction = SocialInteraction::where([
            'user_id' => $user->id,
            'interactable_type' => $request->interactable_type,
            'interactable_id' => $request->interactable_id,
            'interaction_type' => $request->interaction_type,
        ])->first();

        if ($existingInteraction) {
            if ($existingInteraction->status === 'active') {
                return response()->json([
                    'message' => 'Ya has interactuado con este contenido de esta forma',
                    'errors' => ['interaction_type' => ['Interacción duplicada']]
                ], 422);
            } else {
                // Reactivar interacción existente
                $existingInteraction->update([
                    'status' => 'active',
                    'interaction_note' => $request->interaction_note,
                    'is_public' => $request->boolean('is_public', true),
                    'notify_author' => $request->boolean('notify_author', true),
                ]);

                return response()->json([
                    'data' => new SocialInteractionResource($existingInteraction),
                    'message' => 'Interacción reactivada exitosamente'
                ], 200);
            }
        }

        // Verificar que el objeto existe
        $interactableClass = $request->interactable_type;
        if (!class_exists($interactableClass)) {
            return response()->json([
                'message' => 'Tipo de objeto no válido'
            ], 400);
        }

        $interactable = $interactableClass::find($request->interactable_id);
        if (!$interactable) {
            return response()->json([
                'message' => 'Objeto no encontrado'
            ], 404);
        }

        // Crear la interacción
        $interaction = SocialInteraction::create([
            'user_id' => $user->id,
            'interactable_type' => $request->interactable_type,
            'interactable_id' => $request->interactable_id,
            'interaction_type' => $request->interaction_type,
            'interaction_note' => $request->interaction_note,
            'is_public' => $request->boolean('is_public', true),
            'notify_author' => $request->boolean('notify_author', true),
            'engagement_weight' => $this->getEngagementWeight($request->interaction_type),
            'source' => 'api',
            'device_type' => $request->header('User-Agent') ? 'mobile' : 'web',
        ]);

        return response()->json([
            'data' => new SocialInteractionResource($interaction),
            'message' => 'Interacción registrada exitosamente'
        ], 201);
    }

    /**
     * Eliminar una interacción social
     * 
     * Permite al usuario retirar una interacción previamente realizada.
     * 
     * @urlParam id integer required ID de la interacción. Example: 1
     * 
     * @response 200 {
     *   "message": "Interacción eliminada exitosamente"
     * }
     * 
     * @response 403 {
     *   "message": "No tienes permisos para eliminar esta interacción"
     * }
     */
    public function destroy(SocialInteraction $socialInteraction): JsonResponse
    {
        $user = Auth::guard('sanctum')->user();

        // Verificar que el usuario es el dueño de la interacción
        if ($socialInteraction->user_id !== $user->id) {
            return response()->json([
                'message' => 'No tienes permisos para eliminar esta interacción'
            ], 403);
        }

        // Marcar como retirada en lugar de eliminar físicamente
        $socialInteraction->update(['status' => 'withdrawn']);

        return response()->json([
            'message' => 'Interacción eliminada exitosamente'
        ]);
    }

    /**
     * Interacciones de un objeto específico
     * 
     * Obtiene todas las interacciones de un objeto específico.
     * 
     * @queryParam interactable_type string required Tipo del objeto. Example: App\Models\ActivityFeed
     * @queryParam interactable_id integer required ID del objeto. Example: 1
     * @queryParam type string Filtrar por tipo de interacción. Example: like
     * @queryParam limit integer Número de interacciones a retornar. Example: 20
     * 
     * @response 200 {
     *   "data": [
     *     {
     *       "id": 1,
     *       "user": {"id": 1, "name": "Usuario"},
     *       "interaction_type": "like",
     *       "interaction_note": "Me gusta",
     *       "created_at": "2025-01-15T10:30:00Z"
     *     }
     *   ],
     *   "meta": {
     *     "total": 25,
     *     "by_type": {
     *       "like": 15,
     *       "love": 5,
     *       "share": 3,
     *       "bookmark": 2
     *     }
     *   }
     * }
     */
    public function forObject(Request $request): JsonResponse
    {
        $request->validate([
            'interactable_type' => 'required|string',
            'interactable_id' => 'required|integer',
            'type' => 'nullable|string',
            'limit' => 'integer|min:1|max:100',
        ]);

        $query = SocialInteraction::withObject(
            $request->interactable_type,
            $request->interactable_id
        )->active()->public();

        if ($request->filled('type')) {
            $query->ofType($request->type);
        }

        $limit = $request->integer('limit', 20);
        $interactions = $query->with(['user'])
                             ->orderByDesc('created_at')
                             ->limit($limit)
                             ->get();

        // Estadísticas por tipo
        $statsByType = SocialInteraction::withObject(
            $request->interactable_type,
            $request->interactable_id
        )->active()
          ->select('interaction_type', DB::raw('count(*) as count'))
          ->groupBy('interaction_type')
          ->get()
          ->pluck('count', 'interaction_type')
          ->toArray();

        return response()->json([
            'data' => SocialInteractionResource::collection($interactions),
            'meta' => [
                'total' => array_sum($statsByType),
                'by_type' => $statsByType,
                'engagement_score' => $this->calculateObjectEngagement($statsByType),
            ]
        ]);
    }

    /**
     * Interacciones del usuario autenticado
     * 
     * Obtiene las interacciones realizadas por el usuario autenticado.
     * 
     * @queryParam type string Filtrar por tipo de interacción. Example: like
     * @queryParam limit integer Número de interacciones a retornar. Example: 20
     * @queryParam offset integer Número de interacciones a saltar. Example: 0
     * 
     * @response 200 {
     *   "data": [
     *     {
     *       "id": 1,
     *       "interaction_type": "like",
     *       "interactable": {
     *         "type": "ActivityFeed",
     *         "id": 1,
     *         "title": "Actividad de energía"
     *       },
     *       "created_at": "2025-01-15T10:30:00Z"
     *     }
     *   ]
     * }
     */
    public function myInteractions(Request $request): JsonResponse
    {
        $user = Auth::guard('sanctum')->user();
        $limit = min($request->integer('limit', 20), 50);
        $offset = $request->integer('offset', 0);

        $query = $user->socialInteractions()->active();

        if ($request->filled('type')) {
            $query->ofType($request->type);
        }

        $interactions = $query->with(['interactable'])
                             ->orderByDesc('created_at')
                             ->offset($offset)
                             ->limit($limit)
                             ->get();

        return response()->json([
            'data' => SocialInteractionResource::collection($interactions),
            'meta' => [
                'count' => $interactions->count(),
                'total_likes' => $user->likesGiven()->count(),
                'total_shares' => $user->sharesGiven()->count(),
                'total_bookmarks' => $user->bookmarksGiven()->count(),
            ]
        ]);
    }

    /**
     * Estadísticas de interacciones
     * 
     * Obtiene estadísticas generales sobre las interacciones sociales.
     * 
     * @response 200 {
     *   "data": {
     *     "total_interactions": 1250,
     *     "interactions_today": 89,
     *     "most_popular_type": "like",
     *     "top_interacted_content": [
     *       {
     *         "content_type": "ActivityFeed",
     *         "content_id": 1,
     *         "interactions_count": 45
     *       }
     *     ]
     *   }
     * }
     */
    public function stats(Request $request): JsonResponse
    {
        $stats = [
            'total_interactions' => SocialInteraction::active()->count(),
            'interactions_today' => SocialInteraction::active()
                ->whereDate('created_at', today())->count(),
            'interactions_this_week' => SocialInteraction::active()
                ->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
        ];

        // Tipo más popular
        $mostPopularType = SocialInteraction::active()
            ->select('interaction_type', DB::raw('count(*) as count'))
            ->groupBy('interaction_type')
            ->orderByDesc('count')
            ->first();

        $stats['most_popular_type'] = $mostPopularType ? $mostPopularType->interaction_type : null;

        // Contenido más interactuado
        $topContent = SocialInteraction::active()
            ->select('interactable_type', 'interactable_id', DB::raw('count(*) as interactions_count'))
            ->groupBy('interactable_type', 'interactable_id')
            ->orderByDesc('interactions_count')
            ->limit(10)
            ->get()
            ->map(function ($item) {
                return [
                    'content_type' => class_basename($item->interactable_type),
                    'content_id' => $item->interactable_id,
                    'interactions_count' => $item->interactions_count,
                ];
            });

        $stats['top_interacted_content'] = $topContent;

        // Distribución por tipo
        $typeDistribution = SocialInteraction::active()
            ->select('interaction_type', DB::raw('count(*) as count'))
            ->groupBy('interaction_type')
            ->get()
            ->pluck('count', 'interaction_type')
            ->toArray();

        $stats['type_distribution'] = $typeDistribution;

        return response()->json(['data' => $stats]);
    }

    // Métodos auxiliares privados

    /**
     * Obtener peso de engagement según el tipo de interacción
     */
    private function getEngagementWeight(string $type): int
    {
        return match ($type) {
            'like' => 1,
            'love' => 2,
            'wow' => 2,
            'celebrate' => 3,
            'support' => 3,
            'share' => 5,
            'bookmark' => 2,
            'follow' => 1,
            'subscribe' => 1,
            default => 0,
        };
    }

    /**
     * Calcular engagement total de un objeto
     */
    private function calculateObjectEngagement(array $statsByType): int
    {
        $totalEngagement = 0;
        
        foreach ($statsByType as $type => $count) {
            $weight = $this->getEngagementWeight($type);
            $totalEngagement += $count * $weight;
        }

        return $totalEngagement;
    }
}
