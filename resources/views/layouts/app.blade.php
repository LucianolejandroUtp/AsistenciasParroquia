<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Sistema de Asistencias - Primera Comunión">
    <meta name="author" content="Parroquia">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="{{ asset('favicon.ico') }}">
    
    <title>@yield('title', 'Sistema de Asistencias - Primera Comunión')</title>
    
    <!-- Fonts CSS -->
    <link href="https://fonts.googleapis.com/css2?family=Overpass:ital,wght@0,100;0,200;0,300;0,400;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    
    <!-- TinyDash CSS -->
    <link rel="stylesheet" href="{{ asset('tinydash/css/simplebar.css') }}">
    <link rel="stylesheet" href="{{ asset('tinydash/css/feather.css') }}">
    <link rel="stylesheet" href="{{ asset('tinydash/css/app-light.css') }}" id="lightTheme">
    <link rel="stylesheet" href="{{ asset('tinydash/css/app-dark.css') }}" id="darkTheme" disabled>
    
    <!-- Additional CSS -->
    @yield('additional-css')
    
    <!-- Laravel Mix CSS -->
    @vite(['resources/css/app.css'])
</head>
<body class="vertical light">
    <div class="wrapper">
        <!-- Top Navigation -->
        <nav class="topnav navbar navbar-light">
            <button type="button" class="navbar-toggler text-muted mt-2 p-0 mr-3 collapseSidebar">
                <i class="fe fe-menu navbar-toggler-icon"></i>
            </button>
            
            <form class="form-inline mr-auto searchform text-muted">
                <input class="form-control mr-sm-2 bg-transparent border-0 pl-4 text-muted" type="search" 
                       placeholder="Buscar estudiante..." aria-label="Search">
            </form>
            
            <ul class="nav">
                <li class="nav-item">
                    <a class="nav-link text-muted my-2" href="#" id="modeSwitcher" data-mode="light">
                        <i class="fe fe-sun fe-16"></i>
                    </a>
                </li>
                <li class="nav-item nav-notif">
                    <a class="nav-link text-muted my-2" href="#" data-toggle="modal" data-target=".modal-notif">
                        <span class="fe fe-bell fe-16"></span>
                        <span class="dot dot-md bg-success"></span>
                    </a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle text-muted pr-0" href="#" id="navbarDropdownMenuLink" 
                       role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <span class="avatar avatar-sm mt-2">
                            <img src="{{ asset('tinydash/assets/avatars/face-1.jpg') }}" alt="..." class="avatar-img rounded-circle">
                        </span>
                        <span class="ml-2 d-none d-lg-inline">
                            @auth
                                {{ auth()->user()->name ?? 'Usuario' }}
                            @else
                                Invitado
                            @endauth
                        </span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdownMenuLink">
                        @auth
                            <a class="dropdown-item" href="#">Perfil</a>
                            <a class="dropdown-item" href="#">Configuración</a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                Cerrar Sesión
                            </a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                @csrf
                            </form>
                        @else
                            <a class="dropdown-item" href="{{ route('login') }}">Iniciar Sesión</a>
                        @endauth
                    </div>
                </li>
            </ul>
        </nav>

        <!-- Left Sidebar -->
        <aside class="sidebar-left border-right bg-white shadow" id="leftSidebar" data-simplebar>
            <a href="#" class="btn collapseSidebar toggle-btn d-lg-none text-muted ml-2 mt-3" data-toggle="toggle">
                <i class="fe fe-x"><span class="sr-only"></span></i>
            </a>
            <nav class="vertnav navbar navbar-light">
                <!-- Logo -->
                <div class="w-100 mb-4 d-flex">
                    <a class="navbar-brand mx-auto mt-2 flex-fill text-center" href="{{ route('dashboard') }}">
                        <div class="d-flex flex-column align-items-center">
                            <i class="fe fe-heart fe-24 text-primary mb-1"></i>
                            <small class="text-muted text-center">
                                <strong>Primera</strong><br>
                                <strong>Comunión</strong>
                            </small>
                        </div>
                    </a>
                </div>

                <!-- Main Navigation -->
                <ul class="navbar-nav flex-fill w-100 mb-2">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                            <i class="fe fe-home fe-16"></i>
                            <span class="ml-3 item-text">Dashboard</span>
                        </a>
                    </li>
                </ul>

                <!-- Gestión Section -->
                <p class="text-muted nav-heading mt-4 mb-1">
                    <span>Gestión</span>
                </p>
                <ul class="navbar-nav flex-fill w-100 mb-2">
                    <li class="nav-item dropdown">
                        <a href="#estudiantes" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle nav-link">
                            <i class="fe fe-users fe-16"></i>
                            <span class="ml-3 item-text">Estudiantes</span>
                        </a>
                        <ul class="collapse list-unstyled pl-4 w-100" id="estudiantes">
                            <li class="nav-item">
                                <a class="nav-link pl-3" href="#"><span class="ml-1 item-text">Lista Completa</span></a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link pl-3" href="#"><span class="ml-1 item-text">Grupo A</span></a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link pl-3" href="#"><span class="ml-1 item-text">Grupo B</span></a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link pl-3" href="#"><span class="ml-1 item-text">Códigos QR</span></a>
                            </li>
                        </ul>
                    </li>

                    <li class="nav-item dropdown">
                        <a href="#sesiones" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle nav-link">
                            <i class="fe fe-calendar fe-16"></i>
                            <span class="ml-3 item-text">Sesiones</span>
                        </a>
                        <ul class="collapse list-unstyled pl-4 w-100" id="sesiones">
                            <li class="nav-item">
                                <a class="nav-link pl-3" href="#"><span class="ml-1 item-text">Programar</span></a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link pl-3" href="#"><span class="ml-1 item-text">Calendario</span></a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link pl-3" href="#"><span class="ml-1 item-text">Historial</span></a>
                            </li>
                        </ul>
                    </li>

                    <li class="nav-item dropdown">
                        <a href="#asistencias" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle nav-link">
                            <i class="fe fe-check-circle fe-16"></i>
                            <span class="ml-3 item-text">Asistencias</span>
                        </a>
                        <ul class="collapse list-unstyled pl-4 w-100" id="asistencias">
                            <li class="nav-item">
                                <a class="nav-link pl-3" href="#"><span class="ml-1 item-text">Registrar</span></a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link pl-3" href="#"><span class="ml-1 item-text">Escanear QR</span></a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link pl-3" href="#"><span class="ml-1 item-text">Historial</span></a>
                            </li>
                        </ul>
                    </li>
                </ul>

                <!-- Reportes Section -->
                <p class="text-muted nav-heading mt-4 mb-1">
                    <span>Reportes</span>
                </p>
                <ul class="navbar-nav flex-fill w-100 mb-2">
                    <li class="nav-item">
                        <a class="nav-link" href="#">
                            <i class="fe fe-bar-chart-2 fe-16"></i>
                            <span class="ml-3 item-text">Estadísticas</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">
                            <i class="fe fe-file-text fe-16"></i>
                            <span class="ml-3 item-text">Exportar</span>
                        </a>
                    </li>
                </ul>

                @if(auth()->check() && auth()->user()->user_type_id === 1)
                <!-- Administración Section (Solo Admin) -->
                <p class="text-muted nav-heading mt-4 mb-1">
                    <span>Administración</span>
                </p>
                <ul class="navbar-nav flex-fill w-100 mb-2">
                    <li class="nav-item">
                        <a class="nav-link" href="#">
                            <i class="fe fe-user-plus fe-16"></i>
                            <span class="ml-3 item-text">Usuarios</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">
                            <i class="fe fe-settings fe-16"></i>
                            <span class="ml-3 item-text">Configuración</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">
                            <i class="fe fe-database fe-16"></i>
                            <span class="ml-3 item-text">Backup</span>
                        </a>
                    </li>
                </ul>
                @endif
            </nav>
        </aside>

        <!-- Main Content -->
        <main role="main" class="main-content">
            <div class="container-fluid">
                <div class="row justify-content-center">
                    <div class="col-12">
                        <!-- Breadcrumb -->
                        @if(!empty(trim($__env->yieldContent('breadcrumb'))))
                        <div class="row align-items-center mb-2">
                            <div class="col">
                                <h2 class="h5 page-title">@yield('page-title', 'Dashboard')</h2>
                                <nav aria-label="breadcrumb">
                                    <ol class="breadcrumb">
                                        @yield('breadcrumb')
                                    </ol>
                                </nav>
                            </div>
                        </div>
                        @endif

                        <!-- Flash Messages -->
                        @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fe fe-check-circle mr-2"></i>
                            {{ session('success') }}
                            <button type="button" class="close" data-dismiss="alert">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        @endif

                        @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fe fe-alert-circle mr-2"></i>
                            {{ session('error') }}
                            <button type="button" class="close" data-dismiss="alert">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        @endif

                        @if(session('warning'))
                        <div class="alert alert-warning alert-dismissible fade show" role="alert">
                            <i class="fe fe-alert-triangle mr-2"></i>
                            {{ session('warning') }}
                            <button type="button" class="close" data-dismiss="alert">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        @endif

                        <!-- Page Content -->
                        @yield('content')
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- TinyDash JavaScript -->
    <script src="{{ asset('tinydash/js/jquery.min.js') }}"></script>
    <script src="{{ asset('tinydash/js/popper.min.js') }}"></script>
    <script src="{{ asset('tinydash/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('tinydash/js/simplebar.min.js') }}"></script>
    <script src="{{ asset('tinydash/js/tinycolor-min.js') }}"></script>
    <script src="{{ asset('tinydash/js/jquery.stickOnScroll.js') }}"></script>
    <script src="{{ asset('tinydash/js/config.js') }}"></script>
    <script src="{{ asset('tinydash/js/apps.js') }}"></script>
    
    <!-- Laravel Mix JS -->
    @vite(['resources/js/app.js'])
    
    <!-- Additional JavaScript -->
    @yield('additional-js')

    <!-- Custom Scripts Section -->
    @stack('scripts')
</body>
</html>