@extends('layouts.app')

@section('content')

    <div class="row">
        <div class="col-md">
            <h1>{{ __('Welcome') }}</h1>
            <p>Para utilizar Ikasgela necesitas una cuenta de usuario. Si ya tienes una, <a href="{{ route('login') }}">inicia sesión</a>; si no, <a
                        href="{{ route('register') }}">regístrate</a>.</p>
        </div>
    </div>

@endsection
