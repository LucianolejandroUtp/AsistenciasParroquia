<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\AuthService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

/**
 * ProfileController - Gestión de perfil de usuario
 * 
 * Maneja la visualización y edición del perfil del usuario autenticado
 */
class ProfileController extends Controller implements HasMiddleware
{
    protected AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * Get the middleware that should be assigned to the controller.
     */
    public static function middleware(): array
    {
        return [
            new Middleware('auth.custom'),
        ];
    }

    /**
     * Mostrar perfil del usuario
     */
    public function show(): View
    {
        $user = $this->authService->getAuthenticatedUser();

        return view('auth.profile', [
            'title' => 'Mi Perfil - Sistema Asistencias',
            'user' => $user
        ]);
    }

    /**
     * Mostrar formulario de edición de perfil
     */
    public function edit(): View
    {
        $user = $this->authService->getAuthenticatedUser();

        return view('auth.profile-edit', [
            'title' => 'Editar Perfil - Sistema Asistencias',
            'user' => $user
        ]);
    }

    /**
     * Actualizar información del perfil
     */
    public function update(Request $request): RedirectResponse
    {
        $user = $this->authService->getAuthenticatedUser();

        // Validar datos de entrada
        $validated = $request->validate([
            'username' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
        ], [
            'username.required' => 'El nombre de usuario es obligatorio.',
            'username.max' => 'El nombre de usuario no puede exceder 255 caracteres.',
            'first_name.required' => 'El nombre es obligatorio.',
            'last_name.required' => 'El apellido es obligatorio.',
            'email.email' => 'El formato del email no es válido.'
        ]);

        try {
            // Actualizar perfil mediante AuthService
            $updatedUser = $this->authService->updateProfile($user, $validated);

            return redirect()->route('profile.show')->with([
                'success' => 'Perfil actualizado correctamente.'
            ]);

        } catch (ValidationException $e) {
            return back()
                ->withErrors($e->errors())
                ->withInput()
                ->with('error', 'Error al actualizar el perfil.');
        }
    }

    /**
     * Mostrar formulario de cambio de contraseña
     */
    public function showChangePassword(): View
    {
        return view('auth.change-password', [
            'title' => 'Cambiar Contraseña - Sistema Asistencias'
        ]);
    }

    /**
     * Cambiar contraseña del usuario
     */
    public function changePassword(Request $request): RedirectResponse
    {
        $user = $this->authService->getAuthenticatedUser();

        // Validar datos de entrada
        $validated = $request->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'current_password.required' => 'La contraseña actual es obligatoria.',
            'password.required' => 'La nueva contraseña es obligatoria.',
            'password.min' => 'La nueva contraseña debe tener al menos 8 caracteres.',
            'password.confirmed' => 'La confirmación de contraseña no coincide.'
        ]);

        try {
            // Cambiar contraseña mediante AuthService
            $this->authService->changePassword(
                $validated['current_password'],
                $validated['password'],
                $user
            );

            return redirect()->route('profile.show')->with([
                'success' => 'Contraseña cambiada correctamente.'
            ]);

        } catch (ValidationException $e) {
            return back()
                ->withErrors($e->errors())
                ->with('error', 'Error al cambiar la contraseña.');
        }
    }

    /**
     * Obtener información del usuario para AJAX
     */
    public function getUserInfo(Request $request)
    {
        if ($request->expectsJson()) {
            $user = $this->authService->getAuthenticatedUser();
            
            return response()->json([
                'user' => [
                    'id' => $user->id,
                    'username' => $user->username,
                    'email' => $user->email,
                    'first_name' => $user->first_name,
                    'last_name' => $user->last_name,
                    'full_name' => $user->first_name . ' ' . $user->last_name,
                    'user_type' => $user->userType->name,
                    'user_type_display' => $this->getUserTypeDisplay($user->userType->name)
                ]
            ]);
        }

        return redirect()->route('profile.show');
    }

    /**
     * Obtener nombre de visualización del tipo de usuario
     */
    private function getUserTypeDisplay(string $userType): string
    {
        return match($userType) {
            'Admin' => 'Administrador',
            'Profesor' => 'Catequista',
            'Staff' => 'Personal de apoyo',
            default => $userType
        };
    }
}