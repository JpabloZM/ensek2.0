<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Nueva Solicitud de Servicio</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #4e73df;
            color: white;
            padding: 15px;
            text-align: center;
        }
        .content {
            background-color: #f8f9fc;
            padding: 20px;
            border-radius: 5px;
        }
        .detail {
            background-color: white;
            padding: 15px;
            margin-bottom: 15px;
            border-radius: 5px;
            border-left: 4px solid #4e73df;
        }
        .footer {
            text-align: center;
            margin-top: 20px;
            font-size: 12px;
            color: #6c757d;
        }
        .btn {
            display: inline-block;
            background-color: #4e73df;
            color: white;
            text-decoration: none;
            padding: 10px 20px;
            border-radius: 5px;
            margin-top: 15px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Nueva Solicitud de Servicio</h1>
        </div>
        <div class="content">
            <p>Se ha recibido una nueva solicitud de servicio en el sistema. A continuación, los detalles:</p>
            
            <div class="detail">
                <p><strong>Servicio:</strong> {{ $serviceRequest->service->name }}</p>
                <p><strong>Cliente:</strong> {{ $serviceRequest->client_name }}</p>
                <p><strong>Teléfono:</strong> {{ $serviceRequest->client_phone }}</p>
                <p><strong>Email:</strong> {{ $serviceRequest->client_email }}</p>
                <p><strong>Dirección:</strong> {{ $serviceRequest->address }}</p>
                <p><strong>Descripción:</strong> {{ $serviceRequest->description }}</p>
                <p><strong>Fecha de solicitud:</strong> {{ $serviceRequest->created_at->format('d/m/Y H:i') }}</p>
            </div>
            
            <p>Por favor, revise esta solicitud y asigne un técnico a la brevedad.</p>
            
            <div style="text-align: center;">
                <a href="{{ route('admin.service-requests.show', $serviceRequest->id) }}" class="btn">Ver Solicitud</a>
            </div>
        </div>
        <div class="footer">
            <p>Este es un mensaje automático, por favor no responda a este correo.</p>
            <p>&copy; {{ date('Y') }} SISTEMA EMPRESA</p>
        </div>
    </div>
</body>
</html>
