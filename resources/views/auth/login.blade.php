@extends('layouts.auth')

@section('content')
<div class="auth-header">
    <h1><i class="fe fe-user-check me-2"></i>Iniciar Sesión</h1>
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

    @if (session('warning'))
        <div class="alert alert-warning">
            <i class="fe fe-alert-triangle me-2"></i>{{ session('warning') }}
        </div>
    @endif

    <!-- Formulario de Login -->
    <form method="POST" action="{{ route('auth.login') }}" novalidate>
        @csrf
        
        <!-- Email -->
        <div class="form-group">
            <label for="email" class="form-label">
                <i class="fe fe-mail me-1"></i>Email
            </label>
            <input 
                type="email" 
                class="form-control @error('email') is-invalid @enderror" 
                id="email" 
                name="email" 
                value="{{ old('email') }}" 
                placeholder="Ingrese su email"
                autocomplete="email"
                required
            >
            @error('email')
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
                    placeholder="Ingrese su contraseña"
                    autocomplete="current-password"
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

        <!-- Remember Me -->
        <div class="remember-me">
            <input 
                type="checkbox" 
                id="remember" 
                name="remember" 
                value="1"
                {{ old('remember') ? 'checked' : '' }}
            >
            <label for="remember" class="form-label mb-0">
                Recordar mi sesión
            </label>
        </div>

        <!-- Submit Button -->
        <button type="submit" class="btn btn-primary w-100">
            <i class="fe fe-log-in me-2"></i>
            Iniciar Sesión
        </button>
    </form>

    <!-- Footer Links -->
    <div class="auth-footer">
        <p class="mb-2">
            <a href="{{ route('auth.register') }}" class="btn-link">
                <i class="fe fe-user-plus me-1"></i>
                ¿No tiene cuenta? Registrarse
            </a>
        </p>
        <p class="text-muted small">
            <i class="fe fe-shield me-1"></i>
            Acceso seguro al sistema parroquial
        </p>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Validación en tiempo real
        $('#email').on('blur', function() {
            const email = $(this).val().trim();
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                $(this).addClass('is-invalid');
                if (!$(this).siblings('.invalid-feedback').length) {
                    $(this).after('<div class="invalid-feedback">Ingrese un email válido.</div>');
                }
            } else {
                $(this).removeClass('is-invalid');
                $(this).siblings('.invalid-feedback').remove();
            }
        });

        $('#password').on('blur', function() {
            const password = $(this).val();
            if (password.length < 4) {
                $(this).addClass('is-invalid');
                if (!$(this).siblings('.invalid-feedback').length) {
                    $(this).after('<div class="invalid-feedback">La contraseña debe tener al menos 4 caracteres.</div>');
                }
            } else {
                $(this).removeClass('is-invalid');
                $(this).siblings('.invalid-feedback').remove();
            }
        });

        // Detectar Enter en email para mover a password
        $('#email').on('keypress', function(e) {
            if (e.which === 13) {
                e.preventDefault();
                $('#password').focus();
            }
        });

        // Submit con Enter en password
        $('#password').on('keypress', function(e) {
            if (e.which === 13) {
                e.preventDefault();
                $('form').submit();
            }
        });
    });
</script>
@endsection