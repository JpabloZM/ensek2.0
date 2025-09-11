<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>EMPRESA - Solicitud Recibida</title>
    
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
            background-color: #f8f9fc;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        
        .thank-you-container {
            max-width: 700px;
            margin: 0 auto;
            padding: 40px 20px;
            text-align: center;
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        
        .success-icon {
            font-size: 80px;
            color: #1cc88a;
            margin-bottom: 20px;
        }
        
        .next-steps {
            background-color: white;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            margin-top: 30px;
        }
        
        .step {
            margin-bottom: 20px;
            padding-left: 50px;
            position: relative;
            text-align: left;
        }
        
        .step-number {
            position: absolute;
            left: 0;
            top: 0;
            width: 36px;
            height: 36px;
            background-color: #4e73df;
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
        }
        
        footer {
            background-color: #222;
            color: white;
            padding: 20px 0;
            margin-top: 40px;
        }
    </style>
</head>
<body>
    <div class="thank-you-container">
        <i class="fas fa-check-circle success-icon"></i>
        <h1 class="mb-4">¡Gracias por su solicitud!</h1>
        <p class="lead mb-5">Hemos recibido su solicitud de servicio correctamente. Uno de nuestros representantes se pondrá en contacto con usted a la brevedad para confirmar la fecha y hora de la visita técnica.</p>
        
        <div class="next-steps">
            <h3 class="mb-4">Próximos pasos</h3>
            
            <div class="step">
                <div class="step-number">1</div>
                <h5>Revisión de la solicitud</h5>
                <p>Nuestro equipo revisará los detalles de su solicitud y asignará un técnico especializado.</p>
            </div>
            
            <div class="step">
                <div class="step-number">2</div>
                <h5>Contacto</h5>
                <p>Nos comunicaremos con usted para confirmar la fecha y hora de la visita técnica.</p>
            </div>
            
            <div class="step">
                <div class="step-number">3</div>
                <h5>Visita técnica</h5>
                <p>El técnico asignado visitará su dirección en la fecha y hora acordadas para realizar el servicio solicitado.</p>
            </div>
        </div>
        
        <div class="mt-5">
            <a href="{{ route('public.landing') }}" class="btn btn-primary btn-lg px-5">Volver al inicio</a>
        </div>
    </div>
    
    <footer>
        <div class="container text-center">
            <p class="mb-0">&copy; {{ date('Y') }} EMPRESA. Todos los derechos reservados.</p>
        </div>
    </footer>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
