<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

/**
 * RoleMiddleware - Middleware de autorización por roles
 * 
 * Controla el acceso a rutas basado en los roles de usuario:
 * - Admin: Acceso completo al sistema
 * - Profesor: Gestión de asistencias y estudiantes
 * - Staff: Solo visualización de reportes básicos
 */
class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$roles
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // Verificar que el usuario esté autenticado
        if (!Auth::check()) {
            return $this->handleUnauthenticated($request);
        }

        $user = Auth::user();
        $userRole = $user->userType->name;

        // Verificar si el usuario tiene uno de los roles permitidos
        if (!in_array($userRole, $roles)) {
            Log::warning('Acceso denegado por rol insuficiente', [
                'user_id' => $user->id,
                'username' => $user->username,
                'user_role' => $userRole,
                'required_roles' => $roles,
                'route' => $request->route()->getName(),
                'url' => $request->url(),
                'ip' => $request->ip()
            ]);

            return $this->handleUnauthorized($request, $userRole, $roles);
        }

        // Usuario autorizado - continuar
        Log::info('Acceso autorizado', [
            'user_id' => $user->id,
            'username' => $user->username,
            'user_role' => $userRole,
            'route' => $request->route()->getName()
        ]);

        return $next($request);
    }

    /**
     * Manejar usuario no autenticado
     */
    private function handleUnauthenticated(Request $request): Response
    {
        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'No autenticado. Inicie sesión para continuar.',
                'redirect' => route('auth.login')
            ], 401);
        }

        return redirect()->guest(route('auth.login'))->with([
            'warning' => 'Debe iniciar sesión para acceder a esta página.'
        ]);
    }

    /**
     * Manejar acceso no autorizado por rol
     */
    private function handleUnauthorized(Request $request, string $userRole, array $requiredRoles): Response
    {
        $message = $this->getUnauthorizedMessage($userRole, $requiredRoles);
        $redirectRoute = $this->getRedirectRouteByRole($userRole);

        if ($request->expectsJson()) {
            return response()->json([
                'message' => $message,
                'redirect' => route($redirectRoute)
            ], 403);
        }

        return redirect()->route($redirectRoute)->with([
            'error' => $message
        ]);
    }

    /**
     * Obtener mensaje de error personalizado según el rol
     */
    private function getUnauthorizedMessage(string $userRole, array $requiredRoles): string
    {
        $rolesText = $this->formatRoles($requiredRoles);
        
        return match($userRole) {
            'Staff' => "Acceso restringido. Esta función requiere permisos de {$rolesText}.",
            'Profesor' => "Acceso restringido. Esta función está reservada para {$rolesText}.",
            default => "No tiene permisos suficientes para acceder a esta página. Se requiere: {$rolesText}."
        };
    }

    /**
     * Obtener ruta de redirección según el rol del usuario
     */
    private function getRedirectRouteByRole(string $userRole): string
    {
        return match($userRole) {
            'Admin' => 'dashboard.admin',
            'Profesor' => 'dashboard.profesor',
            'Staff' => 'dashboard.staff',
            default => 'dashboard'
        };
    }

    /**
     * Formatear lista de roles para mostrar al usuario
     */
    private function formatRoles(array $roles): string
    {
        $roleTranslations = [
            'Admin' => 'Administrador',
            'Profesor' => 'Catequista',
            'Staff' => 'Personal de apoyo'
        ];

        $translatedRoles = array_map(function($role) use ($roleTranslations) {
            return $roleTranslations[$role] ?? $role;
        }, $roles);

        if (count($translatedRoles) === 1) {
            return $translatedRoles[0];
        }

        if (count($translatedRoles) === 2) {
            return implode(' o ', $translatedRoles);
        }

        $lastRole = array_pop($translatedRoles);
        return implode(', ', $translatedRoles) . ' o ' . $lastRole;
    }
}