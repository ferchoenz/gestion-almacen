<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        // $roles será un array de nombres de rol, ej: ['Administrador']

        // Si el usuario no está logueado O no tiene un rol O su rol no está en la lista permitida...
        if (! $request->user() || ! $request->user()->role || ! in_array($request->user()->role->name, $roles)) {
            // Lo sacamos de ahí.
            abort(403, 'ACCESO NO AUTORIZADO.');
        }

        // Si pasó el filtro, déjalo continuar.
        return $next($request);
    }
}
