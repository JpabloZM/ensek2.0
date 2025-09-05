<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Sistema Empresa') }} - Panel Administrativo</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Scripts -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    
    <!-- FullCalendar -->
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@fullcalendar/resource-timeline@5.11.3/main.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@fullcalendar/locales-all@5.11.3/main.min.js"></script>
    
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <!-- Estilos personalizados -->
    <link href="{{ asset('css/custom.css') }}" rel="stylesheet">
    <style>
        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
            overflow-x: hidden;
        }
        
        body {
            overflow-x: hidden;
            position: relative;
            width: 100%;
            box-sizing: border-box;
        }
        
        #wrapper {
            display: flex;
            min-height: 100vh;
            width: 100%;
            overflow: hidden;
            position: relative;
        }
        
        #sidebar-wrapper {
            min-height: 100vh;
            width: 250px;
            position: fixed;
            height: 100%;
            z-index: 1;
            left: 0;
            transition: margin .25s ease-out;
            overflow-y: auto;
            overflow-x: hidden;
        }
        
        #page-content-wrapper {
            width: calc(100% - 250px);
            margin-left: 250px;
            flex: 1;
            overflow-x: hidden;
        }
        
        #wrapper.toggled #sidebar-wrapper {
            margin-left: -250px;
        }
        
        #wrapper.toggled #page-content-wrapper {
            margin-left: 0;
            width: 100%;
        }
        
        .container-fluid {
            width: 100%;
            padding-right: 15px;
            padding-left: 15px;
            margin-right: auto;
            margin-left: auto;
            overflow-x: hidden;
        }
        
        .list-group-item {
            border: none;
            padding: 12px 20px;
            margin-bottom: 5px;
        }
        
        #sidebar-wrapper .list-group-item {
            color: #fff;
        }
        
        #sidebar-wrapper .list-group-item:hover {
            background-color: rgba(255, 255, 255, 0.1);
            border-radius: 5px;
        }
        
        #sidebar-wrapper .list-group-item.active {
            background-color: #007bff !important;
            color: #fff !important;
            border-radius: 5px;
        }
        
        .second-text {
            color: #bbb;
        }
        
        /* Estilos para tablas responsivas */
        .table-responsive {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }
        
        /* Estilos para FullCalendar */
        .fc {
            max-width: 100%;
            overflow-x: auto;
        }
        
        .fc-view {
            overflow-x: auto;
        }
        
        @media (max-width: 768px) {
            #sidebar-wrapper {
                margin-left: -250px;
                position: fixed;
            }
            
            #page-content-wrapper {
                margin-left: 0;
                width: 100%;
            }
            
            #wrapper.toggled #sidebar-wrapper {
                margin-left: 0;
            }
        }
    </style>
    
    @stack('styles')
</head>
<body>
    <div id="wrapper">
        <!-- Sidebar -->
        <div class="bg-dark border-right" id="sidebar-wrapper">
            <div class="sidebar-heading text-center py-4 text-white fs-4 fw-bold text-uppercase">
                <i class="fas fa-tools me-2"></i>SISTEMA EMPRESA
            </div>
            <div class="list-group list-group-flush my-3">
                <a href="{{ route('admin.dashboard') }}" class="list-group-item list-group-item-action bg-transparent second-text fw-bold {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                </a>
                <a href="{{ route('admin.service-requests.index') }}" class="list-group-item list-group-item-action bg-transparent second-text fw-bold {{ request()->routeIs('admin.service-requests.*') ? 'active' : '' }}">
                    <i class="fas fa-clipboard-list me-2"></i>Solicitudes
                </a>
                <a href="{{ route('admin.schedules.index') }}" class="list-group-item list-group-item-action bg-transparent second-text fw-bold {{ request()->routeIs('admin.schedules.*') ? 'active' : '' }}">
                    <i class="fas fa-calendar-alt me-2"></i>Calendario
                </a>
                <a href="{{ route('admin.technicians.index') }}" class="list-group-item list-group-item-action bg-transparent second-text fw-bold {{ request()->routeIs('admin.technicians.*') ? 'active' : '' }}">
                    <i class="fas fa-user me-2"></i>Técnicos
                </a>
                <a href="{{ route('admin.services.index') }}" class="list-group-item list-group-item-action bg-transparent second-text fw-bold {{ request()->routeIs('admin.services.*') ? 'active' : '' }}">
                    <i class="fas fa-wrench me-2"></i>Servicios
                </a>
                <a href="{{ route('admin.inventory-items.index') }}" class="list-group-item list-group-item-action bg-transparent second-text fw-bold {{ request()->routeIs('admin.inventory-items.*') ? 'active' : '' }}">
                    <i class="fas fa-boxes me-2"></i>Inventario
                </a>
                <a href="{{ route('admin.inventory-categories.index') }}" class="list-group-item list-group-item-action bg-transparent second-text fw-bold {{ request()->routeIs('admin.inventory-categories.*') ? 'active' : '' }}">
                    <i class="fas fa-tags me-2"></i>Categorías
                </a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                    @csrf
                </form>
            </div>
        </div>
        
        <!-- Page Content -->
        <div id="page-content-wrapper">
            <nav class="navbar navbar-expand-lg navbar-light bg-white py-4 px-4 shadow-sm">
                <div class="d-flex align-items-center">
                    <i class="fas fa-align-left primary-text fs-4 me-3" id="menu-toggle"></i>
                    <h2 class="fs-2 m-0">@yield('page-title', 'Panel Administrativo')</h2>
                </div>
                
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle fw-bold text-dark" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-user me-2"></i>{{ Auth::user()->name }}
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                <li><a class="dropdown-item" href="#"><i class="fas fa-user-circle me-2"></i>Perfil</a></li>
                                <li><a class="dropdown-item" href="#"><i class="fas fa-cog me-2"></i>Configuración</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <a class="dropdown-item text-danger" href="{{ route('logout') }}" 
                                       onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                       <i class="fas fa-sign-out-alt me-2"></i>Cerrar sesión
                                    </a>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </nav>
            
            <div class="container-fluid py-3 py-md-4 px-3 px-md-4 overflow-hidden">
                @if (session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif
                
                @if (session('error'))
                    <div class="alert alert-danger">
                        {{ session('error') }}
                    </div>
                @endif
                
                @yield('content')
            </div>
        </div>
    </div>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Manejar el toggle del menú
            document.getElementById("menu-toggle").addEventListener("click", function(e) {
                e.preventDefault();
                document.getElementById("wrapper").classList.toggle("toggled");
            });
            
            // Ajustar altura en dispositivos móviles
            function adjustHeight() {
                var windowHeight = window.innerHeight;
                document.getElementById("sidebar-wrapper").style.minHeight = windowHeight + 'px';
                
                // Si la página es más corta que la ventana, extender el contenido
                var contentHeight = document.getElementById("page-content-wrapper").scrollHeight;
                if (contentHeight < windowHeight) {
                    document.getElementById("page-content-wrapper").style.minHeight = windowHeight + 'px';
                }
                
                // Disparar un evento personalizado para notificar cambios de tamaño
                const resizeEvent = new CustomEvent('app:resize', {
                    detail: {
                        width: window.innerWidth,
                        height: window.innerHeight,
                        isMobile: window.innerWidth < 768,
                        isTablet: window.innerWidth >= 768 && window.innerWidth < 992
                    }
                });
                document.dispatchEvent(resizeEvent);
            }
            
            // Ajustar al cargar y al cambiar el tamaño de la ventana
            adjustHeight();
            window.addEventListener('resize', adjustHeight);
        });
    </script>
    
    @stack('scripts')
</body>
</html>
