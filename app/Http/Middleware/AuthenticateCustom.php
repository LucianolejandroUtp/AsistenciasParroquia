<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * AuthenticateCustom - Middleware de autenticación personalizado
 * 
 * Middleware personalizado para el sistema parroquial que maneja
 * la autenticación de usuarios y redirecciones apropiadas.
 */
class AuthenticateCustom
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$guards): Response
    {
        // Si no hay guards especificados, usar el guard por defecto
        if (empty($guards)) {
            $guards = [null];
        }

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                // Usuario autenticado - continuar
                return $next($request);
            }
        }

        // Usuario no autenticado - redireccionar según el tipo de request
        if ($request->expectsJson()) {
            // Request AJAX/API - retornar JSON
            return response()->json([
                'message' => 'No autenticado. Inicie sesión para continuar.',
                'redirect' => route('auth.login')
            ], 401);
        }

        // Request web - redireccionar a login con intención
        return redirect()->guest(route('auth.login'))->with([
            'warning' => 'Debe iniciar sesión para acceder a esta página.',
            'intended' => $request->url()
        ]);
    }
}