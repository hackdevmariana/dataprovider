<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

/**
 * @OA\Tag(name="System")
 */
class ApiStatusController extends Controller
{
    /**
     * Get API status and statistics.
     * 
     * @OA\Get(
     *     path="/status",
     *     summary="Estado de la API y estadísticas",
     *     description="Obtiene el estado actual de la API, estadísticas generales y información del sistema",
     *     tags={"System"},
     *     @OA\Response(
     *         response=200,
     *         description="Estado de la API obtenido exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="operational"),
     *             @OA\Property(property="version", type="string", example="1.0.0"),
     *             @OA\Property(property="timestamp", type="string", format="date-time"),
     *             @OA\Property(property="statistics", type="object",
     *                 @OA\Property(property="total_models", type="integer", example=145),
     *                 @OA\Property(property="total_controllers", type="integer", example=145),
     *                 @OA\Property(property="total_endpoints", type="integer", example=751)
     *             )
     *         )
     *     )
     * )
     */
    public function status(): JsonResponse
    {
        return response()->json([
            'status' => 'operational',
            'version' => '1.0.0',
            'timestamp' => now()->toISOString(),
            'statistics' => [
                'total_models' => 145,
                'total_controllers' => 145,
                'total_endpoints' => 751,
                'documented_endpoints' => 751,
                'coverage_percentage' => 100,
            ],
            'documentation' => [
                'swagger_ui' => '/api/documentation',
                'openapi_json' => '/docs/api-docs.json',
                'markdown_docs' => '/docs/api-complete-documentation.md',
                'routes_list' => '/api/v1/routes',
            ],
            'categories' => [
                'quotes' => 'Sistema de citas inspiracionales',
                'catholic_saints' => 'Santoral católico',
                'books' => 'Sistema de libros',
                'news_sources' => 'Fuentes de noticias',
                'historical_events' => 'Eventos históricos',
                'parishes' => 'Parroquias',
                'energy' => 'Datos energéticos',
                'geographic' => 'Datos geográficos',
                'social' => 'Funcionalidades sociales',
                'system' => 'Sistema y utilidades',
            ]
        ]);
    }

    /**
     * Get all API routes
     * 
     * @OA\Get(
     *     path="/routes",
     *     summary="Lista todas las rutas de la API",
     *     description="Obtiene una lista completa de todas las rutas disponibles en la API",
     *     tags={"System"},
     *     @OA\Response(
     *         response=200,
     *         description="Lista de rutas obtenida exitosamente"
     *     )
     * )
     */
    public function routes(): JsonResponse
    {
        $routes = [];
        
        // Obtener todas las rutas de la API v1
        $apiRoutes = \Route::getRoutes();
        
        foreach ($apiRoutes as $route) {
            $uri = $route->uri();
            
            // Filtrar solo rutas de API v1
            if (str_starts_with($uri, 'api/v1')) {
                $methods = $route->methods();
                $name = $route->getName();
                
                $routes[] = [
                    'uri' => $uri,
                    'methods' => array_values(array_diff($methods, ['HEAD'])),
                    'name' => $name,
                    'controller' => $route->getActionName(),
                ];
            }
        }
        
        // Agrupar por categorías - versión más completa
        $categorizedRoutes = [];
        
        // Categorías principales
        $categories = [
            'quotes' => ['quotes', 'quote'],
            'catholic_saints' => ['catholic-saints', 'catholic_saints'],
            'books' => ['books', 'book'],
            'provinces' => ['provinces', 'province'],
            'municipalities' => ['municipalities', 'municipality'],
            'cooperatives' => ['cooperatives', 'cooperative'],
            'energy' => ['energy', 'electricity', 'carbon', 'solar', 'wind', 'battery'],
            'users' => ['users', 'user'],
            'achievements' => ['achievements', 'achievement'],
            'news' => ['news', 'articles', 'media', 'outlets'],
            'historical' => ['historical', 'events', 'anniversaries', 'timeline'],
            'social' => ['social', 'follow', 'activity', 'interaction', 'topic', 'hashtag'],
            'geographic' => ['autonomous', 'communities', 'countries', 'languages', 'timezones', 'regions'],
            'festivals' => ['festivals', 'festival', 'events', 'artists', 'venues'],
            'religious' => ['parishes', 'parish', 'bishoprics', 'devotions', 'liturgical'],
            'projects' => ['projects', 'project', 'investments', 'marketplace', 'roof'],
            'certificates' => ['certificates', 'certification', 'verification'],
            'prices' => ['prices', 'price', 'offers', 'forecasts', 'alerts'],
            'lists' => ['lists', 'list', 'bookmarks', 'favorites'],
            'content' => ['content', 'votes', 'reviews', 'endorsements'],
            'organizations' => ['organizations', 'organization', 'companies'],
            'people' => ['people', 'person', 'persons', 'professionals'],
            'awards' => ['awards', 'award', 'winners'],
            'settings' => ['settings', 'config', 'preferences'],
            'system' => ['system', 'status', 'routes', 'logs', 'sync']
        ];
        
        foreach ($categories as $categoryName => $keywords) {
            $categoryRoutes = [];
            foreach ($routes as $route) {
                foreach ($keywords as $keyword) {
                    if (str_contains($route['uri'], $keyword)) {
                        $categoryRoutes[] = $route;
                        break; // Evitar duplicados
                    }
                }
            }
            if (!empty($categoryRoutes)) {
                $categorizedRoutes[$categoryName] = array_values($categoryRoutes);
            }
        }
        
        // Agrupar rutas no categorizadas en "otros"
        $categorizedUris = [];
        foreach ($categorizedRoutes as $routes) {
            foreach ($routes as $route) {
                $categorizedUris[] = $route['uri'];
            }
        }
        
        $uncategorizedRoutes = array_filter($routes, function($route) use ($categorizedUris) {
            return !in_array($route['uri'], $categorizedUris);
        });
        
        if (!empty($uncategorizedRoutes)) {
            $categorizedRoutes['others'] = array_values($uncategorizedRoutes);
        }
        
        return response()->json([
            'total_routes' => count($routes),
            'categorized_routes' => $categorizedRoutes,
            'all_routes' => $routes,
            'debug_info' => [
                'total_api_routes' => count($apiRoutes),
                'filtered_routes_count' => count($routes),
                'sample_uris' => array_slice(array_map(fn($r) => $r['uri'], $routes), 0, 10)
            ],
            'message' => 'Lista completa de rutas de la API v1'
        ]);
    }
}
