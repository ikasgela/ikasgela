@extends('layouts.app')

@section('tinymce')
    @include('feedbacks.partials.tinymce')
@endsection

@section('content')

    @include('partials.titular', ['titular' => __('New activity feedback message')])

    <div class="card">
        <div class="card-body">

            {!! Form::open(['route' => ['feedbacks.save']]) !!}

            <div class="form-group row">
                {!! Form::label('actividad_id', __('Activity'), ['class' => 'col-sm-2 col-form-label']) !!}
                <div class="col-sm-10">
                    {{ $actividad->full_name }}
                </div>
            </div>

            {!! Form::hidden('actividad_id', $actividad->id) !!}

            {{ Form::campoTexto('titulo', __('Title')) }}

            <div class="form-group row">
                {!! Form::label('mensaje', __('Message'), ['class' => 'col-sm-2 col-form-label']) !!}
                <div class="col-sm-10">
                    <textarea rows="25" class="form-control" id="mensaje"
                              name="mensaje"></textarea>
                </div>
            </div>

            @include('partials.guardar_cancelar')

            @include('layouts.errors')
            {!! Form::close() !!}

        </div>
    </div>
@endsection
