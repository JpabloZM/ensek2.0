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
     * @param  string  ...$roles
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!$request->user() || !$request->user()->role) {
            abort(403, 'No tienes permiso para acceder a esta página.');
        }
        
        // Si hay múltiples roles, verificar si el usuario tiene alguno de ellos
        foreach ($roles as $role) {
            if ($request->user()->role->name === $role) {
                return $next($request);
            }
        }
        
        abort(403, 'No tienes permiso para acceder a esta página.');
    }
}
