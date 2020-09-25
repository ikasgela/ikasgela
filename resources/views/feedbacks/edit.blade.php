@extends('layouts.app')

@section('tinymce')
    @include('feedbacks.partials.tinymce')
@endsection

@section('content')

    @include('partials.titular', ['titular' => __('Edit feedback message')])

    <div class="card">
        <div class="card-body">

            {!! Form::model($feedback, ['route' => ['feedbacks.update', $feedback->id], 'method' => 'PUT']) !!}

            {{ Form::campoTexto('curso_actividad',
                is_a($feedback->curso, 'App\Curso') ? __('Course') : __('Activity'),
                is_a($feedback->curso, 'App\Curso')
                ? $feedback->curso->category->period->organization->name.' - '.$feedback->curso->category->period->name.' - '.$feedback->curso->nombre
                : $feedback->curso->unidad->curso->category->period->organization->name.' - '.$feedback->curso->unidad->curso->category->period->name.' - '.$feedback->curso->unidad->curso->nombre
                , ['readonly'])
                }}
            {{ Form::hidden('curso_id',$feedback->curso_id) }}
            {{ Form::campoTexto('titulo', __('Title')) }}

            <div class="form-group row">
                {!! Form::label('mensaje', __('Message'), ['class' => 'col-sm-2 col-form-label']) !!}
                <div class="col-sm-10">
                    <textarea rows="25" class="form-control" id="mensaje"
                              name="mensaje">{!! $feedback->mensaje !!}</textarea>
                </div>
            </div>

            @include('partials.guardar_cancelar')

            @include('layouts.errors')
            {!! Form::close() !!}

        </div>
    </div>
@endsection
