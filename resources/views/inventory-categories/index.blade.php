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
    
    .subcategory-row {
        background-color: #f8f9fc;
    }
    
    .subcategory-icon {
        margin-right: 5px;
        color: #858796;
    }
    
    .badge-subcategory {
        background-color: #36b9cc;
        color: white;
        font-size: 0.7rem;
        margin-left: 10px;
    }
    
    .category-toggle {
        cursor: pointer;
    }
    
    .category-inactive {
        opacity: 0.6;
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
        <h1 class="h3 mb-0 text-gray-800">Categorías de Inventario</h1>
        <div>
            <a href="{{ route('admin.inventory-categories.trashed') }}" class="d-sm-inline-block btn btn-sm btn-secondary shadow-sm mr-2">
                <i class="fas fa-trash fa-sm text-white-50 mr-1"></i> Ver Eliminadas
            </a>
            <a href="{{ route('admin.inventory-items.index') }}" class="d-sm-inline-block btn btn-sm btn-info shadow-sm mr-2">
                <i class="fas fa-boxes fa-sm text-white-50 mr-1"></i> Ver Inventario
            </a>
            <button class="d-sm-inline-block btn btn-sm btn-primary shadow-sm" data-toggle="modal" data-target="#createCategoryModal">
                <i class="fas fa-plus fa-sm text-white-50 mr-1"></i> Nueva Categoría
            </button>
        </div>
    </div>

    <!-- Card de categorías -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Lista de Categorías</h6>
            <div>
                <button class="btn btn-sm btn-outline-primary" id="expandAllBtn">
                    <i class="fas fa-plus-square mr-1"></i> Expandir Todo
                </button>
                <button class="btn btn-sm btn-outline-secondary" id="collapseAllBtn">
                    <i class="fas fa-minus-square mr-1"></i> Colapsar Todo
                </button>
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

            <div class="table-responsive">
                <table class="table table-bordered datatable-table" id="dt-table" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th width="35%">Nombre</th>
                            <th width="35%">Descripción</th>
                            <th width="10%">Estado</th>
                            <th width="10%">Contenido</th>
                            <th width="10%">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($rootCategories as $category)
                            <tr class="{{ $category->active ? '' : 'category-inactive' }}" data-category-id="{{ $category->id }}">
                                <td>
                                    @if($category->children_count > 0)
                                        <span class="category-toggle" data-category-id="{{ $category->id }}">
                                            <i class="fas fa-caret-right mr-1" id="icon-{{ $category->id }}"></i>
                                        </span>
                                    @else
                                        <i class="fas fa-folder mr-1 text-warning"></i>
                                    @endif
                                    {{ $category->name }}
                                </td>
                                <td>{{ $category->description ?? 'Sin descripción' }}</td>
                                <td>
                                    @if($category->active)
                                        <span class="badge badge-success">Activa</span>
                                    @else
                                        <span class="badge badge-danger">Inactiva</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex justify-content-around">
                                        <span class="badge badge-primary badge-pill" title="Productos">
                                            <i class="fas fa-box-open"></i> {{ $category->inventory_items_count }}
                                        </span>
                                        <span class="badge badge-info badge-pill" title="Subcategorías">
                                            <i class="fas fa-folder"></i> {{ $category->children_count }}
                                        </span>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex action-buttons">
                                        <a href="{{ route('admin.inventory-categories.edit', $category->id) }}" class="btn btn-warning action-btn" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="{{ route('admin.inventory-categories.show', $category->id) }}" class="btn btn-info action-btn" title="Ver detalles">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <button class="btn btn-primary action-btn add-subcategory-btn" title="Añadir subcategoría" 
                                            data-toggle="modal" 
                                            data-target="#createSubcategoryModal"
                                            data-parent-id="{{ $category->id }}"
                                            data-parent-name="{{ $category->name }}">
                                            <i class="fas fa-folder-plus"></i>
                                        </button>
                                        <form action="{{ route('admin.inventory-categories.destroy', $category->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger action-btn" title="Eliminar" 
                                                {{ $category->inventory_items_count > 0 || $category->children_count > 0 ? 'disabled' : '' }}>
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">No hay categorías registradas</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="d-flex justify-content-between align-items-center mt-3">
                <div class="text-muted">
                    Mostrando {{ $rootCategories->firstItem() ?? 0 }} a {{ $rootCategories->lastItem() ?? 0 }} de {{ $rootCategories->total() }} resultados
                </div>
                <div class="pagination-container">
                    {{ $rootCategories->onEachSide(1)->links('vendor.pagination.bootstrap-4') }}
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para crear categoría principal -->
<div class="modal fade" id="createCategoryModal" tabindex="-1" role="dialog" aria-labelledby="createCategoryModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="{{ route('admin.inventory-categories.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="createCategoryModalLabel">Nueva Categoría Principal</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="name">Nombre <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="description">Descripción</label>
                        <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="parent_id">Categoría Padre (Opcional)</label>
                        <select class="form-control" id="parent_id" name="parent_id">
                            <option value="">Ninguna (Categoría Principal)</option>
                            @foreach($availableParents as $parent)
                                <option value="{{ $parent->id }}">{{ $parent->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="active" name="active" checked>
                            <label class="custom-control-label" for="active">Categoría Activa</label>
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
                        <input type="text" class="form-control" id="subcategory_parent_name" readonly>
                        <input type="hidden" id="subcategory_parent_id" name="parent_id">
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

@push('scripts')
<script>
    $(document).ready(function() {
        // Primero, contamos las columnas en el encabezado de la tabla
        var columnCount = $('#dataTable thead th').length;
        console.log('Número de columnas detectadas en el encabezado (categorías):', columnCount);
        
        // DataTables con configuración simple
        console.log('Sistema de búsqueda personalizado aplicado a la tabla de categorías');
        
        // Manejar botón de añadir subcategoría
        $('.add-subcategory-btn').click(function() {
            const parentId = $(this).data('parent-id');
            const parentName = $(this).data('parent-name');
            
            $('#subcategory_parent_id').val(parentId);
            $('#subcategory_parent_name').val(parentName);
        });
        
        // Manejar la expansión/colapso de categorías
        $('.category-toggle').on('click', function() {
            const categoryId = $(this).data('category-id');
            const icon = $('#icon-' + categoryId);
            
            if (icon.hasClass('fa-caret-right')) {
                // Expandir
                icon.removeClass('fa-caret-right').addClass('fa-caret-down');
                loadSubcategories(categoryId);
            } else {
                // Colapsar
                icon.removeClass('fa-caret-down').addClass('fa-caret-right');
                // Eliminar todas las filas de subcategoría para esta categoría
                $('.subcategory-row[data-parent="' + categoryId + '"]').remove();
            }
        });
        
        // Botones de expandir/colapsar todo
        $('#expandAllBtn').on('click', function() {
            $('.category-toggle').each(function() {
                const categoryId = $(this).data('category-id');
                const icon = $('#icon-' + categoryId);
                
                if (icon.hasClass('fa-caret-right')) {
                    icon.removeClass('fa-caret-right').addClass('fa-caret-down');
                    loadSubcategories(categoryId);
                }
            });
        });
        
        $('#collapseAllBtn').on('click', function() {
            $('.category-toggle').each(function() {
                const categoryId = $(this).data('category-id');
                const icon = $('#icon-' + categoryId);
                
                if (icon.hasClass('fa-caret-down')) {
                    icon.removeClass('fa-caret-down').addClass('fa-caret-right');
                    $('.subcategory-row[data-parent="' + categoryId + '"]').remove();
                }
            });
        });
        
        // Función para cargar subcategorías mediante AJAX
        function loadSubcategories(parentId) {
            // Verificar si ya se cargaron las subcategorías para evitar duplicados
            if ($('.subcategory-row[data-parent="' + parentId + '"]').length > 0) {
                return;
            }
            
            // Mostrar un indicador de carga
            const parentRow = $('tr[data-category-id="' + parentId + '"]');
            const loadingRow = $('<tr class="subcategory-row" data-parent="' + parentId + '"><td colspan="5" class="text-center"><i class="fas fa-spinner fa-spin"></i> Cargando subcategorías...</td></tr>');
            parentRow.after(loadingRow);
            
            // Simular carga con timeout
            setTimeout(function() {
                loadingRow.remove();
                
                // Por ahora, mostraremos un mensaje para implementación futura
                const messageRow = $('<tr class="subcategory-row" data-parent="' + parentId + '"><td colspan="5" class="text-center font-italic">La carga de subcategorías se implementará en la próxima actualización. Por favor utilice la vista de detalles para ver las subcategorías.</td></tr>');
                parentRow.after(messageRow);
            }, 500);
        }
        
        // SweetAlert para confirmar eliminación
        $('.table').on('submit', 'form', function(e) {
            const hasItems = $(this).find('button[disabled]').length > 0;
            
            if (hasItems) {
                e.preventDefault();
                Swal.fire({
                    title: 'No se puede eliminar',
                    text: "Esta categoría tiene productos o subcategorías asociadas y no puede ser eliminada.",
                    icon: 'error',
                    confirmButtonColor: '#3085d6',
                    confirmButtonText: 'Entendido'
                });
                return false;
            }
            
            e.preventDefault();
            const form = this;
            
            Swal.fire({
                title: '¿Está seguro?',
                text: "Esta categoría será enviada a la papelera",
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
    });
</script>
@endpush
