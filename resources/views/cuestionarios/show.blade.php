@extends('layouts.app')

@section('content')

    @include('partials.titular', ['titular' => __('Questionnaire')])

    <div class="row">
        <div class="col-md-6">
            @include('cuestionarios.tarjeta')
        </div>
    </div>

    @include('partials.backbutton')

@endsection