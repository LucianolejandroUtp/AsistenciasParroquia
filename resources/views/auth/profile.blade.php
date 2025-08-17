@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row">
        <div class="col-12">
            <div class="page-header d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="page-header-title">
                        <i class="fe fe-user me-2"></i>Mi Perfil
                    </h1>
                    <p class="page-header-text mb-0">
                        Gestiona tu información personal y configuración de cuenta
                    </p>
                </div>
                <div>
                    <a href="{{ route('profile.edit') }}" class="btn btn-primary">
                        <i class="fe fe-edit-2 me-1"></i>
                        Editar Perfil
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Profile Content -->
    <div class="row">
        <!-- Profile Information -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">
                        <i class="fe fe-info me-2"></i>Información Personal
                    </h4>
                </div>
                <div class="card-body">
                    <!-- Success/Error Messages -->
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fe fe-check-circle me-2"></i>{{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fe fe-alert-circle me-2"></i>{{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label text-muted">Nombre de Usuario</label>
                                <p class="form-control-plaintext">
                                    <i class="fe fe-at-sign me-2 text-primary"></i>{{ $user->username }}
                                </p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label text-muted">Tipo de Usuario</label>
                                <p class="form-control-plaintext">
                                    @if($user->userType->name === 'Admin')
                                        <span class="badge bg-danger">
                                            <i class="fe fe-shield me-1"></i>Administrador
                                        </span>
                                    @elseif($user->userType->name === 'Profesor')
                                        <span class="badge bg-primary">
                                            <i class="fe fe-book-open me-1"></i>Catequista
                                        </span>
                                    @elseif($user->userType->name === 'Staff')
                                        <span class="badge bg-info">
                                            <i class="fe fe-help-circle me-1"></i>Personal de Apoyo
                                        </span>
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label text-muted">Nombre</label>
                                <p class="form-control-plaintext">
                                    <i class="fe fe-user me-2 text-success"></i>{{ $user->first_name }}
                                </p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label text-muted">Apellido</label>
                                <p class="form-control-plaintext">
                                    <i class="fe fe-user me-2 text-success"></i>{{ $user->last_name }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label text-muted">Email</label>
                                <p class="form-control-plaintext">
                                    @if($user->email)
                                        <i class="fe fe-mail me-2 text-info"></i>{{ $user->email }}
                                    @else
                                        <i class="fe fe-mail me-2 text-muted"></i>
                                        <span class="text-muted">No especificado</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label text-muted">Estado</label>
                                <p class="form-control-plaintext">
                                    @if($user->estado === 'ACTIVO')
                                        <span class="badge bg-success">
                                            <i class="fe fe-check-circle me-1"></i>Activo
                                        </span>
                                    @else
                                        <span class="badge bg-secondary">
                                            <i class="fe fe-x-circle me-1"></i>{{ $user->estado }}
                                        </span>
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label text-muted">Fecha de Registro</label>
                                <p class="form-control-plaintext">
                                    <i class="fe fe-calendar me-2 text-warning"></i>
                                    {{ $user->created_at ? $user->created_at->format('d/m/Y H:i') : 'No disponible' }}
                                </p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label text-muted">Última Actualización</label>
                                <p class="form-control-plaintext">
                                    <i class="fe fe-clock me-2 text-warning"></i>
                                    {{ $user->updated_at ? $user->updated_at->format('d/m/Y H:i') : 'No disponible' }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">
                        <i class="fe fe-settings me-2"></i>Acciones Rápidas
                    </h4>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('profile.edit') }}" class="btn btn-outline-primary">
                            <i class="fe fe-edit-2 me-2"></i>
                            Editar Información
                        </a>
                        
                        <a href="{{ route('profile.change-password') }}" class="btn btn-outline-warning">
                            <i class="fe fe-lock me-2"></i>
                            Cambiar Contraseña
                        </a>
                        
                        <hr class="my-3">
                        
                        @if($user->userType->name === 'Admin')
                            <a href="{{ route('dashboard.admin') }}" class="btn btn-outline-danger">
                                <i class="fe fe-shield me-2"></i>
                                Panel de Administración
                            </a>
                        @elseif($user->userType->name === 'Profesor')
                            <a href="{{ route('dashboard.profesor') }}" class="btn btn-outline-primary">
                                <i class="fe fe-book-open me-2"></i>
                                Panel de Catequista
                            </a>
                        @elseif($user->userType->name === 'Staff')
                            <a href="{{ route('dashboard.staff') }}" class="btn btn-outline-info">
                                <i class="fe fe-help-circle me-2"></i>
                                Panel de Personal
                            </a>
                        @endif
                        
                        <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">
                            <i class="fe fe-home me-2"></i>
                            Ir al Dashboard
                        </a>
                    </div>
                </div>
            </div>

            <!-- Profile Stats -->
            <div class="card mt-4">
                <div class="card-header">
                    <h4 class="card-title">
                        <i class="fe fe-activity me-2"></i>Estadísticas
                    </h4>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6">
                            <div class="mb-3">
                                <h3 class="text-primary">
                                    {{ $user->created_at ? $user->created_at->diffInDays(now()) : 0 }}
                                </h3>
                                <p class="text-muted small mb-0">Días en el sistema</p>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="mb-3">
                                <h3 class="text-success">
                                    <i class="fe fe-check-circle"></i>
                                </h3>
                                <p class="text-muted small mb-0">Cuenta verificada</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Auto-hide alerts after 5 seconds
        setTimeout(function() {
            $('.alert').fadeOut('slow');
        }, 5000);
    });
</script>
@endsection