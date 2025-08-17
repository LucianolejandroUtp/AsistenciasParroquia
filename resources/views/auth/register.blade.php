@extends('layouts.auth')

@section('content')
<div class="auth-header">
    <h1><i class="fe fe-user-plus me-2"></i>Registrar Usuario</h1>
    <p>Sistema de Asistencias - Primera Comunión</p>
</div>

<div class="auth-body">
    <!-- Mensajes de estado -->
    @if (session('success'))
        <div class="alert alert-success">
            <i class="fe fe-check-circle me-2"></i>{{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger">
            <i class="fe fe-alert-circle me-2"></i>{{ session('error') }}
        </div>
    @endif

    <!-- Formulario de Registro -->
    <form method="POST" action="{{ route('auth.register') }}" novalidate>
        @csrf
        
        <!-- Información Personal -->
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="first_name" class="form-label">
                        <i class="fe fe-user me-1"></i>Nombre
                    </label>
                    <input 
                        type="text" 
                        class="form-control @error('first_name') is-invalid @enderror" 
                        id="first_name" 
                        name="first_name" 
                        value="{{ old('first_name') }}" 
                        placeholder="Nombre"
                        autocomplete="given-name"
                        required
                    >
                    @error('first_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="last_name" class="form-label">
                        <i class="fe fe-user me-1"></i>Apellido
                    </label>
                    <input 
                        type="text" 
                        class="form-control @error('last_name') is-invalid @enderror" 
                        id="last_name" 
                        name="last_name" 
                        value="{{ old('last_name') }}" 
                        placeholder="Apellido"
                        autocomplete="family-name"
                        required
                    >
                    @error('last_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Username -->
        <div class="form-group">
            <label for="username" class="form-label">
                <i class="fe fe-at-sign me-1"></i>Nombre de Usuario
            </label>
            <input 
                type="text" 
                class="form-control @error('username') is-invalid @enderror" 
                id="username" 
                name="username" 
                value="{{ old('username') }}" 
                placeholder="Nombre de usuario único"
                autocomplete="username"
                required
            >
            @error('username')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            <small class="form-text text-muted">
                <i class="fe fe-info me-1"></i>
                Solo letras, números y guiones. Mínimo 3 caracteres.
            </small>
        </div>

        <!-- Email (opcional) -->
        <div class="form-group">
            <label for="email" class="form-label">
                <i class="fe fe-mail me-1"></i>Email (Opcional)
            </label>
            <input 
                type="email" 
                class="form-control @error('email') is-invalid @enderror" 
                id="email" 
                name="email" 
                value="{{ old('email') }}" 
                placeholder="correo@ejemplo.com"
                autocomplete="email"
            >
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Tipo de Usuario -->
        <div class="form-group">
            <label for="user_type_id" class="form-label">
                <i class="fe fe-users me-1"></i>Tipo de Usuario
            </label>
            <select 
                class="form-control @error('user_type_id') is-invalid @enderror" 
                id="user_type_id" 
                name="user_type_id" 
                required
            >
                <option value="">Seleccione el tipo de usuario</option>
                <option value="1" {{ old('user_type_id') == '1' ? 'selected' : '' }}>
                    <i class="fe fe-shield"></i> Administrador
                </option>
                <option value="2" {{ old('user_type_id') == '2' ? 'selected' : '' }}>
                    <i class="fe fe-book-open"></i> Catequista
                </option>
                <option value="3" {{ old('user_type_id') == '3' ? 'selected' : '' }}>
                    <i class="fe fe-help-circle"></i> Personal de Apoyo
                </option>
            </select>
            @error('user_type_id')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Password -->
        <div class="form-group">
            <label for="password" class="form-label">
                <i class="fe fe-lock me-1"></i>Contraseña
            </label>
            <div class="password-toggle">
                <input 
                    type="password" 
                    class="form-control @error('password') is-invalid @enderror" 
                    id="password" 
                    name="password" 
                    placeholder="Mínimo 8 caracteres"
                    autocomplete="new-password"
                    required
                >
                <button type="button" class="password-toggle-btn" title="Mostrar/Ocultar contraseña">
                    <i class="fe fe-eye"></i>
                </button>
            </div>
            @error('password')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Confirm Password -->
        <div class="form-group">
            <label for="password_confirmation" class="form-label">
                <i class="fe fe-lock me-1"></i>Confirmar Contraseña
            </label>
            <div class="password-toggle">
                <input 
                    type="password" 
                    class="form-control" 
                    id="password_confirmation" 
                    name="password_confirmation" 
                    placeholder="Repita su contraseña"
                    autocomplete="new-password"
                    required
                >
                <button type="button" class="password-toggle-btn" title="Mostrar/Ocultar contraseña">
                    <i class="fe fe-eye"></i>
                </button>
            </div>
        </div>

        <!-- Submit Button -->
        <button type="submit" class="btn btn-primary w-100">
            <i class="fe fe-user-plus me-2"></i>
            Registrar Usuario
        </button>
    </form>

    <!-- Footer Links -->
    <div class="auth-footer">
        <p class="mb-0">
            <a href="{{ route('auth.login') }}" class="btn-link">
                <i class="fe fe-arrow-left me-1"></i>
                ¿Ya tiene cuenta? Iniciar sesión
            </a>
        </p>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Validación en tiempo real para username
        $('#username').on('input', function() {
            const username = $(this).val();
            const pattern = /^[a-zA-Z0-9_-]+$/;
            
            if (username.length < 3) {
                $(this).addClass('is-invalid');
                updateFeedback($(this), 'El nombre de usuario debe tener al menos 3 caracteres.');
            } else if (!pattern.test(username)) {
                $(this).addClass('is-invalid');
                updateFeedback($(this), 'Solo se permiten letras, números, guiones y guiones bajos.');
            } else {
                $(this).removeClass('is-invalid');
                removeFeedback($(this));
            }
        });

        // Validación de contraseñas
        $('#password, #password_confirmation').on('input', function() {
            const password = $('#password').val();
            const confirmation = $('#password_confirmation').val();
            
            // Validar longitud de contraseña
            if (password.length > 0 && password.length < 8) {
                $('#password').addClass('is-invalid');
                updateFeedback($('#password'), 'La contraseña debe tener al menos 8 caracteres.');
            } else {
                $('#password').removeClass('is-invalid');
                removeFeedback($('#password'));
            }
            
            // Validar coincidencia de contraseñas
            if (confirmation.length > 0 && password !== confirmation) {
                $('#password_confirmation').addClass('is-invalid');
                updateFeedback($('#password_confirmation'), 'Las contraseñas no coinciden.');
            } else {
                $('#password_confirmation').removeClass('is-invalid');
                removeFeedback($('#password_confirmation'));
            }
        });

        // Funciones auxiliares
        function updateFeedback($input, message) {
            removeFeedback($input);
            $input.after('<div class="invalid-feedback">' + message + '</div>');
        }

        function removeFeedback($input) {
            $input.siblings('.invalid-feedback').remove();
        }

        // Auto-generar username basado en nombre y apellido
        $('#first_name, #last_name').on('input', function() {
            const firstName = $('#first_name').val().trim().toLowerCase();
            const lastName = $('#last_name').val().trim().toLowerCase();
            
            if (firstName && lastName) {
                const suggestion = firstName + '.' + lastName;
                if (!$('#username').val()) {
                    $('#username').val(suggestion);
                }
            }
        });
    });
</script>
@endsection