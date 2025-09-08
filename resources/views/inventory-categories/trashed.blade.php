@extends('layouts.admin')

@push('styles')
<style>
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
    
    .action-btn.btn-success {
        background-color: #1cc88a;
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
        <h1 class="h3 mb-0 text-gray-800">Categorías de Inventario Eliminadas</h1>
        <div>
            <a href="{{ route('admin.inventory-categories.index') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left fa-sm"></i> Volver a Categorías
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

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Listado de Categorías Eliminadas</h6>
        </div>
        <div class="card-body">
            @if($trashedCategories->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Descripción</th>
                                <th>Categoría Padre</th>
                                <th>Fecha de Eliminación</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($trashedCategories as $category)
                            <tr>
                                <td>{{ $category->id }}</td>
                                <td>{{ $category->name }}</td>
                                <td>{{ $category->description ?? 'N/A' }}</td>
                                <td>{{ $category->parent ? $category->parent->name : 'N/A' }}</td>
                                <td>{{ $category->deleted_at->format('d/m/Y H:i') }}</td>
                                <td>
                                    <div class="d-flex action-buttons">
                                        <form action="{{ route('admin.inventory-categories.restore', $category->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="btn btn-success action-btn" title="Restaurar">
                                                <i class="fas fa-trash-restore"></i>
                                            </button>
                                        </form>
                                        
                                        <form action="{{ route('admin.inventory-categories.force-delete', $category->id) }}" method="POST" class="delete-form">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger action-btn" title="Eliminar Permanentemente">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <div class="d-flex justify-content-center mt-3">
                    {{ $trashedCategories->links() }}
                </div>
            @else
                <div class="text-center py-4">
                    <p class="text-muted">No hay categorías eliminadas.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('.delete-form').on('submit', function(e) {
            e.preventDefault();
            
            Swal.fire({
                title: '¿Estás seguro?',
                text: "Esta acción eliminará permanentemente la categoría y no se podrá recuperar.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sí, eliminar permanentemente',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    $(this).unbind('submit').submit();
                }
            });
        });
    });
</script>
@endpush
