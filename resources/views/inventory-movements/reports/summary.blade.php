@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-2 text-gray-800">Reporte Resumido de Movimientos</h1>
    <p class="mb-4">
        Periodo: {{ $dateFrom }} - {{ $dateTo }}
        @if($filters['movement_type'] && $filters['movement_type'] !== 'all')
            | Tipo: {{ ucfirst($filters['movement_type']) }}
        @endif
        @if($filters['item_id'])
            | Artículo ID: {{ $filters['item_id'] }}
        @endif
    </p>

    <div class="row">
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Resumen de Movimientos</h6>
                    <div>
                        <button onclick="printReport()" class="btn btn-sm btn-secondary shadow-sm mr-2">
                            <i class="fas fa-print fa-sm text-white-50"></i> Imprimir
                        </button>
                        <a href="#" class="btn btn-sm btn-primary shadow-sm mr-2" id="exportPDF">
                            <i class="fas fa-file-pdf fa-sm text-white-50"></i> Exportar PDF
                        </a>
                        <a href="#" class="btn btn-sm btn-success shadow-sm" id="exportExcel">
                            <i class="fas fa-file-excel fa-sm text-white-50"></i> Exportar Excel
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if($results->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>Tipo de Movimiento</th>
                                        <th>Cantidad de Movimientos</th>
                                        <th>Unidades Totales</th>
                                        <th>Valor Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $totalCount = 0;
                                        $totalQuantity = 0;
                                        $totalValue = 0;
                                    @endphp
                                    
                                    @foreach($results as $result)
                                    @php
                                        $totalCount += $result->count;
                                        $totalQuantity += $result->total_quantity;
                                        $totalValue += $result->total_value;
                                        
                                        $type = '';
                                        $badge = '';
                                        
                                        switch($result->movement_type) {
                                            case 'entry':
                                                $type = 'Entrada';
                                                $badge = 'badge-success';
                                                break;
                                            case 'exit':
                                                $type = 'Salida';
                                                $badge = 'badge-danger';
                                                break;
                                            case 'adjustment':
                                                $type = 'Ajuste';
                                                $badge = 'badge-warning';
                                                break;
                                            default:
                                                $type = $result->movement_type;
                                                $badge = 'badge-info';
                                        }
                                    @endphp
                                    <tr>
                                        <td><span class="badge {{ $badge }}">{{ $type }}</span></td>
                                        <td>{{ $result->count }}</td>
                                        <td>{{ $result->total_quantity }}</td>
                                        <td>${{ number_format($result->total_value, 2) }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr class="font-weight-bold bg-light">
                                        <td>TOTAL</td>
                                        <td>{{ $totalCount }}</td>
                                        <td>{{ $totalQuantity }}</td>
                                        <td>${{ number_format($totalValue, 2) }}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-info">
                            No hay movimientos que coincidan con los criterios de búsqueda.
                        </div>
                    @endif
                    
                    <div class="mt-4">
                        <a href="{{ route('admin.inventory-movements.report.form') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Volver al formulario
                        </a>
                        <a href="{{ route('admin.inventory-movements.report', array_merge($filters, ['report_type' => 'detail'])) }}" class="btn btn-info ml-2">
                            <i class="fas fa-list"></i> Ver Reporte Detallado
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-4 col-lg-5">
            <!-- Gráfico de Distribución de Movimientos -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Distribución de Movimientos</h6>
                </div>
                <div class="card-body">
                    <div class="chart-pie pt-4">
                        <canvas id="movementsDistributionChart"></canvas>
                    </div>
                    <div class="mt-4 text-center small">
                        @foreach($results as $result)
                            @php
                                $color = '';
                                switch($result->movement_type) {
                                    case 'entry':
                                        $color = 'success';
                                        break;
                                    case 'exit':
                                        $color = 'danger';
                                        break;
                                    case 'adjustment':
                                        $color = 'warning';
                                        break;
                                    default:
                                        $color = 'info';
                                }
                            @endphp
                            <span class="mr-2">
                                <i class="fas fa-circle text-{{ $color }}"></i> 
                                @switch($result->movement_type)
                                    @case('entry')
                                        Entradas
                                        @break
                                    @case('exit')
                                        Salidas
                                        @break
                                    @case('adjustment')
                                        Ajustes
                                        @break
                                    @default
                                        {{ ucfirst($result->movement_type) }}
                                @endswitch
                            </span>
                        @endforeach
                    </div>
                </div>
            </div>
            
            <!-- Gráfico de Valor por Tipo -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Valor por Tipo de Movimiento</h6>
                </div>
                <div class="card-body">
                    <div class="chart-bar">
                        <canvas id="valueByTypeChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Datos para los gráficos
        const types = [];
        const counts = [];
        const values = [];
        const backgroundColors = [];
        const hoverBackgroundColors = [];
        
        // Pre-configuramos los datos de PHP para JavaScript
        const resultsData = JSON.parse('{!! addslashes(json_encode($results)) !!}');
        
        // Procesamos los datos con JavaScript puro
        resultsData.forEach(result => {
            types.push(result.movement_type.charAt(0).toUpperCase() + result.movement_type.slice(1));
            counts.push(result.count);
            values.push(result.total_value);
            
            switch(result.movement_type) {
                case 'entry':
                    backgroundColors.push('#1cc88a');
                    hoverBackgroundColors.push('#169b6b');
                    break;
                case 'exit':
                    backgroundColors.push('#e74a3b');
                    hoverBackgroundColors.push('#be3c2e');
                    break;
                case 'adjustment':
                    backgroundColors.push('#f6c23e');
                    hoverBackgroundColors.push('#dda20a');
                    break;
                default:
                    backgroundColors.push('#36b9cc');
                    hoverBackgroundColors.push('#258391');
            }
        });
        
        // Gráfico de Distribución de Movimientos (Pie Chart)
        const ctx1 = document.getElementById("movementsDistributionChart");
        if (ctx1) {
            new Chart(ctx1, {
                type: 'doughnut',
                data: {
                    labels: types,
                    datasets: [{
                        data: counts,
                        backgroundColor: backgroundColors,
                        hoverBackgroundColor: hoverBackgroundColors,
                        hoverBorderColor: "rgba(234, 236, 244, 1)",
                    }],
                },
                options: {
                    maintainAspectRatio: false,
                    tooltips: {
                        backgroundColor: "rgb(255,255,255)",
                        bodyFontColor: "#858796",
                        borderColor: '#dddfeb',
                        borderWidth: 1,
                        xPadding: 15,
                        yPadding: 15,
                        displayColors: false,
                        caretPadding: 10,
                    },
                    legend: {
                        display: false
                    },
                    cutoutPercentage: 80,
                },
            });
        }
        
        // Gráfico de Valor por Tipo de Movimiento (Bar Chart)
        const ctx2 = document.getElementById("valueByTypeChart");
        if (ctx2) {
            new Chart(ctx2, {
                type: 'bar',
                data: {
                    labels: types,
                    datasets: [{
                        label: "Valor ($)",
                        backgroundColor: backgroundColors,
                        hoverBackgroundColor: hoverBackgroundColors,
                        borderColor: "#4e73df",
                        data: values,
                    }],
                },
                options: {
                    maintainAspectRatio: false,
                    layout: {
                        padding: {
                            left: 10,
                            right: 25,
                            top: 25,
                            bottom: 0
                        }
                    },
                    scales: {
                        xAxes: [{
                            gridLines: {
                                display: false,
                                drawBorder: false
                            },
                            maxBarThickness: 25,
                        }],
                        yAxes: [{
                            ticks: {
                                min: 0,
                                maxTicksLimit: 5,
                                padding: 10,
                                callback: function(value) {
                                    return '$' + value.toFixed(2);
                                }
                            }
                        }],
                    },
                    tooltips: {
                        titleMarginBottom: 10,
                        titleFontColor: '#6e707e',
                        titleFontSize: 14,
                        backgroundColor: "rgb(255,255,255)",
                        bodyFontColor: "#858796",
                        borderColor: '#dddfeb',
                        borderWidth: 1,
                        xPadding: 15,
                        yPadding: 15,
                        displayColors: false,
                        caretPadding: 10,
                        callbacks: {
                            label: function(tooltipItem, chart) {
                                return 'Valor: $' + parseFloat(tooltipItem.yLabel).toFixed(2);
                            }
                        }
                    },
                }
            });
        }
        
        // Manejar exportación a Excel
        $('#exportExcel').click(function(e) {
            e.preventDefault();
            // Asegúrate de que esta ruta existe o modifícala según tu sistema
            const excelUrl = '{{ route("admin.inventory-movements.report", array_merge($filters, ["format" => "excel"])) }}';
            window.location.href = excelUrl;
        });
        
        // Manejar exportación a PDF
        $('#exportPDF').click(function(e) {
            e.preventDefault();
            // Asegúrate de que esta ruta existe o modifícala según tu sistema
            const pdfUrl = '{{ route("admin.inventory-movements.report", array_merge($filters, ["format" => "pdf"])) }}';
            window.location.href = pdfUrl;
        });
    });
    
    // Función para imprimir
    function printReport() {
        window.print();
    }
</script>
@endsection
