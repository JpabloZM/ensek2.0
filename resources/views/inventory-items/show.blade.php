@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Detalle del Producto</h1>
        <div>
            <a href="{{ route('admin.inventory-items.edit', $item->id) }}" class="btn btn-warning btn-sm">
                <i class="fas fa-edit fa-sm"></i> Editar
            </a>
            <a href="{{ route('admin.inventory-items.index') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left fa-sm"></i> Volver
            </a>
        </div>
    </div>

    <!-- Notifications are now handled by toast system -->

    <div class="row">
        <!-- Información del producto -->
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Información del Producto</h6>
                    <span class="badge {{ $item->isLowStock() ? 'badge-danger' : 'badge-success' }} badge-lg">
                        {{ $item->isLowStock() ? 'Stock Bajo' : 'Stock Normal' }}
                    </span>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <h6 class="font-weight-bold">Nombre:</h6>
                                <p>{{ $item->name }}</p>
                            </div>
                            <div class="mb-3">
                                <h6 class="font-weight-bold">Código:</h6>
                                <p>{{ $item->code }}</p>
                            </div>
                            <div class="mb-3">
                                <h6 class="font-weight-bold">Categoría:</h6>
                                <p>{{ $item->category->name }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <h6 class="font-weight-bold">Cantidad:</h6>
                                <p>{{ $item->quantity }}</p>
                            </div>
                            <div class="mb-3">
                                <h6 class="font-weight-bold">Stock Mínimo:</h6>
                                <p>{{ $item->minimum_stock }}</p>
                            </div>
                            <div class="mb-3">
                                <h6 class="font-weight-bold">Ubicación:</h6>
                                <p>{{ $item->location ?? 'No especificada' }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <h6 class="font-weight-bold">Descripción:</h6>
                        <p>{{ $item->description ?? 'Sin descripción' }}</p>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <h6 class="font-weight-bold">Precio Unitario:</h6>
                                <p>$ {{ number_format($item->unit_price, 0, ',', '.') }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <h6 class="font-weight-bold">Valor Total:</h6>
                                <p>$ {{ number_format($item->quantity * $item->unit_price, 0, ',', '.') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Historial de movimientos -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Historial de Movimientos</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Fecha</th>
                                    <th>Tipo</th>
                                    <th>Cantidad</th>
                                    <th>Notas</th>
                                    <th>Usuario</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($movements as $movement)
                                    <tr>
                                        <td>{{ $movement->created_at->format('d/m/Y H:i') }}</td>
                                        <td>
                                            @if($movement->type == 'add')
                                                <span class="badge badge-success">Entrada</span>
                                            @elseif($movement->type == 'remove')
                                                <span class="badge badge-danger">Salida</span>
                                            @else
                                                <span class="badge badge-warning">Ajuste</span>
                                            @endif
                                        </td>
                                        <td class="text-center">{{ $movement->quantity }}</td>
                                        <td>{{ $movement->notes ?? '-' }}</td>
                                        <td>{{ $movement->user ? $movement->user->name : 'Sistema' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center">No hay movimientos registrados</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    @if($movements->count() > 0)
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <div class="text-muted">
                                Mostrando {{ $movements->firstItem() ?? 0 }} a {{ $movements->lastItem() ?? 0 }} de {{ $movements->total() }} movimientos
                            </div>
                            <div class="pagination-container">
                                {{ $movements->onEachSide(1)->links('vendor.pagination.bootstrap-4') }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        
        <!-- Gráficas y Acciones -->
        <div class="col-lg-4">
            <!-- Acciones rápidas -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Acciones Rápidas</h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <button id="btnAddStock" class="btn btn-success btn-block mb-2" 
                            data-id="{{ $item->id }}" 
                            data-name="{{ $item->name }}" 
                            data-code="{{ $item->code }}" 
                            data-current="{{ $item->quantity }}">
                            <i class="fas fa-plus-circle"></i> Añadir Stock
                        </button>
                        <button id="btnRemoveStock" class="btn btn-danger btn-block mb-2" 
                            data-id="{{ $item->id }}" 
                            data-name="{{ $item->name }}" 
                            data-code="{{ $item->code }}" 
                            data-current="{{ $item->quantity }}">
                            <i class="fas fa-minus-circle"></i> Retirar Stock
                        </button>
                        <a href="{{ route('admin.inventory-items.edit', $item->id) }}" class="btn btn-warning btn-block mb-2">
                            <i class="fas fa-edit"></i> Editar Información
                        </a>
                        <a href="{{ route('admin.inventory-items.print-barcode', $item->id) }}" class="btn btn-info btn-block" target="_blank">
                            <i class="fas fa-barcode"></i> Imprimir Código de Barras
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Gráfico de niveles de stock -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Nivel de Stock</h6>
                </div>
                <div class="card-body">
                    <div class="chart-pie pt-4 pb-2">
                        <canvas id="stockLevelChart"></canvas>
                    </div>
                    <div class="mt-4 text-center small">
                        <span class="mr-2">
                            <i class="fas fa-circle text-success"></i> Stock Actual
                        </span>
                        <span class="mr-2">
                            <i class="fas fa-circle text-danger"></i> Stock Mínimo
                        </span>
                    </div>
                </div>
            </div>
            
            <!-- Información adicional -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Información Adicional</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <h6 class="font-weight-bold">Fecha de Creación:</h6>
                        <p>{{ $item->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                    <div class="mb-3">
                        <h6 class="font-weight-bold">Última Actualización:</h6>
                        <p>{{ $item->updated_at->format('d/m/Y H:i') }}</p>
                    </div>
                    <div class="mb-3">
                        <h6 class="font-weight-bold">Último Movimiento:</h6>
                        <p>{{ $lastMovement ? ($lastMovement->created_at instanceof \Carbon\Carbon ? $lastMovement->created_at->format('d/m/Y H:i') : \Carbon\Carbon::parse($lastMovement->created_at)->format('d/m/Y H:i')) : 'No hay registros' }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para añadir stock -->
<div class="modal fade" id="addStockModal" tabindex="-1" role="dialog" aria-labelledby="addStockModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="addStockForm" action="{{ route('admin.inventory-items.add-stock', $item->id) }}" method="POST" class="needs-validation" novalidate>
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
                        <label for="current_stock">Stock Actual:</label>
                        <input type="text" class="form-control" id="current_stock" value="{{ $item->quantity }}" readonly>
                    </div>
                    <div class="form-group">
                        <label for="add_quantity">Cantidad a añadir:</label>
                        <input type="number" class="form-control" name="add_quantity" id="add_quantity" min="1" required>
                        <div class="invalid-feedback">
                            Por favor ingrese una cantidad válida (mínimo 1).
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
            <form id="removeStockForm" action="{{ route('admin.inventory-items.remove-stock', $item->id) }}" method="POST" class="needs-validation" novalidate>
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
                        <label for="current_remove_stock">Stock Actual:</label>
                        <input type="text" class="form-control" id="current_remove_stock" value="{{ $item->quantity }}" readonly>
                    </div>
                    <div class="form-group">
                        <label for="remove_quantity">Cantidad a retirar:</label>
                        <input type="number" class="form-control" name="remove_quantity" id="remove_quantity" min="1" max="{{ $item->quantity }}" required>
                        <div class="invalid-feedback">
                            Por favor ingrese una cantidad válida (mínimo 1 y no mayor al stock actual).
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="remove_notes">Notas (opcional):</label>
                        <textarea class="form-control" name="notes" id="remove_notes" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger btn-stock-submit">Confirmar Retiro</button>
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
        // Gráfico de nivel de stock
        var stockCtx = document.getElementById('stockLevelChart');
        
        // Definimos los datos de manera segura
        var stockData = {
            labels: ['Stock Actual', 'Stock Mínimo'],
            datasets: [{
                data: [
                    parseInt("{{ $item->quantity }}"),
                    parseInt("{{ $item->minimum_stock }}")
                ],
                backgroundColor: ['#1cc88a', '#e74a3b'],
                hoverBackgroundColor: ['#17a673', '#c23321'],
                hoverBorderColor: "rgba(234, 236, 244, 1)"
            }]
        };
        
        if (stockCtx) {
            new Chart(stockCtx, {
                type: 'doughnut',
                data: stockData,
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
                        caretPadding: 10
                    },
                    legend: {
                        display: false
                    },
                    cutoutPercentage: 70
                }
            });
        }
        
        // Validación para el stock a retirar
        $('#remove_quantity').on('input', function() {
            var value = parseInt($(this).val()) || 0;
            var max = parseInt($(this).attr('max'));
            
            if (value > max) {
                $(this).val(max);
                $(this).addClass('is-invalid');
                $(this).next('.invalid-feedback').text('La cantidad a retirar no puede ser mayor que ' + max);
            } else if (value <= 0) {
                $(this).addClass('is-invalid');
                $(this).next('.invalid-feedback').text('La cantidad debe ser mayor que 0');
            } else {
                $(this).removeClass('is-invalid').addClass('is-valid');
            }
        });
        
        // Validación para añadir stock
        $('#add_quantity').on('input', function() {
            var value = parseInt($(this).val()) || 0;
            
            if (value <= 0) {
                $(this).addClass('is-invalid');
                $(this).next('.invalid-feedback').text('La cantidad debe ser mayor que 0');
            } else {
                $(this).removeClass('is-invalid').addClass('is-valid');
            }
        });
        
        // Manejar eventos de botones de acción rápida
        $('#btnAddStock').click(function(e) {
            e.preventDefault();
            console.log('Botón Añadir Stock clickeado');
            // Limpiar validaciones previas
            $('#addStockForm').removeClass('was-validated');
            $('#add_quantity').val('').removeClass('is-valid is-invalid');
            $('#notes').val('');
            $('#addStockModal').modal('show');
        });
        
        $('#btnRemoveStock').click(function(e) {
            e.preventDefault();
            console.log('Botón Retirar Stock clickeado');
            // Limpiar validaciones previas
            $('#removeStockForm').removeClass('was-validated');
            $('#remove_quantity').val('').removeClass('is-valid is-invalid');
            $('#remove_notes').val('');
            $('#removeStockModal').modal('show');
        });
        
        // Mostrar indicador de carga al enviar formularios
        $('#addStockForm').on('submit', function(e) {
            console.log('Formulario de añadir stock enviado');
            if (!this.checkValidity()) {
                e.preventDefault();
                e.stopPropagation();
                $(this).addClass('was-validated');
                return false;
            }
            
            $('.btn-stock-submit', this).html('<i class="fas fa-spinner fa-spin"></i> Procesando...').prop('disabled', true);
            return true;
        });
        
        $('#removeStockForm').on('submit', function(e) {
            console.log('Formulario de retirar stock enviado');
            if (!this.checkValidity()) {
                e.preventDefault();
                e.stopPropagation();
                $(this).addClass('was-validated');
                return false;
            }
            
            const quantity = parseInt($('#remove_quantity').val()) || 0;
            const current = parseInt($('#current_remove_stock').val());
            
            if (quantity > current) {
                e.preventDefault();
                $('#remove_quantity').addClass('is-invalid');
                return false;
            }
            
            $('.btn-stock-submit', this).html('<i class="fas fa-spinner fa-spin"></i> Procesando...').prop('disabled', true);
            return true;
        });
    });
</script>
@endpush
