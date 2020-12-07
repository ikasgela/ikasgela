@extends('layouts.auth')

@section('auth')
    <div class="col-12 col-lg-6">
        <div class="card">
            <div class="card-body p-5">
                <div class="text-center">
                    <img src="svg/logo.svg" class="mb-5" width="200" alt="Ikasgela Logo">
                </div>
                @if(isset($current_organization) && $current_organization->isRegistrationOpen())
                    <h1>{{ __('Sign up') }}</h1>
                    <p class="text-muted">{{ __('Create your account') }}</p>

                    <form method="POST" action="{{ route('register') }}">
                        @csrf
                        <x-honey/>
                        {!! RecaptchaV3::field('register') !!}
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                            <span class="input-group-text" style="width:2.75em">
                                <i class="fas fa-user text-secondary"></i>
                            </span>
                            </div>
                            <input id="name" type="text"
                                   class="form-control{{ $errors->has('name') ? ' is-invalid' : '' }}"
                                   name="name" value="{{ old('name') }}"
                                   placeholder="{{ __('Name') }}" required autofocus>
                            <input id="surname" type="text"
                                   class="form-control{{ $errors->has('surname') ? ' is-invalid' : '' }}"
                                   name="surname" value="{{ old('surname') }}"
                                   placeholder="{{ __('Surname') }}">
                            @if($errors->has('name'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('name') }}</strong>
                                </span>
                            @endif
                        </div>
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                            <span class="input-group-text" style="width:2.75em">
                                <i class="fas fa-at text-secondary"></i>
                            </span>
                            </div>
                            <input id="email" type="email"
                                   class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}"
                                   name="email" value="{{ old('email') }}"
                                   placeholder="{{ __('Email Address') }}" required>

                            @if ($errors->has('email'))
                                <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('email') }}</strong>
                        </span>
                            @endif
                        </div>
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                            <span class="input-group-text" style="width:2.75em">
                                <i class="fas fa-at text-secondary"></i>
                            </span>
                            </div>
                            <input id="email-confirm" type="email" class="form-control"
                                   name="email_confirmation" value="{{ old('email_confirmation') }}"
                                   placeholder="{{ __('Confirm Email Address') }}" required>
                        </div>
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                            <span class="input-group-text" style="width:2.75em">
                                <i class="fas fa-lock text-secondary"></i>
                            </span>
                            </div>
                            <input id="password" type="password"
                                   class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}"
                                   placeholder="{{ __('Password') }}" name="password"
                                   required>

                            @if ($errors->has('password'))
                                <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('password') }}</strong>
                        </span>
                            @endif
                        </div>
                        <div class="input-group mb-4">
                            <div class="input-group-prepend">
                            <span class="input-group-text" style="width:2.75em">
                                <i class="fas fa-lock text-secondary"></i>
                            </span>
                            </div>
                            <input id="password-confirm" type="password" class="form-control"
                                   name="password_confirmation"
                                   placeholder="{{ __('Confirm Password') }}" required>
                        </div>
                        @if ($errors->has('g-recaptcha-response'))
                            <div class="alert alert-danger" role="alert">
                                <strong>{{ $errors->first('g-recaptcha-response') }}</strong>
                            </div>
                        @endif
                        <button type="submit" class="btn btn-block btn-success btn-primary">
                            {{ __('Create Account') }}
                        </button>
                    </form>
                @endif
            </div>
            <div class="card-footer p-4">
                <div class="row">
                    <div class="col-12">
                        <a class="btn btn-outline-primary btn-block"
                           href="{{ route('login') }}">{{ __('Sign in') }}</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('recaptcha')
    {!! RecaptchaV3::initJs() !!}
@endsection
