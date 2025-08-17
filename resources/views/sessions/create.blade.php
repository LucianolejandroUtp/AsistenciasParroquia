@extends('layouts.app')

@section('title', 'Crear Sesión de Catequesis')

@section('breadcrumbs')
<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-12">
                <div class="page-header-title">
                    <h2 class="mb-0">Crear Nueva Sesión</h2>
                </div>
                <ul class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('sessions.index') }}">Sesiones</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Crear</li>
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
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fe fe-plus-circle me-2"></i>Nueva Sesión de Catequesis
                </h5>
            </div>

            <form action="{{ route('sessions.store') }}" method="POST" id="sessionForm">
                @csrf
                <div class="card-body">
                    <!-- Fecha -->
                    <div class="mb-3">
                        <label for="date" class="form-label">
                            Fecha de la Sesión <span class="text-danger">*</span>
                        </label>
                        <input type="date" 
                               class="form-control @error('date') is-invalid @enderror" 
                               id="date" 
                               name="date" 
                               value="{{ old('date', isset($session) ? $session->date->format('Y-m-d') : now()->format('Y-m-d')) }}"
                               required>
                        @error('date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        @error('date_context')
                            <div class="form-text text-info">
                                <i class="fe fe-info me-1"></i>{{ $message }}
                            </div>
                        @enderror
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
                               value="{{ old('time', isset($session) ? $session->time?->format('H:i') : '') }}">
                        @error('time')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">
                            Si no especifica hora, se asumirá que es una sesión de día completo.
                        </div>
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
                               value="{{ old('title', isset($session) ? $session->title : '') }}"
                               placeholder="Ej: Preparación para Primera Comunión - Tema 5">
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">
                            Si no especifica título, se generará automáticamente basado en la fecha.
                        </div>
                    </div>

                    <!-- Vista Previa del Título -->
                    <div class="mb-3">
                        <div class="card bg-light">
                            <div class="card-body py-2">
                                <small class="text-muted">Vista previa del título:</small>
                                <div id="titlePreview" class="fw-semibold">
                                    Catequesis del {{ now()->format('d/m/Y') }}
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
                                  placeholder="Notas adicionales sobre la sesión, temas a tratar, materiales necesarios, etc.">{{ old('notes', isset($session) ? $session->notes : '') }}</textarea>
                        @error('notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">
                            <span id="notesCount">0</span>/1000 caracteres
                        </div>
                    </div>

                    <!-- Información Adicional -->
                    <div class="alert alert-info">
                        <i class="fe fe-info me-2"></i>
                        <strong>Recordatorio:</strong> Una vez creada la sesión, podrás registrar las asistencias de los estudiantes. 
                        Las sesiones pueden editarse hasta el día de la misma.
                    </div>
                </div>

                <div class="card-footer d-flex justify-content-between">
                    <a href="{{ route('sessions.index') }}" class="btn btn-outline-secondary">
                        <i class="fe fe-arrow-left me-1"></i>Cancelar
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fe fe-save me-1"></i>Crear Sesión
                    </button>
                </div>
            </form>
        </div>

        <!-- Card de Ayuda -->
        <div class="card mt-4">
            <div class="card-header">
                <h6 class="mb-0">
                    <i class="fe fe-help-circle me-2"></i>Consejos para Crear Sesiones
                </h6>
            </div>
            <div class="card-body">
                <ul class="list-unstyled mb-0">
                    <li class="mb-2">
                        <i class="fe fe-check-circle text-success me-2"></i>
                        Planifica las sesiones con anticipación para mejor organización
                    </li>
                    <li class="mb-2">
                        <i class="fe fe-check-circle text-success me-2"></i>
                        Incluye observaciones detalladas sobre los temas a tratar
                    </li>
                    <li class="mb-2">
                        <i class="fe fe-check-circle text-success me-2"></i>
                        Las sesiones duplicadas te ayudan a crear sesiones recurrentes
                    </li>
                    <li class="mb-0">
                        <i class="fe fe-check-circle text-success me-2"></i>
                        Puedes editar las sesiones hasta el día programado
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
        } else {
            const today = new Date();
            const formattedToday = today.toLocaleDateString('es-ES', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric'
            });
            titlePreview.textContent = `Catequesis del ${formattedToday}`;
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

    // Event listeners
    dateInput.addEventListener('change', updateTitlePreview);
    titleInput.addEventListener('input', updateTitlePreview);
    notesTextarea.addEventListener('input', updateNotesCount);

    // Inicializar
    updateTitlePreview();
    updateNotesCount();

    // Validación de fecha
    dateInput.addEventListener('change', function() {
        const selectedDate = new Date(this.value);
        const today = new Date();
        const minDate = new Date(today.getTime() - (7 * 24 * 60 * 60 * 1000)); // 7 días atrás
        const maxDate = new Date(today.getTime() + (6 * 30 * 24 * 60 * 60 * 1000)); // 6 meses adelante

        if (selectedDate < minDate) {
            this.setCustomValidity('No se pueden crear sesiones con más de 7 días de antigüedad.');
        } else if (selectedDate > maxDate) {
            this.setCustomValidity('No se pueden crear sesiones con más de 6 meses de anticipación.');
        } else {
            this.setCustomValidity('');
        }
    });

    // Confirmación antes de salir si hay cambios
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