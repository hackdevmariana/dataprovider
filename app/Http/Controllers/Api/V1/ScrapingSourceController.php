<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\ScrapingSource;
use App\Http\Resources\V1\ScrapingSourceResource;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

/**
 * Controlador para gestión de fuentes de scraping.
 * 
 * API para gestión de fuentes de datos que alimentan el sistema
 * de noticias mediante scraping automático de RSS y otras fuentes.
 * 
 * @group Medios y Comunicación
 * @subgroup Fuentes de Scraping
 */
class ScrapingSourceController extends Controller
{
    /**
     * Listar fuentes de scraping.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $perPage = min((int) $request->get('per_page', 15), 100);
        
        $query = ScrapingSource::query();

        // Filtros
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('frequency')) {
            $query->where('frequency', $request->frequency);
        }

        if ($request->boolean('active_only')) {
            $query->where('is_active', true);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('source_type_description', 'LIKE', "%{$search}%")
                  ->orWhere('url', 'LIKE', "%{$search}%");
            });
        }

        // Ordenamiento
        $sortBy = $request->get('sort_by', 'name');
        $sortOrder = $request->get('sort_order', 'asc');
        
        $allowedSorts = ['name', 'type', 'frequency', 'last_scraped_at', 'is_active'];
        if (in_array($sortBy, $allowedSorts)) {
            $query->orderBy($sortBy, $sortOrder === 'desc' ? 'desc' : 'asc');
        }

        $sources = $query->paginate($perPage);

        return ScrapingSourceResource::collection($sources);
    }

    /**
     * Mostrar una fuente específica.
     */
    public function show(ScrapingSource $scrapingSource): ScrapingSourceResource
    {
        return new ScrapingSourceResource($scrapingSource);
    }

    /**
     * Fuentes activas.
     */
    public function active(Request $request): AnonymousResourceCollection
    {
        $limit = min((int) $request->get('limit', 20), 100);
        
        $sources = ScrapingSource::where('is_active', true)
                                 ->orderBy('last_scraped_at', 'desc')
                                 ->limit($limit)
                                 ->get();

        return ScrapingSourceResource::collection($sources);
    }

    /**
     * Fuentes por tipo.
     */
    public function byType(Request $request, string $type): JsonResponse|AnonymousResourceCollection
    {
        $allowedTypes = ['blog', 'newspaper', 'wiki', 'other'];
        
        if (!in_array($type, $allowedTypes)) {
            return response()->json([
                'error' => 'Tipo no válido',
                'allowed_types' => $allowedTypes
            ], 400);
        }

        $limit = min((int) $request->get('limit', 20), 100);
        
        $sources = ScrapingSource::where('type', $type)
                                 ->where('is_active', true)
                                 ->orderBy('name')
                                 ->limit($limit)
                                 ->get();

        return ScrapingSourceResource::collection($sources);
    }

    /**
     * Fuentes especializadas en sostenibilidad.
     */
    public function sustainability(Request $request): AnonymousResourceCollection
    {
        $limit = min((int) $request->get('limit', 15), 50);
        
        $sources = ScrapingSource::where('is_active', true)
                                 ->where(function($query) {
                                     $query->where('source_type_description', 'LIKE', '%sostenibilidad%')
                                           ->orWhere('source_type_description', 'LIKE', '%medio ambiente%')
                                           ->orWhere('source_type_description', 'LIKE', '%energía%')
                                           ->orWhere('source_type_description', 'LIKE', '%renovable%')
                                           ->orWhere('name', 'LIKE', '%sostenib%')
                                           ->orWhere('name', 'LIKE', '%energía%')
                                           ->orWhere('name', 'LIKE', '%ambiente%');
                                 })
                                 ->orderBy('last_scraped_at', 'desc')
                                 ->limit($limit)
                                 ->get();

        return ScrapingSourceResource::collection($sources);
    }

    /**
     * Estadísticas de fuentes de scraping.
     */
    public function statistics(): JsonResponse
    {
        $totalSources = ScrapingSource::count();
        $activeSources = ScrapingSource::where('is_active', true)->count();
        $inactiveSources = ScrapingSource::where('is_active', false)->count();

        // Por tipo
        $byType = ScrapingSource::selectRaw('type, COUNT(*) as count')
                                ->groupBy('type')
                                ->get()
                                ->pluck('count', 'type');

        // Por frecuencia
        $byFrequency = ScrapingSource::selectRaw('frequency, COUNT(*) as count')
                                     ->whereNotNull('frequency')
                                     ->groupBy('frequency')
                                     ->get()
                                     ->pluck('count', 'frequency');

        // Scraping reciente
        $recentlyScraped = ScrapingSource::where('last_scraped_at', '>', now()->subDays(7))
                                         ->count();

        // Fuentes especializadas
        $sustainabilitySources = ScrapingSource::where(function($query) {
                                                   $query->where('source_type_description', 'LIKE', '%sostenibilidad%')
                                                         ->orWhere('source_type_description', 'LIKE', '%medio ambiente%')
                                                         ->orWhere('source_type_description', 'LIKE', '%energía%');
                                               })
                                               ->count();

        return response()->json([
            'total_sources' => $totalSources,
            'active_sources' => $activeSources,
            'inactive_sources' => $inactiveSources,
            'by_type' => $byType,
            'by_frequency' => $byFrequency,
            'recently_scraped' => $recentlyScraped,
            'sustainability_sources' => $sustainabilitySources,
            'activity_rate' => $totalSources > 0 ? round(($activeSources / $totalSources) * 100, 1) : 0,
        ]);
    }

    /**
     * Fuentes que necesitan scraping.
     */
    public function needsScraping(Request $request): AnonymousResourceCollection
    {
        $hours = (int) $request->get('hours', 24);
        $limit = min((int) $request->get('limit', 20), 100);
        
        $sources = ScrapingSource::where('is_active', true)
                                 ->where(function($query) use ($hours) {
                                     $query->whereNull('last_scraped_at')
                                           ->orWhere('last_scraped_at', '<', now()->subHours($hours));
                                 })
                                 ->orderBy('last_scraped_at', 'asc')
                                 ->limit($limit)
                                 ->get();

        return ScrapingSourceResource::collection($sources);
    }

    /**
     * Actualizar timestamp de último scraping.
     */
    public function updateLastScraped(ScrapingSource $scrapingSource): JsonResponse
    {
        $scrapingSource->update([
            'last_scraped_at' => now()
        ]);

        return response()->json([
            'source_id' => $scrapingSource->id,
            'source_name' => $scrapingSource->name,
            'last_scraped_at' => $scrapingSource->last_scraped_at,
            'message' => 'Timestamp de scraping actualizado',
        ]);
    }

    /**
     * Activar/desactivar fuente.
     */
    public function toggleActive(ScrapingSource $scrapingSource): JsonResponse
    {
        $scrapingSource->update([
            'is_active' => !$scrapingSource->is_active
        ]);

        return response()->json([
            'source_id' => $scrapingSource->id,
            'source_name' => $scrapingSource->name,
            'is_active' => $scrapingSource->is_active,
            'message' => $scrapingSource->is_active ? 'Fuente activada' : 'Fuente desactivada',
        ]);
    }
}
