@extends('layouts.admin')

@push('styles')
<style>
    .breadcrumb {
        background-color: #f8f9fc;
    }
    
    .category-card {
        transition: all 0.3s ease;
    }
    
    .category-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    }
    
    .subcategory-list {
        padding-left: 20px;
        border-left: 2px solid #4e73df;
    }
    
    .category-inactive {
        opacity: 0.6;
    }
    
    .item-count {
        font-size: 0.8rem;
        color: #4e73df;
    }
    
    /* Estilos para los botones de acción */
    .action-buttons {
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .action-btn {
        width: 32px;
        height: 32px;
        padding: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 2px;
        border-radius: 4px;
        color: white;
    }
    
    .action-btn.btn-warning {
        background-color: #f6c23e;
    }
    
    .action-btn.btn-info {
        background-color: #36b9cc;
    }
    
    .action-btn.btn-primary {
        background-color: #4e73df;
    }
    
    .action-btn.btn-danger {
        background-color: #e74a3b;
    }
    
    .action-btn:hover {
        transform: translateY(-2px);
        transition: all 0.2s;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Detalles de Categoría</h1>
        <div>
            <a href="{{ route('admin.inventory-categories.edit', $category->id) }}" class="btn btn-warning btn-sm">
                <i class="fas fa-edit fa-sm"></i> Editar
            </a>
            <a href="{{ route('admin.inventory-categories.index') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left fa-sm"></i> Volver
            </a>
        </div>
    </div>

    <!-- Success notifications are now handled by toast system -->

    <!-- Breadcrumb de la jerarquía -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.inventory-categories.index') }}">Categorías</a></li>
            @if($category->parent)
                @php
                    $parents = [];
                    $currentCategory = $category->parent;
                    while($currentCategory) {
                        array_unshift($parents, $currentCategory);
                        $currentCategory = $currentCategory->parent;
                    }
                @endphp
                
                @foreach($parents as $parent)
                    <li class="breadcrumb-item"><a href="{{ route('admin.inventory-categories.show', $parent->id) }}">{{ $parent->name }}</a></li>
                @endforeach
            @endif
            <li class="breadcrumb-item active" aria-current="page">{{ $category->name }}</li>
        </ol>
    </nav>

    <div class="row">
        <!-- Información básica -->
        <div class="col-lg-4">
            <div class="card shadow mb-4 {{ $category->active ? '' : 'category-inactive' }}">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Información de la Categoría</h6>
                    <span class="badge badge-{{ $category->active ? 'success' : 'danger' }} badge-lg">
                        {{ $category->active ? 'Activa' : 'Inactiva' }}
                    </span>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <h6 class="font-weight-bold">Nombre:</h6>
                        <p>{{ $category->name }}</p>
                    </div>
                    <div class="mb-3">
                        <h6 class="font-weight-bold">Descripción:</h6>
                        <p>{{ $category->description ?: 'Sin descripción' }}</p>
                    </div>
                    <div class="mb-3">
                        <h6 class="font-weight-bold">Categoría Padre:</h6>
                        @if($category->parent)
                            <p><a href="{{ route('admin.inventory-categories.show', $category->parent->id) }}">{{ $category->parent->name }}</a></p>
                        @else
                            <p><i class="text-muted">Categoría principal</i></p>
                        @endif
                    </div>
                    <div class="mb-3">
                        <h6 class="font-weight-bold">Fecha de Creación:</h6>
                        <p>{{ $category->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                    <div class="mb-3">
                        <h6 class="font-weight-bold">Última Actualización:</h6>
                        <p>{{ $category->updated_at->format('d/m/Y H:i') }}</p>
                    </div>
                    
                    <div class="row mt-4">
                        <div class="col-6">
                            <div class="text-center">
                                <div class="h5 mb-0 font-weight-bold text-primary">{{ $category->inventoryItems->count() }}</div>
                                <div class="text-xs font-weight-bold text-gray-800 text-uppercase mb-1">Productos</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-center">
                                <div class="h5 mb-0 font-weight-bold text-info">{{ $category->children->count() }}</div>
                                <div class="text-xs font-weight-bold text-gray-800 text-uppercase mb-1">Subcategorías</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('admin.inventory-items.index', ['category' => $category->id]) }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-boxes fa-sm"></i> Ver Productos
                        </a>
                        <button class="btn btn-info btn-sm" data-toggle="modal" data-target="#createSubcategoryModal">
                            <i class="fas fa-folder-plus fa-sm"></i> Añadir Subcategoría
                        </button>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Subcategorías -->
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Subcategorías</h6>
                </div>
                <div class="card-body">
                    @if($category->children->count() > 0)
                        <div class="row">
                            @foreach($category->children as $child)
                                <div class="col-md-6 mb-4">
                                    <div class="card category-card h-100 {{ $child->active ? '' : 'category-inactive' }}">
                                        <div class="card-body">
                                            <h5 class="card-title">
                                                <i class="fas fa-folder text-warning mr-2"></i>
                                                {{ $child->name }}
                                                @if(!$child->active)
                                                    <span class="badge badge-danger ml-2">Inactiva</span>
                                                @endif
                                            </h5>
                                            <p class="card-text text-truncate">
                                                {{ $child->description ?: 'Sin descripción' }}
                                            </p>
                                            <div class="d-flex justify-content-between align-items-center">
                                                <small class="item-count">
                                                    <i class="fas fa-box-open mr-1"></i> {{ $child->inventoryItems->count() }} productos
                                                    @if($child->children->count() > 0)
                                                        <span class="mx-1">|</span>
                                                        <i class="fas fa-folder-open mr-1"></i> {{ $child->children->count() }} subcategorías
                                                    @endif
                                                </small>
                                                <div class="d-flex action-buttons">
                                                    <a href="{{ route('admin.inventory-categories.edit', $child->id) }}" class="btn btn-warning action-btn" title="Editar">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <a href="{{ route('admin.inventory-categories.show', $child->id) }}" class="btn btn-info action-btn" title="Ver detalles">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-folder-open fa-3x text-gray-300 mb-3"></i>
                            <p class="text-muted">Esta categoría no tiene subcategorías.</p>
                            <button class="btn btn-primary btn-sm mt-2" data-toggle="modal" data-target="#createSubcategoryModal">
                                <i class="fas fa-folder-plus"></i> Añadir Subcategoría
                            </button>
                        </div>
                    @endif
                </div>
            </div>
            
            <!-- Productos de la categoría -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Productos en esta Categoría</h6>
                </div>
                <div class="card-body">
                    @if($category->inventoryItems->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>Código</th>
                                        <th>Nombre</th>
                                        <th>Stock</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($category->inventoryItems()->take(5)->get() as $item)
                                        <tr>
                                            <td>{{ $item->code }}</td>
                                            <td>{{ $item->name }}</td>
                                            <td>{{ $item->current_stock }}</td>
                                            <td>
                                                <div class="d-flex action-buttons">
                                                    <a href="{{ route('admin.inventory-items.show', $item->id) }}" class="btn btn-info action-btn" title="Ver detalles">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @if($category->inventoryItems->count() > 5)
                            <div class="text-center mt-3">
                                <a href="{{ route('admin.inventory-items.index', ['category' => $category->id]) }}" class="btn btn-primary btn-sm">
                                    Ver todos los productos ({{ $category->inventoryItems->count() }})
                                </a>
                            </div>
                        @endif
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-box-open fa-3x text-gray-300 mb-3"></i>
                            <p class="text-muted">Esta categoría no tiene productos.</p>
                            <a href="{{ route('admin.inventory-items.create') }}?category={{ $category->id }}" class="btn btn-primary btn-sm mt-2">
                                <i class="fas fa-plus"></i> Añadir Producto
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para crear subcategoría -->
<div class="modal fade" id="createSubcategoryModal" tabindex="-1" role="dialog" aria-labelledby="createSubcategoryModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="{{ route('admin.inventory-categories.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="createSubcategoryModalLabel">Nueva Subcategoría</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="subcategory_parent_name">Categoría Padre</label>
                        <input type="text" class="form-control" id="subcategory_parent_name" value="{{ $category->name }}" readonly>
                        <input type="hidden" id="subcategory_parent_id" name="parent_id" value="{{ $category->id }}">
                    </div>
                    <div class="form-group">
                        <label for="subcategory_name">Nombre <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="subcategory_name" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="subcategory_description">Descripción</label>
                        <textarea class="form-control" id="subcategory_description" name="description" rows="3"></textarea>
                    </div>
                    <div class="form-group">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="subcategory_active" name="active" checked>
                            <label class="custom-control-label" for="subcategory_active">Subcategoría Activa</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('body_attributes')
data-has-errors="{{ $errors->any() ? 'true' : 'false' }}"
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Si hay errores de validación, mostrar el modal
        var hasErrors = document.body.getAttribute('data-has-errors') === 'true';
        
        if (hasErrors) {
            $('#createSubcategoryModal').modal('show');
        }
    });
</script>
@endsection
