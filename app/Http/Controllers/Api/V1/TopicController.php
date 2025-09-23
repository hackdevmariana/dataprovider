<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\TopicResource;
use App\Http\Resources\V1\TopicResourceSimple;
use App\Models\Topic;
use App\Models\TopicMembership;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

/**
 * @tags Topics
 * @group Comunidades Temáticas
 * 
 * API endpoints para gestionar comunidades temáticas especializadas en energía.
 */
class TopicController extends Controller
{
    /**
     * Listar temas disponibles
     * 
     * Obtiene la lista de temas de discusión disponibles con filtros y ordenamiento.
     * 
     * @queryParam category string Filtrar por categoría. Example: technology
     * @queryParam difficulty string Filtrar por nivel de dificultad. Example: beginner
     * @queryParam featured boolean Solo temas destacados. Example: true
     * @queryParam trending boolean Solo temas trending. Example: true
     * @queryParam search string Buscar por nombre o descripción. Example: solar
     * @queryParam sort string Ordenar por (activity, members, recent, trending). Example: activity
     * @queryParam limit integer Número de temas a retornar. Example: 20
     * 
     * @response 200 {
     *   "data": [
     *     {
     *       "id": 1,
     *       "name": "Instalaciones Solares",
     *       "slug": "instalaciones-solares",
     *       "description": "Todo sobre instalaciones solares residenciales y comerciales",
     *       "category": "technology",
     *       "difficulty_level": "intermediate",
     *       "icon": "☀️",
     *       "color": "#F59E0B",
     *       "members_count": 1250,
     *       "posts_count": 89,
     *       "activity_score": 245.5,
     *       "is_featured": true,
     *       "creator": {"id": 1, "name": "Expert User"}
     *     }
     *   ],
     *   "meta": {
     *     "total": 45,
     *     "featured_count": 8,
     *     "trending_count": 5
     *   }
     * }
     */
    public function index(Request $request): JsonResponse
    {
        $user = Auth::guard('sanctum')->user();
        
        $query = Topic::accessibleFor($user)->active();

        // Aplicar filtros
        if ($request->filled('category')) {
            $query->byCategory($request->category);
        }

        if ($request->filled('difficulty')) {
            $query->byDifficulty($request->difficulty);
        }

        if ($request->boolean('featured')) {
            $query->featured();
        }

        if ($request->boolean('trending')) {
            $query->trending();
        }

        if ($request->filled('search')) {
            $query->search($request->search);
        }

        // Aplicar ordenamiento
        $sort = $request->get('sort', 'activity');
        match ($sort) {
            'activity' => $query->orderByDesc('activity_score'),
            'members' => $query->orderByDesc('members_count'),
            'recent' => $query->orderByDesc('created_at'),
            'trending' => $query->orderByDesc('trending_score'),
            default => $query->orderByDesc('activity_score'),
        };

        $limit = min($request->integer('limit', 20), 50);
        $topics = $query->with(['creator:id,name,email'])
                       ->limit($limit)
                       ->get();

        return response()->json([
            'data' => TopicResourceSimple::collection($topics),
            'meta' => [
                'count' => $topics->count(),
                'featured_count' => Topic::featured()->active()->count(),
                'trending_count' => Topic::active()->where('activity_score', '>', 50)->count(),
                'categories' => $this->getAvailableCategories(),
            ]
        ]);
    }

    /**
     * Crear nuevo tema
     * 
     * Permite crear un nuevo tema de discusión especializado.
     * 
     * @bodyParam name string required Nombre del tema. Example: Baterías Domésticas
     * @bodyParam description string required Descripción del tema. Example: Discusión sobre sistemas de almacenamiento...
     * @bodyParam category string required Categoría del tema. Example: technology
     * @bodyParam difficulty_level string Nivel de dificultad. Example: intermediate
     * @bodyParam visibility string Visibilidad del tema. Example: public
     * @bodyParam icon string Icono del tema (emoji). Example: 🔋
     * @bodyParam color string Color hexadecimal. Example: #10B981
     * @bodyParam rules string Reglas específicas del tema. Example: Mantener discusiones técnicas...
     * 
     * @response 201 {
     *   "data": {
     *     "id": 1,
     *     "name": "Baterías Domésticas",
     *     "slug": "baterias-domesticas",
     *     "category": "technology",
     *     "creator": {"id": 1, "name": "Creator User"},
     *     "members_count": 1,
     *     "posts_count": 0
     *   },
     *   "message": "Tema creado exitosamente"
     * }
     */
    public function store(Request $request): JsonResponse
    {
        $user = Auth::guard('sanctum')->user();
        
        if (!$user) {
            return response()->json(['message' => 'Usuario no autenticado'], 401);
        }

        $request->validate([
            'name' => 'required|string|max:100|unique:topics,name',
            'description' => 'required|string|max:1000',
            'category' => [
                'required',
                Rule::in(['technology', 'legislation', 'financing', 'installation', 'cooperative', 'market', 'efficiency', 'diy', 'news', 'beginners', 'professional', 'regional', 'research', 'storage', 'grid', 'policy', 'sustainability', 'innovation', 'general'])
            ],
            'difficulty_level' => [
                'nullable',
                Rule::in(['beginner', 'intermediate', 'advanced', 'expert'])
            ],
            'visibility' => [
                'nullable',
                Rule::in(['public', 'private', 'restricted', 'invite_only'])
            ],
            'icon' => 'nullable|string|max:10',
            'color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'rules' => 'nullable|string|max:2000',
        ]);

        $topic = Topic::create([
            'name' => $request->name,
            'description' => $request->description,
            'category' => $request->category,
            'difficulty_level' => $request->get('difficulty_level', 'beginner'),
            'visibility' => $request->get('visibility', 'public'),
            'icon' => $request->icon,
            'color' => $request->get('color', '#3B82F6'),
            'rules' => $request->rules,
            'creator_id' => $user->id,
        ]);

        // Agregar al creador como miembro con rol de creador
        TopicMembership::create([
            'topic_id' => $topic->id,
            'user_id' => $user->id,
            'role' => 'creator',
            'status' => 'active',
            'joined_at' => now(),
        ]);

        $topic->load('creator:id,name,email');

        return response()->json([
            'data' => new TopicResourceSimple($topic),
            'message' => 'Tema creado exitosamente'
        ], 201);
    }

    /**
     * Mostrar tema específico
     * 
     * @urlParam topic integer required ID del tema. Example: 1
     * 
     * @response 200 {
     *   "data": {
     *     "id": 1,
     *     "name": "Instalaciones Solares",
     *     "description": "Todo sobre instalaciones solares...",
     *     "stats": {
     *       "members_count": 1250,
     *       "posts_count": 89,
     *       "activity_score": 245.5
     *     },
     *     "user_membership": {
     *       "is_member": true,
     *       "role": "member",
     *       "joined_at": "2025-01-15T10:00:00Z"
     *     }
     *   }
     * }
     */
    public function show(Topic $topic): JsonResponse
    {
        $user = Auth::guard('sanctum')->user();

        if (!$topic->canBeViewedBy($user)) {
            return response()->json([
                'message' => 'No tienes permisos para ver este tema'
            ], 403);
        }

        // $topic->increment('views_count'); // Column doesn't exist in current schema
        $topic->load(['creator:id,name,email']);

        return response()->json([
            'data' => new TopicResourceSimple($topic)
        ]);
    }

    /**
     * Unirse a un tema
     * 
     * @urlParam topic integer required ID del tema. Example: 1
     * 
     * @response 200 {
     *   "data": {
     *     "topic_id": 1,
     *     "user_id": 1,
     *     "role": "member",
     *     "status": "active",
     *     "joined_at": "2025-01-15T10:30:00Z"
     *   },
     *   "message": "Te has unido al tema exitosamente"
     * }
     */
    public function join(Topic $topic): JsonResponse
    {
        $user = Auth::guard('sanctum')->user();
        
        if (!$user) {
            return response()->json(['message' => 'Usuario no autenticado'], 401);
        }

        if (!$topic->canBeViewedBy($user)) {
            return response()->json([
                'message' => 'No puedes unirte a este tema'
            ], 403);
        }

        // Verificar si ya es miembro
        $existingMembership = TopicMembership::where([
            'topic_id' => $topic->id,
            'user_id' => $user->id,
        ])->first();

        if ($existingMembership) {
            if ($existingMembership->status === 'active') {
                return response()->json([
                    'message' => 'Ya eres miembro de este tema'
                ], 422);
            } else {
                // Reactivar membresía existente
                $existingMembership->activate();
                $membership = $existingMembership;
            }
        } else {
            // Crear nueva membresía
            $membership = $topic->addMember($user);
        }

        return response()->json([
            'data' => $membership,
            'message' => $membership->status === 'pending' 
                ? 'Solicitud de unión enviada, pendiente de aprobación'
                : 'Te has unido al tema exitosamente'
        ]);
    }

    /**
     * Abandonar un tema
     * 
     * @urlParam topic integer required ID del tema. Example: 1
     * 
     * @response 200 {
     *   "message": "Has abandonado el tema exitosamente"
     * }
     */
    public function leave(Topic $topic): JsonResponse
    {
        $user = Auth::guard('sanctum')->user();
        
        if (!$user) {
            return response()->json(['message' => 'Usuario no autenticado'], 401);
        }

        $membership = TopicMembership::where([
            'topic_id' => $topic->id,
            'user_id' => $user->id,
        ])->first();

        if (!$membership || $membership->status !== 'active') {
            return response()->json([
                'message' => 'No eres miembro de este tema'
            ], 404);
        }

        if ($membership->role === 'creator') {
            return response()->json([
                'message' => 'El creador del tema no puede abandonarlo'
            ], 403);
        }

        $membership->update(['status' => 'left']);

        return response()->json([
            'message' => 'Has abandonado el tema exitosamente'
        ]);
    }

    /**
     * Temas trending
     * 
     * @queryParam limit integer Número de temas trending. Example: 10
     * 
     * @response 200 {
     *   "data": [
     *     {
     *       "id": 1,
     *       "name": "Tema Trending",
     *       "trending_score": 89.5,
     *       "activity_score": 156.2,
     *       "recent_posts": 12
     *     }
     *   ]
     * }
     */
    public function trending(Request $request): JsonResponse
    {
        $user = Auth::guard('sanctum')->user();
        $limit = min($request->integer('limit', 10), 20);

        $topics = Topic::accessibleFor($user)
                      ->active()
                      ->where('activity_score', '>', 50)
                      ->orderByDesc('activity_score')
                      ->with(['creator:id,name,email'])
                      ->limit($limit)
                      ->get();

        return response()->json([
            'data' => TopicResourceSimple::collection($topics),
            'meta' => [
                'count' => $topics->count(),
                'algorithm_version' => 'v1.0',
            ]
        ]);
    }

    /**
     * Estadísticas de temas
     * 
     * @response 200 {
     *   "data": {
     *     "total_topics": 45,
     *     "active_topics": 42,
     *     "featured_topics": 8,
     *     "total_members": 15670,
     *     "total_posts": 2340,
     *     "categories": {
     *       "technology": 15,
     *       "financing": 8,
     *       "diy": 12
     *     }
     *   }
     * }
     */
    public function stats(): JsonResponse
    {
        $user = Auth::guard('sanctum')->user();

        $stats = [
            'total_topics' => Topic::accessibleFor($user)->count(),
            'active_topics' => Topic::accessibleFor($user)->active()->count(),
            'featured_topics' => Topic::accessibleFor($user)->featured()->count(),
            'trending_topics' => Topic::accessibleFor($user)->active()->where('activity_score', '>', 50)->count(),
            'total_members' => TopicMembership::active()->count(),
            'total_posts' => Topic::accessibleFor($user)->sum('posts_count'),
            'total_comments' => Topic::accessibleFor($user)->sum('comments_count'),
            'avg_activity_score' => Topic::accessibleFor($user)->active()->avg('activity_score'),
        ];

        // Estadísticas por categoría
        $categoryStats = Topic::accessibleFor($user)
            ->active()
            ->selectRaw('category, count(*) as count, sum(members_count) as total_members')
            ->groupBy('category')
            ->get()
            ->pluck('count', 'category')
            ->toArray();

        $stats['categories'] = $categoryStats;

        return response()->json(['data' => $stats]);
    }

    // Métodos auxiliares privados

    /**
     * Obtener categorías disponibles
     */
    private function getAvailableCategories(): array
    {
        return [
            'technology' => 'Tecnología',
            'legislation' => 'Legislación',
            'financing' => 'Financiación',
            'installation' => 'Instalación',
            'cooperative' => 'Cooperativas',
            'market' => 'Mercado',
            'efficiency' => 'Eficiencia',
            'diy' => 'Hazlo Tú Mismo',
            'news' => 'Noticias',
            'beginners' => 'Principiantes',
            'professional' => 'Profesionales',
            'regional' => 'Regional',
            'research' => 'Investigación',
            'storage' => 'Almacenamiento',
            'grid' => 'Red Eléctrica',
            'policy' => 'Políticas',
            'sustainability' => 'Sostenibilidad',
            'innovation' => 'Innovación',
            'general' => 'General',
        ];
    }
}