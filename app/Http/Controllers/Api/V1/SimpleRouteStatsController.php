<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use OpenApi\Annotations as OA;
use Illuminate\Support\Facades\Artisan;

/**
 * @OA\Tag(
 *     name="Estadísticas Simples de Rutas",
 *     description="APIs para la gestión de Estadísticas Simples de Rutas"
 * )
 */
class SimpleRouteStatsController extends Controller
{
    /**
     * Get simple route statistics
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
            $getCount = 0;
            $postCount = 0;
            $deleteCount = 0;
            $putCount = 0;
            $patchCount = 0;
            
            foreach ($lines as $line) {
                if (empty($line) || !str_contains($line, 'api/v1')) continue;
                
                $totalRoutes++;
                
                // Contar métodos específicos - versión más precisa
                $parts = preg_split('/\s+/', trim($line));
                if (count($parts) > 0) {
                    $methods = explode('|', $parts[0]);
                    foreach ($methods as $method) {
                        switch ($method) {
                            case 'GET':
                                $getCount++;
                                break;
                            case 'POST':
                                $postCount++;
                                break;
                            case 'DELETE':
                                $deleteCount++;
                                break;
                            case 'PUT':
                                $putCount++;
                                break;
                            case 'PATCH':
                                $patchCount++;
                                break;
                        }
                    }
                }
            }
            
            return response()->json([
                'total_routes' => $totalRoutes,
                'get_routes' => $getCount,
                'post_routes' => $postCount,
                'delete_routes' => $deleteCount,
                'put_routes' => $putCount,
                'patch_routes' => $patchCount,
                'message' => 'Conteo simple de rutas'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
