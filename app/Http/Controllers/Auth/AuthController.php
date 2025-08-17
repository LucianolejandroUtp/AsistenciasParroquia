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
 * AuthController - Controlador de autenticación
 * 
 * Maneja las acciones de login, logout y registro de usuarios
 * siguiendo el patrón Controller → Service → Repository
 */
class AuthController extends Controller implements HasMiddleware
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
            new Middleware('guest', only: ['showLogin', 'login', 'showRegister', 'register']),
        ];
    }

    /**
     * Mostrar formulario de login
     */
    public function showLogin(): View
    {
        return view('auth.login', [
            'title' => 'Iniciar Sesión - Sistema Asistencias'
        ]);
    }

    /**
     * Procesar intento de login
     */
    public function login(Request $request): RedirectResponse
    {
        // Validar datos de entrada
        $credentials = $request->validate([
            'email' => ['required', 'email', 'max:255'],
            'password' => ['required', 'string'],
            'remember' => ['nullable', 'boolean']
        ], [
            'email.required' => 'El email es obligatorio.',
            'email.email' => 'Debe ingresar un email válido.',
            'email.max' => 'El email no puede exceder 255 caracteres.',
            'password.required' => 'La contraseña es obligatoria.'
        ]);

        try {
            // Intentar autenticación mediante AuthService
            $result = $this->authService->attemptLogin($credentials, $request);

            // Login exitoso - redireccionar según rol
            return redirect()->intended($result['redirect_url'])->with([
                'success' => "¡Bienvenido, {$result['user']->name}! Has iniciado sesión correctamente."
            ]);

        } catch (ValidationException $e) {
            // Error de validación - retornar con errores
            return back()
                ->withErrors($e->errors())
                ->withInput($request->only('email', 'remember'))
                ->with('error', 'Error en las credenciales proporcionadas.');
        }
    }

    /**
     * Mostrar formulario de registro
     */
    public function showRegister(): View
    {
        return view('auth.register', [
            'title' => 'Registrar Usuario - Sistema Asistencias'
        ]);
    }

    /**
     * Procesar registro de nuevo usuario
     */
    public function register(Request $request): RedirectResponse
    {
        // Validar datos de entrada
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'user_type_id' => ['required', 'exists:user_types,id']
        ], [
            'name.required' => 'El nombre es obligatorio.',
            'email.required' => 'El email es obligatorio.',
            'email.email' => 'Debe ingresar un email válido.',
            'email.unique' => 'Este email ya está en uso.',
            'password.required' => 'La contraseña es obligatoria.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'password.confirmed' => 'La confirmación de contraseña no coincide.',
            'user_type_id.required' => 'Debe seleccionar un tipo de usuario.',
            'user_type_id.exists' => 'El tipo de usuario seleccionado no es válido.'
        ]);

        try {
            // Registrar usuario mediante AuthService
            $user = $this->authService->register($validated);

            // Registro exitoso - redireccionar a login
            return redirect()->route('auth.login')->with([
                'success' => "¡Usuario {$user->name} registrado exitosamente! Ya puede iniciar sesión."
            ]);

        } catch (ValidationException $e) {
            // Error de validación - retornar con errores
            return back()
                ->withErrors($e->errors())
                ->withInput($request->except('password', 'password_confirmation'))
                ->with('error', 'Error al registrar el usuario.');
        }
    }

    /**
     * Cerrar sesión del usuario
     */
    public function logout(Request $request): RedirectResponse
    {
        // Logout mediante AuthService
        $this->authService->logout($request);

        // Redireccionar a login con mensaje
        return redirect()->route('auth.login')->with([
            'success' => 'Ha cerrado sesión correctamente. ¡Hasta pronto!'
        ]);
    }

    /**
     * Verificar estado de autenticación (para AJAX)
     */
    public function checkAuth(Request $request)
    {
        if ($request->expectsJson()) {
            return response()->json([
                'authenticated' => $this->authService->isAuthenticated(),
                'user' => $this->authService->getAuthenticatedUser()
            ]);
        }

        return redirect()->route('dashboard');
    }
}