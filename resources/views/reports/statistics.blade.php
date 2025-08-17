@extends('layouts.app')

@section('title', 'Estadísticas - Sistema de Asistencias')

@section('page-title', 'Estadísticas y Reportes')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item">Reportes</li>
    <li class="breadcrumb-item active">Estadísticas</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="fe fe-bar-chart-2 fe-64 text-muted mb-3"></i>
                <h4 class="text-muted">Módulo de Estadísticas</h4>
                <p class="text-muted">Esta sección está en desarrollo como parte del enfoque UI-first.</p>
                <small class="text-muted">Próximamente: Dashboard de estadísticas, gráficos interactivos y análisis de tendencias.</small>
            </div>
        </div>
    </div>
</div>
@endsection