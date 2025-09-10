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

    <!-- jQuery primero que todo -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <!-- Bootstrap JS (necesario para DataTables) -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.min.js"></script>
    
    <!-- Sistema de búsqueda personalizado (reemplaza completamente DataTables) -->
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.1.3/css/bootstrap.min.css"/>
    <script type="text/javascript" src="{{ asset('js/datatables-custom.js') }}"></script>
    
    <!-- FullCalendar -->
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css" rel="stylesheet">
    <!-- Cargamos FullCalendar como script normal en lugar de como módulo -->
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.global.min.js"></script>
    
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <!-- Scripts de Vite -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    
    <!-- Estilos personalizados -->
    <link href="{{ asset('css/custom.css') }}" rel="stylesheet">
    <style>
        /* Alert styles */
        .alert {
            border-radius: 0.25rem;
            padding: 0.75rem 1.25rem;
            margin-bottom: 1.5rem;
            border: 1px solid transparent;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        
        .alert-info {
            color: #0c5460;
            background-color: #d1ecf1;
            border-color: #bee5eb;
        }
        
        .alert .close {
            padding: 0;
            background-color: transparent;
            border: 0;
            float: right;
            font-size: 1.5rem;
            font-weight: 700;
            line-height: 1;
            color: inherit;
            opacity: .5;
            margin-left: 15px;
        }
        
        /* Toast notification styles */
        .toast-container {
            position: fixed;
            z-index: 1500;
            right: 20px;
            top: 80px;
        }
        
        .toast {
            opacity: 0;
            transition: all 0.5s ease-in-out;
            margin-bottom: 10px;
            max-width: 350px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
            border-radius: 4px;
        }
        
        .toast.showing {
            opacity: 0;
            transform: translateX(20px);
        }
        
        .toast.show {
            opacity: 1;
            transform: translateX(0);
        }
        
        .toast-body {
            padding: 12px 15px;
            font-weight: 500;
        }
        
        .btn-close-white {
            filter: brightness(0) invert(1);
        }
        
        .bg-success {
            background-color: #1cc88a !important;
        }
        
        .bg-danger {
            background-color: #e74a3b !important;
        }
        
        .bg-info {
            background-color: #36b9cc !important;
        }
        
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
<body @yield('body_attributes')>
    <!-- Toast Container for Notifications -->
    <div class="toast-container position-fixed p-3" style="z-index: 1500; right: 20px; top: 80px;">
        @if(session('success'))
            <div id="successToast" class="toast align-items-center text-white bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body">
                        <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>
        @endif
        
        @if(session('error'))
            <div id="errorToast" class="toast align-items-center text-white bg-danger border-0" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body">
                        <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>
        @endif
        
        @if(session('info'))
            <div id="infoToast" class="toast align-items-center text-white bg-info border-0" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body">
                        <i class="fas fa-info-circle me-2"></i> {{ session('info') }}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>
        @endif
    </div>

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
            
            // Toast Notifications System
            function initializeToasts() {
                // Initialize all toast elements
                const toasts = document.querySelectorAll('.toast');
                
                if (toasts.length > 0) {
                    toasts.forEach(toast => {
                        // Show the toast with animation
                        setTimeout(() => {
                            toast.classList.add('showing');
                            setTimeout(() => {
                                toast.classList.add('show');
                            }, 100);
                            
                            // Auto-hide after 5 seconds
                            setTimeout(() => {
                                toast.classList.remove('show');
                                setTimeout(() => {
                                    toast.remove();
                                }, 500);
                            }, 5000);
                        }, 200);
                    });
                    
                    // Add event listeners to close buttons
                    document.querySelectorAll('.btn-close').forEach(button => {
                        button.addEventListener('click', function() {
                            const toast = this.closest('.toast');
                            toast.classList.remove('show');
                            setTimeout(() => {
                                toast.remove();
                            }, 500);
                        });
                    });
                }
            }
            
            // Initialize toasts on page load
            initializeToasts();
            
            // Apply standardized styling to all alerts in the application
            function standardizeAlerts() {
                // Find all alerts that don't already have proper structure
                $('.alert').each(function() {
                    const $alert = $(this);
                    
                    // Skip alerts that have already been processed
                    if ($alert.data('processed')) return;
                    
                    // Make sure alert is dismissible
                    if (!$alert.hasClass('alert-dismissible')) {
                        $alert.addClass('alert-dismissible fade show');
                    }
                    
                    // Add close button if not present
                    if ($alert.find('.close').length === 0) {
                        $alert.append(
                            '<button type="button" class="close" data-dismiss="alert" aria-label="Close">' +
                            '<span aria-hidden="true">&times;</span>' +
                            '</button>'
                        );
                    }
                    
                    // Wrap content in div if not already wrapped
                    const $children = $alert.contents().filter(function() {
                        // Exclude the close button
                        return !$(this).hasClass('close') && 
                               !$(this).parent().hasClass('close');
                    });
                    
                    if (!$children.parent().is('div') || $children.parent().is($alert)) {
                        $children.wrapAll('<div></div>');
                    }
                    
                    // Mark as processed
                    $alert.data('processed', true);
                    
                    // Auto-dismiss non-error alerts after 10 seconds
                    if (!$alert.hasClass('alert-danger') && !$alert.attr('id')) {
                        setTimeout(function() {
                            $alert.fadeOut('slow', function() {
                                $(this).remove();
                            });
                        }, 10000);
                    }
                });
            }
            
            // Run when DOM is ready and after AJAX calls
            standardizeAlerts();
            $(document).ajaxComplete(function() {
                standardizeAlerts();
            });
        });
    </script>
    
    @stack('scripts')
    
    <!-- Utilizamos nuestro script personalizado datatables-custom.js para inicializar las tablas -->
</body>
</html>
