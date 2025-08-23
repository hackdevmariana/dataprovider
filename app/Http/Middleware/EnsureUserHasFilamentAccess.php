<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class EnsureUserHasFilamentAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        // Si el usuario no ha iniciado sesión, permitir el acceso para que vea el login
        if (! Auth::check()) {
            return $next($request);
        }

        $user = Auth::user();
        
        // Verificar que el usuario existe y tiene el método necesario
        if (!$user) {
            abort(403, 'Usuario no encontrado.');
        }

        try {
            // Verificar permisos de una manera más robusta
            $hasPermission = false;
            
            // Primero verificar si el usuario tiene permisos directos
            if (method_exists($user, 'hasPermissionTo')) {
                $hasPermission = $user->hasPermissionTo('access filament');
            }
            
            // Si no tiene permiso directo, verificar roles
            if (!$hasPermission && method_exists($user, 'hasRole')) {
                $hasPermission = $user->hasRole(['admin', 'gestor', 'tecnico']);
            }
            
            // Si aún no tiene permiso, denegar el acceso
            if (!$hasPermission) {
                abort(403, 'No tienes acceso al panel de administración.');
            }
            
        } catch (\Exception $e) {
            // Log del error para debugging
            \Log::error('Error en EnsureUserHasFilamentAccess: ' . $e->getMessage() . ' - User ID: ' . ($user->id ?? 'null'));
            
            // En caso de error, permitir acceso solo para admins conocidos
            if ($user && isset($user->email) && in_array($user->email, ['admin@demo.com', 'test@example.com'])) {
                return $next($request);
            }
            
            abort(500, 'Error interno del sistema. Contacta al administrador.');
        }

        return $next($request);
    }
}
