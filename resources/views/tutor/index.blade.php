@extends('layouts.app')

@section('content')

    <div class="mb-3">
        <h1>{{ __('Group report') }}</h1>
    </div>

    @include('tutor.partials.tabla_usuarios')

@endsection
