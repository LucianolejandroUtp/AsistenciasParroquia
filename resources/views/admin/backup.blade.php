@extends('layouts.app')

@section('title', 'Backup del Sistema - Sistema de Asistencias')

@section('page-title', 'Backup del Sistema')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item">Administración</li>
    <li class="breadcrumb-item active">Backup</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="fe fe-hard-drive fe-64 text-muted mb-3"></i>
                <h4 class="text-muted">Backup del Sistema</h4>
                <p class="text-muted">Esta sección está en desarrollo como parte del enfoque UI-first.</p>
                <small class="text-muted">Próximamente: Gestión de respaldos automáticos y manuales del sistema.</small>
            </div>
        </div>
    </div>
</div>
@endsection