<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\NewsArticle;
use App\Http\Resources\V1\NewsArticleResource;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use OpenApi\Annotations as OA;

/**
 * Controlador para gestión de artículos de noticias.
 * 
 * API completa para gestión de contenido mediático con funcionalidades
 * avanzadas de análisis de sostenibilidad, geolocalización y engagement.
 * 
 * @group Medios y Comunicación
 * @subgroup Artículos de Noticias
 */
/**
 * @OA\Tag(
 *     name="Artículos de Noticias",
 *     description="APIs para la gestión de Artículos de Noticias"
 * )
 */
class NewsArticleController extends Controller
{
    /**
     * Listar artículos de noticias.
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = min((int) $request->get('per_page', 15), 100);
        
        $query = NewsArticle::with(['author', 'mediaOutlet', 'municipality', 'language', 'image'])
                            ->published();

        // Filtros básicos
        if ($request->filled('category')) {
            $query->byCategory($request->category);
        }

        if ($request->filled('search')) {
            $query->search($request->search);
        }

        if ($request->boolean('featured')) {
            $query->featured();
        }

        if ($request->boolean('sustainability')) {
            $query->sustainability();
        }

        if ($request->boolean('breaking')) {
            $query->breaking();
        }

        if ($request->filled('author_id')) {
            $query->where('author_id', $request->author_id);
        }

        if ($request->filled('media_outlet_id')) {
            $query->where('media_outlet_id', $request->media_outlet_id);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('published_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('published_at', '<=', $request->date_to);
        }

        // Ordenamiento
        $sortBy = $request->get('sort_by', 'published_at');
        $sortOrder = $request->get('sort_order', 'desc');
        
        $allowedSorts = ['published_at', 'views_count', 'shares_count', 'title'];
        if (in_array($sortBy, $allowedSorts)) {
            $query->orderBy($sortBy, $sortOrder === 'asc' ? 'asc' : 'desc');
        }

        $articles = $query->paginate($perPage);

        return NewsArticleResource::collection($articles);
    }

    /**
     * Mostrar un artículo específico.
     */
    public function show(Request $request, string $idOrSlug): JsonResponse
    {
        $article = NewsArticle::with(['author', 'mediaOutlet', 'municipality', 'language', 'image', 'tags'])
                              ->where('id', $idOrSlug)
                              ->orWhere('slug', $idOrSlug)
                              ->firstOrFail();

        // Incrementar contador de vistas
        $article->incrementViews();

        // Obtener artículos relacionados
        $relatedArticles = $article->getRelatedArticles(5);

        // Agregar artículos relacionados como atributo temporal
        $article->related_articles = $relatedArticles->map(function($related) {
            return [
                'id' => $related->id,
                'title' => $related->title,
                'slug' => $related->slug,
                'published_at' => $related->published_at,
                'environmental_impact_score' => $related->environmental_impact_score,
            ];
        });

        return new NewsArticleResource($article);
    }

    /**
     * Artículos destacados.
     */
    public function featured(Request $request): JsonResponse
    {
        $limit = min((int) $request->get('limit', 10), 50);
        
        $articles = NewsArticle::with(['author', 'mediaOutlet'])
                              ->featured()
                              ->published()
                              ->orderBy('published_at', 'desc')
                              ->limit($limit)
                              ->get();

        return NewsArticleResource::collection($articles);
    }

    /**
     * Noticias de última hora.
     */
    public function breaking(Request $request): JsonResponse
    {
        $hours = min((int) $request->get('hours', 24), 168);
        $limit = min((int) $request->get('limit', 20), 50);
        
        $articles = NewsArticle::with(['author', 'mediaOutlet'])
                              ->where('is_breaking_news', true)
                              ->where('published_at', '>', now()->subHours($hours))
                              ->published()
                              ->orderBy('published_at', 'desc')
                              ->limit($limit)
                              ->get();

        return NewsArticleResource::collection($articles);
    }

    /**
     * Artículos de sostenibilidad.
     */
    public function sustainability(Request $request): JsonResponse
    {
        $limit = min((int) $request->get('limit', 15), 50);
        
        $query = NewsArticle::with(['author', 'mediaOutlet', 'municipality'])
                           ->sustainability()
                           ->published();

        if ($request->filled('topic')) {
            $query->whereJsonContains('sustainability_topics', $request->topic);
        }

        if ($request->filled('min_impact_score')) {
            $query->where('environmental_impact_score', '>=', (float) $request->min_impact_score);
        }

        $articles = $query->orderBy('environmental_impact_score', 'desc')
                         ->orderBy('published_at', 'desc')
                         ->limit($limit)
                         ->get();

        return NewsArticleResource::collection($articles);
    }

    /**
     * Buscar artículos por proximidad geográfica.
     */
    public function nearLocation(Request $request): JsonResponse
    {
        $request->validate([
            'lat' => 'required|numeric|between:-90,90',
            'lng' => 'required|numeric|between:-180,180',
            'radius' => 'integer|min:1|max:500',
            'limit' => 'integer|min:1|max:50',
        ]);

        $lat = (float) $request->lat;
        $lng = (float) $request->lng;
        $radius = (int) $request->get('radius', 50);
        $limit = (int) $request->get('limit', 15);

        $articles = NewsArticle::with(['author', 'mediaOutlet', 'municipality'])
                              ->nearLocation($lat, $lng, $radius)
                              ->published()
                              ->orderBy('published_at', 'desc')
                              ->limit($limit)
                              ->get();

        return NewsArticleResource::collection($articles);
    }

    /**
     * Artículos más populares por engagement.
     */
    public function popular(Request $request): JsonResponse
    {
        $period = $request->get('period', 'month');
        $metric = $request->get('metric', 'views');
        $limit = min((int) $request->get('limit', 20), 50);

        $query = NewsArticle::with(['author', 'mediaOutlet'])
                           ->published();

        // Filtro de período
        switch ($period) {
            case 'today':
                $query->whereDate('published_at', today());
                break;
            case 'week':
                $query->where('published_at', '>=', now()->subWeek());
                break;
            case 'month':
                $query->where('published_at', '>=', now()->subMonth());
                break;
            case 'year':
                $query->where('published_at', '>=', now()->subYear());
                break;
        }

        // Ordenamiento por métrica
        switch ($metric) {
            case 'shares':
                $query->orderBy('shares_count', 'desc');
                break;
            case 'comments':
                $query->orderBy('comments_count', 'desc');
                break;
            case 'engagement':
                $query->orderByRaw('(shares_count + comments_count) DESC');
                break;
            default:
                $query->orderBy('views_count', 'desc');
        }

        $articles = $query->limit($limit)->get();

        return NewsArticleResource::collection($articles);
    }

    /**
     * Estadísticas de artículos.
     */
    public function statistics(): JsonResponse
    {
        $totalArticles = NewsArticle::count();
        $published = NewsArticle::published()->count();
        $featured = NewsArticle::featured()->count();
        $sustainability = NewsArticle::sustainability()->count();
        $breakingActive = NewsArticle::breaking()->count();

        $totalViews = NewsArticle::sum('views_count');
        $totalShares = NewsArticle::sum('shares_count');

        $topCategories = NewsArticle::published()
                                   ->selectRaw('category, COUNT(*) as count')
                                   ->groupBy('category')
                                   ->orderBy('count', 'desc')
                                   ->limit(5)
                                   ->get();

        $avgImpactScore = NewsArticle::whereNotNull('environmental_impact_score')
                                    ->avg('environmental_impact_score');
        
        $highImpactArticles = NewsArticle::where('environmental_impact_score', '>=', 8)
                                        ->count();

        return response()->json([
            'total_articles' => $totalArticles,
            'published' => $published,
            'featured' => $featured,
            'sustainability_articles' => $sustainability,
            'breaking_news_active' => $breakingActive,
            'total_views' => $totalViews,
            'total_shares' => $totalShares,
            'top_categories' => $topCategories,
            'sustainability_impact' => [
                'avg_score' => round((float) $avgImpactScore, 1),
                'high_impact_articles' => $highImpactArticles,
            ],
        ]);
    }

    /**
     * Analizar temas de sostenibilidad en un artículo.
     */
    public function analyzeSustainability(NewsArticle $article): JsonResponse
    {
        $topics = $article->analyzeSustainabilityTopics();
        $impactScore = $article->calculateEnvironmentalImpact();
        
        $article->save();

        return response()->json([
            'article_id' => $article->id,
            'sustainability_topics' => $topics,
            'environmental_impact_score' => $impactScore,
            'analysis_timestamp' => now(),
        ]);
    }

    /**
     * Incrementar contador de compartidos.
     */
    public function incrementShares(NewsArticle $article): JsonResponse
    {
        $article->incrementShares();

        return response()->json([
            'article_id' => $article->id,
            'shares_count' => $article->shares_count,
            'message' => 'Contador de compartidos incrementado',
        ]);
    }
}