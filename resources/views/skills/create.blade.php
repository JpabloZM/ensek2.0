@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Crear Nueva Habilidad</h6>
                    <div>
                        <a href="{{ route('admin.skills.index') }}" class="btn btn-sm btn-secondary">
                            <i class="fas fa-arrow-left"></i> Volver
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif

                    <form action="{{ route('admin.skills.store') }}" method="POST">
                        @csrf
                        
                        <div class="form-group">
                            <label for="name">Nombre de la Habilidad <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="category">Categoría</label>
                            <select class="form-control @error('category') is-invalid @enderror" id="category" name="category">
                                <option value="">Seleccionar categoría</option>
                                <option value="technical" {{ old('category') == 'technical' ? 'selected' : '' }}>Técnica</option>
                                <option value="software" {{ old('category') == 'software' ? 'selected' : '' }}>Software</option>
                                <option value="hardware" {{ old('category') == 'hardware' ? 'selected' : '' }}>Hardware</option>
                                <option value="networking" {{ old('category') == 'networking' ? 'selected' : '' }}>Redes</option>
                                <option value="security" {{ old('category') == 'security' ? 'selected' : '' }}>Seguridad</option>
                                <option value="cloud" {{ old('category') == 'cloud' ? 'selected' : '' }}>Cloud</option>
                                <option value="certification" {{ old('category') == 'certification' ? 'selected' : '' }}>Certificaciones</option>
                                <option value="soft_skills" {{ old('category') == 'soft_skills' ? 'selected' : '' }}>Habilidades Blandas</option>
                                <option value="other" {{ old('category') == 'other' ? 'selected' : '' }}>Otra</option>
                            </select>
                            @error('category')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="description">Descripción</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group text-center">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Guardar Habilidad
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
