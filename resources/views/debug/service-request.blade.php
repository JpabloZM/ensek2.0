<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Depuración de Solicitud de Servicio</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row">
            <div class="col-12">
                <h1>Datos recibidos en el formulario</h1>
                <hr>
                
                <h3>Request</h3>
                <pre>{{ print_r($request->all(), true) }}</pre>
                
                <h3>Usuario autenticado</h3>
                <pre>{{ Auth::check() ? json_encode(Auth::user()->toArray(), JSON_PRETTY_PRINT) : 'No autenticado' }}</pre>
                
                <h3>Sesión</h3>
                <pre>{{ json_encode(session()->all(), JSON_PRETTY_PRINT) }}</pre>
            </div>
        </div>
        
        <div class="mt-4">
            <a href="{{ url()->previous() }}" class="btn btn-primary">Volver atrás</a>
        </div>
    </div>
</body>
</html>
