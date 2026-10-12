<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Restringe una ruta a uno o varios roles.
 *
 * Uso en las rutas:  ->middleware('rol:Administrador')
 *                    ->middleware('rol:Administrador,Nutricionista')
 */
class VerificarRol
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $usuario = Auth::user();

        if (! $usuario || ! $usuario->act_usu) {
            Auth::logout();

            return redirect()->route('login')
                             ->withErrors(['cor_usu' => 'Su cuenta no se encuentra activa.']);
        }

        if (! $usuario->tieneRol(...$roles)) {
            abort(403, 'No cuenta con permisos para acceder a esta seccion.');
        }

        return $next($request);
    }
}
