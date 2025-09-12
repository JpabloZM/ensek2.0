@extends('layouts.auth')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-lg border-0 rounded-lg mt-5 auth-card">
                <div class="card-header text-white text-center py-4">
                    <h3 class="font-weight-light my-2">{{ __('Restablecer Contraseña') }}</h3>
                </div>

                <div class="card-body">
                    <div class="small text-center text-muted mb-4">{{ __('Ingrese su correo para recibir un enlace de recuperación') }}</div>
                    
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('password.email') }}">
                        @csrf

                        <div class="form-floating mb-3">
                            <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus placeholder="{{ __('Email Address') }}">
                            <label for="email">{{ __('Email Address') }}</label>
                            @error('email')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="d-flex align-items-center justify-content-between mt-4 mb-0">
                            <a class="small link-ensek" href="{{ route('login') }}">
                                {{ __('Volver al inicio de sesión') }}
                            </a>
                            <button type="submit" class="btn btn-primary">
                                {{ __('Enviar enlace de recuperación') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>


@endsection
