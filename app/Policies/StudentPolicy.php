<?php

namespace App\Policies;

use App\Models\Student;
use App\Models\User;

class StudentPolicy
{
    /**
     * Determine whether the user can view any students.
     */
    public function viewAny(User $user): bool
    {
        // Admin, Catequista y Apoyo pueden ver la lista
        return in_array($user->userType->name, ['Admin', 'Catequista', 'Apoyo']);
    }

    /**
     * Determine whether the user can view the student or its QR.
     */
    public function view(User $user, Student $student): bool
    {
        // Admin, Catequista y Apoyo pueden ver detalles y QR
        return in_array($user->userType->name, ['Admin', 'Catequista', 'Apoyo']);
    }
}
