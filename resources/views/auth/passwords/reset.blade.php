@extends('layouts.auth')

@section('auth')
    <div class="col-md-6">
        <div class="card mx-4">
            <div class="card-body p-4">
                <div class="text-center">
                    <img src="/svg/logo.svg" class="mb-5" width="200" alt="Ikasgela Logo">
                </div>
                <h1>{{ __('Reset Password') }}</h1>
                <p class="text-muted">{{ __('Reset you password') }}</p>

                <form method="POST" action="{{ route('password.update') }}">
                    @csrf
                    <x-honey/>
                    <input type="hidden" name="token" value="{{ $token }}">

                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text" style="width:2.75em">
                                <i class="fas fa-at text-secondary"></i>
                            </span>
                        </div>
                        <input id="email" type="email"
                               class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}"
                               name="email" value="{{ old('email') }}"
                               placeholder="{{ __('Email Address') }}" required autofocus>

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
                    <button type="submit" class="btn btn-primary">
                        {{ __('Reset Password') }}
                    </button>
                </form>
            </div>
        </div>
    </div>
@endsection
