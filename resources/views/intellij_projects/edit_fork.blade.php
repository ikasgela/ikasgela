@extends('layouts.app')

@section('content')

    @include('partials.titular', ['titular' => __('Edit fork')])

    <div class="card">
        <div class="card-body">

            {!! Form::model($pivote, ['route' => ['intellij_projects.update_fork', [$pivote->intellij_project_id, $pivote->actividad_id]], 'method' => 'POST']) !!}

            {{ Form::campoTexto('fork', __('Fork')) }}

            @include('partials.guardar_cancelar')

            @include('layouts.errors')
            {!! Form::close() !!}

        </div>
    </div>
@endsection
