<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;

// Ruta principal - redirigir al dashboard
Route::get('/', function () {
    return redirect()->route('dashboard');
});

// Dashboard principal
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

// API endpoints para estadísticas (AJAX)
Route::get('/api/dashboard/stats', [DashboardController::class, 'getStats'])->name('dashboard.stats');

// Rutas de autenticación (se implementarán en la fase siguiente)
Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::post('/logout', function () {
    // Implementar logout
    return redirect()->route('login');
})->name('logout');
