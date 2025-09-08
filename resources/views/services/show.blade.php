@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Detalles del Servicio</h1>
        <div>
            <a href="{{ route('admin.services.edit', $service->id) }}" class="btn btn-warning btn-sm">
                <i class="fas fa-edit fa-sm"></i> Editar
            </a>
            <a href="{{ route('admin.services.index') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left fa-sm"></i> Volver
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="row">
        <!-- Información básica -->
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Información del Servicio</h6>
                    <span class="badge badge-{{ $service->active ? 'success' : 'danger' }} badge-lg">
                        {{ $service->active ? 'Activo' : 'Inactivo' }}
                    </span>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <h6 class="font-weight-bold">Nombre:</h6>
                                <p>{{ $service->name }}</p>
                            </div>
                            <div class="mb-3">
                                <h6 class="font-weight-bold">Precio (sin IVA):</h6>
                                <p>{{ $service->formatted_price }}</p>
                            </div>
                            <div class="mb-3">
                                <h6 class="font-weight-bold">Tasa de impuesto:</h6>
                                <p>{{ number_format($service->tax_rate, 2) }}%</p>
                            </div>
                            <div class="mb-3">
                                <h6 class="font-weight-bold">Precio (con IVA):</h6>
                                <p>{{ $service->formatted_price_with_tax }}</p>
                            </div>
                            <div class="mb-3">
                                <h6 class="font-weight-bold">Duración:</h6>
                                <p>{{ $service->duration }} minutos</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <h6 class="font-weight-bold">Requiere técnico especializado:</h6>
                                <p>{{ $service->requires_technician_approval ? 'Sí' : 'No' }}</p>
                            </div>
                            <div class="mb-3">
                                <h6 class="font-weight-bold">Estado:</h6>
                                <p>
                                    <span class="badge badge-{{ $service->active ? 'success' : 'danger' }} badge-lg">
                                        {{ $service->active ? 'Activo' : 'Inactivo' }}
                                    </span>
                                </p>
                            </div>
                            <div class="mb-3">
                                <h6 class="font-weight-bold">Fecha de Creación:</h6>
                                <p>{{ $service->created_at->format('d/m/Y H:i') }}</p>
                            </div>
                            <div class="mb-3">
                                <h6 class="font-weight-bold">Última Actualización:</h6>
                                <p>{{ $service->updated_at->format('d/m/Y H:i') }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <h6 class="font-weight-bold">Descripción:</h6>
                        <p>{{ $service->description }}</p>
                    </div>
                    
                    @if($service->special_requirements)
                    <div class="mb-3">
                        <h6 class="font-weight-bold">Requisitos especiales:</h6>
                        <p>{{ $service->special_requirements }}</p>
                    </div>
                    @endif
                    
                    @if($service->materials_included)
                    <div class="mb-3">
                        <h6 class="font-weight-bold">Materiales incluidos:</h6>
                        <p>{{ $service->materials_included }}</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        
        <!-- Estadísticas -->
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Estadísticas</h6>
                </div>
                <div class="card-body">
                    <div class="chart-pie pt-4 pb-2">
                        <canvas id="servicePieChart"></canvas>
                    </div>
                    <div class="mt-4 text-center small">
                        <span class="mr-2">
                            <i class="fas fa-circle text-warning"></i> Pendientes ({{ $pendientes }})
                        </span>
                        <span class="mr-2">
                            <i class="fas fa-circle text-info"></i> Agendadas ({{ $agendadas }})
                        </span>
                        <span class="mr-2">
                            <i class="fas fa-circle text-success"></i> Completadas ({{ $completadas }})
                        </span>
                        <span class="mr-2">
                            <i class="fas fa-circle text-danger"></i> Canceladas ({{ $canceladas }})
                        </span>
                    </div>
                    <div class="mt-4 text-center">
                        <h5>Total de Solicitudes: {{ $total }}</h5>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Solicitudes Recientes -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Solicitudes Recientes</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Cliente</th>
                            <th>Fecha</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($service->serviceRequests()->latest()->take(5)->get() as $request)
                            <tr>
                                <td>{{ $request->client_name }}</td>
                                <td>{{ $request->created_at->format('d/m/Y H:i') }}</td>
                                <td>
                                    @php
                                        $statusClass = 'secondary';
                                        switch($request->status) {
                                            case 'pendiente': $statusClass = 'warning'; break;
                                            case 'agendado': $statusClass = 'info'; break;
                                            case 'completado': $statusClass = 'success'; break;
                                            case 'cancelado': $statusClass = 'danger'; break;
                                        }
                                    @endphp
                                    <span class="badge badge-{{ $statusClass }} badge-lg">
                                        {{ ucfirst($request->status) }}
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('admin.service-requests.show', $request->id) }}" class="btn btn-info btn-sm">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center">No hay solicitudes registradas para este servicio</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($total > 5)
                <div class="text-center mt-3">
                    <a href="{{ route('admin.service-requests.filter') }}?service_id={{ $service->id }}" class="btn btn-primary btn-sm">
                        Ver todas las solicitudes
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@2.9.4/dist/Chart.min.js"></script>
<script type="text/javascript">
    // Pasamos los datos de PHP a JavaScript mediante una variable global
    var chartDataPendientes = parseInt("{{ $pendientes }}");
    var chartDataAgendadas = parseInt("{{ $agendadas }}");
    var chartDataCompletadas = parseInt("{{ $completadas }}");
    var chartDataCanceladas = parseInt("{{ $canceladas }}");
    
    // Gráfico de solicitudes
    document.addEventListener('DOMContentLoaded', function() {
        var ctx = document.getElementById("servicePieChart");
        
        // Verificar si hay datos disponibles
        var hayDatos = (chartDataPendientes > 0 || chartDataAgendadas > 0 || 
                    chartDataCompletadas > 0 || chartDataCanceladas > 0);
                
        if (!hayDatos) {
            // Si no hay datos, mostrar mensaje
            var noDataText = document.createElement('div');
            noDataText.className = 'text-center text-muted p-4';
            noDataText.innerHTML = '<i class="fas fa-chart-pie fa-3x mb-3"></i><br>No hay datos disponibles';
            ctx.parentNode.replaceChild(noDataText, ctx);
        } else {
            // Si hay datos, crear el gráfico
            var myPieChart = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: ["Pendientes", "Agendadas", "Completadas", "Canceladas"],
                    datasets: [{
                        data: [chartDataPendientes, chartDataAgendadas, chartDataCompletadas, chartDataCanceladas],
                        backgroundColor: ['#f6c23e', '#36b9cc', '#1cc88a', '#e74a3b'],
                        hoverBackgroundColor: ['#daa520', '#2c9faf', '#17a673', '#c23321'],
                        hoverBorderColor: "rgba(234, 236, 244, 1)"
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    tooltips: {
                        backgroundColor: "rgb(255,255,255)",
                        bodyFontColor: "#858796",
                        borderColor: '#dddfeb',
                        borderWidth: 1,
                        xPadding: 15,
                        yPadding: 15,
                        displayColors: false,
                        caretPadding: 10
                    },
                    legend: {
                        display: false
                    },
                    cutoutPercentage: 70
                }
            });
        }
    });
</script>
@endpush
