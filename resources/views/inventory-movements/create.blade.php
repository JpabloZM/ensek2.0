@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-2 text-gray-800">Registrar Movimiento de Inventario</h1>
    <p class="mb-4">Complete el formulario para registrar un nuevo movimiento en el inventario.</p>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Datos del Movimiento</h6>
        </div>
        <div class="card-body">
            @if(session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif
            
            <form method="POST" action="{{ route('admin.inventory-movements.store') }}">
                @csrf
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="inventory_item_id">Artículo *</label>
                            <select class="form-control @error('inventory_item_id') is-invalid @enderror" 
                                    id="inventory_item_id" 
                                    name="inventory_item_id" 
                                    required>
                                <option value="">Seleccione un artículo</option>
                                @foreach($items as $item)
                                    <option value="{{ $item->id }}" data-price="{{ $item->unit_price }}" data-stock="{{ $item->quantity }}" data-uom="{{ $item->unit_of_measure }}">
                                        {{ $item->name }} - Stock: {{ $item->quantity }} {{ $item->unit_of_measure }}
                                    </option>
                                @endforeach
                            </select>
                            @error('inventory_item_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="form-group">
                            <label for="movement_type">Tipo de Movimiento *</label>
                            <select class="form-control @error('movement_type') is-invalid @enderror" 
                                    id="movement_type" 
                                    name="movement_type" 
                                    required>
                                <option value="entry">Entrada</option>
                                <option value="exit">Salida</option>
                                <option value="adjustment">Ajuste</option>
                            </select>
                            @error('movement_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="form-group">
                            <label for="quantity">Cantidad *</label>
                            <div class="input-group">
                                <input type="number" 
                                       class="form-control @error('quantity') is-invalid @enderror" 
                                       id="quantity" 
                                       name="quantity" 
                                       min="1" 
                                       value="{{ old('quantity', 1) }}" 
                                       required>
                                <div class="input-group-append">
                                    <span class="input-group-text" id="unit-of-measure">Unidad</span>
                                </div>
                                @error('quantity')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="form-group" id="unit-price-group">
                            <label for="unit_price">Precio Unitario</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">$</span>
                                </div>
                                <input type="number" 
                                       step="0.01" 
                                       class="form-control @error('unit_price') is-invalid @enderror" 
                                       id="unit_price" 
                                       name="unit_price" 
                                       min="0" 
                                       value="{{ old('unit_price') }}">
                                @error('unit_price')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <small class="form-text text-muted">Solo para entradas. Opcional.</small>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="reference_type">Tipo de Referencia *</label>
                            <select class="form-control @error('reference_type') is-invalid @enderror" 
                                    id="reference_type" 
                                    name="reference_type" 
                                    required>
                                <option value="manual">Manual</option>
                                <option value="provider">Proveedor</option>
                                <option value="service">Servicio</option>
                                <option value="purchase">Orden de Compra</option>
                            </select>
                            @error('reference_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="form-group reference-select" id="provider-select" style="display: none;">
                            <label for="provider_id">Proveedor</label>
                            <select class="form-control" id="provider_id" name="reference_id">
                                <option value="">Seleccione un proveedor</option>
                                @foreach($providers as $provider)
                                    <option value="{{ $provider->id }}">{{ $provider->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="form-group reference-select" id="service-select" style="display: none;">
                            <label for="service_id">Servicio</label>
                            <select class="form-control" id="service_id" name="reference_id">
                                <option value="">Seleccione un servicio</option>
                                @foreach($services as $service)
                                    <option value="{{ $service->id }}">{{ $service->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="notes">Notas</label>
                            <textarea class="form-control @error('notes') is-invalid @enderror" 
                                      id="notes" 
                                      name="notes" 
                                      rows="4">{{ old('notes') }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Agregue cualquier información adicional relevante para este movimiento.</small>
                        </div>
                        
                        <div class="form-group">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h5 class="card-title">Información del Artículo</h5>
                                    <div id="item-info">
                                        <p class="mb-1">Seleccione un artículo para ver detalles</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="row mt-4">
                    <div class="col-md-12">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Registrar Movimiento
                        </button>
                        <a href="{{ route('admin.inventory-movements.index') }}" class="btn btn-secondary ml-2">
                            <i class="fas fa-arrow-left"></i> Volver
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Inicializar select2 para selectores con muchas opciones
        $('#inventory_item_id, #provider_id, #service_id').select2({
            placeholder: "Seleccione una opción",
            allowClear: true
        });
        
        // Cambiar tipo de referencia
        $('#reference_type').change(function() {
            const type = $(this).val();
            $('.reference-select').hide();
            
            if (type === 'provider') {
                $('#provider-select').show();
            } else if (type === 'service') {
                $('#service-select').show();
            }
        });
        
        // Mostrar/ocultar precio unitario según el tipo de movimiento
        $('#movement_type').change(function() {
            if ($(this).val() === 'entry') {
                $('#unit-price-group').show();
            } else {
                $('#unit-price-group').hide();
            }
        });
        
        // Actualizar información del artículo seleccionado
        $('#inventory_item_id').change(function() {
            const option = $(this).find('option:selected');
            const price = option.data('price');
            const stock = option.data('stock');
            const uom = option.data('uom');
            
            if (option.val()) {
                $('#unit-of-measure').text(uom);
                $('#unit_price').val(price);
                $('#item-info').html(`
                    <p class="mb-1"><strong>Stock actual:</strong> ${stock} ${uom}</p>
                    <p class="mb-1"><strong>Precio actual:</strong> $${price.toFixed(2)}</p>
                    <p class="mb-1"><strong>Valor en inventario:</strong> $${(stock * price).toFixed(2)}</p>
                `);
            } else {
                $('#unit-of-measure').text('Unidad');
                $('#unit_price').val('');
                $('#item-info').html('<p class="mb-1">Seleccione un artículo para ver detalles</p>');
            }
        });
        
        // Ejecutar la primera vez
        $('#movement_type').trigger('change');
    });
</script>
@endsection
