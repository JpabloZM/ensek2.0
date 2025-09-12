<!DOCTYPE html>
<html>
<head>
    <title>Confirmar Cita</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #4e73df;
            color: white;
            padding: 15px;
            text-align: center;
            border-radius: 5px 5px 0 0;
        }
        .content {
            background-color: #f8f9fc;
            padding: 20px;
            border-left: 1px solid #ddd;
            border-right: 1px solid #ddd;
        }
        .footer {
            background-color: #eaecf4;
            padding: 15px;
            text-align: center;
            font-size: 12px;
            color: #666;
            border-radius: 0 0 5px 5px;
            border: 1px solid #ddd;
        }
        .info-box {
            background-color: #ffffff;
            border: 1px solid #ddd;
            padding: 15px;
            margin: 15px 0;
            border-radius: 5px;
        }
        .btn {
            display: inline-block;
            background-color: #4e73df;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 20px;
        }
        .text-center {
            text-align: center;
        }
        .action-box {
            text-align: center;
            margin: 30px 0;
        }
        .btn-large {
            padding: 12px 30px;
            font-size: 16px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>Confirmar su Cita</h2>
    </div>
    
    <div class="content">
        <p>Estimado(a) <strong>{{ $appointment->serviceRequest->client->name }}</strong>,</p>
        <p>Por favor confirme su cita de servicio técnico haciendo clic en el botón a continuación:</p>
        
        <div class="info-box">
            <h3>Detalles de la Cita:</h3>
            <p><strong>Servicio:</strong> {{ $appointment->serviceRequest->service->name }}</p>
            <p><strong>Fecha:</strong> {{ $appointment->date->format('d/m/Y') }}</p>
            <p><strong>Hora:</strong> {{ $appointment->start_time->format('h:i A') }} - {{ $appointment->end_time->format('h:i A') }}</p>
            <p><strong>Dirección:</strong> {{ $appointment->serviceRequest->address }}</p>
            <p><strong>Técnico asignado:</strong> {{ $appointment->technician->name }}</p>
            
            @if($appointment->notes)
                <p><strong>Notas:</strong> {{ $appointment->notes }}</p>
            @endif
        </div>
        
        <div class="action-box">
            <a href="{{ $confirmationLink }}" class="btn btn-large">
                Confirmar mi Asistencia
            </a>
        </div>
        
        <p>Al confirmar, nos ayuda a brindarle un mejor servicio y planificar adecuadamente la agenda de nuestros técnicos.</p>
        
        <p>Si necesita reprogramar o cancelar su cita, por favor contáctenos lo antes posible.</p>
    </div>
    
    <div class="footer">
        <p>Este es un mensaje automático, por favor no responda a este correo.</p>
        <p>&copy; {{ date('Y') }} EMPRESA - Todos los derechos reservados</p>
    </div>
</body>
</html>