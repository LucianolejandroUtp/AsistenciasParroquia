@extends('layouts.app')

@section('title', 'Códigos QR - Sistema de Asistencias')

@section('page-title', 'Códigos QR de Estudiantes')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('students.index') }}">Estudiantes</a></li>
    <li class="breadcrumb-item active">Códigos QR</li>
@endsection

@section('additional-css')
<style>
.qr-code-display {
    min-height: 200px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #f8f9fa;
    border: 2px dashed #dee2e6;
    border-radius: 8px;
}

.qr-card {
    transition: transform 0.2s ease-in-out;
}

.qr-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

@media print {
    .no-print { display: none !important; }
    .qr-card { break-inside: avoid; }
}
</style>
@endsection

@section('content')
<!-- Stats de QR -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col">
                        <span class="h2 mb-0">{{ $qrStats->total_codes }}</span>
                        <p class="small text-muted mb-0">Códigos QR Total</p>
                    </div>
                    <div class="col-auto">
                        <span class="fe fe-qr-code fe-32 text-primary"></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col">
                        <span class="h2 mb-0">{{ $qrStats->active_codes }}</span>
                        <p class="small text-muted mb-0">Códigos Activos</p>
                    </div>
                    <div class="col-auto">
                        <span class="fe fe-check-circle fe-32 text-success"></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col">
                        <span class="h2 mb-0">{{ $qrStats->total_scans_today }}</span>
                        <p class="small text-muted mb-0">Escaneos Hoy</p>
                    </div>
                    <div class="col-auto">
                        <span class="fe fe-scan fe-32 text-warning"></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col">
                        <span class="h6 mb-0">{{ \Carbon\Carbon::parse($qrStats->last_generated)->format('d/m/Y') }}</span>
                        <p class="small text-muted mb-0">Última Generación</p>
                    </div>
                    <div class="col-auto">
                        <span class="fe fe-calendar fe-32 text-info"></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filtros y Acciones -->
<div class="row mb-3">
    <div class="col-md-4">
        <div class="form-group">
            <div class="input-group">
                <input type="text" class="form-control" placeholder="Buscar código QR...">
                <div class="input-group-append">
                    <button class="btn btn-outline-secondary" type="button">
                        <span class="fe fe-search"></span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <select class="form-control">
            <option value="">Todos los grupos</option>
            @php
                $groups = App\Models\Group::orderBy('name')->get();
            @endphp
            @foreach($groups as $group)
                <option value="{{ $group->id }}">{{ $group->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-6 text-right">
        <button class="btn btn-success" type="button">
            <span class="fe fe-refresh-cw fe-12 mr-2"></span>Regenerar Todos
        </button>
        <button class="btn btn-primary" type="button">
            <span class="fe fe-download fe-12 mr-2"></span>Descargar PDF
        </button>
        <button class="btn btn-outline-primary" type="button" onclick="window.print()">
            <span class="fe fe-printer fe-12 mr-2"></span>Imprimir
        </button>
    </div>
</div>

<!-- Modo de Vista -->
<div class="row mb-3 no-print">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col">
                        <h6 class="mb-0">Modo de Vista</h6>
                    </div>
                    <div class="col-auto">
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-outline-primary active" id="view-grid">
                                <span class="fe fe-grid"></span> Cuadrícula
                            </button>
                            <button type="button" class="btn btn-outline-primary" id="view-list">
                                <span class="fe fe-list"></span> Lista
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Vista en Cuadrícula (por defecto) -->
<div id="grid-view">
    <div class="row">
        @foreach($qrCodes as $qrCode)
        <div class="col-md-4 col-lg-3 mb-4">
            <div class="card qr-card">
                <div class="card-header text-center">
                    <h6 class="mb-0">{{ $qrCode->full_name }}</h6>
                    <small class="text-muted">{{ $qrCode->group_name }}</small>
                </div>
                <div class="card-body text-center">
                    <!-- Placeholder para QR Code -->
                    <div class="qr-code-display mb-3">
                        <div class="text-center">
                            <div class="mb-2" style="font-size: 64px; color: #dee2e6;">
                                <span class="fe fe-qr-code"></span>
                            </div>
                            <small class="text-muted">QR Code Placeholder</small>
                        </div>
                    </div>
                    
                    <div class="mb-2">
                        <code class="small">{{ $qrCode->qr_code }}</code>
                    </div>
                    
                    <div class="row text-center">
                        <div class="col-6">
                            <small class="text-muted">Escaneos</small>
                            <div class="font-weight-bold">{{ $qrCode->total_scans }}</div>
                        </div>
                        <div class="col-6">
                            <small class="text-muted">Último</small>
                            <div class="small">{{ \Carbon\Carbon::parse($qrCode->last_scanned)->format('d/m') }}</div>
                        </div>
                    </div>
                </div>
                <div class="card-footer text-center no-print">
                    <div class="btn-group" role="group">
                        <button class="btn btn-sm btn-outline-primary" title="Descargar">
                            <span class="fe fe-download fe-12"></span>
                        </button>
                        <button class="btn btn-sm btn-outline-secondary" title="Regenerar">
                            <span class="fe fe-refresh-cw fe-12"></span>
                        </button>
                        <button class="btn btn-sm btn-outline-info" title="Imprimir Individual">
                            <span class="fe fe-printer fe-12"></span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
        
        <!-- Más códigos QR (indicador) -->
        <div class="col-md-4 col-lg-3 mb-4">
            <div class="card border-dashed text-center" style="border-style: dashed; border-color: #dee2e6;">
                <div class="card-body d-flex flex-column justify-content-center" style="min-height: 300px;">
                    <span class="fe fe-plus fe-32 text-muted mb-2"></span>
                    <h6 class="text-muted">Ver Más Códigos</h6>
                    <small class="text-muted">+75 códigos adicionales</small>
                    <button class="btn btn-outline-primary btn-sm mt-2">
                        Cargar Más
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Vista en Lista (oculta por defecto) -->
<div id="list-view" style="display: none;">
    <div class="card">
        <div class="card-header">
            <h4 class="card-header-title">Lista de Códigos QR</h4>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-borderless table-hover">
                    <thead>
                        <tr>
                            <th>Estudiante</th>
                            <th>Grupo</th>
                            <th>Código QR</th>
                            <th>Total Escaneos</th>
                            <th>Último Escaneo</th>
                            <th class="text-right no-print">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($qrCodes as $qrCode)
                        <tr>
                            <td>
                                <div class="media align-items-center">
                                    <div class="avatar avatar-sm mr-3">
                                        <span class="avatar-title rounded-circle bg-soft-primary text-primary">
                                            {{ substr($qrCode->full_name, 0, 1) }}
                                        </span>
                                    </div>
                                    <div class="media-body">
                                        <strong>{{ $qrCode->full_name }}</strong>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge badge-{{ str_contains($qrCode->group_name, 'A') ? 'primary' : 'info' }}">
                                    {{ $qrCode->group_name }}
                                </span>
                            </td>
                            <td>
                                <code class="small">{{ $qrCode->qr_code }}</code>
                            </td>
                            <td>
                                <span class="badge badge-success">{{ $qrCode->total_scans }}</span>
                            </td>
                            <td>
                                <small>{{ \Carbon\Carbon::parse($qrCode->last_scanned)->format('d/m/Y H:i') }}</small>
                            </td>
                            <td class="text-right no-print">
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-white" data-toggle="dropdown">
                                        <span class="fe fe-more-vertical fe-12"></span>
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-right">
                                        <a class="dropdown-item" href="#">
                                            <span class="fe fe-eye fe-12 mr-2"></span>Ver QR Grande
                                        </a>
                                        <a class="dropdown-item" href="#">
                                            <span class="fe fe-download fe-12 mr-2"></span>Descargar
                                        </a>
                                        <a class="dropdown-item" href="#">
                                            <span class="fe fe-refresh-cw fe-12 mr-2"></span>Regenerar
                                        </a>
                                        <div class="dropdown-divider"></div>
                                        <a class="dropdown-item" href="#">
                                            <span class="fe fe-bar-chart-2 fe-12 mr-2"></span>Historial
                                        </a>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Instrucciones de Uso -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card bg-light">
            <div class="card-body">
                <h6 class="card-title">
                    <i class="fe fe-info text-primary mr-2"></i>
                    Instrucciones de Uso de Códigos QR
                </h6>
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="small font-weight-bold">Para Catequistas:</h6>
                        <ul class="small text-muted mb-0">
                            <li>Cada estudiante tiene un código QR único</li>
                            <li>Use la cámara del dispositivo para escanear</li>
                            <li>El código se registra automáticamente en la sesión activa</li>
                            <li>Verifique el nombre del estudiante tras el escaneo</li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h6 class="small font-weight-bold">Para Administradores:</h6>
                        <ul class="small text-muted mb-0">
                            <li>Los códigos se generan automáticamente</li>
                            <li>Pueden regenerarse si es necesario</li>
                            <li>Imprima códigos para estudiantes que olviden el suyo</li>
                            <li>Monitoree el historial de escaneos</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('additional-js')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Alternar entre vista de cuadrícula y lista
    const viewGrid = document.getElementById('view-grid');
    const viewList = document.getElementById('view-list');
    const gridView = document.getElementById('grid-view');
    const listView = document.getElementById('list-view');
    
    viewGrid.addEventListener('click', function() {
        gridView.style.display = 'block';
        listView.style.display = 'none';
        viewGrid.classList.add('active');
        viewList.classList.remove('active');
    });
    
    viewList.addEventListener('click', function() {
        gridView.style.display = 'none';
        listView.style.display = 'block';
        viewList.classList.add('active');
        viewGrid.classList.remove('active');
    });
    
    // Funcionalidad de búsqueda
    const searchInput = document.querySelector('input[placeholder="Buscar código QR..."]');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            console.log('Buscando QR:', this.value);
        });
    }
    
    // Simulación de descarga de QR
    document.querySelectorAll('button[title="Descargar"]').forEach(btn => {
        btn.addEventListener('click', function() {
            alert('Funcionalidad de descarga de QR (próximamente)');
        });
    });
    
    // Simulación de regeneración de QR
    document.querySelectorAll('button[title="Regenerar"]').forEach(btn => {
        btn.addEventListener('click', function() {
            if (confirm('¿Está seguro de regenerar este código QR?')) {
                alert('QR regenerado exitosamente');
            }
        });
    });
});
</script>
@endsection