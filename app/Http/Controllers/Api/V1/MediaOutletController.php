<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\MediaOutlet;
use App\Http\Resources\V1\MediaOutletResource;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

/**
 * Controlador para gestión de medios de comunicación.
 * 
 * API completa para gestión de medios con análisis de credibilidad,
 * influencia y especialización en sostenibilidad.
 * 
 * @group Medios y Comunicación
 * @subgroup Medios de Comunicación
 */
class MediaOutletController extends Controller
{
    /**
     * Listar medios de comunicación.
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = min((int) $request->get('per_page', 15), 100);
        
        $query = MediaOutlet::with(['municipality', 'contacts'])
                           ->active();

        // Filtros
        if ($request->filled('type')) {
            $query->byType($request->type);
        }

        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'LIKE', "%{$request->search}%")
                  ->orWhere('description', 'LIKE', "%{$request->search}%");
            });
        }

        if ($request->boolean('verified')) {
            $query->verified();
        }

        if ($request->boolean('sustainability_focused')) {
            $query->sustainabilityFocused();
        }

        if ($request->boolean('digital_native')) {
            $query->digitalNative();
        }

        if ($request->filled('coverage_scope')) {
            $query->where('coverage_scope', $request->coverage_scope);
        }

        if ($request->filled('min_credibility')) {
            $query->where('credibility_score', '>=', (float) $request->min_credibility);
        }

        if ($request->filled('min_influence')) {
            $query->where('influence_score', '>=', (float) $request->min_influence);
        }

        if ($request->filled('municipality_id')) {
            $query->where('municipality_id', $request->municipality_id);
        }

        // Ordenamiento
        $sortBy = $request->get('sort_by', 'name');
        $sortOrder = $request->get('sort_order', 'asc');
        
        $allowedSorts = ['name', 'credibility_score', 'influence_score', 'founding_year', 'articles_count'];
        if (in_array($sortBy, $allowedSorts)) {
            $query->orderBy($sortBy, $sortOrder === 'asc' ? 'asc' : 'desc');
        }

        $outlets = $query->paginate($perPage);

        return MediaOutletResource::collection($outlets);
    }

    /**
     * Mostrar un medio específico.
     */
    public function show(Request $request, string $idOrSlug): JsonResponse
    {
        $outlet = MediaOutlet::with(['municipality', 'contacts', 'specializedTags'])
                            ->where('id', $idOrSlug)
                            ->orWhere('slug', $idOrSlug)
                            ->firstOrFail();

        // Obtener artículos recientes
        $recentArticles = $outlet->getRecentArticles(7, 5);
        $popularArticles = $outlet->getPopularArticles(5);
        $sustainabilityArticles = $outlet->getSustainabilityArticles(3);

        $data = $outlet->toArray();
        $data['recent_articles'] = $recentArticles;
        $data['popular_articles'] = $popularArticles;
        $data['sustainability_articles'] = $sustainabilityArticles;

        return response()->json($data);
    }

    /**
     * Medios verificados.
     */
    public function verified(Request $request): JsonResponse
    {
        $limit = min((int) $request->get('limit', 20), 50);
        
        $outlets = MediaOutlet::with(['municipality'])
                             ->verified()
                             ->active()
                             ->orderBy('credibility_score', 'desc')
                             ->limit($limit)
                             ->get();

        return response()->json(['data' => $outlets]);
    }

    /**
     * Medios especializados en sostenibilidad.
     */
    public function sustainabilityFocused(Request $request): JsonResponse
    {
        $limit = min((int) $request->get('limit', 15), 50);
        $minFocus = (float) $request->get('min_focus', 0.3);
        
        $outlets = MediaOutlet::with(['municipality'])
                             ->sustainabilityFocused()
                             ->where('sustainability_focus', '>=', $minFocus)
                             ->active()
                             ->orderBy('sustainability_focus', 'desc')
                             ->limit($limit)
                             ->get();

        return response()->json(['data' => $outlets]);
    }

    /**
     * Medios con alta credibilidad.
     */
    public function highCredibility(Request $request): JsonResponse
    {
        $minScore = (float) $request->get('min_score', 7.0);
        $limit = min((int) $request->get('limit', 20), 50);
        
        $outlets = MediaOutlet::with(['municipality'])
                             ->highCredibility($minScore)
                             ->active()
                             ->orderBy('credibility_score', 'desc')
                             ->limit($limit)
                             ->get();

        return response()->json(['data' => $outlets]);
    }

    /**
     * Medios influyentes.
     */
    public function influential(Request $request): JsonResponse
    {
        $minScore = (float) $request->get('min_score', 7.0);
        $limit = min((int) $request->get('limit', 20), 50);
        
        $outlets = MediaOutlet::with(['municipality'])
                             ->influential($minScore)
                             ->active()
                             ->orderBy('influence_score', 'desc')
                             ->limit($limit)
                             ->get();

        return response()->json(['data' => $outlets]);
    }

    /**
     * Medios nativos digitales.
     */
    public function digitalNative(Request $request): JsonResponse
    {
        $limit = min((int) $request->get('limit', 15), 50);
        
        $outlets = MediaOutlet::with(['municipality'])
                             ->digitalNative()
                             ->active()
                             ->orderBy('influence_score', 'desc')
                             ->limit($limit)
                             ->get();

        return response()->json(['data' => $outlets]);
    }

    /**
     * Medios locales.
     */
    public function local(Request $request): JsonResponse
    {
        $limit = min((int) $request->get('limit', 20), 50);
        
        $query = MediaOutlet::with(['municipality'])
                           ->local()
                           ->active();

        if ($request->filled('municipality_id')) {
            $query->where('municipality_id', $request->municipality_id);
        }

        $outlets = $query->orderBy('credibility_score', 'desc')
                        ->limit($limit)
                        ->get();

        return response()->json(['data' => $outlets]);
    }

    /**
     * Medios nacionales.
     */
    public function national(Request $request): JsonResponse
    {
        $limit = min((int) $request->get('limit', 20), 50);
        
        $outlets = MediaOutlet::with(['municipality'])
                             ->national()
                             ->active()
                             ->orderBy('influence_score', 'desc')
                             ->limit($limit)
                             ->get();

        return response()->json(['data' => $outlets]);
    }

    /**
     * Medios de referencia.
     */
    public function reference(Request $request): JsonResponse
    {
        $limit = min((int) $request->get('limit', 15), 30);
        
        $outlets = MediaOutlet::with(['municipality'])
                             ->where('is_verified', true)
                             ->where('credibility_score', '>=', 8.0)
                             ->where('influence_score', '>=', 7.0)
                             ->active()
                             ->orderBy('credibility_score', 'desc')
                             ->limit($limit)
                             ->get();

        return response()->json(['data' => $outlets]);
    }

    /**
     * Calcular puntuaciones de un medio.
     */
    public function calculateScores(MediaOutlet $outlet): JsonResponse
    {
        $credibilityScore = $outlet->calculateCredibilityScore();
        $influenceScore = $outlet->calculateInfluenceScore();
        $sustainabilityFocus = $outlet->analyzeSustainabilityFocus();
        
        $outlet->save();

        return response()->json([
            'outlet_id' => $outlet->id,
            'credibility_score' => $credibilityScore,
            'influence_score' => $influenceScore,
            'sustainability_focus' => $sustainabilityFocus,
            'is_reference_media' => $outlet->is_reference_media,
            'calculated_at' => now(),
        ]);
    }

    /**
     * Estadísticas de medios.
     */
    public function statistics(): JsonResponse
    {
        $totalOutlets = MediaOutlet::count();
        $activeOutlets = MediaOutlet::active()->count();
        $verifiedOutlets = MediaOutlet::verified()->count();
        $sustainabilityFocused = MediaOutlet::sustainabilityFocused()->count();
        $digitalNative = MediaOutlet::digitalNative()->count();
        $referenceMedia = MediaOutlet::where('is_verified', true)
                                    ->where('credibility_score', '>=', 8.0)
                                    ->where('influence_score', '>=', 7.0)
                                    ->count();

        $avgCredibility = MediaOutlet::active()
                                    ->whereNotNull('credibility_score')
                                    ->avg('credibility_score');
        
        $avgInfluence = MediaOutlet::active()
                                  ->whereNotNull('influence_score')
                                  ->avg('influence_score');

        $typeDistribution = MediaOutlet::active()
                                      ->selectRaw('type, COUNT(*) as count')
                                      ->groupBy('type')
                                      ->orderBy('count', 'desc')
                                      ->get();

        $scopeDistribution = MediaOutlet::active()
                                       ->selectRaw('coverage_scope, COUNT(*) as count')
                                       ->whereNotNull('coverage_scope')
                                       ->groupBy('coverage_scope')
                                       ->orderBy('count', 'desc')
                                       ->get();

        return response()->json([
            'total_outlets' => $totalOutlets,
            'active_outlets' => $activeOutlets,
            'verified_outlets' => $verifiedOutlets,
            'sustainability_focused' => $sustainabilityFocused,
            'digital_native' => $digitalNative,
            'reference_media' => $referenceMedia,
            'quality_metrics' => [
                'avg_credibility_score' => round((float) $avgCredibility, 1),
                'avg_influence_score' => round((float) $avgInfluence, 1),
            ],
            'type_distribution' => $typeDistribution,
            'scope_distribution' => $scopeDistribution,
        ]);
    }

    /**
     * Ranking de medios por credibilidad.
     */
    public function credibilityRanking(Request $request): JsonResponse
    {
        $limit = min((int) $request->get('limit', 25), 50);
        
        $outlets = MediaOutlet::with(['municipality'])
                             ->active()
                             ->whereNotNull('credibility_score')
                             ->orderBy('credibility_score', 'desc')
                             ->orderBy('influence_score', 'desc')
                             ->limit($limit)
                             ->get()
                             ->map(function($outlet, $index) {
                                 $data = $outlet->toArray();
                                 $data['ranking_position'] = $index + 1;
                                 return $data;
                             });

        return response()->json(['data' => $outlets]);
    }

    /**
     * Ranking de medios por influencia.
     */
    public function influenceRanking(Request $request): JsonResponse
    {
        $limit = min((int) $request->get('limit', 25), 50);
        
        $outlets = MediaOutlet::with(['municipality'])
                             ->active()
                             ->whereNotNull('influence_score')
                             ->orderBy('influence_score', 'desc')
                             ->orderBy('credibility_score', 'desc')
                             ->limit($limit)
                             ->get()
                             ->map(function($outlet, $index) {
                                 $data = $outlet->toArray();
                                 $data['ranking_position'] = $index + 1;
                                 return $data;
                             });

        return response()->json(['data' => $outlets]);
    }
}