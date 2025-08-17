<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

/**
 * AuthService - Lógica de negocio para autenticación
 * 
 * Maneja toda la lógica relacionada con autenticación, sesiones
 * y autorización de usuarios del sistema parroquial.
 */
class AuthService
{
    protected UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * Intentar autenticar usuario
     *
     * @param array $credentials
     * @param Request $request
     * @return array
     * @throws ValidationException
     */
    public function attemptLogin(array $credentials, Request $request): array
    {
        // Buscar usuario por email
        $user = $this->userRepository->findByEmail($credentials['email']);

        if (!$user) {
            Log::warning('Intento de login con email inexistente', [
                'email' => $credentials['email'],
                'ip' => $request->ip()
            ]);
            
            throw ValidationException::withMessages([
                'email' => ['Las credenciales proporcionadas no coinciden con nuestros registros.']
            ]);
        }

        // Verificar contraseña
        if (!Hash::check($credentials['password'], $user->password)) {
            Log::warning('Intento de login con contraseña incorrecta', [
                'email' => $credentials['email'],
                'user_id' => $user->id,
                'ip' => $request->ip()
            ]);

            throw ValidationException::withMessages([
                'password' => ['Las credenciales proporcionadas no coinciden con nuestros registros.']
            ]);
        }

        // Verificar estado del usuario
        if ($user->estado !== 'ACTIVO') {
            Log::warning('Intento de login con usuario inactivo', [
                'email' => $credentials['email'],
                'user_id' => $user->id,
                'estado' => $user->estado
            ]);

            throw ValidationException::withMessages([
                'email' => ['Su cuenta está desactivada. Contacte al administrador.']
            ]);
        }

        // Autenticar usando Auth facade
        if (Auth::attempt(['email' => $credentials['email'], 'password' => $credentials['password']], $credentials['remember'] ?? false)) {
            // Regenerar sesión para prevenir session fixation
            $request->session()->regenerate();

            Log::info('Login exitoso', [
                'user_id' => $user->id,
                'email' => $user->email,
                'user_type' => $user->userType->name,
                'ip' => $request->ip()
            ]);

            return [
                'success' => true,
                'user' => $user,
                'redirect_url' => $this->getRedirectUrlByRole($user)
            ];
        }

        throw ValidationException::withMessages([
            'email' => ['Error interno de autenticación.']
        ]);
    }

    /**
     * Cerrar sesión del usuario
     *
     * @param Request $request
     * @return void
     */
    public function logout(Request $request): void
    {
        $user = Auth::user();
        
        if ($user) {
            Log::info('Logout exitoso', [
                'user_id' => $user->id,
                'email' => $user->email,
                'ip' => $request->ip()
            ]);
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
    }

    /**
     * Registrar nuevo usuario
     *
     * @param array $userData
     * @return User
     */
    public function register(array $userData): User
    {
        // Verificar que el email no existe
        if ($this->userRepository->emailExists($userData['email'])) {
            throw ValidationException::withMessages([
                'email' => ['El email ya está en uso.']
            ]);
        }

        $user = $this->userRepository->create($userData);

        Log::info('Usuario registrado exitosamente', [
            'user_id' => $user->id,
            'email' => $user->email,
            'user_type' => $user->userType->name
        ]);

        return $user;
    }

    /**
     * Obtener URL de redirección según el rol del usuario
     *
     * @param User $user
     * @return string
     */
    public function getRedirectUrlByRole(User $user): string
    {
        return match($user->userType->name) {
            'Admin' => route('dashboard.admin'),
            'Profesor' => route('dashboard.profesor'),
            'Staff' => route('dashboard.staff'),
            default => route('dashboard')
        };
    }

    /**
     * Verificar si el usuario tiene un rol específico
     *
     * @param User $user
     * @param string $role
     * @return bool
     */
    public function hasRole(User $user, string $role): bool
    {
        return $user->userType->name === $role;
    }

    /**
     * Verificar si el usuario puede acceder a un recurso
     *
     * @param User $user
     * @param array $allowedRoles
     * @return bool
     */
    public function canAccess(User $user, array $allowedRoles): bool
    {
        return in_array($user->userType->name, $allowedRoles);
    }

    /**
     * Cambiar contraseña del usuario autenticado
     *
     * @param string $currentPassword
     * @param string $newPassword
     * @param User $user
     * @return bool
     * @throws ValidationException
     */
    public function changePassword(string $currentPassword, string $newPassword, User $user): bool
    {
        // Verificar contraseña actual
        if (!Hash::check($currentPassword, $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => ['La contraseña actual es incorrecta.']
            ]);
        }

        $success = $this->userRepository->changePassword($user, $newPassword);

        if ($success) {
            Log::info('Contraseña cambiada exitosamente', [
                'user_id' => $user->id,
                'email' => $user->email
            ]);
        }

        return $success;
    }

    /**
     * Actualizar perfil del usuario
     *
     * @param User $user
     * @param array $data
     * @return User
     */
    public function updateProfile(User $user, array $data): User
    {
        // Verificar email si se está cambiando
        if (isset($data['email']) && $data['email'] !== $user->email) {
            if ($this->userRepository->emailExists($data['email'], $user->id)) {
                throw ValidationException::withMessages([
                    'email' => ['El email ya está en uso.']
                ]);
            }
        }

        $updatedUser = $this->userRepository->update($user, $data);

        Log::info('Perfil actualizado exitosamente', [
            'user_id' => $user->id,
            'email' => $user->email,
            'campos_actualizados' => array_keys($data)
        ]);

        return $updatedUser;
    }

    /**
     * Verificar si el usuario está autenticado
     *
     * @return bool
     */
    public function isAuthenticated(): bool
    {
        return Auth::check();
    }

    /**
     * Obtener usuario autenticado
     *
     * @return User|null
     */
    public function getAuthenticatedUser(): ?User
    {
        return Auth::user();
    }
}