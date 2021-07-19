@extends('layouts.auth')

@section('auth')
    <div class="col-12 col-lg-6">
        <div class="card mx-md-4 mx-sm-0">
            <div class="card-body p-md-5 p-sm-1">
                <div class="text-center">
                    <img src="{{ asset('/svg/logo.svg') }}" class="mb-5" width="200" alt="Ikasgela Logo">
                </div>

                <h1>{{ __('Reset Password') }}</h1>
                <p class="text-muted">{{ __('Reset you password') }}</p>

                <form method="POST" action="{{ route('password.email') }}">
                    @csrf
                    <x-honey/>
                    <div class="input-group mb-4">
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

                    <button type="submit" class="btn btn-primary">
                        {{ __('Send Password Reset Link') }}
                    </button>

                    @if (session('status'))
                        <div class="alert alert-success mt-4" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif
                </form>
            </div>
        </div>
    </div>
@endsection
