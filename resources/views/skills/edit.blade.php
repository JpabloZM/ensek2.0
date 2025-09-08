@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Editar Habilidad</h6>
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

                    <form action="{{ route('admin.skills.update', $skill->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="form-group">
                            <label for="name">Nombre de la Habilidad <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $skill->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="category">Categoría</label>
                            <select class="form-control @error('category') is-invalid @enderror" id="category" name="category">
                                <option value="">Seleccionar categoría</option>
                                <option value="technical" {{ old('category', $skill->category) == 'technical' ? 'selected' : '' }}>Técnica</option>
                                <option value="software" {{ old('category', $skill->category) == 'software' ? 'selected' : '' }}>Software</option>
                                <option value="hardware" {{ old('category', $skill->category) == 'hardware' ? 'selected' : '' }}>Hardware</option>
                                <option value="networking" {{ old('category', $skill->category) == 'networking' ? 'selected' : '' }}>Redes</option>
                                <option value="security" {{ old('category', $skill->category) == 'security' ? 'selected' : '' }}>Seguridad</option>
                                <option value="cloud" {{ old('category', $skill->category) == 'cloud' ? 'selected' : '' }}>Cloud</option>
                                <option value="certification" {{ old('category', $skill->category) == 'certification' ? 'selected' : '' }}>Certificaciones</option>
                                <option value="soft_skills" {{ old('category', $skill->category) == 'soft_skills' ? 'selected' : '' }}>Habilidades Blandas</option>
                                <option value="other" {{ old('category', $skill->category) == 'other' ? 'selected' : '' }}>Otra</option>
                            </select>
                            @error('category')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="description">Descripción</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description', $skill->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group text-center">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Actualizar Habilidad
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
