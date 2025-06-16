@extends('layouts.app')

@section('content')

    @include('partials.titular', ['titular' => __('Rubric')])

    <div class="row">
        <div class="col-md-6">
            @include('rubrics.tarjeta')
        </div>
    </div>

    @include('partials.backbutton')

@endsection
