@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-2 text-gray-800">Reporte Detallado de Movimientos</h1>
    <p class="mb-4">
        Periodo: {{ $dateFrom }} - {{ $dateTo }}
        @if($filters['movement_type'] && $filters['movement_type'] !== 'all')
            | Tipo: {{ ucfirst($filters['movement_type']) }}
        @endif
        @if($filters['item_id'])
            | Artículo ID: {{ $filters['item_id'] }}
        @endif
    </p>

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Detalles del Reporte</h6>
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
            @if($movements->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>Fecha</th>
                                <th>Artículo</th>
                                <th>Categoría</th>
                                <th>Tipo</th>
                                <th>Cantidad</th>
                                <th>Precio Unit.</th>
                                <th>Valor Total</th>
                                <th>Usuario</th>
                                <th>Referencia</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($movements as $movement)
                            <tr>
                                <td>{{ $movement->created_at->format('d/m/Y H:i') }}</td>
                                <td>{{ $movement->item->name }}</td>
                                <td>{{ $movement->item->category->name }}</td>
                                <td>
                                    @if($movement->movement_type == 'entry')
                                        <span class="badge badge-success">{{ $movement->formatted_type }}</span>
                                    @elseif($movement->movement_type == 'exit')
                                        <span class="badge badge-danger">{{ $movement->formatted_type }}</span>
                                    @else
                                        <span class="badge badge-warning">{{ $movement->formatted_type }}</span>
                                    @endif
                                </td>
                                <td>{{ $movement->quantity }} {{ $movement->item->unit_of_measure }}</td>
                                <td>${{ number_format($movement->unit_price, 2) }}</td>
                                <td>{{ $movement->value }}</td>
                                <td>{{ $movement->user->name }}</td>
                                <td>
                                    @switch($movement->reference_type)
                                        @case('manual')
                                            Manual
                                            @break
                                        @case('provider')
                                            Proveedor: {{ $movement->reference ? $movement->reference->name : 'N/A' }}
                                            @break
                                        @case('service')
                                            Servicio: {{ $movement->reference ? $movement->reference->name : 'N/A' }}
                                            @break
                                        @case('purchase')
                                            Compra #{{ $movement->reference_id }}
                                            @break
                                        @default
                                            {{ $movement->reference_type }}
                                    @endswitch
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <!-- Resumen del Reporte -->
                <div class="row mt-4">
                    <div class="col-md-6">
                        <div class="card bg-light">
                            <div class="card-header">
                                Resumen
                            </div>
                            <div class="card-body">
                                <p class="mb-1"><strong>Total de movimientos:</strong> {{ $movements->count() }}</p>
                                <p class="mb-1"><strong>Entradas:</strong> {{ $movements->where('movement_type', 'entry')->count() }}</p>
                                <p class="mb-1"><strong>Salidas:</strong> {{ $movements->where('movement_type', 'exit')->count() }}</p>
                                <p class="mb-1"><strong>Ajustes:</strong> {{ $movements->where('movement_type', 'adjustment')->count() }}</p>
                                <hr>
                                <p class="mb-1">
                                    <strong>Valor de Entradas:</strong> 
                                    ${{ number_format($movements->where('movement_type', 'entry')->sum(function($m) { return $m->quantity * $m->unit_price; }), 2) }}
                                </p>
                                <p class="mb-1">
                                    <strong>Valor de Salidas:</strong> 
                                    ${{ number_format($movements->where('movement_type', 'exit')->sum(function($m) { return $m->quantity * $m->unit_price; }), 2) }}
                                </p>
                            </div>
                        </div>
                    </div>
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
                <a href="{{ route('admin.inventory-movements.index') }}" class="btn btn-primary ml-2">
                    <i class="fas fa-list"></i> Ir a Movimientos
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Inicializar DataTables para permitir ordenamiento y búsqueda
        $('#dataTable').DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.25/i18n/Spanish.json"
            },
            "order": [[0, 'desc']], // Ordenar por fecha descendente
            "dom": 'Bfrtip',
            "buttons": [
                'copy', 'excel', 'pdf', 'print'
            ]
        });
        
        // Manejar exportación a Excel
        $('#exportExcel').click(function(e) {
            e.preventDefault();
            $('.buttons-excel').click();
        });
        
        // Manejar exportación a PDF
        $('#exportPDF').click(function(e) {
            e.preventDefault();
            $('.buttons-pdf').click();
        });
    });
    
    // Función para imprimir
    function printReport() {
        window.print();
    }
</script>
@endsection
