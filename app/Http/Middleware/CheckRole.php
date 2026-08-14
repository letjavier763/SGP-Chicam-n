<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Maneja una petición entrante.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$roles Roles permitidos (ej: Administrador, Recepcionista, Director)
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $usuario = Auth::user();

        // Carga la relación si no está cargada
        if (!$usuario->relationLoaded('rol')) {
            $usuario->load('rol');
        }

        // Verifica si el rol del usuario está dentro de los permitidos (Fila de la tabla roles)
        if (in_array($usuario->rol->nombre_rol, $roles)) {
            return $next($request);
        }

        // Si no tiene acceso, abortar con error 403 (No autorizado) o redirigir
        abort(403, 'No tienes permisos para acceder a esta sección.');
    }
}
