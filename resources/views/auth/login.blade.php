@extends('layouts.auth')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-lg border-0 rounded-lg mt-5 auth-card">
                <div class="card-header text-white text-center py-4">
                    <h3 class="font-weight-light my-2">{{ __('Iniciar Sesión') }}</h3>
                </div>
                <div class="card-body">
                    <div class="small text-center text-muted mb-4">{{ __('Ingrese sus credenciales para acceder al sistema') }}</div>
                    <form method="POST" action="{{ route('login') }}">
                        @csrf
                        
                        <div class="form-floating mb-4">
                            <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus placeholder="{{ __('Correo Electrónico') }}">
                            <label for="email">{{ __('Correo Electrónico') }}</label>
                            @error('email')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        
                        <div class="form-floating mb-4">
                            <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password" placeholder="{{ __('Contraseña') }}">
                            <label for="password">{{ __('Contraseña') }}</label>
                            @error('password')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        
                        <div class="form-check mb-4">
                            <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                            <label class="form-check-label" for="remember">
                                {{ __('Recordarme') }}
                            </label>
                        </div>
                        
                        <div class="d-flex align-items-center justify-content-between mt-4 mb-0">
                            @if (Route::has('password.request'))
                                <a class="small link-ensek" href="{{ route('password.request') }}">
                                    {{ __('¿Olvidó su contraseña?') }}
                                </a>
                            @endif
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="fas fa-sign-in-alt me-2"></i> {{ __('Iniciar sesión') }}
                            </button>
                        </div>
                    </form>
                </div>
                <div class="card-footer text-center py-3 bg-light">
                    @if (Route::has('register'))
                        <div class="small">
                            <a href="{{ route('register') }}" class="link-ensek">{{ __('¿Necesita una cuenta? Regístrese') }}</a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
