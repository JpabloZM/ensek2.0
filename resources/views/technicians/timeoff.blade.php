@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Gestión de Tiempo Libre - {{ $technician->user->name }}</h5>
            <div>
                <a href="{{ route('admin.technicians.show', $technician->id) }}" class="btn btn-sm btn-secondary">
                    <i class="fas fa-arrow-left"></i> Volver
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-5">
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">Registrar Tiempo Libre</h5>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('admin.technicians.store-time-off', $technician->id) }}" method="POST">
                                @csrf
                                
                                <div class="form-group">
                                    <label for="start_date">Fecha de inicio:</label>
                                    <input type="date" class="form-control" id="start_date" name="start_date" required>
                                </div>
                                
                                <div class="form-group">
                                    <label for="end_date">Fecha de fin:</label>
                                    <input type="date" class="form-control" id="end_date" name="end_date" required>
                                </div>
                                
                                <div class="form-group">
                                    <label for="reason">Motivo:</label>
                                    <select class="form-control" id="reason" name="reason" required>
                                        <option value="vacation">Vacaciones</option>
                                        <option value="sick_leave">Baja por enfermedad</option>
                                        <option value="personal">Asunto personal</option>
                                        <option value="training">Capacitación/Formación</option>
                                        <option value="other">Otro</option>
                                    </select>
                                </div>
                                
                                <div class="form-group">
                                    <label for="notes">Notas adicionales:</label>
                                    <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
                                </div>
                                
                                <div class="form-group">
                                    <label for="status">Estado:</label>
                                    <select class="form-control" id="status" name="status" required>
                                        <option value="requested">Solicitado</option>
                                        <option value="approved">Aprobado</option>
                                        <option value="denied">Denegado</option>
                                    </select>
                                </div>
                                
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-save"></i> Guardar
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-7">
                    <div class="card">
                        <div class="card-header bg-info text-white">
                            <h5 class="mb-0">Historial de Tiempo Libre</h5>
                        </div>
                        <div class="card-body">
                            @if($technician->timeOffRequests->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Desde</th>
                                                <th>Hasta</th>
                                                <th>Motivo</th>
                                                <th>Estado</th>
                                                <th>Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($technician->timeOffRequests->sortByDesc('start_date') as $timeOff)
                                                <tr>
                                                    <td>{{ $timeOff->start_date->format('d/m/Y') }}</td>
                                                    <td>{{ $timeOff->end_date->format('d/m/Y') }}</td>
                                                    <td>
                                                        @switch($timeOff->reason)
                                                            @case('vacation')
                                                                Vacaciones
                                                                @break
                                                            @case('sick_leave')
                                                                Baja por enfermedad
                                                                @break
                                                            @case('personal')
                                                                Asunto personal
                                                                @break
                                                            @case('training')
                                                                Capacitación/Formación
                                                                @break
                                                            @default
                                                                {{ $timeOff->reason }}
                                                        @endswitch
                                                    </td>
                                                    <td>
                                                        @switch($timeOff->status)
                                                            @case('requested')
                                                                <span class="badge bg-warning text-dark">Solicitado</span>
                                                                @break
                                                            @case('approved')
                                                                <span class="badge bg-success">Aprobado</span>
                                                                @break
                                                            @case('denied')
                                                                <span class="badge bg-danger">Denegado</span>
                                                                @break
                                                            @default
                                                                <span class="badge bg-secondary">{{ $timeOff->status }}</span>
                                                        @endswitch
                                                    </td>
                                                    <td>
                                                        <button type="button" class="btn btn-sm btn-info edit-time-off" 
                                                                data-id="{{ $timeOff->id }}"
                                                                data-reason="{{ $timeOff->reason }}"
                                                                data-notes="{{ $timeOff->notes }}"
                                                                data-status="{{ $timeOff->status }}">
                                                            <i class="fas fa-edit"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="alert alert-info">
                                    No hay registros de tiempo libre para este técnico.
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para editar estado de tiempo libre -->
<div class="modal fade" id="editTimeOffModal" tabindex="-1" aria-labelledby="editTimeOffModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editTimeOffModalLabel">Actualizar Estado de Tiempo Libre</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="updateTimeOffForm" method="POST">
                @csrf
                @method('PATCH')
                <div class="modal-body">
                    <div class="form-group">
                        <label for="edit_status">Estado:</label>
                        <select class="form-control" id="edit_status" name="status" required>
                            <option value="requested">Solicitado</option>
                            <option value="approved">Aprobado</option>
                            <option value="denied">Denegado</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="edit_notes">Notas adicionales:</label>
                        <textarea class="form-control" id="edit_notes" name="notes" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Actualizar</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    $(document).ready(function() {
        // Validación de fechas
        $('#end_date').change(function() {
            const startDate = new Date($('#start_date').val());
            const endDate = new Date($(this).val());
            
            if (endDate < startDate) {
                alert('La fecha de fin no puede ser anterior a la fecha de inicio');
                $(this).val('');
            }
        });
        
        $('#start_date').change(function() {
            const startDate = new Date($(this).val());
            const endDateInput = $('#end_date');
            
            if (endDateInput.val()) {
                const endDate = new Date(endDateInput.val());
                if (endDate < startDate) {
                    alert('La fecha de fin no puede ser anterior a la fecha de inicio');
                    endDateInput.val('');
                }
            }
        });
        
        // Editar tiempo libre
        $('.edit-time-off').click(function() {
            const timeOffId = $(this).data('id');
            const reason = $(this).data('reason');
            const notes = $(this).data('notes');
            const status = $(this).data('status');
            
            $('#edit_status').val(status);
            $('#edit_notes').val(notes);
            
            const form = $('#updateTimeOffForm');
            const url = '{{ route("admin.technicians.update-time-off", ["id" => ":id", "timeOffId" => ":timeOffId"]) }}';
            const finalUrl = url.replace(':id', '{{ $technician->id }}').replace(':timeOffId', timeOffId);
            form.attr('action', finalUrl);
            
            $('#editTimeOffModal').modal('show');
        });
    });
</script>
@endpush
@endsection
