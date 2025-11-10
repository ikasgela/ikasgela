@extends('layouts.app')

@section('content')

    @include('partials.titular', ['titular' => __('Test result')])

    <div class="row">
        <div class="col-md-12">
            @include('test_results.tarjeta')
        </div>
    </div>

    @include('partials.backbutton')

@endsection
