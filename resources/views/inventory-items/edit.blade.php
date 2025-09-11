@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Editar Producto de Inventario</h1>
        <div>
            <a href="{{ route('admin.inventory-items.show', $item->id) }}" class="btn btn-info btn-sm mr-2">
                <i class="fas fa-eye fa-sm"></i> Ver Detalles
            </a>
            <a href="{{ route('admin.inventory-items.index') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left fa-sm"></i> Volver
            </a>
        </div>
    </div>

    <!-- Formulario -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Información del Producto</h6>
        </div>
        <div class="card-body">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('admin.inventory-items.update', $item->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="name">Nombre <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $item->name) }}" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="code">Código <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="code" name="code" value="{{ old('code', $item->code) }}" required>
                            <small class="form-text text-muted">Código único para identificar el producto</small>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="inventory_category_id">Categoría <span class="text-danger">*</span></label>
                    <select class="form-control" id="inventory_category_id" name="inventory_category_id" required>
                        <option value="">Seleccione una categoría</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ (old('inventory_category_id', $item->inventory_category_id) == $category->id) ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="description">Descripción</label>
                    <textarea class="form-control" id="description" name="description" rows="3">{{ old('description', $item->description) }}</textarea>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="quantity">Cantidad <span class="text-danger">*</span></label>
                            <input type="number" class="form-control bg-light" id="quantity" name="quantity" value="{{ old('quantity', $item->quantity) }}" min="0" required readonly>
                            <small class="form-text text-muted">La cantidad debe ajustarse usando los botones de añadir/retirar stock</small>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="unit_price">Precio Unitario (COP) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">$</span>
                                </div>
                                <input type="number" class="form-control" id="unit_price" name="unit_price" value="{{ old('unit_price', $item->unit_price) }}" min="0" step="1" required>
                            </div>
                            <small class="form-text text-muted">Precio en pesos colombianos (sin decimales)</small>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="minimum_stock">Stock Mínimo <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="minimum_stock" name="minimum_stock" value="{{ old('minimum_stock', $item->minimum_stock) }}" min="1" required>
                            <small class="form-text text-muted">Cantidad mínima para alertar de stock bajo</small>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="location">Ubicación</label>
                    <input type="text" class="form-control" id="location" name="location" value="{{ old('location', $item->location) }}" placeholder="Ej: Estante A, Bodega 2, etc.">
                    <small class="form-text text-muted">Ubicación física del producto en el almacén (opcional)</small>
                </div>

                <hr class="my-4">

                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group mb-0">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save fa-sm mr-1"></i> Guardar Cambios
                            </button>
                            <a href="{{ route('admin.inventory-items.index') }}" class="btn btn-secondary ml-2">
                                <i class="fas fa-times fa-sm mr-1"></i> Cancelar
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Validación en tiempo real
        $('#name, #code, #unit_price, #minimum_stock').on('input', function() {
            if ($(this).val() === '') {
                $(this).addClass('is-invalid');
            } else {
                $(this).removeClass('is-invalid').addClass('is-valid');
            }
        });
        
        // Validación para el stock a retirar
        $('#remove_quantity').on('input', function() {
            var value = parseInt($(this).val());
            var max = parseInt($(this).attr('max'));
            
            if (value > max) {
                $(this).val(max);
            }
        });
    });
</script>
@endpush
