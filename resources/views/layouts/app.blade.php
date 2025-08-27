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
    
    <!-- Custom Layout Styles -->
    <style>
        /* Adjust main content to fill space without topnav */
        .main-content {
            margin-left: 240px;
            width: calc(100% - 240px);
            min-height: 100vh;
            padding-top: 1rem;
        }
        
        /* Responsive adjustments */
        @media (max-width: 991px) {
            .main-content {
                margin-left: 0;
                width: 100%;
                padding-top: 1rem; /* Reducido ya que el botón está en el lado */
            }
            
            .sidebar-left {
                transform: translateX(-100%);
                transition: transform 0.3s ease;
                z-index: 1001; /* Higher than overlay */
            }
            
            .sidebar-left.show {
                transform: translateX(0) !important;
            }
            
            /* Override any conflicting styles */
            .sidebar-left.show {
                left: 0 !important;
                display: block !important;
                visibility: visible !important;
                opacity: 1 !important;
                transform: translateX(0) !important;
            }
            
            /* Overlay for mobile */
            .sidebar-overlay {
                position: fixed;
                top: 0;
                left: 0;
                width: 100vw;
                height: 100vh;
                background-color: rgba(0, 0, 0, 0.5);
                z-index: 1000;
                display: none;
                opacity: 0;
                transition: opacity 0.3s ease;
            }
            
            .sidebar-overlay.show {
                display: block;
                opacity: 1;
            }
            
            /* Mobile menu button styling */
            #mobileMenuBtn {
                box-shadow: 2px 0 8px rgba(0,0,0,0.15);
                z-index: 1002;
                transition: all 0.3s ease;
                border: none;
                width: 56px;
                height: 56px;
                border-radius: 0 28px 28px 0; /* Media luna - solo redondeado a la derecha */
                display: flex;
                align-items: center;
                justify-content: center;
                padding-left: 16px; /* Más padding para compensar la posición negativa */
                background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
                border-left: none; /* Sin borde izquierdo para pegarse al edge */
                margin-left: 0;
                overflow: visible; /* Permitir que sobresalga del viewport */
            }
            
            #mobileMenuBtn:hover {
                transform: translateY(-50%) translateX(12px); /* Deslizar más hacia la derecha al hover */
                box-shadow: 4px 0 12px rgba(0,0,0,0.25);
                width: 64px; /* Expandir ligeramente */
                left: -4px; /* Menos negativo en hover para efecto suave */
            }
            
            #mobileMenuBtn:active {
                transform: translateY(-50%) translateX(6px);
            }
            
            #mobileMenuBtn:focus {
                box-shadow: 2px 0 8px rgba(0,0,0,0.15), 0 0 0 3px rgba(0,123,255,0.25);
            }
        }
        
        /* Desktop behavior */
        @media (min-width: 992px) {
            .sidebar-left {
                transform: translateX(0) !important;
            }
        }
        
        /* NOTE: Desktop sidebar collapsed functionality REMOVED */
        /* Sidebar is always expanded at 240px width on desktop */
        /* Mobile functionality preserved below */
        
        /* Enhanced sidebar styling */
        .sidebar-left {
            width: 240px;
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            z-index: 1000;
            transition: transform 0.3s ease;
            background-color: white;
        }
        
        /* User profile card in sidebar */
        .sidebar-left .card {
            transition: all 0.2s ease;
        }
        
        .sidebar-left .card:hover {
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        
        /* Quick tools buttons */
        .sidebar-left .btn-outline-secondary {
            border-color: #e9ecef;
            color: #6c757d;
        }
        
        .sidebar-left .btn-outline-secondary:hover {
            background-color: #f8f9fa;
            border-color: #dee2e6;
            color: #495057;
        }
        
        /* FORCE MOBILE SIDEBAR VISIBILITY - DEBUG */
        @media (max-width: 991px) {
            #leftSidebar.show {
                transform: translateX(0px) !important;
                left: 0px !important;
                display: block !important;
                visibility: visible !important;
                opacity: 1 !important;
                position: fixed !important;
                top: 0 !important;
                width: 240px !important;
                height: 100vh !important;
                z-index: 1001 !important;
                background: white !important;
                border-right: 1px solid #dee2e6 !important;
                box-shadow: 2px 0 10px rgba(0,0,0,0.2) !important;
            }
        }
    </style>
    
    <!-- Additional CSS -->
    @yield('additional-css')
    
    <!-- Laravel Mix CSS -->
    @vite(['resources/css/app.css'])
</head>
<body class="vertical light">
    <div class="wrapper">
        <!-- Mobile Menu Button -->
        <button type="button" class="btn btn-primary d-lg-none position-fixed" id="mobileMenuBtn" 
                style="top: 15%; left: -20px; transform: translateY(-50%); z-index: 1001;">
            <i class="fe fe-menu"></i>
        </button>
        
        <!-- Mobile Sidebar Overlay -->
        <div class="sidebar-overlay" id="sidebarOverlay"></div>
        
        <!-- Left Sidebar -->
        <aside class="sidebar-left border-right bg-white shadow" id="leftSidebar" data-simplebar>
            <nav class="vertnav navbar navbar-light">
                <!-- Logo -->
                <div class="w-100 mb-4 d-flex">
                    <a class="navbar-brand mx-auto mt-2 flex-fill text-center" href="{{ route('dashboard') }}">
                        <div class="d-flex flex-column align-items-center">
                            <i class="fe fe-user-check fe-24 text-primary mb-1"></i>
                            <small class="text-muted text-center">
                                <strong>Asistencia</strong>
                            </small>
                        </div>
                    </a>
                </div>

                <!-- Quick Tools -->
                <ul class="navbar-nav flex-fill w-100 mb-2">
                    <!-- Sidebar Toggle - REMOVED FOR DESKTOP -->
                    <!-- Mobile sidebar toggle is handled by #mobileMenuBtn -->
                    
                    <!-- Theme Switcher -->
                    <li class="nav-item theme-switcher-item">
                        <a href="#" class="nav-link" id="modeSwitcher" data-mode="light" title="Cambiar tema">
                            <i class="fe fe-sun fe-16"></i>
                            <span class="ml-3 item-text">Tema Claro</span>
                        </a>
                    </li>
                </ul>

                <!-- Main Navigation -->
                <ul class="navbar-nav flex-fill w-100 mb-2">
                    <!-- User Profile Section -->
                    <li class="nav-item dropdown">
                        <a href="#userProfile" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle nav-link">
                            <i class="fe fe-user fe-16"></i>
                            <span class="ml-3 item-text">
                                @auth
                                    {{ auth()->user()->userType->name ?? 'Usuario' }}
                                @else
                                    Invitado
                                @endauth
                            </span>
                        </a>
                        <ul class="collapse list-unstyled pl-4 w-100" id="userProfile">
                            <li class="nav-item">
                                <div class="d-flex align-items-center px-3 py-2">
                                    <div class="mr-2">
                                        <i class="fe fe-user fe-16 text-muted"></i>
                                    </div>
                                    <div class="flex-fill">
                                        <small class="text-muted d-block">
                                            @auth
                                                {{ auth()->user()->email ?? 'Sin email' }}
                                            @else
                                                No autenticado
                                            @endauth
                                        </small>
                                    </div>
                                </div>
                            </li>
                            @auth
                                <li class="nav-item">
                                    <a class="nav-link pl-3" href="#">
                                        <span class="ml-1 item-text">
                                            <i class="fe fe-user fe-12 mr-2"></i>Perfil
                                        </span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link pl-3" href="#">
                                        <span class="ml-1 item-text">
                                            <i class="fe fe-settings fe-12 mr-2"></i>Configuración
                                        </span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link pl-3" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                        <span class="ml-1 item-text">
                                            <i class="fe fe-log-out fe-12 mr-2"></i>Cerrar Sesión
                                        </span>
                                    </a>
                                    <form id="logout-form" action="{{ route('auth.logout') }}" method="POST" style="display: none;">
                                        @csrf
                                    </form>
                                </li>
                            @else
                                <li class="nav-item">
                                    <a class="nav-link pl-3" href="{{ route('auth.login') }}">
                                        <span class="ml-1 item-text">
                                            <i class="fe fe-log-in fe-12 mr-2"></i>Iniciar Sesión
                                        </span>
                                    </a>
                                </li>
                            @endauth
                        </ul>
                    </li>

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
                        <a href="#estudiantes" data-toggle="collapse" aria-expanded="{{ request()->routeIs('students.*') ? 'true' : 'false' }}" 
                           class="dropdown-toggle nav-link {{ request()->routeIs('students.*') ? 'active' : '' }}">
                            <i class="fe fe-users fe-16"></i>
                            <span class="ml-3 item-text">Estudiantes</span>
                        </a>
                        <ul class="collapse list-unstyled pl-4 w-100 {{ request()->routeIs('students.*') ? 'show' : '' }}" id="estudiantes">
                            <li class="nav-item">
                                <a class="nav-link pl-3 {{ request()->routeIs('students.index') ? 'active' : '' }}" 
                                   href="{{ route('students.index') }}">
                                    <span class="ml-1 item-text">Lista Completa</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link pl-3 {{ request()->routeIs('students.qr-codes') ? 'active' : '' }}" 
                                   href="{{ route('students.qr-codes') }}">
                                    <span class="ml-1 item-text">Códigos QR</span>
                                </a>
                            </li>
                        </ul>
                    </li>

                    <li class="nav-item dropdown">
                        <a href="#sesiones" data-toggle="collapse" aria-expanded="{{ request()->routeIs('sessions.*') ? 'true' : 'false' }}" 
                           class="dropdown-toggle nav-link {{ request()->routeIs('sessions.*') ? 'active' : '' }}">
                            <i class="fe fe-calendar fe-16"></i>
                            <span class="ml-3 item-text">Sesiones</span>
                        </a>
                        <ul class="collapse list-unstyled pl-4 w-100 {{ request()->routeIs('sessions.*') ? 'show' : '' }}" id="sesiones">
                            <li class="nav-item">
                                <a class="nav-link pl-3 {{ request()->routeIs('sessions.create') ? 'active' : '' }}" 
                                   href="{{ route('sessions.create') }}">
                                    <span class="ml-1 item-text">Programar Sesión</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link pl-3 {{ request()->routeIs('sessions.index') ? 'active' : '' }}" 
                                   href="{{ route('sessions.index') }}">
                                    <span class="ml-1 item-text">Todas las Sesiones</span>
                                </a>
                            </li>
                        </ul>
                    </li>

                    <li class="nav-item dropdown">
                        <a href="#asistencias" data-toggle="collapse" aria-expanded="{{ request()->routeIs('attendances.*') ? 'true' : 'false' }}" 
                           class="dropdown-toggle nav-link {{ request()->routeIs('attendances.*') ? 'active' : '' }}">
                            <i class="fe fe-check-circle fe-16"></i>
                            <span class="ml-3 item-text">Asistencias</span>
                        </a>
                        <ul class="collapse list-unstyled pl-4 w-100 {{ request()->routeIs('attendances.*') ? 'show' : '' }}" id="asistencias">
                            <li class="nav-item">
                                <a class="nav-link pl-3 {{ request()->routeIs('attendances.register') ? 'active' : '' }}" 
                                   href="{{ route('attendances.register') }}">
                                    <span class="ml-1 item-text">Registrar</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link pl-3 {{ request()->routeIs('attendances.qr-scanner') ? 'active' : '' }}" 
                                   href="{{ route('attendances.qr-scanner') }}">
                                    <span class="ml-1 item-text">Escanear QR</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link pl-3 {{ request()->routeIs('attendances.history') ? 'active' : '' }}" 
                                   href="{{ route('attendances.history') }}">
                                    <span class="ml-1 item-text">Historial</span>
                                </a>
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
                        <a class="nav-link {{ request()->routeIs('reports.statistics') ? 'active' : '' }}" 
                           href="{{ route('reports.statistics') }}">
                            <i class="fe fe-bar-chart-2 fe-16"></i>
                            <span class="ml-3 item-text">Estadísticas</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('reports.export') ? 'active' : '' }}" 
                           href="{{ route('reports.export') }}">
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
                        <a class="nav-link {{ request()->routeIs('admin.users') ? 'active' : '' }}" 
                           href="{{ route('admin.users') }}">
                            <i class="fe fe-user-plus fe-16"></i>
                            <span class="ml-3 item-text">Usuarios</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.settings') ? 'active' : '' }}" 
                           href="{{ route('admin.settings') }}">
                            <i class="fe fe-settings fe-16"></i>
                            <span class="ml-3 item-text">Configuración</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.backup') ? 'active' : '' }}" 
                           href="{{ route('admin.backup') }}">
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
    
    <!-- Custom Layout JavaScript -->
    <script>
        $(document).ready(function() {
            // Mobile sidebar toggle
            function toggleMobileSidebar() {
                const sidebar = $('#leftSidebar');
                const overlay = $('#sidebarOverlay');
                
                if ($(window).width() < 992) {
                    console.log('Toggling mobile sidebar');
                    
                    if (sidebar.hasClass('show')) {
                        // Hide sidebar
                        sidebar.removeClass('show');
                        sidebar.css('transform', 'translateX(-100%)');
                        overlay.removeClass('show');
                    } else {
                        // Show sidebar
                        sidebar.addClass('show');
                        // Force style inline as backup
                        sidebar.css({
                            'transform': 'translateX(0px)',
                            'display': 'block',
                            'visibility': 'visible',
                            'left': '0px',
                            'z-index': '1001'
                        });
                        overlay.addClass('show');
                    }
                    
                    console.log('Sidebar classes:', sidebar.attr('class'));
                    console.log('Overlay classes:', overlay.attr('class'));
                    console.log('Sidebar computed transform:', sidebar.css('transform'));
                    console.log('Sidebar computed left:', sidebar.css('left'));
                }
            }
            
            // Mobile menu button click
            $('#mobileMenuBtn').on('click', function(e) {
                e.preventDefault();
                console.log('Mobile menu button clicked');
                toggleMobileSidebar();
            });
            
            // Sidebar overlay click to close
            $('#sidebarOverlay').on('click', function() {
                toggleMobileSidebar();
            });
            
            // NOTE: Desktop sidebar collapse functionality REMOVED
            // Only mobile sidebar toggle remains active via #mobileMenuBtn
            
            // Enhanced theme switcher
            $('#modeSwitcher').on('click', function(e) {
                e.preventDefault();
                const currentMode = $(this).attr('data-mode');
                const newMode = currentMode === 'light' ? 'dark' : 'light';
                
                // Update button
                $(this).attr('data-mode', newMode);
                $(this).find('i').removeClass('fe-sun fe-moon').addClass(newMode === 'light' ? 'fe-sun' : 'fe-moon');
                
                // Update text in sidebar
                const textSpan = $(this).find('.item-text');
                textSpan.text(newMode === 'light' ? 'Tema Claro' : 'Tema Oscuro');
                
                // Update body class
                $('body').removeClass('light dark').addClass(newMode);
                
                // Toggle CSS files
                if (newMode === 'dark') {
                    $('#lightTheme').prop('disabled', true);
                    $('#darkTheme').prop('disabled', false);
                } else {
                    $('#lightTheme').prop('disabled', false);
                    $('#darkTheme').prop('disabled', true);
                }
                
                // Save preference
                localStorage.setItem('theme', newMode);
            });
            
            // NOTE: Sidebar collapse state restoration REMOVED for desktop
            // Sidebar is always expanded on desktop now
            
            // Restore theme on page load
            const savedTheme = localStorage.getItem('theme') || 'light';
            if (savedTheme === 'dark') {
                // Initialize dark theme
                $('#modeSwitcher').attr('data-mode', 'light'); // Set to light first
                $('#modeSwitcher').click(); // Then trigger the switch to dark
            } else {
                // Ensure light theme text is set correctly
                $('#modeSwitcher .item-text').text('Tema Claro');
            }
            
            console.log('Enhanced sidebar layout with mobile support initialized');
            
            // Initialize mobile sidebar state
            function initializeMobileSidebar() {
                if ($(window).width() < 992) {
                    $('#leftSidebar').removeClass('show');
                    $('#sidebarOverlay').removeClass('show');
                    console.log('Mobile sidebar initialized - hidden state');
                }
            }
            
            // Initialize on load
            initializeMobileSidebar();
            
            // Re-initialize on window resize
            $(window).on('resize', function() {
                initializeMobileSidebar();
                
                if ($(window).width() >= 992) {
                    // Desktop: hide overlay and reset mobile classes
                    $('#sidebarOverlay').removeClass('show');
                    $('#leftSidebar').removeClass('show');
                    console.log('Switched to desktop mode');
                }
            });
            
            // Close mobile sidebar when clicking on main content
            $('.main-content').on('click', function(e) {
                if ($(window).width() < 992 && $('#leftSidebar').hasClass('show')) {
                    toggleMobileSidebar();
                }
            });
        });
    </script>
    
    <!-- Laravel Mix JS -->
    @vite(['resources/js/app.js'])
    
    <!-- Additional JavaScript -->
    @yield('additional-js')

    <!-- Custom Scripts Section -->
    @stack('scripts')
</body>
</html>