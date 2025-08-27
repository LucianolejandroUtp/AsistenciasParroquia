@extends('layouts.app')

@section('title', 'Escáner QR - Asistencias')

@section('breadcrumb')
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('attendances.history') }}">Asistencias</a></li>
        <li class="breadcrumb-item active" aria-current="page">Escáner QR</li>
    </ol>
</nav>
@endsection

@section('content')
<div class="container-fluid">

    <!-- Alert informativo -->
    <div class="alert alert-warning" role="alert">
        <div class="row align-items-center">
            <div class="col">
                <i class="fe fe-alert-triangle me-2"></i>
                <strong>No hay sesiones activas</strong> - Para usar el escáner QR necesita crear al menos una sesión de catequesis.
            </div>
            <div class="col-auto">
                <a href="{{ route('sessions.create') }}" class="btn btn-warning btn-sm">
                    <i class="fe fe-plus me-1"></i>Crear Nueva Sesión
                </a>
            </div>
        </div>
    </div>

    <!-- Información principal -->
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body text-center py-5">
                    <div class="mb-4">
                        <i class="fe fe-calendar-x text-muted" style="font-size: 4rem;"></i>
                    </div>
                    
                    <h3 class="text-muted mb-3">No hay sesiones de catequesis activas</h3>
                    
                    <p class="text-muted mb-4">
                        Para comenzar a registrar asistencias mediante códigos QR, primero debe crear una sesión de catequesis activa.
                    </p>
                    
                    <div class="row justify-content-center">
                        <div class="col-md-8">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h5 class="card-title">¿Qué es una sesión de catequesis?</h5>
                                    <p class="card-text small text-muted">
                                        Una sesión representa una clase o encuentro específico donde se registrará la asistencia de los estudiantes. 
                                        Cada sesión tiene fecha, hora y puede incluir notas sobre el contenido tratado.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-4">
                        <a href="{{ route('sessions.create') }}" class="btn btn-primary btn-lg me-3">
                            <i class="fe fe-plus me-2"></i>Crear Primera Sesión
                        </a>
                        <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">
                            <i class="fe fe-arrow-left me-2"></i>Volver al Dashboard
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Información adicional -->
    <div class="row mt-4">
        <div class="col-lg-4">
            <div class="card">
                <div class="card-body text-center">
                    <i class="fe fe-calendar text-primary mb-3" style="font-size: 2rem;"></i>
                    <h5>Programar Sesión</h5>
                    <p class="text-muted small">Define fecha, hora y contenido de la catequesis</p>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card">
                <div class="card-body text-center">
                    <i class="fe fe-camera text-success mb-3" style="font-size: 2rem;"></i>
                    <h5>Escanear QR</h5>
                    <p class="text-muted small">Registro rápido de asistencia con códigos QR</p>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card">
                <div class="card-body text-center">
                    <i class="fe fe-bar-chart text-info mb-3" style="font-size: 2rem;"></i>
                    <h5>Ver Estadísticas</h5>
                    <p class="text-muted small">Seguimiento del progreso y asistencia</p>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@push('styles')
<style>
    .card {
        border: none;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    }
    
    .fe {
        transition: all 0.3s ease;
    }
    
    .card:hover .fe {
        transform: scale(1.1);
    }
</style>
@endpush