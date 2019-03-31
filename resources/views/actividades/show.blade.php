@extends('layouts.app')

@section('content')

    @include('partials.titular', ['titular' => __('Activity')])

    <div class="card">
        <div class="card-body">

            {!! Form::open() !!}

            {{ Form::campoTextoLabel('unidad', __('Unit'), $actividad->unidad->nombre) }}
            {{ Form::campoTextoLabel('nombre', __('Name'), $actividad->nombre) }}
            {{ Form::campoTextoLabel('descripcion', __('Description'), $actividad->descripcion) }}
            {{ Form::campoTextoLabel('slug', __('Slug'), $actividad->slug) }}
            {{ Form::campoTextoLabel('puntuacion', __('Score'), $actividad->puntuacion) }}

            {{ Form::campoCheckLabel('plantilla', __('Template'), $actividad->plantilla) }}
            {{ Form::campoTextoLabel('siguiente', __('Next'), !is_null($actividad->siguiente) ? $actividad->siguiente->slug : '') }}
            {{ Form::campoCheckLabel('final', __('Final'), $actividad->final) }}

            <div class="form-group">
                @include('partials.backbutton')
            </div>
            {!! Form::close() !!}

        </div>
    </div>
@endsection
