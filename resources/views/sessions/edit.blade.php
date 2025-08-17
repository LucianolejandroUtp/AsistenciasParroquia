@extends('layouts.app')

@section('title', 'Editar Sesión de Catequesis')

@section('breadcrumbs')
<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-12">
                <div class="page-header-title">
                    <h2 class="mb-0">Editar Sesión</h2>
                </div>
                <ul class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('sessions.index') }}">Sesiones</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('sessions.show', $session) }}">{{ $session->display_title }}</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Editar</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fe fe-edit me-2"></i>Editar Sesión de Catequesis
                </h5>
                <span class="badge bg-primary">{{ $session->date->format('d/m/Y') }}</span>
            </div>

            <form action="{{ route('sessions.update', $session) }}" method="POST" id="sessionForm">
                @csrf
                @method('PUT')
                <div class="card-body">
                    @if($session->attendances->count() > 0)
                    <div class="alert alert-info">
                        <i class="fe fe-info me-2"></i>
                        <strong>Nota:</strong> Esta sesión ya tiene {{ $session->attendances->count() }} 
                        asistencias registradas. Los cambios en la fecha podrían afectar los reportes.
                    </div>
                    @endif

                    <!-- Fecha -->
                    <div class="mb-3">
                        <label for="date" class="form-label">
                            Fecha de la Sesión <span class="text-danger">*</span>
                        </label>
                        <input type="date" 
                               class="form-control @error('date') is-invalid @enderror" 
                               id="date" 
                               name="date" 
                               value="{{ old('date', $session->date->format('Y-m-d')) }}"
                               required>
                        @error('date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        @if($session->isPast() && !$session->isToday())
                        <div class="form-text text-warning">
                            <i class="fe fe-alert-triangle me-1"></i>
                            Esta es una sesión pasada. Ten cuidado al modificar la fecha.
                        </div>
                        @endif
                    </div>

                    <!-- Hora -->
                    <div class="mb-3">
                        <label for="time" class="form-label">
                            Hora (Opcional)
                        </label>
                        <input type="time" 
                               class="form-control @error('time') is-invalid @enderror" 
                               id="time" 
                               name="time" 
                               value="{{ old('time', $session->time?->format('H:i')) }}">
                        @error('time')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Título -->
                    <div class="mb-3">
                        <label for="title" class="form-label">
                            Título de la Sesión (Opcional)
                        </label>
                        <input type="text" 
                               class="form-control @error('title') is-invalid @enderror" 
                               id="title" 
                               name="title" 
                               value="{{ old('title', $session->title) }}"
                               placeholder="Ej: Preparación para Primera Comunión - Tema 5">
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Vista Previa del Título -->
                    <div class="mb-3">
                        <div class="card bg-light">
                            <div class="card-body py-2">
                                <small class="text-muted">Vista previa del título:</small>
                                <div id="titlePreview" class="fw-semibold">
                                    {{ $session->display_title }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Observaciones -->
                    <div class="mb-3">
                        <label for="notes" class="form-label">
                            Observaciones (Opcional)
                        </label>
                        <textarea class="form-control @error('notes') is-invalid @enderror" 
                                  id="notes" 
                                  name="notes" 
                                  rows="4" 
                                  placeholder="Notas adicionales sobre la sesión, temas a tratar, materiales necesarios, etc.">{{ old('notes', $session->notes) }}</textarea>
                        @error('notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">
                            <span id="notesCount">{{ strlen($session->notes ?? '') }}</span>/1000 caracteres
                        </div>
                    </div>

                    <!-- Información de Modificación -->
                    <div class="alert alert-light">
                        <i class="fe fe-clock me-2"></i>
                        <strong>Historial:</strong> Sesión creada el {{ $session->created_at->format('d/m/Y H:i') }} 
                        por {{ $session->creator->names }}.
                        @if($session->updated_at != $session->created_at)
                            Última modificación: {{ $session->updated_at->format('d/m/Y H:i') }}.
                        @endif
                    </div>
                </div>

                <div class="card-footer d-flex justify-content-between">
                    <div>
                        <a href="{{ route('sessions.show', $session) }}" class="btn btn-outline-secondary">
                            <i class="fe fe-arrow-left me-1"></i>Cancelar
                        </a>
                        <a href="{{ route('sessions.index') }}" class="btn btn-outline-primary ms-2">
                            <i class="fe fe-list me-1"></i>Ver Todas
                        </a>
                    </div>
                    <button type="submit" class="btn btn-warning">
                        <i class="fe fe-save me-1"></i>Actualizar Sesión
                    </button>
                </div>
            </form>
        </div>

        <!-- Información Adicional -->
        @if($session->attendances->count() > 0)
        <div class="card mt-4">
            <div class="card-header">
                <h6 class="mb-0">
                    <i class="fe fe-users me-2"></i>Asistencias Registradas
                </h6>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    @php $stats = $session->attendance_stats @endphp
                    <div class="col-3">
                        <div class="h5 mb-0 text-success">{{ $stats['present'] }}</div>
                        <small class="text-muted">Presentes</small>
                    </div>
                    <div class="col-3">
                        <div class="h5 mb-0 text-warning">{{ $stats['late'] }}</div>
                        <small class="text-muted">Tardanzas</small>
                    </div>
                    <div class="col-3">
                        <div class="h5 mb-0 text-info">{{ $stats['justified'] }}</div>
                        <small class="text-muted">Justificados</small>
                    </div>
                    <div class="col-3">
                        <div class="h5 mb-0 text-danger">{{ $stats['absent'] }}</div>
                        <small class="text-muted">Ausentes</small>
                    </div>
                </div>
                <div class="mt-3">
                    <small class="text-muted">
                        <i class="fe fe-info me-1"></i>
                        Al modificar la fecha, considera cómo esto podría afectar los reportes de asistencia.
                    </small>
                </div>
            </div>
        </div>
        @endif

        <!-- Card de Ayuda -->
        <div class="card mt-4">
            <div class="card-header">
                <h6 class="mb-0">
                    <i class="fe fe-help-circle me-2"></i>Consideraciones al Editar
                </h6>
            </div>
            <div class="card-body">
                <ul class="list-unstyled mb-0">
                    <li class="mb-2">
                        <i class="fe fe-check-circle text-success me-2"></i>
                        Los cambios se aplicarán inmediatamente
                    </li>
                    <li class="mb-2">
                        <i class="fe fe-alert-triangle text-warning me-2"></i>
                        Modificar la fecha afectará el orden cronológico de las sesiones
                    </li>
                    <li class="mb-2">
                        <i class="fe fe-info text-info me-2"></i>
                        Las asistencias ya registradas se mantendrán vinculadas a esta sesión
                    </li>
                    <li class="mb-0">
                        <i class="fe fe-shield text-primary me-2"></i>
                        Solo usuarios autorizados pueden editar sesiones
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const dateInput = document.getElementById('date');
    const titleInput = document.getElementById('title');
    const titlePreview = document.getElementById('titlePreview');
    const notesTextarea = document.getElementById('notes');
    const notesCount = document.getElementById('notesCount');

    // Valores originales para detectar cambios
    const originalValues = {
        date: dateInput.value,
        title: titleInput.value,
        notes: notesTextarea.value
    };

    // Actualizar vista previa del título
    function updateTitlePreview() {
        const customTitle = titleInput.value.trim();
        const selectedDate = dateInput.value;
        
        if (customTitle) {
            titlePreview.textContent = customTitle;
        } else if (selectedDate) {
            const date = new Date(selectedDate + 'T00:00:00');
            const formattedDate = date.toLocaleDateString('es-ES', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric'
            });
            titlePreview.textContent = `Catequesis del ${formattedDate}`;
        }
    }

    // Contar caracteres en observaciones
    function updateNotesCount() {
        const count = notesTextarea.value.length;
        notesCount.textContent = count;
        
        if (count > 1000) {
            notesCount.classList.add('text-danger');
        } else if (count > 800) {
            notesCount.classList.add('text-warning');
            notesCount.classList.remove('text-danger');
        } else {
            notesCount.classList.remove('text-danger', 'text-warning');
        }
    }

    // Detectar cambios para mostrar indicador
    function detectChanges() {
        const hasChanges = 
            dateInput.value !== originalValues.date ||
            titleInput.value !== originalValues.title ||
            notesTextarea.value !== originalValues.notes;

        // Agregar indicador visual si hay cambios
        const submitBtn = document.querySelector('button[type="submit"]');
        if (hasChanges) {
            submitBtn.classList.add('btn-warning');
            submitBtn.classList.remove('btn-primary');
        } else {
            submitBtn.classList.add('btn-primary');
            submitBtn.classList.remove('btn-warning');
        }
    }

    // Event listeners
    dateInput.addEventListener('change', function() {
        updateTitlePreview();
        detectChanges();
    });
    
    titleInput.addEventListener('input', function() {
        updateTitlePreview();
        detectChanges();
    });
    
    notesTextarea.addEventListener('input', function() {
        updateNotesCount();
        detectChanges();
    });

    // Inicializar
    updateTitlePreview();
    updateNotesCount();

    // Confirmación antes de salir si hay cambios no guardados
    let formChanged = false;
    const formInputs = document.querySelectorAll('#sessionForm input, #sessionForm textarea');
    
    formInputs.forEach(input => {
        input.addEventListener('change', () => {
            formChanged = true;
        });
    });

    window.addEventListener('beforeunload', function(e) {
        if (formChanged) {
            e.preventDefault();
            e.returnValue = '';
        }
    });

    document.getElementById('sessionForm').addEventListener('submit', function() {
        formChanged = false;
    });
});
</script>
@endpush