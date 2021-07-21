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
                is_a($feedback->comentable, 'App\Models\Curso') ? __('Course') : __('Activity'),
                is_a($feedback->comentable, 'App\Models\Curso')
                ? $feedback->comentable->category->period->organization->name.' - '.$feedback->comentable->category->period->name.' - '.$feedback->comentable->nombre
                : $feedback->comentable->unidad->curso->category->period->organization->name.' - '.$feedback->comentable->unidad->curso->category->period->name.' - '.$feedback->comentable->unidad->curso->nombre
                , ['readonly'])
                }}
            {{ Form::hidden('comentable_id',$feedback->comentable_id) }}
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
