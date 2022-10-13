@extends('layouts.app')

@section('content')

    @include('partials.titular', ['titular' => __('Selector')])

    <div class="row">
        <div class="col-md-6">
            @include('selectors.tarjeta')
        </div>
    </div>

    @include('partials.backbutton')

@endsection
