<!DOCTYPE html>
<html>
<head>
    <title>Recordatorio de Cita</title>
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
            background-color: #2ecc71;
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
            background-color: #2ecc71;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 20px;
        }
        .text-center {
            text-align: center;
        }
        .alert {
            background-color: #d4edda;
            color: #155724;
            padding: 10px;
            border-radius: 5px;
            margin: 15px 0;
            border: 1px solid #c3e6cb;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>Recordatorio de Cita</h2>
    </div>
    
    <div class="content">
        @if($isTechnician)
            <p>Hola <strong>{{ $appointment->technician->name }}</strong>,</p>
            <p>Este es un recordatorio de una cita de servicio técnico programada para mañana.</p>
        @else
            <p>Estimado(a) <strong>{{ $appointment->serviceRequest->client->name }}</strong>,</p>
            <p>Te recordamos que tienes una cita programada para mañana.</p>
        @endif
        
        <div class="alert">
            <p><strong>Recordatorio:</strong> Tu cita está programada para mañana. Por favor confirma tu asistencia.</p>
        </div>
        
        <div class="info-box">
            <h3>Detalles de la Cita:</h3>
            <p><strong>Servicio:</strong> {{ $appointment->serviceRequest->service->name }}</p>
            <p><strong>Fecha:</strong> {{ $appointment->date->format('d/m/Y') }}</p>
            <p><strong>Hora:</strong> {{ $appointment->start_time->format('h:i A') }} - {{ $appointment->end_time->format('h:i A') }}</p>
            <p><strong>Dirección:</strong> {{ $appointment->serviceRequest->address }}</p>
            
            @if(!$isTechnician)
                <p><strong>Técnico asignado:</strong> {{ $appointment->technician->name }}</p>
            @else
                <p><strong>Cliente:</strong> {{ $appointment->serviceRequest->client->name }}</p>
                <p><strong>Teléfono:</strong> {{ $appointment->serviceRequest->client->phone }}</p>
            @endif
            
            @if($appointment->notes)
                <p><strong>Notas:</strong> {{ $appointment->notes }}</p>
            @endif
        </div>
        
        @if(!$isTechnician)
            <p class="text-center">
                <a href="{{ route('appointments.confirm', ['id' => $appointment->id, 'token' => hash('sha256', $appointment->id . $appointment->created_at)]) }}" class="btn">
                    Confirmar Asistencia
                </a>
            </p>
        @endif
    </div>
    
    <div class="footer">
        <p>Este es un mensaje automático, por favor no responda a este correo.</p>
        <p>Si necesitas hacer algún cambio, por favor contáctanos directamente.</p>
        <p>&copy; {{ date('Y') }} EMPRESA - Todos los derechos reservados</p>
    </div>
</body>
</html>