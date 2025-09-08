@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Gestión de Habilidades - {{ $technician->user->name }}</h5>
            <div>
                <a href="{{ route('admin.technicians.show', $technician->id) }}" class="btn btn-sm btn-secondary">
                    <i class="fas fa-arrow-left"></i> Volver
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-12">
                    <form action="{{ route('admin.technicians.store-skills', $technician->id) }}" method="POST">
                        @csrf
                        
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> Seleccione las habilidades del técnico y su nivel de competencia (1-5).
                        </div>
                        
                        <div id="skills-container" class="mb-4">
                            @if($technician->skills->count() > 0)
                                @foreach($technician->skills as $index => $techSkill)
                                    <div class="row mb-2 skill-row">
                                        <div class="col-md-5">
                                            <div class="form-group">
                                                <label>Habilidad:</label>
                                                <select class="form-control skill-select" name="skills[{{ $index }}][id]" required>
                                                    @foreach($skills as $skill)
                                                        <option value="{{ $skill->id }}" {{ $techSkill->id == $skill->id ? 'selected' : '' }}>
                                                            {{ $skill->name }} {{ $skill->category ? "({$skill->category})" : '' }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Nivel de competencia (1-5):</label>
                                                <div class="d-flex">
                                                    <input type="range" class="form-control-range flex-grow-1 me-2" 
                                                        name="skills[{{ $index }}][proficiency_level]" 
                                                        min="1" max="5" step="1" 
                                                        value="{{ $techSkill->pivot->proficiency_level }}"
                                                        oninput="this.nextElementSibling.value = this.value">
                                                    <output>{{ $techSkill->pivot->proficiency_level }}</output>
                                                </div>
                                                <div class="d-flex justify-content-between small text-muted">
                                                    <span>Básico</span>
                                                    <span>Intermedio</span>
                                                    <span>Experto</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-1">
                                            <div class="form-group mt-4">
                                                <button type="button" class="btn btn-danger btn-sm remove-skill">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="row mb-2 skill-row">
                                    <div class="col-md-5">
                                        <div class="form-group">
                                            <label>Habilidad:</label>
                                            <select class="form-control skill-select" name="skills[0][id]" required>
                                                <option value="">Seleccionar habilidad</option>
                                                @foreach($skills as $skill)
                                                    <option value="{{ $skill->id }}">
                                                        {{ $skill->name }} {{ $skill->category ? "({$skill->category})" : '' }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Nivel de competencia (1-5):</label>
                                            <div class="d-flex">
                                                <input type="range" class="form-control-range flex-grow-1 me-2" 
                                                    name="skills[0][proficiency_level]" 
                                                    min="1" max="5" step="1" 
                                                    value="3"
                                                    oninput="this.nextElementSibling.value = this.value">
                                                <output>3</output>
                                            </div>
                                            <div class="d-flex justify-content-between small text-muted">
                                                <span>Básico</span>
                                                <span>Intermedio</span>
                                                <span>Experto</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-1">
                                        <div class="form-group mt-4">
                                            <button type="button" class="btn btn-danger btn-sm remove-skill">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                        
                        <div class="mb-4 text-center">
                            <button type="button" class="btn btn-primary" id="add-skill">
                                <i class="fas fa-plus"></i> Agregar otra habilidad
                            </button>
                        </div>
                        
                        <div class="form-group text-center">
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-save"></i> Guardar habilidades
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    $(document).ready(function() {
        // Add new skill row
        $('#add-skill').click(function() {
            const skillsContainer = $('#skills-container');
            const rowCount = $('.skill-row').length;
            
            const skillOptions = `
                @foreach($skills as $skill)
                    <option value="{{ $skill->id }}">
                        {{ $skill->name }} {{ $skill->category ? "({$skill->category})" : '' }}
                    </option>
                @endforeach
            `;
            
            const newRow = `
                <div class="row mb-2 skill-row">
                    <div class="col-md-5">
                        <div class="form-group">
                            <label>Habilidad:</label>
                            <select class="form-control skill-select" name="skills[${rowCount}][id]" required>
                                <option value="">Seleccionar habilidad</option>
                                ${skillOptions}
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Nivel de competencia (1-5):</label>
                            <div class="d-flex">
                                <input type="range" class="form-control-range flex-grow-1 me-2" 
                                    name="skills[${rowCount}][proficiency_level]" 
                                    min="1" max="5" step="1" 
                                    value="3"
                                    oninput="this.nextElementSibling.value = this.value">
                                <output>3</output>
                            </div>
                            <div class="d-flex justify-content-between small text-muted">
                                <span>Básico</span>
                                <span>Intermedio</span>
                                <span>Experto</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-1">
                        <div class="form-group mt-4">
                            <button type="button" class="btn btn-danger btn-sm remove-skill">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            `;
            
            skillsContainer.append(newRow);
            
            // Initialize select2 on the new select element if select2 is available
            if ($.fn.select2) {
                $('.skill-select').select2();
            }
        });
        
        // Remove skill row
        $(document).on('click', '.remove-skill', function() {
            if ($('.skill-row').length > 1) {
                $(this).closest('.skill-row').remove();
                
                // Renumber the name attributes to maintain sequential indexes
                $('.skill-row').each(function(index) {
                    $(this).find('select').attr('name', `skills[${index}][id]`);
                    $(this).find('input[type="range"]').attr('name', `skills[${index}][proficiency_level]`);
                });
            } else {
                alert('Debe tener al menos una habilidad');
            }
        });
        
        // Initialize select2 if available
        if ($.fn.select2) {
            $('.skill-select').select2();
        }
    });
</script>
@endpush
@endsection
