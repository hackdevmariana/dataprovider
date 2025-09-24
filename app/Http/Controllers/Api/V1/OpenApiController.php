<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Artisan;

class OpenApiController extends Controller
{
    /**
     * Generate complete OpenAPI documentation
     */
    public function generate(): JsonResponse
    {
        try {
            // Obtener todas las rutas
            $exitCode = Artisan::call('route:list', [
                '--path' => 'api/v1'
            ]);
            
            $output = Artisan::output();
            $lines = explode("\n", trim($output));
            
            $paths = [];
            $tags = [];
            $tagCounts = [];
            
            foreach ($lines as $line) {
                if (empty($line) || !str_contains($line, 'api/v1')) continue;
                
                // Parsear la línea
                $parts = preg_split('/\s+/', trim($line));
                if (count($parts) < 3) continue;
                
                $methods = explode('|', $parts[0]);
                $uri = $parts[1];
                $controller = null;
                
                // Extraer controlador
                if (str_contains($line, '›')) {
                    $controllerPart = explode('›', $line)[1] ?? '';
                    $controller = trim($controllerPart);
                }
                
                // Determinar tag basado en la URI
                $tag = $this->determineTag($uri, $controller);
                if (!isset($tagCounts[$tag])) {
                    $tagCounts[$tag] = 0;
                }
                $tagCounts[$tag]++;
                
                // Crear path
                $pathKey = $uri;
                if (!isset($paths[$pathKey])) {
                    $paths[$pathKey] = [];
                }
                
                foreach ($methods as $method) {
                    if (in_array($method, ['GET', 'POST', 'PUT', 'DELETE', 'PATCH'])) {
                        $paths[$pathKey][strtolower($method)] = [
                            'tags' => [$tag],
                            'summary' => $this->generateSummary($method, $uri, $controller),
                            'description' => $this->generateDescription($method, $uri, $controller),
                            'responses' => [
                                '200' => [
                                    'description' => 'Respuesta exitosa',
                                    'content' => [
                                        'application/json' => [
                                            'schema' => ['type' => 'object']
                                        ]
                                    ]
                                ]
                            ]
                        ];
                    }
                }
            }
            
            // Crear tags con conteos
            foreach ($tagCounts as $tagName => $count) {
                $tags[] = [
                    'name' => $tagName,
                    'description' => $this->getTagDescription($tagName) . " - {$count} endpoints"
                ];
            }
            
            // Estructura OpenAPI completa
            $openApi = [
                'openapi' => '3.0.0',
                'info' => [
                    'title' => 'DataProvider API',
                    'description' => 'API completa para el sistema DataProvider con gestión de datos geográficos, energéticos, culturales y sociales. Total: ' . array_sum($tagCounts) . ' endpoints',
                    'version' => '1.0.0'
                ],
                'servers' => [
                    [
                        'url' => '/api/v1',
                        'description' => 'Servidor API V1'
                    ]
                ],
                'paths' => $paths,
                'components' => [
                    'securitySchemes' => [
                        'sanctum' => [
                            'type' => 'http',
                            'scheme' => 'bearer',
                            'bearerFormat' => 'JWT',
                            'description' => 'Autenticación mediante Laravel Sanctum'
                        ]
                    ]
                ],
                'tags' => $tags
            ];
            
            return response()->json($openApi);
            
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    private function determineTag($uri, $controller): string
    {
        $uri = strtolower($uri);
        
        if (str_contains($uri, 'status') || str_contains($uri, 'routes')) {
            return 'System';
        }
        if (str_contains($uri, 'quotes')) return 'Quotes';
        if (str_contains($uri, 'catholic-saints')) return 'Catholic Saints';
        if (str_contains($uri, 'books')) return 'Books';
        if (str_contains($uri, 'provinces') || str_contains($uri, 'municipalities')) return 'Geographic';
        if (str_contains($uri, 'cooperatives') || str_contains($uri, 'energy')) return 'Energy';
        if (str_contains($uri, 'users') || str_contains($uri, 'follow')) return 'Social';
        if (str_contains($uri, 'news')) return 'News Sources';
        if (str_contains($uri, 'historical') || str_contains($uri, 'events')) return 'Historical Events';
        if (str_contains($uri, 'parishes')) return 'Parishes';
        if (str_contains($uri, 'festivals')) return 'Festivals';
        if (str_contains($uri, 'organizations')) return 'Organizations';
        if (str_contains($uri, 'people') || str_contains($uri, 'persons')) return 'People';
        if (str_contains($uri, 'awards')) return 'Awards';
        if (str_contains($uri, 'settings')) return 'Settings';
        
        return 'Others';
    }
    
    private function generateSummary($method, $uri, $controller): string
    {
        $action = match(strtolower($method)) {
            'get' => 'Obtener',
            'post' => 'Crear',
            'put' => 'Actualizar',
            'patch' => 'Modificar',
            'delete' => 'Eliminar',
            default => 'Procesar'
        };
        
        $resource = $this->extractResource($uri);
        return "{$action} {$resource}";
    }
    
    private function generateDescription($method, $uri, $controller): string
    {
        $action = match(strtolower($method)) {
            'get' => 'Obtiene',
            'post' => 'Crea',
            'put' => 'Actualiza',
            'patch' => 'Modifica',
            'delete' => 'Elimina',
            default => 'Procesa'
        };
        
        $resource = $this->extractResource($uri);
        return "{$action} {$resource}";
    }
    
    private function extractResource($uri): string
    {
        $parts = explode('/', $uri);
        $resource = end($parts);
        
        if (str_starts_with($resource, '{')) {
            $resource = $parts[count($parts) - 2] ?? 'recurso';
        }
        
        return ucfirst(str_replace('-', ' ', $resource));
    }
    
    private function getTagDescription($tag): string
    {
        return match($tag) {
            'System' => 'Sistema y utilidades',
            'Quotes' => 'Sistema de citas inspiracionales',
            'Catholic Saints' => 'Santoral católico',
            'Books' => 'Sistema de libros',
            'Geographic' => 'Datos geográficos',
            'Energy' => 'Datos energéticos',
            'Social' => 'Funcionalidades sociales',
            'News Sources' => 'Fuentes de noticias',
            'Historical Events' => 'Eventos históricos',
            'Parishes' => 'Parroquias',
            'Festivals' => 'Festivales y eventos',
            'Organizations' => 'Organizaciones',
            'People' => 'Personas y profesionales',
            'Awards' => 'Premios y reconocimientos',
            'Settings' => 'Configuración',
            default => 'Otros recursos'
        };
    }
}







