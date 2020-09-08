@extends('layouts.app')

@section('content')

    @include('partials.titular', ['titular' => __('Edit feedback message')])

    <div class="card">
        <div class="card-body">

            {!! Form::model($feedback, ['route' => ['feedbacks.update', $feedback->id], 'method' => 'PUT']) !!}

            {{ Form::campoTexto('curso_actividad',
                is_a($feedback->curso, 'App\Curso') ? __('Course') : __('Activity'),
                $feedback->curso->nombre, ['readonly'])
                }}
            {{ Form::hidden('curso_id',$feedback->curso_id) }}
            {{ Form::campoTexto('titulo', __('Title')) }}
            {{ Form::campoTexto('mensaje', __('Message')) }}

            @include('partials.guardar_cancelar')

            @include('layouts.errors')
            {!! Form::close() !!}

        </div>
    </div>
@endsection
