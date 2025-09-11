@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Nuevo Producto de Inventario</h1>
        <a href="{{ route('admin.inventory-items.index') }}" class="d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Volver
        </a>
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

            <form action="{{ route('admin.inventory-items.store') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="name">Nombre <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="code">Código <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="code" name="code" value="{{ old('code') }}" required>
                                <div class="input-group-append">
                                    <button type="button" class="btn btn-outline-secondary" id="generateCode">
                                        <i class="fas fa-random"></i> Generar
                                    </button>
                                </div>
                            </div>
                            <small class="form-text text-muted">Código único para identificar el producto</small>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="inventory_category_id">Categoría <span class="text-danger">*</span></label>
                    <select class="form-control" id="inventory_category_id" name="inventory_category_id" required>
                        <option value="">Seleccione una categoría</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('inventory_category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="description">Descripción</label>
                    <textarea class="form-control" id="description" name="description" rows="3">{{ old('description') }}</textarea>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="quantity">Cantidad Inicial <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="quantity" name="quantity" value="{{ old('quantity', 0) }}" min="0" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="unit_price">Precio Unitario (COP) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">$</span>
                                </div>
                                <input type="number" class="form-control" id="unit_price" name="unit_price" value="{{ old('unit_price', '0') }}" min="0" step="1" required>
                            </div>
                            <small class="form-text text-muted">Precio en pesos colombianos (sin decimales)</small>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="minimum_stock">Stock Mínimo <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="minimum_stock" name="minimum_stock" value="{{ old('minimum_stock', 5) }}" min="1" required>
                            <small class="form-text text-muted">Cantidad mínima para alertar de stock bajo</small>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="location">Ubicación</label>
                    <input type="text" class="form-control" id="location" name="location" value="{{ old('location') }}" placeholder="Ej: Estante A, Bodega 2, etc.">
                    <small class="form-text text-muted">Ubicación física del producto en el almacén (opcional)</small>
                </div>

                <hr class="my-4">

                <div class="form-group row mb-0">
                    <div class="col-md-6">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save fa-sm mr-1"></i> Guardar Producto
                        </button>
                        <a href="{{ route('admin.inventory-items.index') }}" class="btn btn-secondary ml-2">
                            <i class="fas fa-times fa-sm mr-1"></i> Cancelar
                        </a>
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
        // Generador de código único
        $('#generateCode').click(function() {
            // Generar un código con prefijo INV- seguido de 6 caracteres alfanuméricos
            var randomString = 'INV-' + Math.random().toString(36).substring(2, 8).toUpperCase();
            $('#code').val(randomString);
        });
        
        // Validación en tiempo real
        $('#name, #code, #quantity, #unit_price, #minimum_stock').on('input', function() {
            if ($(this).val() === '') {
                $(this).addClass('is-invalid');
            } else {
                $(this).removeClass('is-invalid').addClass('is-valid');
            }
        });
        
        // Creación dinámica de categoría
        $('#inventory_category_id').on('change', function() {
            if ($(this).val() === 'new') {
                Swal.fire({
                    title: 'Nueva Categoría',
                    html:
                        '<div class="form-group text-left">' +
                        '<label for="new-category-name">Nombre de la Categoría</label>' +
                        '<input id="new-category-name" class="form-control" required>' +
                        '</div>' +
                        '<div class="form-group text-left">' +
                        '<label for="new-category-description">Descripción (opcional)</label>' +
                        '<textarea id="new-category-description" class="form-control" rows="3"></textarea>' +
                        '</div>',
                    showCancelButton: true,
                    confirmButtonText: 'Crear',
                    cancelButtonText: 'Cancelar',
                    preConfirm: () => {
                        const name = document.getElementById('new-category-name').value;
                        const description = document.getElementById('new-category-description').value;
                        if (!name) {
                            Swal.showValidationMessage('El nombre de la categoría es requerido');
                        }
                        return { name, description };
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Simular la creación de categoría en tiempo real
                        // En una implementación real, esto haría una solicitud AJAX al backend
                        const newOption = new Option(result.value.name, 'new_temp', true, true);
                        $('#inventory_category_id').append(newOption).trigger('change');
                        
                        Swal.fire(
                            '¡Categoría Creada!',
                            'La categoría se ha creado exitosamente',
                            'success'
                        );
                    } else {
                        $('#inventory_category_id').val('').trigger('change');
                    }
                });
            }
        });
        
        // Cálculo automático del valor total
        $('#quantity, #unit_price').on('input', function() {
            calculateTotalValue();
        });
        
        function calculateTotalValue() {
            var quantity = parseFloat($('#quantity').val()) || 0;
            var unitPrice = parseFloat($('#unit_price').val()) || 0;
            var totalValue = quantity * unitPrice;
            
            if ($('#total_value').length) {
                $('#total_value').val(totalValue.toFixed(2));
            }
        }
    });
</script>
@endpush
