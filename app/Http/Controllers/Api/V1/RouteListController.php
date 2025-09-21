<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

class RouteListController extends Controller
{
    /**
     * Get all API routes using a more reliable method
     */
    public function index(): JsonResponse
    {
        try {
            // Usar Artisan directamente para obtener las rutas
            $exitCode = Artisan::call('route:list', [
                '--path' => 'api/v1',
                '--json' => false
            ]);
            
            $output = Artisan::output();
            $lines = explode("\n", trim($output));
            
            $routes = [];
            
            foreach ($lines as $line) {
                if (empty($line) || !str_contains($line, 'api/v1')) continue;
                
                // Parsear la línea
                $parts = preg_split('/\s+/', trim($line));
                if (count($parts) < 3) continue;
                
                $methods = explode('|', $parts[0]);
                $methods = array_filter($methods, fn($m) => $m !== 'HEAD');
                
                $uri = $parts[1];
                
                // Extraer nombre del controlador
                $controller = null;
                if (str_contains($line, '›')) {
                    $controllerPart = explode('›', $line)[1] ?? '';
                    $controller = trim($controllerPart);
                }
                
                $routes[] = [
                    'uri' => $uri,
                    'methods' => array_values($methods),
                    'controller' => $controller,
                ];
            }
            
            return response()->json([
                'total_routes' => count($routes),
                'routes' => array_slice($routes, 0, 100), // Solo las primeras 100 para evitar problemas de tamaño
                'message' => 'Rutas obtenidas exitosamente',
                'note' => 'Solo se muestran las primeras 100 rutas para evitar problemas de tamaño de respuesta'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'total_routes' => 0,
                'routes' => []
            ], 500);
        }
    }
}
