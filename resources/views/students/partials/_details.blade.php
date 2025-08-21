<div class="student-details-px">
    <!-- Partial: Detalles del estudiante para modal -->
    <div class="row">
        <div class="col-md-4 text-center">
            {{-- Placeholder para foto si existe --}}
            <div class="mb-2">
                <i class="fe fe-user" style="font-size:56px;color:#6c757d;"></i>
            </div>
            <h5 class="mb-0">{{ $student->full_name }}</h5>
            <small class="text-muted">{{ $student->names }} {{ $student->paternal_surname }}</small>
        </div>
        <div class="col-md-8">
            <dl class="row">
                <dt class="col-sm-4">Grupo</dt>
                <dd class="col-sm-8">{{ $student->group_name ?? 'Sin Grupo' }}</dd>

                <dt class="col-sm-4">Asistencia</dt>
                <dd class="col-sm-8">{{ $student->attendance_percentage }}% ({{ $student->attended_sessions }}/{{ $student->total_sessions }})</dd>

                <dt class="col-sm-4">Estado</dt>
                <dd class="col-sm-8">{{ $student->status }}</dd>

                <dt class="col-sm-4">Orden</dt>
                <dd class="col-sm-8">{{ $student->order_number ?? '-' }}</dd>

                <dt class="col-sm-4">ID</dt>
                <dd class="col-sm-8"><code>{{ $student->id }}</code></dd>
            </dl>
        </div>
    </div>
</div>
