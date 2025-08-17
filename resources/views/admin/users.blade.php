@extends('layouts.app')

@section('title', 'Gestión de Usuarios - Sistema de Asistencias')

@section('page-title', 'Gestión de Usuarios')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item">Administración</li>
    <li class="breadcrumb-item active">Usuarios</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="fe fe-users fe-64 text-muted mb-3"></i>
                <h4 class="text-muted">Gestión de Usuarios</h4>
                <p class="text-muted">Esta sección está en desarrollo como parte del enfoque UI-first.</p>
                <small class="text-muted">Próximamente: CRUD de usuarios, roles, permisos y gestión de cuentas.</small>
            </div>
        </div>
    </div>
</div>
@endsection