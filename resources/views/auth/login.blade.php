@extends('layouts.auth')

@section('auth')
    <div class="col-12 col-lg-8 pb-4">
        <div class="card-group">
            <div class="card">
                <div class="card-body p-5">
                    <div class="text-center d-lg-none">
                        <img src="svg/logo.svg" class="mb-5" width="200"
                             alt="Ikasgela Logo">
                    </div>
                    <h1>{{ __('Sign in') }}</h1>
                    <p class="text-muted">{{ __('Sign in to your account') }}</p>

                    <form method="POST" action="{{ route('login') }}">
                        @csrf
                        <x-honey/>
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                            <span class="input-group-text" style="width:2.75em">
                                <i class="fas fa-at text-secondary"></i>
                            </span>
                            </div>
                            <input id="email" type="email"
                                   class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}"
                                   name="email" value="{{ old('email') }}"
                                   placeholder="{{ __('Email Address') }}" required
                                   autofocus>

                            @if ($errors->has('email'))
                                <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('email') }}</strong>
                        </span>
                            @endif
                        </div>
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                            <span class="input-group-text" style="width:2.75em">
                                <i class="fas fa-lock text-secondary"></i>
                            </span>
                            </div>
                            <input id="password" type="password"
                                   class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}"
                                   name="password" placeholder="{{ __('Password') }}"
                                   required>

                            @if ($errors->has('password'))
                                <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('password') }}</strong>
                        </span>
                            @endif
                        </div>
                        <div class="input-group mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox"
                                       name="remember"
                                       id="remember" {{ old('remember') ? 'checked' : '' }}>

                                <label class="form-check-label" for="remember">
                                    {{ __('Remember Me') }}
                                </label>
                            </div>
                        </div>
                        @if(session('message'))
                            <div class="alert alert-danger" role="alert">
                                {{ session('message') }}
                            </div>
                        @endif
                        <div class="row d-flex align-items-center">
                            <div class="col-12 col-md-6 m-0">
                                <button dusk="boton-submit" type="submit" class="btn btn-primary btn-block">
                                    {{ __('Login') }}
                                </button>
                            </div>
                            <div class="col-12 col-md-6 mt-3 mt-md-0 text-center">
                                <a class="btn btn-link text-primary text-sm-center"
                                   href="{{ route('password.request') }}">
                                    {{ __('Forgotten password?') }}
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
                @if(isset($current_organization) && $current_organization->isRegistrationOpen())
                    <div class="card-footer p-4 d-lg-none">
                        <div class="col-12 text-right">
                            <a class="btn btn-outline-primary btn-block mt-3"
                               href="{{ route('register') }}">{{ __('Register') }}</a>
                        </div>
                    </div>
                @endif
            </div>
            <div class="card text-white bg-primary py-5 d-md-down-none">
                <div class="card-body text-center">
                    <div style="height:20em">
                        <img src="svg/logo-dark.svg" class="mb-5" width="200" alt="Logo">
                        @if(isset($current_organization) && $current_organization->isRegistrationOpen())
                            <h2>{{ __('Sign up') }}</h2>
                            <p>{{ __("If you don't have account, create one.") }}</p>
                            <a class="btn btn-link btn-light text-dark mt-2"
                               href="{{ route('register') }}">{{ __('Register Now!') }}</a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
