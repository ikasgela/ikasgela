@extends('layouts.app')

@section('content')

    @include('partials.titular', ['titular' => __('Items')])

    <div class="mb-3">
        <a class="btn btn-primary" href="{{ route('items.create') }}">{{ __('New item') }}</a>
    </div>

    @include('items.tabla')

@endsection
