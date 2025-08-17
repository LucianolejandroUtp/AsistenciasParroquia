@extends('layouts.app')

@section('title', 'Configuración del Sistema - Sistema de Asistencias')

@section('page-title', 'Configuración del Sistema')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item">Administración</li>
    <li class="breadcrumb-item active">Configuración</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="fe fe-settings fe-64 text-muted mb-3"></i>
                <h4 class="text-muted">Configuración del Sistema</h4>
                <p class="text-muted">Esta sección está en desarrollo como parte del enfoque UI-first.</p>
                <small class="text-muted">Próximamente: Configuraciones generales, parámetros del sistema y opciones avanzadas.</small>
            </div>
        </div>
    </div>
</div>
@endsection