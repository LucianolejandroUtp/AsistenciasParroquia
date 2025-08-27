<div>
    <form id="studentEditForm" method="POST" action="{{ route('students.update', ['student' => $student->id]) }}">
        @csrf
        @method('PUT')

        <div class="form-row">
            <div class="form-group col-md-6">
                <label for="names">Nombres</label>
                <input type="text" class="form-control form-control-sm" id="names" name="names" value="{{ old('names', $student->names) }}" required>
            </div>
            <div class="form-group col-md-6">
                <label for="paternal_surname">Apellido Paterno</label>
                <input type="text" class="form-control form-control-sm" id="paternal_surname" name="paternal_surname" value="{{ old('paternal_surname', $student->paternal_surname) }}" required>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group col-md-6">
                <label for="maternal_surname">Apellido Materno</label>
                <input type="text" class="form-control form-control-sm" id="maternal_surname" name="maternal_surname" value="{{ old('maternal_surname', $student->maternal_surname) }}">
            </div>
            <div class="form-group col-md-3">
                <label for="group_id">Grupo</label>
                <select id="group_id" name="group_id" class="form-control form-control-sm">
                    <option value="">Sin Grupo</option>
                    @foreach($groups as $g)
                        <option value="{{ $g->id }}" {{ (old('group_id', $student->group_id) == $g->id) ? 'selected' : '' }}>{{ $g->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group col-md-3">
                <label for="order_number">Orden</label>
                <input type="number" class="form-control form-control-sm" id="order_number" name="order_number" value="{{ old('order_number', $student->order_number) }}" min="1" required>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group col-md-4">
                <label for="student_code">Código Estudiante</label>
                <input type="text" class="form-control form-control-sm bg-light" id="student_code" name="student_code" value="{{ old('student_code', $student->student_code) }}" readonly>
                <small class="form-text text-muted">
                    <i class="fe fe-lock text-warning mr-1"></i>
                    Código permanente - No se regenera automáticamente
                </small>
            </div>
            <div class="form-group col-md-4">
                <label for="status">Estado</label>
                <select id="status" name="status" class="form-control form-control-sm">
                    <option value="ACTIVO" {{ (old('status', $student->status) == 'ACTIVO') ? 'selected' : '' }}>ACTIVO</option>
                    <option value="INACTIVO" {{ (old('status', $student->status) == 'INACTIVO') ? 'selected' : '' }}>INACTIVO</option>
                    <option value="ELIMINADO" {{ (old('status', $student->status) == 'ELIMINADO') ? 'selected' : '' }}>ELIMINADO</option>
                </select>
            </div>
            <div class="form-group col-md-4 d-flex align-items-end">
                <button type="submit" class="btn btn-primary btn-sm w-100">Guardar cambios</button>
            </div>
        </div>
    </form>
</div>
