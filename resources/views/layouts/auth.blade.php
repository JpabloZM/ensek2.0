<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'ensek') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        :root {
            --ensek-green-light: #87c947;
            --ensek-green-dark: #004122;
            --ensek-gray: #2c2e35;
            --ensek-white: #ffffff;
        }
        
        body {
            font-family: 'Nunito', sans-serif;
            color: var(--ensek-gray);
            background-color: var(--ensek-white);
        }
        
        .navbar {
            background-color: var(--ensek-green-dark) !important;
        }
        
        .text-ensek-logo {
            color: var(--ensek-white);
        }
        
        .text-ensek-logo-green {
            color: var(--ensek-green-light);
        }
        
        .auth-card {
            max-width: 500px;
            margin: 0 auto;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }
        
        .auth-card .card-header {
            background-color: var(--ensek-green-dark) !important;
            color: white;
        }
        
        .btn-primary {
            background-color: var(--ensek-green-light) !important;
            border-color: var(--ensek-green-light) !important;
        }
        
        .btn-primary:hover, .btn-primary:focus, .btn-primary:active {
            background-color: var(--ensek-green-dark) !important;
            border-color: var(--ensek-green-dark) !important;
            color: var(--ensek-white) !important;
        }
        
        .form-control:focus {
            border-color: var(--ensek-green-light);
            box-shadow: 0 0 0 0.2rem rgba(135, 201, 71, 0.25);
        }
        
        .form-check-input:checked {
            background-color: var(--ensek-green-light);
            border-color: var(--ensek-green-light);
        }
        
        .form-check-input:focus {
            border-color: var(--ensek-green-light);
            box-shadow: 0 0 0 0.2rem rgba(135, 201, 71, 0.25);
        }
        
        a {
            color: var(--ensek-green-dark);
        }
        
        a:hover {
            color: var(--ensek-green-light);
        }
        
        .link-ensek {
            color: var(--ensek-green-dark);
            text-decoration: none;
        }
        
        .link-ensek:hover {
            color: var(--ensek-green-light);
            transition: color 0.3s ease;
        }
    </style>
</head>
<body>
    <div id="app">
        <!-- Navigation Bar -->
        <nav class="navbar navbar-expand-md navbar-dark shadow-sm">
            <div class="container">
                <a class="navbar-brand fw-bold" href="{{ url('/') }}">
                    <i class="fas fa-recycle me-2"></i><span class="text-ensek-logo">e</span><span class="text-ensek-logo-green">ns</span><span class="text-ensek-logo">e</span><span class="text-ensek-logo-green">k</span>
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    <ul class="navbar-nav me-auto">
                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="{{ url('/') }}">
                                <i class="fas fa-home me-1"></i> Volver al sitio
                            </a>
                        </li>
                        @if (Route::has('login') && Route::currentRouteName() !== 'login')
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('login') }}">{{ __('Iniciar sesión') }}</a>
                            </li>
                        @endif
                        @if (Route::has('register') && Route::currentRouteName() !== 'register')
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('register') }}">{{ __('Registrarse') }}</a>
                            </li>
                        @endif
                    </ul>
                </div>
            </div>
        </nav>

        <main class="py-4">
            @yield('content')
        </main>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>