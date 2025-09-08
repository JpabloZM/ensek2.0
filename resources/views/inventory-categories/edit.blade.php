@extends('layouts.admin')

@section('body_attributes')
data-has-items="{{ $category->inventoryItems->count() }}" 
data-original-parent-id="{{ $category->parent_id ?? '' }}"
@endsection

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Editar Categoría</h1>
        <div>
            <a href="{{ route('admin.inventory-categories.show', $category->id) }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left fa-sm"></i> Volver a Detalles
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

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif
    
    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <!-- Formulario de Edición -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Información de la Categoría</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.inventory-categories.update', $category->id) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="name">Nombre <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $category->name) }}" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="parent_id">Categoría Padre</label>
                            <select class="form-control" id="parent_id" name="parent_id">
                                <option value="">Ninguna (Categoría Principal)</option>
                                @foreach($availableParents as $parent)
                                    <option value="{{ $parent->id }}" {{ old('parent_id', $category->parent_id) == $parent->id ? 'selected' : '' }}>
                                        {{ $parent->name }}
                                    </option>
                                @endforeach
                            </select>
                            <small class="form-text text-muted">
                                <i class="fas fa-info-circle mr-1"></i> Cambiar la categoría padre puede afectar la jerarquía de categorías
                            </small>
                        </div>
                        
                        <div class="form-group">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" id="active" name="active" 
                                    {{ old('active', $category->active) ? 'checked' : '' }}>
                                <label class="custom-control-label" for="active">Categoría Activa</label>
                            </div>
                            <small class="form-text text-muted">
                                <i class="fas fa-info-circle mr-1"></i> Las categorías inactivas no se mostrarán en los listados públicos
                            </small>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="description">Descripción</label>
                            <textarea class="form-control" id="description" name="description" rows="7">{{ old('description', $category->description) }}</textarea>
                        </div>
                    </div>
                </div>
                
                <hr>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="d-flex align-items-center">
                            <span class="mr-3">Creada: {{ $category->created_at->format('d/m/Y H:i') }}</span>
                            <span>Actualizada: {{ $category->updated_at->format('d/m/Y H:i') }}</span>
                        </div>
                    </div>
                    <div class="col-md-6 text-right">
                        <a href="{{ route('admin.inventory-categories.show', $category->id) }}" class="btn btn-secondary">Cancelar</a>
                        <button type="submit" class="btn btn-primary">Actualizar Categoría</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Información adicional -->
    <div class="row">
        <div class="col-md-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Subcategorías ({{ $category->children->count() }})</h6>
                </div>
                <div class="card-body">
                    @if($category->children->count() > 0)
                        <ul class="list-group">
                            @foreach($category->children as $child)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <i class="fas fa-folder text-warning mr-2"></i>
                                        {{ $child->name }}
                                        @if(!$child->active)
                                            <span class="badge badge-danger ml-2">Inactiva</span>
                                        @endif
                                    </div>
                                    <a href="{{ route('admin.inventory-categories.edit', $child->id) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-center text-muted mb-0">Esta categoría no tiene subcategorías</p>
                    @endif
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Productos ({{ $category->inventoryItems->count() }})</h6>
                </div>
                <div class="card-body">
                    @if($category->inventoryItems->count() > 0)
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle mr-2"></i>
                            Esta categoría tiene productos asociados. Si la desactiva, estos productos seguirán existiendo pero no se mostrarán correctamente.
                        </div>
                        <a href="{{ route('admin.inventory-items.index', ['category' => $category->id]) }}" class="btn btn-outline-primary btn-block">
                            <i class="fas fa-boxes mr-2"></i> Ver todos los productos
                        </a>
                    @else
                        <p class="text-center text-muted mb-0">Esta categoría no tiene productos</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Código JavaScript sin interpolaciones Blade
    document.addEventListener('DOMContentLoaded', function() {
        // Obtenemos los valores desde atributos data en HTML
        var parentSelect = document.getElementById('parent_id');
        if (!parentSelect) return;
        
        var hasItems = parseInt(document.body.getAttribute('data-has-items') || '0');
        var originalParentId = document.body.getAttribute('data-original-parent-id') || '';
        
        // Solo activar el evento si hay productos asociados
        if (hasItems > 0) {
            parentSelect.addEventListener('change', function() {
                if (this.value !== originalParentId) {
                    Swal.fire({
                        title: '¿Cambiar categoría padre?',
                        text: "Esta categoría tiene productos asociados. Cambiar la categoría padre puede afectar la organización del inventario.",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Sí, cambiar',
                        cancelButtonText: 'Cancelar'
                    }).then(function(result) {
                        if (!result.isConfirmed) {
                            parentSelect.value = originalParentId;
                        }
                    });
                }
            });
        }
    });
</script>
@endsection
