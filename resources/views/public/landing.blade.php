<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>EMPRESA - Servicios Técnicos</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Nunito', sans-serif;
            color: #333;
            overflow-x: hidden;
            position: relative;
        }
        
        @keyframes highlight {
            0% { box-shadow: 0 0 0 rgba(78, 115, 223, 0); }
            50% { box-shadow: 0 0 20px rgba(78, 115, 223, 0.8); }
            100% { box-shadow: 0 0 0 rgba(78, 115, 223, 0); }
        }
        
        /* Hero Section */
        .hero {
            background: linear-gradient(rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.7)), url('/img/hero-background.jpg');
            background-size: cover;
            background-position: center;
            color: white;
            padding: 100px 0;
            margin-bottom: 60px;
        }
        
        /* Navbar */
        .navbar {
            transition: all 0.3s ease;
            background-color: transparent !important;
        }
        
        .navbar.scrolled {
            background-color: white !important;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        
        .navbar.scrolled .navbar-brand,
        .navbar.scrolled .nav-link {
            color: #333 !important;
        }
        
        /* Service Cards */
        .service-card {
            border-radius: 10px;
            overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            margin-bottom: 30px;
        }
        
        .service-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }
        
        .service-icon {
            font-size: 40px;
            margin-bottom: 20px;
            color: #4e73df;
        }
        
        /* Form Styles */
        .form-container {
            background-color: #f8f9fc;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
        }
        
        .form-control:focus {
            border-color: #4e73df;
            box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
        }
        
        /* Footer */
        footer {
            background-color: #222;
            color: white;
            padding: 60px 0 30px;
        }
        
        .footer-links h5 {
            color: #4e73df;
            margin-bottom: 20px;
        }
        
        .footer-links ul {
            list-style-type: none;
            padding: 0;
        }
        
        .footer-links li {
            margin-bottom: 10px;
        }
        
        .footer-links a {
            color: #ccc;
            text-decoration: none;
            transition: color 0.3s ease;
        }
        
        .footer-links a:hover {
            color: white;
        }
        
        .social-icons a {
            display: inline-block;
            margin-right: 15px;
            color: white;
            font-size: 24px;
            transition: transform 0.3s ease;
        }
        
        .social-icons a:hover {
            transform: translateY(-5px);
        }
        
        /* Testimonials */
        .testimonial {
            background-color: white;
            border-radius: 10px;
            padding: 25px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            margin-bottom: 20px;
        }
        
        .testimonial-img {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            object-fit: cover;
            margin-right: 15px;
        }
        
        /* About Us */
        .about-icon {
            font-size: 30px;
            color: #4e73df;
            margin-bottom: 20px;
        }
        
        /* Back to top */
        .back-to-top {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background-color: #4e73df;
            color: white;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            z-index: 99;
            transition: background-color 0.3s;
            opacity: 0;
            visibility: hidden;
        }
        
        .back-to-top.show {
            opacity: 1;
            visibility: visible;
        }
        
        .back-to-top:hover {
            background-color: #2e59d9;
        }
    </style>
</head>
<body>
    <!-- Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top">
        <div class="container">
            <a class="navbar-brand fw-bold" href="#">
                <i class="fas fa-tools me-2"></i>EMPRESA
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="#inicio">Inicio</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#servicios">Servicios</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#nosotros">Nosotros</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#testimonios">Testimonios</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#contacto">Contacto</a>
                    </li>
                    @guest
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('login') }}">
                                <i class="fas fa-sign-in-alt me-1"></i> Iniciar Sesión
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('register') }}">
                                <i class="fas fa-user-plus me-1"></i> Registrarse
                            </a>
                        </li>
                    @else
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-user-circle me-1"></i> {{ Auth::user()->name }}
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                @if(Auth::user()->role->name === 'Administrador')
                                    <li><a class="dropdown-item" href="{{ route('admin.dashboard') }}">Panel de Administración</a></li>
                                @elseif(Auth::user()->role->name === 'Técnico')
                                    <li><a class="dropdown-item" href="{{ route('technician.dashboard') }}">Panel de Técnico</a></li>
                                @endif
                                <li><a class="dropdown-item" href="#solicitar">Solicitar Servicio</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('logout') }}"
                                       onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                        Cerrar Sesión
                                    </a>
                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                        @csrf
                                    </form>
                                </li>
                            </ul>
                        </li>
                    @endguest
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero d-flex align-items-center" id="inicio">
        <div class="container text-center">
            <h1 class="display-4 fw-bold mb-4">Servicios Técnicos Profesionales</h1>
            <p class="lead mb-5">Soluciones efectivas y rápidas para todas sus necesidades técnicas y de mantenimiento</p>
            <a href="#solicitar" class="btn btn-primary btn-lg px-5 py-3">Solicitar Servicio</a>
        </div>
    </section>

    <!-- Services Section -->
    <section class="py-5" id="servicios">
        <div class="container">
            <div class="row text-center mb-5">
                <div class="col-12">
                    <h2 class="fw-bold">Nuestros Servicios</h2>
                    <p class="text-muted">Ofrecemos una amplia gama de servicios técnicos de alta calidad</p>
                </div>
            </div>
            <div class="row">
                @foreach ($services as $service)
                <div class="col-md-4">
                    <div class="card service-card h-100">
                        <div class="card-body text-center p-5">
                            <div class="service-icon">
                                <i class="fas fa-{{ $service->icon ?? 'wrench' }}"></i>
                            </div>
                            <h4 class="card-title">{{ $service->name }}</h4>
                            <p class="card-text text-muted">{{ $service->description }}</p>
                            <a href="#solicitar" class="btn btn-outline-primary mt-3" onclick="preSelectService('{{ $service->id }}', '{{ $service->name }}')">Solicitar</a>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- About Us Section -->
    <section class="py-5 bg-light" id="nosotros">
        <div class="container">
            <div class="row mb-5">
                <div class="col-12 text-center">
                    <h2 class="fw-bold">¿Quiénes Somos?</h2>
                    <p class="text-muted">Conoce más sobre nuestra empresa y nuestros valores</p>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-4 mb-md-0">
                    <img src="/img/about-us.jpg" alt="Nuestro equipo" class="img-fluid rounded">
                </div>
                <div class="col-md-6">
                    <h3 class="mb-4">Nuestra Historia</h3>
                    <p>Fundada en 2010, EMPRESA ha sido líder en servicios técnicos por más de una década. Nuestro compromiso es brindar soluciones eficientes y de calidad a todos nuestros clientes.</p>
                    <p>Contamos con un equipo de técnicos altamente capacitados y certificados en diversas áreas, lo que nos permite ofrecer un servicio integral y profesional.</p>
                    
                    <div class="row mt-5">
                        <div class="col-md-6 mb-4">
                            <div class="about-icon">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <h5>Calidad Garantizada</h5>
                            <p class="text-muted">Todos nuestros servicios cuentan con garantía.</p>
                        </div>
                        <div class="col-md-6 mb-4">
                            <div class="about-icon">
                                <i class="fas fa-user-tie"></i>
                            </div>
                            <h5>Personal Calificado</h5>
                            <p class="text-muted">Técnicos certificados y capacitados.</p>
                        </div>
                        <div class="col-md-6 mb-4">
                            <div class="about-icon">
                                <i class="fas fa-tools"></i>
                            </div>
                            <h5>Equipos Modernos</h5>
                            <p class="text-muted">Utilizamos herramientas de última generación.</p>
                        </div>
                        <div class="col-md-6 mb-4">
                            <div class="about-icon">
                                <i class="fas fa-clock"></i>
                            </div>
                            <h5>Servicio Rápido</h5>
                            <p class="text-muted">Respuesta inmediata a sus necesidades.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonials Section -->
    <section class="py-5" id="testimonios">
        <div class="container">
            <div class="row text-center mb-5">
                <div class="col-12">
                    <h2 class="fw-bold">Testimonios</h2>
                    <p class="text-muted">Lo que dicen nuestros clientes sobre nuestros servicios</p>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4 mb-4">
                    <div class="testimonial h-100">
                        <div class="d-flex align-items-center mb-3">
                            <img src="https://randomuser.me/api/portraits/men/32.jpg" alt="Cliente" class="testimonial-img">
                            <div>
                                <h5 class="mb-0">Juan Pérez</h5>
                                <small class="text-muted">Cliente desde 2019</small>
                            </div>
                        </div>
                        <p class="mb-0">"Excelente servicio. Llegaron puntualmente y resolvieron el problema rápidamente. Definitivamente los recomendaría."</p>
                        <div class="mt-2">
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="testimonial h-100">
                        <div class="d-flex align-items-center mb-3">
                            <img src="https://randomuser.me/api/portraits/women/44.jpg" alt="Cliente" class="testimonial-img">
                            <div>
                                <h5 class="mb-0">María García</h5>
                                <small class="text-muted">Cliente desde 2020</small>
                            </div>
                        </div>
                        <p class="mb-0">"El técnico fue muy amable y profesional. Me explicó todo el proceso y me dio recomendaciones para evitar futuros problemas."</p>
                        <div class="mt-2">
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star-half-alt text-warning"></i>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="testimonial h-100">
                        <div class="d-flex align-items-center mb-3">
                            <img src="https://randomuser.me/api/portraits/men/67.jpg" alt="Cliente" class="testimonial-img">
                            <div>
                                <h5 class="mb-0">Carlos López</h5>
                                <small class="text-muted">Cliente desde 2018</small>
                            </div>
                        </div>
                        <p class="mb-0">"He contratado sus servicios varias veces y siempre quedé satisfecho. Son rápidos, eficientes y sus precios son justos."</p>
                        <div class="mt-2">
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Request Service Form Section -->
    <section class="py-5 bg-light" id="solicitar">
        <div class="container">
            <div class="row mb-5">
                <div class="col-12 text-center">
                    <h2 class="fw-bold">Solicitar Servicio</h2>
                    <p class="text-muted">Complete el formulario y nos pondremos en contacto con usted a la brevedad</p>
                </div>
            </div>
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="form-container">
                        @guest
                            <div class="text-center py-4">
                                <div class="mb-4">
                                    <i class="fas fa-user-lock fa-4x text-primary mb-3"></i>
                                    <h4>Necesita iniciar sesión para solicitar un servicio</h4>
                                    <p class="text-muted">Para brindarle un mejor servicio y llevar un seguimiento adecuado de sus solicitudes, es necesario que tenga una cuenta.</p>
                                </div>
                                <div class="d-flex flex-column flex-md-row justify-content-center gap-3">
                                    <a href="{{ route('login') }}" class="btn btn-primary btn-lg">Iniciar Sesión</a>
                                    <a href="{{ route('register') }}" class="btn btn-outline-primary btn-lg">Registrarse</a>
                                </div>
                            </div>
                        @else
                            <div class="alert alert-info mb-4">
                                <strong>Bienvenido(a), {{ Auth::user()->name }}!</strong>
                                <p class="mb-0">Complete el siguiente formulario para solicitar un servicio.</p>
                            </div>
                            
                            @if (session('error_general'))
                                <div class="alert alert-danger mb-4">
                                    {{ session('error_general') }}
                                </div>
                            @endif
                            
                            @if ($errors->any())
                                <div class="alert alert-danger">
                                    <ul class="mb-0">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                            
                            <!-- Formulario simplificado con feedback visual -->
                            <form id="serviceRequestForm" action="{{ route('public.service-request') }}" method="POST">
                                @csrf
                                
                                <div class="mb-3">
                                    <label for="service_id" class="form-label">Servicio que necesita*</label>
                                    <select class="form-select" id="service_id" name="service_id" required>
                                        <option value="">Seleccione un servicio</option>
                                        @foreach ($services as $service)
                                            <option value="{{ $service->id }}" {{ old('service_id') == $service->id ? 'selected' : '' }}>
                                                {{ $service->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="address" class="form-label">Dirección donde se realizará el servicio*</label>
                                    <input type="text" class="form-control" id="address" name="address" value="{{ old('address') }}" required>
                                </div>
                                
                                <div class="mb-4">
                                    <label for="description" class="form-label">Descripción del problema*</label>
                                    <textarea class="form-control" id="description" name="description" rows="5" required>{{ old('description') }}</textarea>
                                    <div class="form-text">Por favor, describa el problema con el mayor detalle posible.</div>
                                </div>
                                
                                <div class="mb-3 form-check">
                                    <input type="checkbox" class="form-check-input" id="terms" name="terms" required>
                                    <label class="form-check-label" for="terms">
                                        Acepto los términos y condiciones del servicio
                                    </label>
                                </div>
                                
                                <div class="text-center">
                                    <button type="submit" id="submitBtn" class="btn btn-primary btn-lg px-5">
                                        <span>Enviar Solicitud</span>
                                        <span id="submitSpinner" class="spinner-border spinner-border-sm ms-2 d-none" role="status" aria-hidden="true"></span>
                                    </button>
                                </div>
                            </form>
                            
                            <script>
                                // Mostrar indicador de carga cuando se envía el formulario
                                document.getElementById('serviceRequestForm').addEventListener('submit', function() {
                                    // Mostrar el spinner
                                    document.getElementById('submitSpinner').classList.remove('d-none');
                                    
                                    // Cambiar el texto del botón
                                    document.getElementById('submitBtn').querySelector('span').textContent = 'Enviando...';
                                    
                                    // Deshabilitar el botón para evitar múltiples envíos
                                    document.getElementById('submitBtn').disabled = true;
                                });
                            </script>
                        @endguest
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section class="py-5" id="contacto">
        <div class="container">
            <div class="row text-center mb-5">
                <div class="col-12">
                    <h2 class="fw-bold">Contáctenos</h2>
                    <p class="text-muted">Estamos a su disposición para cualquier consulta</p>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4 mb-4 mb-md-0">
                    <div class="card h-100">
                        <div class="card-body text-center p-4">
                            <div class="service-icon">
                                <i class="fas fa-map-marker-alt"></i>
                            </div>
                            <h4>Dirección</h4>
                            <p class="mb-0">Calle Principal #123<br>Ciudad, CP 12345</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4 mb-md-0">
                    <div class="card h-100">
                        <div class="card-body text-center p-4">
                            <div class="service-icon">
                                <i class="fas fa-phone-alt"></i>
                            </div>
                            <h4>Teléfono</h4>
                            <p class="mb-0">+1 234 567 8900<br>+1 234 567 8901</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100">
                        <div class="card-body text-center p-4">
                            <div class="service-icon">
                                <i class="fas fa-envelope"></i>
                            </div>
                            <h4>Email</h4>
                            <p class="mb-0">info@empresa.com<br>soporte@empresa.com</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="row mt-5">
                <div class="col-12">
                    <div class="ratio ratio-21x9" style="height: 400px;">
                        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d387193.3059445135!2d-74.25986613799748!3d40.69714941774136!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x89c24fa5d33f083b%3A0xc80b8f06e177fe62!2sNew%20York%2C%20NY%2C%20USA!5e0!3m2!1sen!2sco!4v1631452424174!5m2!1sen!2sco" width="100%" height="400" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-5 mb-md-0 footer-links">
                    <h5>EMPRESA</h5>
                    <p>Brindando servicios técnicos de calidad desde 2010. Nuestro compromiso es ofrecer soluciones efectivas para todas sus necesidades técnicas.</p>
                    <div class="social-icons mt-4">
                        <a href="#"><i class="fab fa-facebook-f"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-linkedin-in"></i></a>
                    </div>
                </div>
                <div class="col-md-4 mb-5 mb-md-0 footer-links">
                    <h5>Enlaces Rápidos</h5>
                    <ul>
                        <li><a href="#inicio">Inicio</a></li>
                        <li><a href="#servicios">Servicios</a></li>
                        <li><a href="#nosotros">Nosotros</a></li>
                        <li><a href="#testimonios">Testimonios</a></li>
                        <li><a href="#contacto">Contacto</a></li>
                    </ul>
                </div>
                <div class="col-md-4 footer-links">
                    <h5>Servicios</h5>
                    <ul>
                        @foreach ($services as $service)
                            <li><a href="#solicitar" onclick="preSelectService('{{ $service->id }}', '{{ $service->name }}')">{{ $service->name }}</a></li>
                        @endforeach
                    </ul>
                </div>
            </div>
            <hr class="my-4 bg-light">
            <div class="row">
                <div class="col-md-6 text-center text-md-start">
                    <p class="mb-0">&copy; {{ date('Y') }} EMPRESA. Todos los derechos reservados.</p>
                </div>
                <div class="col-md-6 text-center text-md-end">
                    <p class="mb-0">
                        <a href="#" class="text-white me-3">Términos y Condiciones</a>
                        <a href="#" class="text-white me-3">Política de Privacidad</a>
                        @guest
                            <a href="{{ route('login') }}" class="text-white">Acceso <i class="fas fa-sign-in-alt fa-xs"></i></a>
                        @else
                            @if(Auth::user()->role->name === 'Administrador')
                                <a href="{{ route('admin.dashboard') }}" class="text-white">Panel de Administración <i class="fas fa-tachometer-alt fa-xs"></i></a>
                            @elseif(Auth::user()->role->name === 'Técnico')
                                <a href="{{ route('technician.dashboard') }}" class="text-white">Panel de Técnico <i class="fas fa-tools fa-xs"></i></a>
                            @endif
                        @endguest
                    </p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Back to Top Button -->
    <a href="#" class="back-to-top" id="backToTop">
        <i class="fas fa-chevron-up"></i>
    </a>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Navbar scroll effect
        window.addEventListener('scroll', function() {
            const navbar = document.querySelector('.navbar');
            if (window.scrollY > 50) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });
        
        // Back to top button
        window.addEventListener('scroll', function() {
            const backToTopBtn = document.getElementById('backToTop');
            if (window.scrollY > 300) {
                backToTopBtn.classList.add('show');
            } else {
                backToTopBtn.classList.remove('show');
            }
        });
        
        document.getElementById('backToTop').addEventListener('click', function(e) {
            e.preventDefault();
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
        
        // Smooth scrolling for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                e.preventDefault();
                
                const targetId = this.getAttribute('href');
                if (targetId === '#') return;
                
                const targetElement = document.querySelector(targetId);
                if (targetElement) {
                    window.scrollTo({
                        top: targetElement.offsetTop - 70,
                        behavior: 'smooth'
                    });
                }
            });
        });
        
        // Pre-select service in form
        function preSelectService(serviceId, serviceName) {
            const serviceSelect = document.getElementById('service_id');
            if (serviceSelect) {
                serviceSelect.value = serviceId;
                
                // Highlight the form
                const formContainer = document.querySelector('.form-container');
                if (formContainer) {
                    formContainer.style.animation = 'highlight 1.5s';
                    
                    setTimeout(() => {
                        formContainer.style.animation = '';
                    }, 1500);
                }
                
                // Scroll to form
                document.querySelector('#solicitar').scrollIntoView({ 
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        }
        
        // Removed duplicate form validation - now handled in the form's own script
        
        function isValidEmail(email) {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return emailRegex.test(email);
        }
    </script>
</body>
</html>
