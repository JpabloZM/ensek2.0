@extends('layouts.auth')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-lg border-0 rounded-lg mt-5 auth-card">
                <div class="card-header text-white text-center py-4">
                    <h3 class="font-weight-light my-2">{{ __('Registrarse') }}</h3>
                </div>
                
                <div class="card-body">
                    <div class="small text-center text-muted mb-4">{{ __('Complete el formulario para crear una cuenta') }}</div>
                    <form method="POST" action="{{ route('register') }}">
                        @csrf

                        <div class="form-floating mb-3">
                            <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required autocomplete="name" autofocus placeholder="{{ __('Nombre') }}">
                            <label for="name">{{ __('Nombre') }}</label>
                            @error('name')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        
                        <div class="form-floating mb-3">
                            <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" placeholder="{{ __('Correo Electrónico') }}">
                            <label for="email">{{ __('Correo Electrónico') }}</label>
                            @error('email')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        
                        <div class="form-floating mb-3">
                            <input id="phone" type="text" class="form-control @error('phone') is-invalid @enderror" name="phone" value="{{ old('phone') }}" autocomplete="tel" placeholder="{{ __('Teléfono') }}">
                            <label for="phone">{{ __('Teléfono') }}</label>
                            @error('phone')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="form-floating mb-3 mb-md-0">
                                    <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password" placeholder="{{ __('Contraseña') }}">
                                    <label for="password">{{ __('Contraseña') }}</label>
                                    @error('password')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-floating mb-3 mb-md-0">
                                    <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password" placeholder="{{ __('Confirmar Contraseña') }}">
                                    <label for="password-confirm">{{ __('Confirmar Contraseña') }}</label>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mt-4 mb-0">
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary btn-block py-2">
                                    {{ __('Registrarse') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
                
                <div class="card-footer text-center py-3 bg-light">
                    <div class="small">
                        <a href="{{ route('login') }}" class="link-ensek">{{ __('¿Ya tiene una cuenta? Inicie sesión') }}</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


@endsection
