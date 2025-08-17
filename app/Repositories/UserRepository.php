<?php

namespace App\Repositories;

use App\Models\User;
use App\Models\UserType;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * UserRepository - Manejo de acceso a datos de usuarios
 * 
 * Abstrae las consultas complejas de la base de datos y proporciona
 * una interfaz limpia para el acceso a datos de usuarios.
 */
class UserRepository
{
    /**
     * Buscar usuario por email para autenticación
     *
     * @param string $email
     * @return User|null
     */
    public function findByEmail(string $email): ?User
    {
        return User::where('email', $email)
            ->where('estado', 'ACTIVO')
            ->with('userType')
            ->first();
    }

    /**
     * Buscar usuario por ID
     *
     * @param int $id
     * @return User|null
     */
    public function findById(int $id): ?User
    {
        return User::where('id', $id)
            ->where('estado', 'ACTIVO')
            ->with('userType')
            ->first();
    }

    /**
     * Crear nuevo usuario
     *
     * @param array $userData
     * @return User
     */
    public function create(array $userData): User
    {
        // Validar que el user_type existe
        $userType = UserType::find($userData['user_type_id']);
        if (!$userType) {
            throw new ModelNotFoundException('Tipo de usuario no encontrado');
        }

        return User::create([
            'name' => $userData['name'],
            'email' => $userData['email'],
            'password' => bcrypt($userData['password']),
            'user_type_id' => $userData['user_type_id'],
            'estado' => 'ACTIVO'
        ]);
    }

    /**
     * Actualizar información del usuario
     *
     * @param User $user
     * @param array $data
     * @return User
     */
    public function update(User $user, array $data): User
    {
        $user->update($data);
        return $user->fresh(['userType']);
    }

    /**
     * Obtener todos los usuarios activos por tipo
     *
     * @param string $userTypeName
     * @return Collection
     */
    public function getByUserType(string $userTypeName): Collection
    {
        return User::whereHas('userType', function($query) use ($userTypeName) {
            $query->where('name', $userTypeName);
        })
        ->where('estado', 'ACTIVO')
        ->with('userType')
        ->get();
    }

    /**
     * Verificar si email existe
     *
     * @param string $email
     * @param int|null $excludeUserId Para actualizaciones
     * @return bool
     */
    public function emailExists(string $email, ?int $excludeUserId = null): bool
    {
        $query = User::where('email', $email);
        
        if ($excludeUserId) {
            $query->where('id', '!=', $excludeUserId);
        }

        return $query->exists();
    }

    /**
     * Cambiar contraseña del usuario
     *
     * @param User $user
     * @param string $newPassword
     * @return bool
     */
    public function changePassword(User $user, string $newPassword): bool
    {
        return $user->update([
            'password' => bcrypt($newPassword)
        ]);
    }

    /**
     * Desactivar usuario (soft delete)
     *
     * @param User $user
     * @return bool
     */
    public function deactivate(User $user): bool
    {
        return $user->update(['estado' => 'INACTIVO']);
    }

    /**
     * Obtener estadísticas de usuarios por tipo
     *
     * @return array
     */
    public function getUserStats(): array
    {
        return User::join('user_types', 'users.user_type_id', '=', 'user_types.id')
            ->where('users.estado', 'ACTIVO')
            ->selectRaw('user_types.name as tipo, COUNT(*) as cantidad')
            ->groupBy('user_types.name')
            ->pluck('cantidad', 'tipo')
            ->toArray();
    }
}