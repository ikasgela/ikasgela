@extends('layouts.app')

@section('content')

    <div class="row">
        <div class="col-sm-6 mx-auto">

            {{-- Tarjeta --}}
            <div class="card">
                <div class="card-header">{{ __('Welcome') }}</div>
                <div class="card-body pb-1">
                    <p>{{ __('Para utilizar ikasgela necesitas una cuenta de usuario.') }}</p>
                </div>
                <hr class="mt-0 mb-0">
                <div class="card-body pb-4 text-center">
                    <a class="btn btn-primary mr-3" href="{{ route('login') }}">{{ __('Sign in') }}</a>
                    <a class="btn btn-secondary" href="{{ route('register') }}">{{ __('Register') }}</a>
                </div>
            </div>
            {{-- Fin tarjeta--}}

        </div>
    </div>

@endsection
