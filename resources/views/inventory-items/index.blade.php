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
    }
    
    .action-btn {
        width: 38px !important;
        height: 38px !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        margin: 0 2px !important;
        padding: 0 !important;
        border-radius: 4px !important;
        transition: all 0.2s ease !important;
    }
    
    .action-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.15);
    }
    
    .d-flex.justify-content-center {
        gap: 5px;
    }
    
    .low-stock {
        background-color: #e74a3b;
        color: white;
    }
    
    .normal-stock {
        background-color: #1cc88a;
        color: white;
    }
    
    /* Estilos mejorados para tarjetas de dashboard */
    .dashboard-card {
        transition: height 0.3s ease;
    }
    
    .chart-container {
        width: 100%;
        height: 350px;
    }
    
    .stock-list {
        max-height: 350px;
        overflow-y: auto;
    }
    
    /* Estilos para la búsqueda dinámica */
    .opacity-50 {
        opacity: 0.5;
        transition: opacity 0.3s ease;
    }
    
    .table-responsive {
        transition: opacity 0.3s ease;
    }
    
    .search-highlight {
        background-color: #fff3cd;
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
                    <div class="input-group">
                        <input type="text" name="search" id="search" class="form-control" value="{{ request('search') }}" placeholder="Nombre o código..." autocomplete="off">
                        <div class="input-group-append">
                            <button id="clearSearch" type="button" class="btn btn-outline-secondary" @if(!request('search')) style="display: none;" @endif>
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <button type="submit" id="filterBtn" class="btn btn-primary mr-2">
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
            <!-- Notifications are now handled by toast system -->

            <div class="table-responsive">
                <table class="table table-bordered datatable-table" id="dt-table" width="100%" cellspacing="0">
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
                                <td class="text-right">$ {{ number_format($item->unit_price, 0, ',', '.') }}</td>
                                <td class="text-right">$ {{ number_format($item->quantity * $item->unit_price, 0, ',', '.') }}</td>
                                <td class="text-center">
                                    @if($item->isLowStock())
                                        <span class="badge badge-danger">Stock Bajo</span>
                                    @else
                                        <span class="badge badge-success">Stock Normal</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center">
                                        <a href="{{ route('admin.inventory-items.edit', $item->id) }}" class="btn btn-sm btn-warning action-btn" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="{{ route('admin.inventory-items.show', $item->id) }}" class="btn btn-sm btn-info action-btn" title="Ver detalles">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <form action="{{ route('admin.inventory-items.destroy', $item->id) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger action-btn" title="Eliminar" onclick="return confirm('¿Está seguro de eliminar este producto?')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
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
            <div class="card shadow mb-4 dashboard-card">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Resumen de Inventario</h6>
                </div>
                <div class="card-body">
                    <div class="chart-container" style="position: relative; height: 350px;">
                        <canvas id="inventorySummaryChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Items con Stock Bajo -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4 dashboard-card">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Items con Stock Bajo</h6>
                </div>
                <div class="card-body">
                    @if($lowStockItems->count() > 0)
                        <div class="list-group stock-list" style="max-height: 350px; overflow-y: auto;">
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
                        <div class="text-center py-5 d-flex flex-column justify-content-center" style="height: 350px;">
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
            <form id="addStockForm" action="" method="POST" class="needs-validation" novalidate>
                @csrf
                @method('PATCH')
                <div class="modal-header">
                    <h5 class="modal-title" id="addStockModalLabel">Añadir Stock</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
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
                                <div class="invalid-feedback">
                                    Por favor ingrese una cantidad válida (mínimo 1).
                                </div>
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
                    <button type="submit" class="btn btn-success btn-stock-submit">Guardar Cambios</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal para retirar stock -->
<div class="modal fade" id="removeStockModal" tabindex="-1" role="dialog" aria-labelledby="removeStockModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="removeStockForm" action="" method="POST" class="needs-validation" novalidate>
                @csrf
                @method('PATCH')
                <div class="modal-header">
                    <h5 class="modal-title" id="removeStockModalLabel">Retirar Stock</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <div class="form-group">
                        <label for="removeItemName">Producto:</label>
                        <input type="text" class="form-control" id="removeItemName" readonly>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="removeCurrentStock">Stock Actual:</label>
                                <input type="text" class="form-control" id="removeCurrentStock" readonly>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="removeQuantity">Cantidad a retirar:</label>
                                <input type="number" class="form-control" name="remove_quantity" id="removeQuantity" min="1" required>
                                <div class="invalid-feedback">
                                    Por favor ingrese una cantidad válida (mínimo 1 y no mayor al stock actual).
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="removeNotes">Motivo / Notas (opcional):</label>
                        <textarea class="form-control" name="notes" id="removeNotes" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-warning btn-stock-submit">Confirmar Retiro</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<!-- Debugging de Chart.js -->
<script>
    // Verificar si Chart.js está disponible
    if (typeof Chart !== 'undefined') {
        console.log("✓ Chart.js detectado, versión:", Chart.version);
    } else {
        console.error("❌ Chart.js NO detectado");
    }
</script>

<script src="{{ asset('js/inventory-charts.js') }}"></script>

<!-- Datos de inventario como variables JavaScript -->
<script>
    // Mejor forma de manejar los datos PHP en JS
    (function() {
        console.log("%c[DATA LOADER] Iniciando carga de datos", "background:blue; color:white");
        
        // Datos crudos (para inspección de consola)
        console.log("Datos de categorías disponibles para JS");
        
        // Usar variables PHP renderizadas como strings y luego parseadas
        window.inventoryData = {
            categoryLabels: JSON.parse('{!! json_encode($categoryLabels ?? []) !!}'),
            categoryQuantities: JSON.parse('{!! json_encode($categoryQuantities ?? []) !!}'),
            lowStockLabels: JSON.parse('{!! json_encode($lowStockLabels ?? []) !!}'),
            lowStockQuantities: JSON.parse('{!! json_encode($lowStockQuantities ?? []) !!}'),
            lowStockThresholds: JSON.parse('{!! json_encode($lowStockThresholds ?? []) !!}')
        };
        
        // Verificar los datos
        if (!Array.isArray(window.inventoryData.categoryLabels)) {
            console.error("categoryLabels no es un array:", window.inventoryData.categoryLabels);
            window.inventoryData.categoryLabels = [];
        }
        
        if (!Array.isArray(window.inventoryData.categoryQuantities)) {
            console.error("categoryQuantities no es un array:", window.inventoryData.categoryQuantities);
            window.inventoryData.categoryQuantities = [];
        }
        
        console.log("%c✓ Datos cargados correctamente", "color:green; font-weight:bold");
        console.log("Resumen de datos:", {
            categorías: window.inventoryData.categoryLabels.length,
            stockBajo: window.inventoryData.lowStockLabels.length
        });
        
        // El gráfico ahora se inicializa directamente en document.ready, 
        // así que no necesitamos esta lógica adicional de inicialización
        console.log("%c✓ Los gráficos se inicializarán cuando el documento esté listo", "color:blue; font-weight:bold");
    })();
</script>

<!-- 
    Datos del inventario (Este método mantiene una copia en formato JSON)
    VSCode no analiza el contenido de comentarios HTML, por lo que no generará errores
-->
<script id="inventory-data" type="application/json">
@php
    // Preparamos los datos en PHP primero
    $inventoryData = [
        'categoryLabels' => $categoryLabels ?? [],
        'categoryQuantities' => $categoryQuantities ?? [],
        'lowStockLabels' => $lowStockLabels ?? [],
        'lowStockQuantities' => $lowStockQuantities ?? [],
        'lowStockThresholds' => $lowStockThresholds ?? []
    ];
    
    // Luego los codificamos a JSON una sola vez
    echo json_encode($inventoryData, JSON_PRETTY_PRINT);
@endphp
</script>

<script>
    // Inicialización principal cuando el documento está listo
    $(document).ready(function() {
        console.log('%c DOCUMENT READY', 'background:purple; color:white; font-size: 14px');
        
        // Verificar elementos críticos
        const chartElement = document.getElementById('inventorySummaryChart');
        if (chartElement) {
            console.log("✓ Canvas encontrado correctamente", chartElement);
        } else {
            console.error("❌ Canvas NO encontrado en el DOM");
        }
        
        // Implementación directa del gráfico de resumen
        setTimeout(function() {
            try {
                if (typeof Chart === 'undefined') {
                    throw new Error("Chart.js no disponible");
                }
                
                const canvas = document.getElementById('inventorySummaryChart');
                if (!canvas) {
                    throw new Error("Canvas no encontrado");
                }
                
                // Destruir cualquier instancia previa del gráfico
                const chartInstance = Chart.getChart(canvas);
                if (chartInstance) {
                    console.log("Destruyendo instancia previa del gráfico");
                    chartInstance.destroy();
                }
                
                // Datos para el gráfico desde el objeto global window.inventoryData
                const categoryLabels = window.inventoryData.categoryLabels;
                const categoryQuantities = window.inventoryData.categoryQuantities;
                
                console.log("Datos para el gráfico:", {
                    labels: categoryLabels,
                    data: categoryQuantities
                });
                
                if (!categoryLabels.length) {
                    throw new Error("No hay datos de categorías disponibles");
                }
                
                // Colores predefinidos para el gráfico
                const colors = [
                    '#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b', 
                    '#858796', '#5a5c69', '#2e59d9', '#17a673', '#2c9faf'
                ];
                
                // Crear gráfico directamente
                const ctx = canvas.getContext('2d');
                const summaryChart = new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: categoryLabels,
                        datasets: [{
                            data: categoryQuantities,
                            backgroundColor: colors,
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: true,
                                position: 'bottom'
                            }
                        },
                        cutout: '60%'
                    }
                });
                
                console.log("%c ¡Gráfico creado exitosamente! 🎉", "color:green; font-weight:bold; font-size: 14px");
                console.log("ID de gráfico asignado:", summaryChart.id);
                
            } catch (error) {
                console.error("ERROR CRÍTICO:", error);
                
                // Mostrar error en la interfaz
                const container = document.querySelector('.chart-container');
                if (container) {
                    container.innerHTML = `
                        <div class="alert alert-danger text-center p-3">
                            <i class="fas fa-exclamation-circle fa-2x mb-3"></i>
                            <h5>Error al cargar el gráfico</h5>
                            <p>${error.message}</p>
                        </div>
                    `;
                }
            }
        }, 500);
        
        // SweetAlert para confirmación de eliminación
        $('.table').on('submit', 'form', function(e) {
            if ($(this).hasClass('stock-form')) {
                return; // No aplicar esta confirmación a los formularios de stock
            }
            
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
        
        // Validación de formularios
        (function() {
            'use strict';
            
            // Fetch all forms we want to apply custom validation styles to
            var forms = document.querySelectorAll('.needs-validation');
            
            // Loop over them and prevent submission
            Array.prototype.slice.call(forms).forEach(function(form) {
                form.addEventListener('submit', function(event) {
                    if (!form.checkValidity()) {
                        event.preventDefault();
                        event.stopPropagation();
                    }
                    form.classList.add('was-validated');
                }, false);
            });
        })();
        
        // Funciones para manejar los modales y eventos de stock
        function setupAddStockModal(id, name, code, current) {
            console.log('Setup Add Stock Modal:', id, name, code, current);
            $('#itemName').val(name + ' (' + code + ')');
            $('#currentStock').val(current);
            $('#addQuantity').val('');
            $('#notes').val('');
            
            // Limpiar validaciones previas
            $('#addStockForm').removeClass('was-validated');
            
            // Usar la ruta correcta de Laravel
            const addStockUrl = "{{ route('admin.inventory-items.add-stock', ['id' => ':id']) }}".replace(':id', id);
            console.log('URL para añadir stock:', addStockUrl);
            $('#addStockForm').attr('action', addStockUrl);
            
            // Mostrar Toast al enviar el formulario correctamente
            $('#addStockForm').off('submit').on('submit', function(e) {
                console.log('Formulario de añadir stock enviado');
                if (!this.checkValidity()) {
                    e.preventDefault();
                    return false;
                }
                
                // Mostrar indicador de carga
                $('.btn-stock-submit', this).html('<i class="fas fa-spinner fa-spin"></i> Procesando...').prop('disabled', true);
                return true;
            });
            
            // Mostrar el modal
            $('#addStockModal').modal('show');
        }
        
        function setupRemoveStockModal(id, name, code, current) {
            console.log('Setup Remove Stock Modal:', id, name, code, current);
            $('#removeItemName').val(name + ' (' + code + ')');
            $('#removeCurrentStock').val(current);
            $('#removeQuantity').val('');
            $('#removeNotes').val('');
            
            // Establecer la cantidad máxima que se puede retirar y validación
            $('#removeQuantity').attr('max', current);
            
            // Validación en tiempo real para la cantidad
            $('#removeQuantity').off('input').on('input', function() {
                const val = parseInt($(this).val()) || 0;
                if (val > parseInt(current)) {
                    $(this).addClass('is-invalid');
                    $(this).next('.invalid-feedback').text('La cantidad a retirar no puede ser mayor que ' + current);
                } else if (val <= 0) {
                    $(this).addClass('is-invalid');
                    $(this).next('.invalid-feedback').text('La cantidad debe ser mayor que 0');
                } else {
                    $(this).removeClass('is-invalid').addClass('is-valid');
                }
            });
            
            // Limpiar validaciones previas
            $('#removeStockForm').removeClass('was-validated');
            
            // Usar la ruta correcta de Laravel
            const removeStockUrl = "{{ route('admin.inventory-items.remove-stock', ['id' => ':id']) }}".replace(':id', id);
            console.log('URL para retirar stock:', removeStockUrl);
            $('#removeStockForm').attr('action', removeStockUrl);
            
            // Mostrar Toast al enviar el formulario correctamente
            $('#removeStockForm').off('submit').on('submit', function(e) {
                console.log('Formulario de retirar stock enviado');
                if (!this.checkValidity()) {
                    e.preventDefault();
                    return false;
                }
                
                const quantity = parseInt($('#removeQuantity').val()) || 0;
                if (quantity > parseInt(current)) {
                    e.preventDefault();
                    $('#removeQuantity').addClass('is-invalid');
                    return false;
                }
                
                // Mostrar indicador de carga
                $('.btn-stock-submit', this).html('<i class="fas fa-spinner fa-spin"></i> Procesando...').prop('disabled', true);
                return true;
            });
            
            // Mostrar el modal
            $('#removeStockModal').modal('show');
        }
        
        // Modal para añadir stock - Botones en tabla
        $(document).on('click', '.btn-add-stock', function(e) {
            e.preventDefault();
            const id = $(this).data('id');
            const name = $(this).data('name');
            const code = $(this).data('code');
            const current = $(this).data('current');
            console.log('Botón de añadir stock clickeado:', id, name, code, current);
            setupAddStockModal(id, name, code, current);
        });
        
        // Modal para retirar stock - Botones en tabla
        $(document).on('click', '.btn-remove-stock', function(e) {
            e.preventDefault();
            const id = $(this).data('id');
            const name = $(this).data('name');
            const code = $(this).data('code');
            const current = $(this).data('current');
            console.log('Botón de retirar stock clickeado:', id, name, code, current);
            setupRemoveStockModal(id, name, code, current);
        });
        
        // Botones de acciones rápidas (segunda imagen)
        $(document).on('click', '#btnAddStock', function(e) {
            e.preventDefault();
            const id = $(this).data('id');
            const name = $(this).data('name');
            const code = $(this).data('code');
            const current = $(this).data('current');
            console.log('Botón rápido de añadir stock clickeado:', id, name, code, current);
            setupAddStockModal(id, name, code, current);
        });
        
        $(document).on('click', '#btnRemoveStock', function(e) {
            e.preventDefault();
            const id = $(this).data('id');
            const name = $(this).data('name');
            const code = $(this).data('code');
            const current = $(this).data('current');
            console.log('Botón rápido de retirar stock clickeado:', id, name, code, current);
            setupRemoveStockModal(id, name, code, current);
        });
        
        // Función para ajustar alturas de tarjetas
        function fixCardHeights() {
            console.log('Ajustando alturas de las tarjetas...');
            // Resetear alturas para un nuevo cálculo
            $('.dashboard-card').css('height', 'auto');
            
            // Aplicar la misma altura a las tarjetas en la misma fila
            if (window.innerWidth >= 992) {
                const rowCards = $('#dashboardRow .dashboard-card');
                let maxHeight = 0;
                
                // Encontrar la altura máxima
                rowCards.each(function() {
                    const height = $(this).outerHeight();
                    if (height > maxHeight) {
                        maxHeight = height;
                    }
                });
                
                // Aplicar la altura máxima a todas las tarjetas
                if (maxHeight > 0) {
                    rowCards.css('height', maxHeight + 'px');
                }
            }
        }
        
        // Inicialización cuando la página está completamente cargada
        $(window).on('load', function() {
            console.log('Página cargada completamente, inicializando componentes...');
            
            // Primero ajustar alturas
            fixCardHeights();
            
            // Los gráficos ahora se inicializan directamente en el document.ready principal
            
            // Ajustar alturas de tarjetas para mantener el diseño uniforme
            fixCardHeights();
            
            // Alertas ahora son manejadas por el sistema de toast
        });
        
        // Manejar eventos de redimensionamiento
        let resizeTimeout;
        $(window).on('resize', function() {
            // Debounce para evitar múltiples ejecuciones
            clearTimeout(resizeTimeout);
            resizeTimeout = setTimeout(function() {
                fixCardHeights();
            }, 250);
        });
        
        // Búsqueda dinámica para productos del inventario
        let searchTimer;
        
        // Función para realizar la búsqueda en tiempo real
        function performLiveSearch() {
            const searchValue = $('#search').val().trim();
            const category = $('#category').val();
            const stockStatus = $('#stock_status').val();
            
            // Si hay al menos 2 caracteres o el campo está vacío y hay otros filtros
            if (searchValue.length >= 2 || searchValue.length === 0) {
                // Mostrar indicador de carga
                const btn = $('#filterBtn');
                const originalBtnHtml = btn.html();
                btn.html('<i class="fas fa-spinner fa-spin"></i> Buscando...').prop('disabled', true);
                
                // Mostrar indicador de carga en la tabla
                $('.table-responsive').addClass('opacity-50');
                
                // Enviar formulario automáticamente
                $.ajax({
                    url: "{{ route('admin.inventory-items.index') }}",
                    data: {
                        search: searchValue,
                        category: category,
                        stock_status: stockStatus
                    },
                    success: function(response) {
                        // Extraer solo el contenido de la tabla del HTML de respuesta
                        const newContent = $(response).find('.table-responsive').html();
                        const paginationContent = $(response).find('.pagination-container').html();
                        const resultsInfo = $(response).find('.text-muted').html();
                        
                        // Actualizar solo la tabla sin recargar toda la página
                        $('.table-responsive').html(newContent).removeClass('opacity-50');
                        $('.pagination-container').html(paginationContent);
                        $('.text-muted').html(resultsInfo);
                        
                        // Restaurar el botón
                        btn.html(originalBtnHtml).prop('disabled', false);
                        
                        // Actualizar URL sin recargar página (para mantener el historial)
                        const params = new URLSearchParams();
                        if (searchValue) params.append('search', searchValue);
                        if (category) params.append('category', category);
                        if (stockStatus) params.append('stock_status', stockStatus);
                        const newUrl = window.location.pathname + (params.toString() ? '?' + params.toString() : '');
                        window.history.replaceState({}, '', newUrl);
                    },
                    error: function() {
                        // Restaurar el botón y la tabla en caso de error
                        btn.html(originalBtnHtml).prop('disabled', false);
                        $('.table-responsive').removeClass('opacity-50');
                        showToast('Error', 'No se pudo realizar la búsqueda', 'error');
                    }
                });
            }
        }
        
        // Manejar eventos para búsqueda en vivo
        $('#search').on('input', function() {
            const searchValue = $(this).val().trim();
            
            // Mostrar/ocultar botón de limpiar
            if (searchValue.length > 0) {
                $('#clearSearch').show();
            } else {
                $('#clearSearch').hide();
            }
            
            // Debounce para evitar demasiadas peticiones
            clearTimeout(searchTimer);
            searchTimer = setTimeout(performLiveSearch, 500); // Esperar 500ms después de que el usuario deje de escribir
        });
        
        // También hacer búsqueda en vivo al cambiar los filtros
        $('#category, #stock_status').on('change', function() {
            performLiveSearch();
        });
        
        // Limpiar búsqueda con botón
        $('#clearSearch').on('click', function() {
            $('#search').val('').focus();
            $(this).hide();
            performLiveSearch(); // Ejecutar búsqueda inmediatamente para mostrar todos los resultados
        });
        
        // Función para mostrar notificaciones toast
        function showToast(title, message, type = 'success') {
            // Si existe la función showToast en el contexto global, úsala
            if (typeof window.showToast === 'function') {
                window.showToast(title, message, type);
            } else {
                // Implementación básica de notificación
                console.log(`${type}: ${title} - ${message}`);
            }
        }
    });
</script>
@endpush
