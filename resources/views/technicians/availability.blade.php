@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Gestión de Disponibilidad - {{ $technician->user->name }}</h5>
            <div>
                <a href="{{ route('admin.technicians.show', $technician->id) }}" class="btn btn-sm btn-secondary">
                    <i class="fas fa-arrow-left"></i> Volver
                </a>
            </div>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.technicians.store-availability', $technician->id) }}" method="POST">
                @csrf
                
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> Configure la disponibilidad regular del técnico en cada día de la semana.
                </div>
                
                <div class="availabilities-container">
                    @php
                        $daysOfWeek = [
                            0 => 'Domingo',
                            1 => 'Lunes',
                            2 => 'Martes',
                            3 => 'Miércoles',
                            4 => 'Jueves',
                            5 => 'Viernes',
                            6 => 'Sábado'
                        ];
                        
                        // Organize availabilities by day
                        $availabilitiesByDay = [];
                        foreach($technician->availabilities as $availability) {
                            $availabilitiesByDay[$availability->day_of_week][] = $availability;
                        }
                    @endphp
                    
                    @foreach($daysOfWeek as $dayNum => $dayName)
                        <div class="card mb-3">
                            <div class="card-header bg-light">
                                <h6 class="mb-0">{{ $dayName }}</h6>
                            </div>
                            <div class="card-body day-availabilities" data-day="{{ $dayNum }}">
                                @if(isset($availabilitiesByDay[$dayNum]) && count($availabilitiesByDay[$dayNum]) > 0)
                                    @foreach($availabilitiesByDay[$dayNum] as $index => $availability)
                                        <div class="row mb-2 availability-row">
                                            <input type="hidden" name="availabilities[{{ $dayNum }}_{{ $index }}][day_of_week]" value="{{ $dayNum }}">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>Hora de inicio:</label>
                                                    <input type="time" class="form-control" name="availabilities[{{ $dayNum }}_{{ $index }}][start_time]" value="{{ substr($availability->start_time, 0, 5) }}" required>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>Hora de fin:</label>
                                                    <input type="time" class="form-control" name="availabilities[{{ $dayNum }}_{{ $index }}][end_time]" value="{{ substr($availability->end_time, 0, 5) }}" required>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group mt-4">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" name="availabilities[{{ $dayNum }}_{{ $index }}][is_available]" value="1" {{ $availability->is_available ? 'checked' : '' }}>
                                                        <label class="form-check-label">Disponible</label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-1">
                                                <div class="form-group mt-4">
                                                    <button type="button" class="btn btn-danger btn-sm remove-time-slot">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="row mb-2 availability-row">
                                        <input type="hidden" name="availabilities[{{ $dayNum }}_0][day_of_week]" value="{{ $dayNum }}">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Hora de inicio:</label>
                                                <input type="time" class="form-control" name="availabilities[{{ $dayNum }}_0][start_time]" value="09:00" required>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Hora de fin:</label>
                                                <input type="time" class="form-control" name="availabilities[{{ $dayNum }}_0][end_time]" value="17:00" required>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group mt-4">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="availabilities[{{ $dayNum }}_0][is_available]" value="1">
                                                    <label class="form-check-label">Disponible</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-1">
                                            <div class="form-group mt-4">
                                                <button type="button" class="btn btn-danger btn-sm remove-time-slot">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                                
                                <div class="text-center mt-2">
                                    <button type="button" class="btn btn-primary btn-sm add-time-slot" data-day="{{ $dayNum }}">
                                        <i class="fas fa-plus"></i> Agregar horario
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                
                <div class="form-group text-center mt-4">
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save"></i> Guardar disponibilidad
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    $(document).ready(function() {
        // Add time slot
        $('.add-time-slot').click(function() {
            const day = $(this).data('day');
            const container = $(this).closest('.day-availabilities');
            const rowCount = container.find('.availability-row').length;
            
            const newRow = `
                <div class="row mb-2 availability-row">
                    <input type="hidden" name="availabilities[${day}_${rowCount}][day_of_week]" value="${day}">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Hora de inicio:</label>
                            <input type="time" class="form-control" name="availabilities[${day}_${rowCount}][start_time]" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Hora de fin:</label>
                            <input type="time" class="form-control" name="availabilities[${day}_${rowCount}][end_time]" required>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group mt-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="availabilities[${day}_${rowCount}][is_available]" value="1" checked>
                                <label class="form-check-label">Disponible</label>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-1">
                        <div class="form-group mt-4">
                            <button type="button" class="btn btn-danger btn-sm remove-time-slot">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            `;
            
            $(newRow).insertBefore($(this).parent());
        });
        
        // Remove time slot
        $(document).on('click', '.remove-time-slot', function() {
            const container = $(this).closest('.day-availabilities');
            if (container.find('.availability-row').length > 1) {
                $(this).closest('.availability-row').remove();
            } else {
                alert('Debe tener al menos un horario definido por día');
            }
        });
    });
</script>
@endpush
@endsection
