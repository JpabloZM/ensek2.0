@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-2 text-gray-800">Valoración de Inventario</h1>
    <p class="mb-4">Reporte de valoración actual del inventario por categorías.</p>

    <!-- Tarjetas de Resumen -->
    <div class="row">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Artículos Totales</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total_items'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-boxes fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Valor Total</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">${{ number_format($stats['total_value'], 2) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Unidades Totales</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($stats['total_quantity']) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-cubes fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Artículos con Stock Bajo</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['low_stock_count'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Valoración por Categoría -->
    <div class="row">
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Valoración por Categoría</h6>
                    <div>
                        <button onclick="printReport()" class="btn btn-sm btn-secondary shadow-sm mr-2">
                            <i class="fas fa-print fa-sm text-white-50"></i> Imprimir
                        </button>
                        <button id="exportPDF" class="btn btn-sm btn-primary shadow-sm">
                            <i class="fas fa-file-pdf fa-sm text-white-50"></i> Exportar PDF
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Categoría</th>
                                    <th>Artículos</th>
                                    <th>Unidades</th>
                                    <th>Valor Total</th>
                                    <th>% del Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($categories as $category)
                                <tr>
                                    <td>{{ $category->name }}</td>
                                    <td>{{ $category->item_count }}</td>
                                    <td>{{ number_format($category->total_quantity) }}</td>
                                    <td>${{ number_format($category->total_value, 2) }}</td>
                                    <td>
                                        @if($stats['total_value'] > 0)
                                            {{ number_format(($category->total_value / $stats['total_value']) * 100, 1) }}%
                                        @else
                                            0%
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr class="font-weight-bold bg-light">
                                    <td>TOTAL</td>
                                    <td>{{ $stats['total_items'] }}</td>
                                    <td>{{ number_format($stats['total_quantity']) }}</td>
                                    <td>${{ number_format($stats['total_value'], 2) }}</td>
                                    <td>100%</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
            
            <!-- Top 10 Artículos más Valiosos -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Top 10 Artículos más Valiosos</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Artículo</th>
                                    <th>Categoría</th>
                                    <th>Cantidad</th>
                                    <th>Precio Unit.</th>
                                    <th>Valor Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($topItems as $item)
                                <tr>
                                    <td>{{ $item->name }}</td>
                                    <td>{{ $item->category->name }}</td>
                                    <td>{{ $item->quantity }} {{ $item->unit_of_measure }}</td>
                                    <td>{{ $item->formatted_unit_price }}</td>
                                    <td>{{ $item->formatted_total_value }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-4 col-lg-5">
            <!-- Gráfico de Distribución de Valor por Categoría -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Distribución de Valor</h6>
                </div>
                <div class="card-body">
                    <div class="chart-pie pt-4">
                        <canvas id="inventoryValueDistribution"></canvas>
                    </div>
                    <div class="mt-4 text-center small">
                        @foreach($categories as $index => $category)
                            <span class="mr-2">
                                <i class="fas fa-circle category-color-{{ $index }}"></i> {{ $category->name }}
                            </span>
                        @endforeach
                    </div>
                </div>
            </div>
            
            <!-- Acciones -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Acciones</h6>
                </div>
                <div class="card-body">
                    <p>Desde aquí puede acceder a otras funcionalidades relacionadas con el inventario.</p>
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.inventory-movements.index') }}" class="btn btn-primary btn-block mb-2">
                            <i class="fas fa-exchange-alt"></i> Ver Movimientos
                        </a>
                        <a href="{{ route('admin.inventory-items.index') }}" class="btn btn-info btn-block mb-2">
                            <i class="fas fa-boxes"></i> Ver Artículos
                        </a>
                        <a href="{{ route('admin.inventory-movements.report.form') }}" class="btn btn-success btn-block">
                            <i class="fas fa-chart-bar"></i> Otros Reportes
                        </a>
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
        // Inicializar DataTables
        $('#dataTable').DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.25/i18n/Spanish.json"
            },
            "order": [[3, 'desc']], // Ordenar por valor total descendente
        });
        
        // Datos para el gráfico de distribución
        const categoryData = [];
        const values = [];
        const backgroundColors = [
            '#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b', 
            '#5a5c69', '#6610f2', '#fd7e14', '#20c9a6', '#6f42c1',
            '#2ecc71', '#3498db', '#e67e22', '#9b59b6', '#1abc9c'
        ];
        
        // Parseamos los datos de categorías desde PHP
        const categoriesData = JSON.parse('{!! addslashes(json_encode($categories)) !!}');
        
        // Procesamos los datos en JavaScript
        categoriesData.forEach((category, index) => {
            categoryData.push(category.name);
            values.push(category.total_value);
            
            // Aplicamos los colores dinámicos (esto se ejecutará después de que se cargue el DOM)
            setTimeout(() => {
                $('.category-color-' + index).css('color', backgroundColors[index % backgroundColors.length]);
            }, 0);
        });
        
        // Gráfico de distribución de valor por categoría
        const ctx = document.getElementById("inventoryValueDistribution");
        if (ctx) {
            new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: categoryData,
                    datasets: [{
                        data: values,
                        backgroundColor: backgroundColors.slice(0, categoryData.length),
                        hoverBackgroundColor: backgroundColors.map(color => LightenDarkenColor(color, -20)),
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
                        callbacks: {
                            label: function(tooltipItem, data) {
                                const dataIndex = tooltipItem.index;
                                const value = data.datasets[0].data[dataIndex];
                                const total = data.datasets[0].data.reduce((a, b) => a + b, 0);
                                const percentage = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                                return `${data.labels[dataIndex]}: $${value.toLocaleString()} (${percentage}%)`;
                            }
                        }
                    },
                    legend: {
                        display: false
                    },
                    cutoutPercentage: 70,
                },
            });
        }
        
        // Manejar exportación a PDF
        $('#exportPDF').click(function(e) {
            e.preventDefault();
            window.location.href = "{{ route('admin.inventory-movements.valuation') }}?format=pdf";
        });
    });
    
    // Función para oscurecer o aclarar colores (para hover)
    function LightenDarkenColor(col, amt) {
        let usePound = false;
        
        if (col[0] === "#") {
            col = col.slice(1);
            usePound = true;
        }
        
        const num = parseInt(col, 16);
        
        let r = (num >> 16) + amt;
        r = r > 255 ? 255 : (r < 0 ? 0 : r);
        
        let g = ((num >> 8) & 0x00FF) + amt;
        g = g > 255 ? 255 : (g < 0 ? 0 : g);
        
        let b = (num & 0x0000FF) + amt;
        b = b > 255 ? 255 : (b < 0 ? 0 : b);
        
        return (usePound ? "#" : "") + (g | (r << 8) | (b << 16)).toString(16).padStart(6, '0');
    }
    
    // Función para imprimir
    function printReport() {
        window.print();
    }
</script>
@endsection
