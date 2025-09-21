<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Artisan;

class RouteStatsController extends Controller
{
    /**
     * Get route statistics without returning all routes
     */
    public function index(): JsonResponse
    {
        try {
            // Ejecutar comando artisan para obtener el conteo
            $exitCode = Artisan::call('route:list', [
                '--path' => 'api/v1'
            ]);
            
            $output = Artisan::output();
            $lines = explode("\n", trim($output));
            
            $totalRoutes = 0;
            $routesByMethod = [];
            $routesByController = [];
            
            foreach ($lines as $line) {
                if (empty($line) || !str_contains($line, 'api/v1')) continue;
                
                $totalRoutes++;
                
                // Contar por método
                $parts = preg_split('/\s+/', trim($line));
                if (count($parts) > 0) {
                    $methods = explode('|', $parts[0]);
                    foreach ($methods as $method) {
                        // Solo contar HEAD si no hay GET en la misma línea
                        if ($method === 'HEAD' && in_array('GET', $methods)) {
                            continue; // No contar HEAD si ya hay GET
                        }
                        $routesByMethod[$method] = ($routesByMethod[$method] ?? 0) + 1;
                    }
                }
                
                // Contar por controlador
                if (str_contains($line, '›')) {
                    $controllerPart = explode('›', $line)[1] ?? '';
                    $controller = trim(explode('@', $controllerPart)[0] ?? '');
                    if (!empty($controller)) {
                        $routesByController[$controller] = ($routesByController[$controller] ?? 0) + 1;
                    }
                }
            }
            
            // Ordenar por número de rutas
            arsort($routesByMethod);
            arsort($routesByController);
            
            return response()->json([
                'total_routes' => $totalRoutes,
                'routes_by_method' => $routesByMethod,
                'top_controllers' => array_slice($routesByController, 0, 20, true),
                'debug_info' => [
                    'total_lines' => count($lines),
                    'get_count' => $routesByMethod['GET'] ?? 0,
                    'post_count' => $routesByMethod['POST'] ?? 0,
                    'delete_count' => $routesByMethod['DELETE'] ?? 0,
                    'put_count' => $routesByMethod['PUT'] ?? 0,
                    'patch_count' => $routesByMethod['PATCH'] ?? 0,
                ],
                'message' => 'Estadísticas de rutas obtenidas exitosamente'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'total_routes' => 0
            ], 500);
        }
    }
}
