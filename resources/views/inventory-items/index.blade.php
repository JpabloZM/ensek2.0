@extends('layouts.admin')

@push('styles')
<style>
    .pagination {
        margin-bottom: 0;
    }
    
    .pagination .page-item.active .page-link {
        background-color: #4e73df;
        border-color: #4e73df;
    }
    
    .pagination .page-link {
        color: #4e73df;
    }
    
    .pagination .page-link:focus {
        box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
    }
    
    .pagination .page-item.disabled .page-link {
        color: #858796;
    }
    
    .table th, .table td {
        vertical-align: middle;
    }
    
    .stock-badge {
        font-size: 0.8rem;
        padding: 0.35em 0.65em;
    }
    
    .low-stock {
        background-color: #e74a3b;
        color: white;
    }
    
    .normal-stock {
        background-color: #1cc88a;
        color: white;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Inventario</h1>
        <div>
            <a href="{{ route('admin.inventory-categories.index') }}" class="d-sm-inline-block btn btn-sm btn-info shadow-sm mr-2">
                <i class="fas fa-tags fa-sm text-white-50 mr-1"></i> Categorías
            </a>
            <a href="{{ route('admin.inventory-items.create') }}" class="d-sm-inline-block btn btn-sm btn-primary shadow-sm">
                <i class="fas fa-plus fa-sm text-white-50 mr-1"></i> Nuevo Producto
            </a>
        </div>
    </div>

    <!-- Filtros -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Filtros de búsqueda</h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.inventory-items.index') }}" class="row align-items-end">
                <div class="col-md-3 mb-3">
                    <label for="category">Categoría:</label>
                    <select name="category" id="category" class="form-control">
                        <option value="">Todas las categorías</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 mb-3">
                    <label for="stock_status">Estado de stock:</label>
                    <select name="stock_status" id="stock_status" class="form-control">
                        <option value="">Todos</option>
                        <option value="low" {{ request('stock_status') == 'low' ? 'selected' : '' }}>Stock bajo</option>
                        <option value="normal" {{ request('stock_status') == 'normal' ? 'selected' : '' }}>Stock normal</option>
                    </select>
                </div>
                <div class="col-md-3 mb-3">
                    <label for="search">Buscar por nombre o código:</label>
                    <input type="text" name="search" id="search" class="form-control" value="{{ request('search') }}" placeholder="Nombre o código...">
                </div>
                <div class="col-md-3 mb-3">
                    <button type="submit" class="btn btn-primary mr-2">
                        <i class="fas fa-search fa-sm"></i> Filtrar
                    </button>
                    <a href="{{ route('admin.inventory-items.index') }}" class="btn btn-secondary">
                        <i class="fas fa-sync-alt fa-sm"></i> Limpiar
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Card de inventario -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Lista de Productos</h6>
            <div class="dropdown">
                <button class="btn btn-sm btn-outline-primary dropdown-toggle" type="button" id="exportDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fas fa-download fa-sm"></i> Exportar
                </button>
                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="exportDropdown">
                    <a class="dropdown-item" href="{{ route('admin.inventory-items.export', ['format' => 'pdf']) }}">
                        <i class="fas fa-file-pdf fa-sm text-danger"></i> PDF
                    </a>
                    <a class="dropdown-item" href="{{ route('admin.inventory-items.export', ['format' => 'excel']) }}">
                        <i class="fas fa-file-excel fa-sm text-success"></i> Excel
                    </a>
                </div>
            </div>
        </div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif
            
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Código</th>
                            <th>Nombre</th>
                            <th>Categoría</th>
                            <th>Ubicación</th>
                            <th>Cantidad</th>
                            <th>Precio Unit.</th>
                            <th>Valor Total</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($items as $item)
                            <tr>
                                <td>{{ $item->code }}</td>
                                <td>{{ $item->name }}</td>
                                <td>{{ $item->category->name }}</td>
                                <td>{{ $item->location ?? 'No especificada' }}</td>
                                <td class="text-center">{{ $item->quantity }}</td>
                                <td class="text-right">${{ number_format($item->unit_price, 2) }}</td>
                                <td class="text-right">${{ number_format($item->quantity * $item->unit_price, 2) }}</td>
                                <td class="text-center">
                                    @if($item->isLowStock())
                                        <span class="badge badge-danger">Stock Bajo</span>
                                    @else
                                        <span class="badge badge-success">Stock Normal</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('admin.inventory-items.edit', $item->id) }}" class="btn btn-sm btn-warning">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="{{ route('admin.inventory-items.show', $item->id) }}" class="btn btn-sm btn-info">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-success btn-add-stock" title="Añadir Stock" 
                                        data-toggle="modal" data-target="#addStockModal" 
                                        data-id="{{ $item->id }}" 
                                        data-name="{{ $item->name }}" 
                                        data-code="{{ $item->code }}"
                                        data-current="{{ $item->quantity }}">
                                        <i class="fas fa-plus-circle"></i>
                                    </button>
                                    <form action="{{ route('admin.inventory-items.destroy', $item->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('¿Está seguro de eliminar este producto?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center">No hay productos registrados en el inventario</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="d-flex justify-content-between align-items-center mt-3">
                <div class="text-muted">
                    Mostrando {{ $items->firstItem() ?? 0 }} a {{ $items->lastItem() ?? 0 }} de {{ $items->total() }} resultados
                </div>
                <div class="pagination-container">
                    {{ $items->onEachSide(1)->appends(request()->query())->links('vendor.pagination.bootstrap-4') }}
                </div>
            </div>
        </div>
    </div>
    
    <!-- Stock Dashboard -->
    <div class="row" id="dashboardRow">
        <!-- Stock General -->
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4 h-100">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Resumen de Inventario</h6>
                </div>
                <div class="card-body d-flex flex-column">
                    <div class="chart-area flex-grow-1">
                        <canvas id="inventorySummaryChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Items con Stock Bajo -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4 h-100">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Items con Stock Bajo</h6>
                </div>
                <div class="card-body d-flex flex-column">
                    @if($lowStockItems->count() > 0)
                        <div class="list-group flex-grow-1" style="overflow-y: auto;">
                            @foreach($lowStockItems as $item)
                                <a href="{{ route('admin.inventory-items.show', $item->id) }}" class="list-group-item list-group-item-action flex-column align-items-start">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h6 class="mb-1">{{ $item->name }}</h6>
                                        <span class="badge badge-danger">{{ $item->quantity }} / {{ $item->minimum_stock }}</span>
                                    </div>
                                    <small class="text-muted">Código: {{ $item->code }}</small>
                                </a>
                            @endforeach
                        </div>
                        @if($lowStockItems->count() < $lowStockTotal)
                            <div class="text-center mt-3">
                                <a href="{{ route('admin.inventory-items.index', ['stock_status' => 'low']) }}" class="btn btn-sm btn-danger">
                                    Ver todos ({{ $lowStockTotal }})
                                </a>
                            </div>
                        @endif
                    @else
                        <div class="text-center py-5 flex-grow-1 d-flex flex-column justify-content-center">
                            <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                            <p class="mb-0">Todos los productos tienen stock suficiente</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para añadir stock -->
<div class="modal fade" id="addStockModal" tabindex="-1" role="dialog" aria-labelledby="addStockModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="addStockForm" action="" method="POST">
                @csrf
                @method('PATCH')
                <div class="modal-header">
                    <h5 class="modal-title" id="addStockModalLabel">Añadir Stock</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="itemName">Producto:</label>
                        <input type="text" class="form-control" id="itemName" readonly>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="currentStock">Stock Actual:</label>
                                <input type="text" class="form-control" id="currentStock" readonly>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="addQuantity">Cantidad a añadir:</label>
                                <input type="number" class="form-control" name="add_quantity" id="addQuantity" min="1" required>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="notes">Notas (opcional):</label>
                        <textarea class="form-control" name="notes" id="notes" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">Guardar Cambios</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@2.9.4/dist/Chart.min.js"></script>
<script>
    $(document).ready(function() {
        // Primero, contamos las columnas en el encabezado de la tabla
        var columnCount = $('#dataTable thead th').length;
        console.log('Número de columnas detectadas en el encabezado:', columnCount);
        
        // Configuración de DataTables con inicialización simple
        $('#dataTable').DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Spanish.json"
            },
            "paging": false,
            "info": false,
            "searching": true,
            "ordering": true,
            "order": [[1, 'asc']],
            // Configuración simplificada para evitar problemas de conteo de columnas
            "columnDefs": [
                { "orderable": false, "targets": [columnCount - 1] } // Última columna no ordenable (Acciones)
            ]
        });
        
        // SweetAlert para confirmación de eliminación
        $('.table').on('submit', 'form', function(e) {
            e.preventDefault();
            const form = this;
            
            Swal.fire({
                title: '¿Está seguro?',
                text: "No podrá revertir esta acción",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
        
        // Modal para añadir stock
        $('.btn-add-stock').click(function() {
            const id = $(this).data('id');
            const name = $(this).data('name');
            const code = $(this).data('code');
            const current = $(this).data('current');
            
            $('#itemName').val(name + ' (' + code + ')');
            $('#currentStock').val(current);
            $('#addQuantity').val('');
            $('#notes').val('');
            
            $('#addStockForm').attr('action', `/admin/inventory-items/${id}/add-stock`);
        });

        // Gráfico de resumen de inventario
        if ($('#inventorySummaryChart').length > 0) {
            const ctx = document.getElementById('inventorySummaryChart');
            // Convertimos los datos PHP a variables JavaScript de manera segura
            const labels = JSON.parse('@json($categoryLabels)'.replace(/&quot;/g, '"'));
            const quantities = JSON.parse('@json($categoryQuantities)'.replace(/&quot;/g, '"'));
            const colors = generateColors(labels.length);
            
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Cantidad en Inventario',
                        data: quantities,
                        backgroundColor: colors,
                        borderColor: colors.map(color => darkenColor(color, -20)),
                        borderWidth: 1
                    }]
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
                            time: {
                                unit: 'category'
                            },
                            gridLines: {
                                display: false,
                                drawBorder: false
                            },
                            ticks: {
                                maxTicksLimit: 10
                            },
                            maxBarThickness: 50,
                        }],
                        yAxes: [{
                            ticks: {
                                min: 0,
                                maxTicksLimit: 5,
                                padding: 10,
                                callback: function(value) {
                                    return value;
                                }
                            },
                            gridLines: {
                                color: "rgb(234, 236, 244)",
                                zeroLineColor: "rgb(234, 236, 244)",
                                drawBorder: false,
                                borderDash: [2],
                                zeroLineBorderDash: [2]
                            }
                        }],
                    },
                    legend: {
                        display: false
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
                                return 'Cantidad: ' + tooltipItem.yLabel;
                            }
                        }
                    }
                }
            });
        }
        
        // Función para generar colores
        function generateColors(count) {
            const baseColors = ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b', '#858796', '#5a5c69'];
            const colors = [];
            
            for (let i = 0; i < count; i++) {
                const baseColor = baseColors[i % baseColors.length];
                const shiftAmount = Math.floor(i / baseColors.length) * 20;
                colors.push(shiftColor(baseColor, shiftAmount));
            }
            
            return colors;
        }
        
        function shiftColor(hex, amount) {
            const rgb = hexToRgb(hex);
            const hsl = rgbToHsl(rgb.r, rgb.g, rgb.b);
            
            hsl.h = (hsl.h + amount / 360) % 1;
            
            const newRgb = hslToRgb(hsl.h, hsl.s, hsl.l);
            return rgbToHex(newRgb.r, newRgb.g, newRgb.b);
        }
        
        function darkenColor(hex, percent) {
            const rgb = hexToRgb(hex);
            const hsl = rgbToHsl(rgb.r, rgb.g, rgb.b);
            
            hsl.l = Math.max(0, Math.min(1, hsl.l + percent / 100));
            
            const newRgb = hslToRgb(hsl.h, hsl.s, hsl.l);
            return rgbToHex(newRgb.r, newRgb.g, newRgb.b);
        }
        
        function hexToRgb(hex) {
            const shorthandRegex = /^#?([a-f\d])([a-f\d])([a-f\d])$/i;
            hex = hex.replace(shorthandRegex, function(m, r, g, b) {
                return r + r + g + g + b + b;
            });
            
            const result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);
            return result ? {
                r: parseInt(result[1], 16),
                g: parseInt(result[2], 16),
                b: parseInt(result[3], 16)
            } : null;
        }
        
        function rgbToHex(r, g, b) {
            return "#" + ((1 << 24) + (r << 16) + (g << 8) + b).toString(16).slice(1);
        }
        
        function rgbToHsl(r, g, b) {
            r /= 255;
            g /= 255;
            b /= 255;
            
            const max = Math.max(r, g, b);
            const min = Math.min(r, g, b);
            let h, s, l = (max + min) / 2;
            
            if (max === min) {
                h = s = 0; // achromatic
            } else {
                const d = max - min;
                s = l > 0.5 ? d / (2 - max - min) : d / (max + min);
                
                switch (max) {
                    case r: h = (g - b) / d + (g < b ? 6 : 0); break;
                    case g: h = (b - r) / d + 2; break;
                    case b: h = (r - g) / d + 4; break;
                }
                
                h /= 6;
            }
            
            return { h, s, l };
        }
        
        function hslToRgb(h, s, l) {
            let r, g, b;
            
            if (s === 0) {
                r = g = b = l; // achromatic
            } else {
                const hue2rgb = (p, q, t) => {
                    if (t < 0) t += 1;
                    if (t > 1) t -= 1;
                    if (t < 1/6) return p + (q - p) * 6 * t;
                    if (t < 1/2) return q;
                    if (t < 2/3) return p + (q - p) * (2/3 - t) * 6;
                    return p;
                };
                
                const q = l < 0.5 ? l * (1 + s) : l + s - l * s;
                const p = 2 * l - q;
                
                r = hue2rgb(p, q, h + 1/3);
                g = hue2rgb(p, q, h);
                b = hue2rgb(p, q, h - 1/3);
            }
            
            return {
                r: Math.round(r * 255),
                g: Math.round(g * 255),
                b: Math.round(b * 255)
            };
        }
        
        // Función para igualar la altura de las cards en el dashboard
        function equalizeDashboardCardHeights() {
            const summaryCard = document.querySelector('#dashboardRow .col-xl-8 .card');
            const lowStockCard = document.querySelector('#dashboardRow .col-xl-4 .card');
            
            if (summaryCard && lowStockCard) {
                // Asegurarse de que ambas tarjetas tengan la misma altura
                const observer = new ResizeObserver(() => {
                    // Permitir que el DOM se actualice completamente
                    setTimeout(() => {
                        // Resetear alturas a automático para medir correctamente
                        summaryCard.style.minHeight = 'auto';
                        lowStockCard.style.minHeight = 'auto';
                        
                        // Obtener la altura del más grande
                        const maxHeight = Math.max(
                            summaryCard.offsetHeight,
                            lowStockCard.offsetHeight
                        );
                        
                        // Aplicar la misma altura a ambos
                        if (maxHeight > 0) {
                            summaryCard.style.minHeight = maxHeight + 'px';
                            lowStockCard.style.minHeight = maxHeight + 'px';
                        }
                    }, 50);
                });
                
                // Observar cambios en las dimensiones
                observer.observe(summaryCard);
                observer.observe(lowStockCard);
                
                // También ajustar al cambiar el tamaño de la ventana
                window.addEventListener('resize', () => {
                    observer.disconnect();
                    equalizeDashboardCardHeights();
                });
            }
        }
        
        // Iniciar la igualdad de alturas cuando el documento esté listo
        equalizeDashboardCardHeights();
    });
</script>
@endpush
