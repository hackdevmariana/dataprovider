<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\ActivityFeedResource;
use App\Models\ActivityFeed;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use OpenApi\Attributes as OA;

#[OA\Tag(name: 'Activity Feed', description: 'Gestión del feed de actividades y timeline de la comunidad energética')]
class ActivityFeedController extends Controller
{
    #[OA\Get(
        path: '/api/v1/activity-feed',
        summary: 'Feed personalizado del usuario',
        description: 'Obtiene el feed personalizado de actividades basado en los usuarios seguidos, preferencias de algoritmo y configuración de relevancia.',
        security: [['sanctum' => []]],
        tags: ['Activity Feed'],
        parameters: [
            new OA\Parameter(
                name: 'limit',
                description: 'Número de actividades a retornar (máximo 50)',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'integer', minimum: 1, maximum: 50, default: 20)
            ),
            new OA\Parameter(
                name: 'offset',
                description: 'Número de actividades a saltar',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'integer', minimum: 0, default: 0)
            ),
            new OA\Parameter(
                name: 'type',
                description: 'Filtrar por tipo de actividad',
                in: 'query',
                required: false,
                schema: new OA\Schema(
                    type: 'string',
                    enum: ['energy_saved', 'solar_generated', 'achievement_unlocked', 'project_funded', 'installation_completed', 'cooperative_joined', 'roof_published', 'investment_made', 'production_right_sold', 'challenge_completed', 'milestone_reached', 'content_published', 'expert_verified', 'review_published', 'topic_created', 'community_contribution', 'carbon_milestone', 'efficiency_improvement', 'grid_contribution', 'sustainability_goal']
                )
            ),
            new OA\Parameter(
                name: 'visibility',
                description: 'Filtrar por visibilidad',
                in: 'query',
                required: false,
                schema: new OA\Schema(
                    type: 'string',
                    enum: ['public', 'cooperative', 'followers']
                )
            ),
            new OA\Parameter(
                name: 'featured',
                description: 'Solo actividades destacadas',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'boolean')
            ),
            new OA\Parameter(
                name: 'milestones',
                description: 'Solo hitos importantes',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'boolean')
            ),
            new OA\Parameter(
                name: 'location',
                description: 'Coordenadas lat,lng para filtro geográfico',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'string', example: '40.4168,-3.7038')
            ),
            new OA\Parameter(
                name: 'radius',
                description: 'Radio en km para filtro geográfico',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'integer', minimum: 1, maximum: 100, default: 25)
            ),
            new OA\Parameter(
                name: 'date_from',
                description: 'Fecha desde (YYYY-MM-DD)',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'string', format: 'date', example: '2025-01-01')
            ),
            new OA\Parameter(
                name: 'date_to',
                description: 'Fecha hasta (YYYY-MM-DD)',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'string', format: 'date', example: '2025-01-31')
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Feed personalizado obtenido exitosamente',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: 'data',
                            type: 'array',
                            items: new OA\Items(ref: '#/components/schemas/ActivityFeed')
                        ),
                        new OA\Property(
                            property: 'meta',
                            type: 'object',
                            properties: [
                                new OA\Property(property: 'total', type: 'integer', description: 'Total de actividades encontradas'),
                                new OA\Property(property: 'has_more', type: 'boolean', description: 'Si hay más actividades disponibles'),
                                new OA\Property(property: 'algorithm_version', type: 'string', description: 'Versión del algoritmo de personalización'),
                                new OA\Property(property: 'personalization_score', type: 'number', description: 'Score de personalización del usuario')
                            ]
                        )
                    ]
                )
            ),
            new OA\Response(
                response: 401,
                description: 'No autenticado',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'Unauthenticated.')
                    ]
                )
            )
        ]
    )]
    /**
     * Feed personalizado del usuario autenticado
     * 
     * Obtiene el feed personalizado de actividades basado en los usuarios seguidos,
     * preferencias de algoritmo y configuración de relevancia.
     * 
     * @queryParam limit integer Número de actividades a retornar (máximo 50). Example: 20
     * @queryParam offset integer Número de actividades a saltar. Example: 0
     * @queryParam type string Filtrar por tipo de actividad. Example: energy_saved
     * @queryParam visibility string Filtrar por visibilidad (public, cooperative, followers). Example: public
     * @queryParam featured boolean Solo actividades destacadas. Example: true
     * @queryParam milestones boolean Solo hitos importantes. Example: true
     * @queryParam location string Coordenadas lat,lng para filtro geográfico. Example: 40.4168,-3.7038
     * @queryParam radius integer Radio en km para filtro geográfico. Example: 25
     * @queryParam date_from string Fecha desde (YYYY-MM-DD). Example: 2025-01-01
     * @queryParam date_to string Fecha hasta (YYYY-MM-DD). Example: 2025-01-31
     * 
     * @response 200 {
     *   "data": [
     *     {
     *       "id": 1,
     *       "user": {
     *         "id": 1,
     *         "name": "María González",
     *         "email": "maria@kirolux.com"
     *       },
     *       "activity_type": "energy_saved",
     *       "description": "Ahorró 25.5 kWh de energía esta semana",
     *       "energy_amount_kwh": 25.5,
     *       "cost_savings_eur": 12.75,
     *       "co2_savings_kg": 8.5,
     *       "engagement_score": 145,
     *       "likes_count": 12,
     *       "loves_count": 3,
     *       "shares_count": 2,
     *       "is_featured": true,
     *       "is_milestone": false,
     *       "created_at": "2025-01-15T10:30:00Z"
     *     }
     *   ],
     *   "meta": {
     *     "total": 156,
     *     "has_more": true,
     *     "algorithm_version": "v1.2"
     *   }
     * }
     */
    public function index(Request $request): JsonResponse
    {
        $user = Auth::guard('sanctum')->user();
        $limit = min($request->integer('limit', 20), 50);
        $offset = $request->integer('offset', 0);

        $query = ActivityFeed::feedFor($user);

        // Aplicar filtros
        if ($request->filled('type')) {
            $query->ofType($request->string('type'));
        }

        if ($request->filled('visibility')) {
            $query->where('visibility', $request->string('visibility'));
        }

        if ($request->boolean('featured')) {
            $query->featured();
        }

        if ($request->boolean('milestones')) {
            $query->milestones();
        }

        // Filtro geográfico
        if ($request->filled('location') && $request->filled('radius')) {
            [$lat, $lng] = explode(',', $request->string('location'));
            $radius = $request->integer('radius', 25);
            $query->nearLocation((float)$lat, (float)$lng, $radius);
        }

        // Filtro de fechas
        if ($request->filled('date_from')) {
            $query->where('activity_occurred_at', '>=', $request->date('date_from'));
        }

        if ($request->filled('date_to')) {
            $query->where('activity_occurred_at', '<=', $request->date('date_to'));
        }

        $activities = $query->with(['user', 'related'])
                           ->offset($offset)
                           ->limit($limit)
                           ->get();

        $total = $query->count();

        return response()->json([
            'data' => ActivityFeedResource::collection($activities),
            'meta' => [
                'total' => $total,
                'has_more' => ($offset + $limit) < $total,
                'algorithm_version' => 'v1.2',
                'personalization_score' => $this->calculatePersonalizationScore($user),
            ]
        ]);
    }

    #[OA\Get(
        path: '/api/v1/activity-feed/public',
        summary: 'Actividades públicas globales',
        description: 'Obtiene las actividades públicas más relevantes y recientes de toda la comunidad.',
        tags: ['Activity Feed'],
        parameters: [
            new OA\Parameter(
                name: 'limit',
                description: 'Número de actividades a retornar',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'integer', minimum: 1, maximum: 50, default: 20)
            ),
            new OA\Parameter(
                name: 'featured',
                description: 'Solo actividades destacadas',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'boolean')
            ),
            new OA\Parameter(
                name: 'energy_only',
                description: 'Solo actividades relacionadas con energía',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'boolean')
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Actividades públicas obtenidas exitosamente',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: 'data',
                            type: 'array',
                            items: new OA\Items(ref: '#/components/schemas/ActivityFeed')
                        ),
                        new OA\Property(
                            property: 'meta',
                            type: 'object',
                            properties: [
                                new OA\Property(property: 'count', type: 'integer', description: 'Número de actividades retornadas'),
                                new OA\Property(property: 'featured_count', type: 'integer', description: 'Total de actividades destacadas'),
                                new OA\Property(property: 'energy_activities_today', type: 'integer', description: 'Actividades energéticas de hoy')
                            ]
                        )
                    ]
                )
            )
        ]
    )]
    /**
     * Actividades públicas globales
     * 
     * Obtiene las actividades públicas más relevantes y recientes de toda la comunidad.
     * 
     * @queryParam limit integer Número de actividades a retornar. Example: 20
     * @queryParam featured boolean Solo actividades destacadas. Example: true
     * @queryParam energy_only boolean Solo actividades relacionadas con energía. Example: true
     * 
     * @response 200 {
     *   "data": [
     *     {
     *       "id": 1,
     *       "user": {"id": 1, "name": "Juan Pérez"},
     *       "activity_type": "solar_generated",
     *       "description": "Generó 150 kWh con su instalación solar",
     *       "energy_amount_kwh": 150,
     *       "engagement_score": 89,
     *       "created_at": "2025-01-15T08:00:00Z"
     *     }
     *   ]
     * }
     */
    public function public(Request $request): JsonResponse
    {
        $limit = min($request->integer('limit', 20), 50);

        $query = ActivityFeed::public()
                            ->active()
                            ->where('show_in_feed', true);

        if ($request->boolean('featured')) {
            $query->featured();
        }

        if ($request->boolean('energy_only')) {
            $query->energyRelated();
        }

        $activities = $query->with(['user', 'related'])
                           ->orderByDesc('engagement_score')
                           ->orderByDesc('created_at')
                           ->limit($limit)
                           ->get();

        return response()->json([
            'data' => ActivityFeedResource::collection($activities),
            'meta' => [
                'count' => $activities->count(),
                'featured_count' => ActivityFeed::public()->featured()->count(),
                'energy_activities_today' => ActivityFeed::public()->energyRelated()
                    ->whereDate('created_at', today())->count(),
            ]
        ]);
    }

    #[OA\Get(
        path: '/api/v1/activity-feed/featured',
        summary: 'Actividades destacadas',
        description: 'Obtiene las actividades marcadas como destacadas por la comunidad o moderadores.',
        tags: ['Activity Feed'],
        parameters: [
            new OA\Parameter(
                name: 'limit',
                description: 'Número de actividades a retornar',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'integer', minimum: 1, maximum: 20, default: 10)
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Actividades destacadas obtenidas exitosamente',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: 'data',
                            type: 'array',
                            items: new OA\Items(ref: '#/components/schemas/ActivityFeed')
                        ),
                        new OA\Property(
                            property: 'meta',
                            type: 'object',
                            properties: [
                                new OA\Property(property: 'count', type: 'integer', description: 'Número de actividades retornadas'),
                                new OA\Property(property: 'total_featured', type: 'integer', description: 'Total de actividades destacadas')
                            ]
                        )
                    ]
                )
            )
        ]
    )]
    /**
     * Actividades destacadas
     * 
     * Obtiene las actividades marcadas como destacadas por la comunidad o moderadores.
     * 
     * @response 200 {
     *   "data": [
     *     {
     *       "id": 1,
     *       "user": {"id": 1, "name": "Ana López"},
     *       "activity_type": "installation_completed",
     *       "description": "Completó instalación solar de 5kW",
     *       "is_featured": true,
     *       "is_milestone": true,
     *       "engagement_score": 245
     *     }
     *   ]
     * }
     */
    public function featured(Request $request): JsonResponse
    {
        $limit = min($request->integer('limit', 10), 20);

        $activities = ActivityFeed::featured()
                                 ->active()
                                 ->public()
                                 ->with(['user', 'related'])
                                 ->orderByDesc('engagement_score')
                                 ->orderByDesc('created_at')
                                 ->limit($limit)
                                 ->get();

        return response()->json([
            'data' => ActivityFeedResource::collection($activities),
            'meta' => [
                'count' => $activities->count(),
                'total_featured' => ActivityFeed::featured()->active()->count(),
            ]
        ]);
    }

    #[OA\Get(
        path: '/api/v1/activity-feed/milestones',
        summary: 'Hitos de la comunidad',
        description: 'Obtiene los hitos más importantes alcanzados por la comunidad.',
        tags: ['Activity Feed'],
        parameters: [
            new OA\Parameter(
                name: 'limit',
                description: 'Número de hitos a retornar',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'integer', minimum: 1, maximum: 30, default: 15)
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Hitos obtenidos exitosamente',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: 'data',
                            type: 'array',
                            items: new OA\Items(ref: '#/components/schemas/ActivityFeed')
                        ),
                        new OA\Property(
                            property: 'meta',
                            type: 'object',
                            properties: [
                                new OA\Property(property: 'count', type: 'integer', description: 'Número de hitos retornados'),
                                new OA\Property(property: 'total_milestones', type: 'integer', description: 'Total de hitos en el sistema'),
                                new OA\Property(property: 'community_co2_saved', type: 'number', description: 'Total de CO2 ahorrado por la comunidad')
                            ]
                        )
                    ]
                )
            )
        ]
    )]
    /**
     * Hitos de la comunidad
     * 
     * Obtiene los hitos más importantes alcanzados por la comunidad.
     * 
     * @response 200 {
     *   "data": [
     *     {
     *       "id": 1,
     *       "user": {"id": 1, "name": "Cooperativa Verde"},
     *       "activity_type": "carbon_milestone",
     *       "description": "Alcanzó 1 tonelada de CO2 ahorrado",
     *       "co2_savings_kg": 1000,
     *       "is_milestone": true,
     *       "community_impact_score": 95
     *     }
     *   ]
     * }
     */
    public function milestones(Request $request): JsonResponse
    {
        $limit = min($request->integer('limit', 15), 30);

        $activities = ActivityFeed::milestones()
                                 ->active()
                                 ->public()
                                 ->with(['user', 'related'])
                                 ->orderByDesc('community_impact_score')
                                 ->orderByDesc('created_at')
                                 ->limit($limit)
                                 ->get();

        return response()->json([
            'data' => ActivityFeedResource::collection($activities),
            'meta' => [
                'count' => $activities->count(),
                'total_milestones' => ActivityFeed::milestones()->active()->count(),
                'community_co2_saved' => ActivityFeed::milestones()
                    ->whereNotNull('co2_savings_kg')
                    ->sum('co2_savings_kg'),
            ]
        ]);
    }

    #[OA\Get(
        path: '/api/v1/activity-feed/nearby',
        summary: 'Actividades por ubicación',
        description: 'Obtiene actividades cercanas a una ubicación específica.',
        security: [['sanctum' => []]],
        tags: ['Activity Feed'],
        parameters: [
            new OA\Parameter(
                name: 'lat',
                description: 'Latitud',
                in: 'query',
                required: true,
                schema: new OA\Schema(type: 'number', format: 'float', minimum: -90, maximum: 90, example: 40.4168)
            ),
            new OA\Parameter(
                name: 'lng',
                description: 'Longitud',
                in: 'query',
                required: true,
                schema: new OA\Schema(type: 'number', format: 'float', minimum: -180, maximum: 180, example: -3.7038)
            ),
            new OA\Parameter(
                name: 'radius',
                description: 'Radio en kilómetros (máximo 100)',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'integer', minimum: 1, maximum: 100, default: 25)
            ),
            new OA\Parameter(
                name: 'limit',
                description: 'Número de actividades a retornar',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'integer', minimum: 1, maximum: 50, default: 15)
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Actividades cercanas obtenidas exitosamente',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: 'data',
                            type: 'array',
                            items: new OA\Items(ref: '#/components/schemas/ActivityFeed')
                        ),
                        new OA\Property(
                            property: 'meta',
                            type: 'object',
                            properties: [
                                new OA\Property(property: 'count', type: 'integer', description: 'Número de actividades encontradas'),
                                new OA\Property(
                                    property: 'search_center',
                                    type: 'object',
                                    properties: [
                                        new OA\Property(property: 'lat', type: 'number'),
                                        new OA\Property(property: 'lng', type: 'number')
                                    ]
                                ),
                                new OA\Property(property: 'search_radius_km', type: 'integer'),
                                new OA\Property(property: 'avg_distance_km', type: 'number')
                            ]
                        )
                    ]
                )
            ),
            new OA\Response(
                response: 422,
                description: 'Error de validación en los parámetros',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string'),
                        new OA\Property(
                            property: 'errors',
                            type: 'object',
                            additionalProperties: new OA\AdditionalProperties(
                                type: 'array',
                                items: new OA\Items(type: 'string')
                            )
                        )
                    ]
                )
            )
        ]
    )]
    /**
     * Actividades por ubicación
     * 
     * Obtiene actividades cercanas a una ubicación específica.
     * 
     * @queryParam lat float required Latitud. Example: 40.4168
     * @queryParam lng float required Longitud. Example: -3.7038
     * @queryParam radius integer Radio en kilómetros (máximo 100). Example: 25
     * @queryParam limit integer Número de actividades a retornar. Example: 15
     * 
     * @response 200 {
     *   "data": [
     *     {
     *       "id": 1,
     *       "user": {"id": 1, "name": "Local User"},
     *       "activity_type": "installation_completed",
     *       "latitude": 40.4200,
     *       "longitude": -3.7050,
     *       "location_name": "Madrid Centro",
     *       "distance_km": 2.3
     *     }
     *   ]
     * }
     */
    public function nearby(Request $request): JsonResponse
    {
        $request->validate([
            'lat' => 'required|numeric|between:-90,90',
            'lng' => 'required|numeric|between:-180,180',
            'radius' => 'integer|min:1|max:100',
            'limit' => 'integer|min:1|max:50',
        ]);

        $lat = $request->float('lat');
        $lng = $request->float('lng');
        $radius = $request->integer('radius', 25);
        $limit = $request->integer('limit', 15);

        $user = Auth::guard('sanctum')->user();
        
        $activities = ActivityFeed::visibleFor($user)
                                 ->active()
                                 ->nearLocation($lat, $lng, $radius)
                                 ->with(['user', 'related'])
                                 ->selectRaw('*, (6371 * acos(cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) + sin(radians(?)) * sin(radians(latitude)))) AS distance_km', [$lat, $lng, $lat])
                                 ->orderBy('distance_km')
                                 ->orderByDesc('engagement_score')
                                 ->limit($limit)
                                 ->get();

        return response()->json([
            'data' => ActivityFeedResource::collection($activities),
            'meta' => [
                'count' => $activities->count(),
                'search_center' => ['lat' => $lat, 'lng' => $lng],
                'search_radius_km' => $radius,
                'avg_distance_km' => $activities->avg('distance_km'),
            ]
        ]);
    }

    #[OA\Get(
        path: '/api/v1/activity-feed/stats',
        summary: 'Estadísticas del feed',
        description: 'Obtiene estadísticas generales del feed de actividades.',
        security: [['sanctum' => []]],
        tags: ['Activity Feed'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Estadísticas obtenidas exitosamente',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: 'data',
                            type: 'object',
                            properties: [
                                new OA\Property(property: 'total_activities', type: 'integer', description: 'Total de actividades'),
                                new OA\Property(property: 'activities_today', type: 'integer', description: 'Actividades de hoy'),
                                new OA\Property(property: 'activities_this_week', type: 'integer', description: 'Actividades de esta semana'),
                                new OA\Property(property: 'featured_activities', type: 'integer', description: 'Actividades destacadas'),
                                new OA\Property(property: 'milestones_count', type: 'integer', description: 'Número de hitos'),
                                new OA\Property(property: 'total_energy_saved_kwh', type: 'number', description: 'Total de energía ahorrada en kWh'),
                                new OA\Property(property: 'total_co2_saved_kg', type: 'number', description: 'Total de CO2 ahorrado en kg'),
                                new OA\Property(property: 'total_cost_savings_eur', type: 'number', description: 'Total de ahorro en euros'),
                                new OA\Property(property: 'avg_engagement_score', type: 'number', description: 'Score promedio de engagement'),
                                new OA\Property(
                                    property: 'top_activity_types',
                                    type: 'array',
                                    items: new OA\Items(
                                        type: 'object',
                                        properties: [
                                            new OA\Property(property: 'type', type: 'string'),
                                            new OA\Property(property: 'count', type: 'integer'),
                                            new OA\Property(property: 'label', type: 'string')
                                        ]
                                    )
                                )
                            ]
                        )
                    ]
                )
            ),
            new OA\Response(
                response: 401,
                description: 'No autenticado',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'Unauthenticated.')
                    ]
                )
            )
        ]
    )]
    /**
     * Estadísticas del feed
     * 
     * Obtiene estadísticas generales del feed de actividades.
     * 
     * @response 200 {
     *   "data": {
     *     "total_activities": 1250,
     *     "activities_today": 45,
     *     "activities_this_week": 312,
     *     "featured_activities": 89,
     *     "milestones_count": 23,
     *     "total_energy_saved_kwh": 15678.5,
     *     "total_co2_saved_kg": 5234.2,
     *     "total_cost_savings_eur": 7839.25,
     *     "top_activity_types": [
     *       {"type": "energy_saved", "count": 456},
     *       {"type": "solar_generated", "count": 234}
     *     ]
     *   }
     * }
     */
    public function stats(Request $request): JsonResponse
    {
        $user = Auth::guard('sanctum')->user();

        $stats = [
            'total_activities' => ActivityFeed::visibleFor($user)->active()->count(),
            'activities_today' => ActivityFeed::visibleFor($user)->active()
                ->whereDate('created_at', today())->count(),
            'activities_this_week' => ActivityFeed::visibleFor($user)->active()
                ->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'featured_activities' => ActivityFeed::visibleFor($user)->featured()->count(),
            'milestones_count' => ActivityFeed::visibleFor($user)->milestones()->count(),
            'total_energy_saved_kwh' => ActivityFeed::visibleFor($user)->active()
                ->whereNotNull('energy_amount_kwh')->sum('energy_amount_kwh'),
            'total_co2_saved_kg' => ActivityFeed::visibleFor($user)->active()
                ->whereNotNull('co2_savings_kg')->sum('co2_savings_kg'),
            'total_cost_savings_eur' => ActivityFeed::visibleFor($user)->active()
                ->whereNotNull('cost_savings_eur')->sum('cost_savings_eur'),
            'avg_engagement_score' => ActivityFeed::visibleFor($user)->active()
                ->avg('engagement_score'),
        ];

        // Top tipos de actividad
        $topActivityTypes = ActivityFeed::visibleFor($user)->active()
            ->select('activity_type', DB::raw('count(*) as count'))
            ->groupBy('activity_type')
            ->orderByDesc('count')
            ->limit(10)
            ->get()
            ->map(fn($item) => [
                'type' => $item->activity_type,
                'count' => $item->count,
                'label' => $this->getActivityTypeLabel($item->activity_type)
            ]);

        $stats['top_activity_types'] = $topActivityTypes;

        return response()->json(['data' => $stats]);
    }

    #[OA\Get(
        path: '/api/v1/activity-feed/{id}',
        summary: 'Detalle de una actividad específica',
        description: 'Obtiene los detalles completos de una actividad específica por su ID.',
        security: [['sanctum' => []]],
        tags: ['Activity Feed'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                description: 'ID único de la actividad',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer', format: 'int64')
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Actividad obtenida exitosamente',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: 'data',
                            ref: '#/components/schemas/ActivityFeed'
                        )
                    ]
                )
            ),
            new OA\Response(
                response: 403,
                description: 'Sin permisos para ver esta actividad',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'No tienes permisos para ver esta actividad.')
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: 'Actividad no encontrada',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'Actividad no encontrada')
                    ]
                )
            )
        ]
    )]
    /**
     * Detalle de una actividad específica
     * 
     * @urlParam id integer required ID de la actividad. Example: 1
     * 
     * @response 200 {
     *   "data": {
     *     "id": 1,
     *     "user": {"id": 1, "name": "Usuario"},
     *     "activity_type": "energy_saved",
     *     "description": "Descripción detallada",
     *     "activity_data": {"details": "..."},
     *     "engagement_score": 125,
     *     "interactions": [
     *       {"type": "like", "count": 15},
     *       {"type": "share", "count": 3}
     *     ]
     *   }
     * }
     */
    public function show(ActivityFeed $activityFeed): JsonResponse
    {
        $user = Auth::guard('sanctum')->user();

        // Verificar permisos
        if (!$activityFeed->canBeViewedBy($user)) {
            return response()->json([
                'message' => 'No tienes permisos para ver esta actividad.'
            ], 403);
        }

        // Incrementar contador de visualizaciones
        $activityFeed->increment('views_count');

        $activityFeed->load(['user', 'related', 'interactions.user']);

        return response()->json([
            'data' => new ActivityFeedResource($activityFeed)
        ]);
    }

    // Métodos auxiliares privados

    /**
     * Calcular score de personalización para el usuario
     */
    private function calculatePersonalizationScore(User $user): float
    {
        $score = 0;

        // Factor de seguimientos
        $followingCount = $user->activeFollowing()->count();
        $score += min(30, $followingCount * 2);

        // Factor de interacciones
        $interactionsCount = $user->socialInteractions()->count();
        $score += min(25, $interactionsCount * 0.5);

        // Factor de actividad propia
        $activitiesCount = $user->activityFeeds()->count();
        $score += min(20, $activitiesCount * 1);

        // Factor de tiempo en la plataforma
        $daysActive = $user->created_at->diffInDays(now());
        $score += min(15, $daysActive * 0.1);

        // Factor de cooperativa
        if ($user->cooperatives()->exists()) {
            $score += 10;
        }

        return round($score, 1);
    }

    /**
     * Obtener etiqueta legible para tipo de actividad
     */
    private function getActivityTypeLabel(string $type): string
    {
        return match ($type) {
            'energy_saved' => 'Energía Ahorrada',
            'solar_generated' => 'Energía Solar Generada',
            'achievement_unlocked' => 'Logro Desbloqueado',
            'project_funded' => 'Proyecto Financiado',
            'installation_completed' => 'Instalación Completada',
            'cooperative_joined' => 'Se Unió a Cooperativa',
            'roof_published' => 'Techo Publicado',
            'investment_made' => 'Inversión Realizada',
            'production_right_sold' => 'Derecho de Producción Vendido',
            'challenge_completed' => 'Desafío Completado',
            'milestone_reached' => 'Hito Alcanzado',
            'content_published' => 'Contenido Publicado',
            'expert_verified' => 'Experto Verificado',
            'review_published' => 'Reseña Publicada',
            'topic_created' => 'Tema Creado',
            'community_contribution' => 'Contribución Comunitaria',
            'carbon_milestone' => 'Hito de CO2',
            'efficiency_improvement' => 'Mejora de Eficiencia',
            'grid_contribution' => 'Contribución a Red',
            'sustainability_goal' => 'Meta de Sostenibilidad',
            default => 'Actividad',
        };
    }
}
