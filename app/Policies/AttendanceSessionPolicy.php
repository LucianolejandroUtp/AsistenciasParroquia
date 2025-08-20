<?php

namespace App\Policies;

use App\Models\AttendanceSession;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class AttendanceSessionPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // Admin, Catequista y Apoyo pueden ver la lista de sesiones
        return in_array($user->userType->name, ['Admin', 'Catequista', 'Apoyo']);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, AttendanceSession $attendanceSession): bool
    {
        // Admin, Catequista y Apoyo pueden ver detalles de sesiones
        return in_array($user->userType->name, ['Admin', 'Catequista', 'Apoyo']);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Solo Admin y Catequista pueden crear sesiones
        return in_array($user->userType->name, ['Admin', 'Catequista']);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, AttendanceSession $attendanceSession): bool
    {
        // Solo Admin y Catequista pueden editar sesiones
        return in_array($user->userType->name, ['Admin', 'Catequista']);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, AttendanceSession $attendanceSession): bool
    {
        // Solo Admin puede eliminar sesiones
        return $user->userType->name === 'Admin';
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, AttendanceSession $attendanceSession): bool
    {
        // Solo Admin puede restaurar sesiones
        return $user->userType->name === 'Admin';
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, AttendanceSession $attendanceSession): bool
    {
        // Solo Admin puede eliminar permanentemente
        return $user->userType->name === 'Admin';
    }

    /**
     * Determine whether the user can close the session.
     */
    public function close(User $user, AttendanceSession $attendanceSession): bool
    {
        // Solo Admin y Catequista pueden cerrar sesiones
        return in_array($user->userType->name, ['Admin', 'Catequista']) && $attendanceSession->canBeClosed();
    }

    /**
     * Determine whether the user can reopen the session.
     */
    public function reopen(User $user, AttendanceSession $attendanceSession): bool
    {
        // Solo Admin y Catequista pueden reabrir sesiones
        return in_array($user->userType->name, ['Admin', 'Catequista']) && $attendanceSession->canBeReopened();
    }
}
