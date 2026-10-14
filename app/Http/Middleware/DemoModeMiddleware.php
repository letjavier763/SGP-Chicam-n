<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class DemoModeMiddleware
{
    /**
     * Intercepta peticiones de modificación (POST, PUT, PATCH, DELETE)
     * para el usuario de demostración, impidiendo alterar los datos de producción.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $user = Auth::user();

            // Identificar si es el usuario de demostración
            if ($user->username === 'demo') {
                // Permitir siempre peticiones GET / HEAD y la ruta de cierre de sesión
                if ($request->isMethodSafe() || $request->routeIs('logout')) {
                    return $next($request);
                }

                $mensaje = 'Modo Demostración: Las acciones de creación, modificación y eliminación están deshabilitadas para proteger los datos reales de producción.';

                // Si la petición es AJAX / API JSON
                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json([
                        'status'  => 'error',
                        'message' => $mensaje,
                    ], 403);
                }

                // Redirigir hacia atrás con alerta
                return back()
                    ->with('error', $mensaje)
                    ->withInput();
            }
        }

        return $next($request);
    }
}
