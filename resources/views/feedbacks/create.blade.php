@extends('layouts.app')

@section('tinymce')
    @include('feedbacks.partials.tinymce')
@endsection

@section('content')

    @include('partials.titular', ['titular' => __('New course feedback message')])

    <div class="card">
        <div class="card-body">

            {!! Form::open(['route' => ['feedbacks.store']]) !!}

            <div class="form-group row">
                {!! Form::label('comentable_id', __('Course'), ['class' => 'col-sm-2 col-form-label']) !!}
                <div class="col-sm-10">
                    <select class="form-control" id="comentable_id" name="comentable_id">
                        @foreach($cursos as $curso)
                            <option value="{{ $curso?->id }}" {{ $curso?->id == $curso_actual?->id ? 'selected' : '' }}>
                                {{ $curso->category->period->organization->name }}
                                - {{ $curso->category->period->name }} - {{ $curso->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

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
