<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\AttendanceSession;
use App\Policies\AttendanceSessionPolicy;
use App\Models\Student;
use App\Policies\StudentPolicy;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Registrar políticas de autorización
        Gate::policy(AttendanceSession::class, AttendanceSessionPolicy::class);
    Gate::policy(Student::class, StudentPolicy::class);
    }
}
