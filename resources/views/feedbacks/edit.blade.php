@extends('layouts.app')

@section('content')

    @include('partials.titular', ['titular' => __('Edit feedback message')])

    <div class="card">
        <div class="card-body">

            {!! Form::model($feedback, ['route' => ['feedbacks.update', $feedback->id], 'method' => 'PUT']) !!}

            <div class="form-group row">
                {!! Form::label('curso_id', __('Course'), ['class' => 'col-sm-2 col-form-label']) !!}
                <div class="col-sm-10">
                    <select class="form-control" id="curso_id" name="curso_id">
                        @foreach($cursos as $curso)
                            <option value="{{ $curso->id }}" <?php if ($feedback->curso_id == $curso->id) echo 'selected'; ?>>{{ $curso->nombre }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{ Form::campoTexto('mensaje', __('Message')) }}

            @include('partials.guardar_cancelar')

            @include('layouts.errors')
            {!! Form::close() !!}

        </div>
    </div>
@endsection
