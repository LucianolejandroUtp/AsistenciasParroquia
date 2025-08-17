@extends('layouts.app')

@section('title', 'Exportar Datos - Sistema de Asistencias')

@section('page-title', 'Exportar Datos')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item">Reportes</li>
    <li class="breadcrumb-item active">Exportar</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="fe fe-download fe-64 text-muted mb-3"></i>
                <h4 class="text-muted">Exportación de Datos</h4>
                <p class="text-muted">Esta sección está en desarrollo como parte del enfoque UI-first.</p>
                <small class="text-muted">Próximamente: Exportación a Excel, PDF, y otros formatos de datos de asistencia.</small>
            </div>
        </div>
    </div>
</div>
@endsection