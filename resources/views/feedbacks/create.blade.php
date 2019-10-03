@extends('layouts.app')

@section('content')

    @include('partials.titular', ['titular' => __('New feedback message')])

    <div class="card">
        <div class="card-body">

            {!! Form::open(['route' => ['feedbacks.store']]) !!}

            <div class="form-group row">
                {!! Form::label('curso_id', __('Course'), ['class' => 'col-sm-2 col-form-label']) !!}
                <div class="col-sm-10">
                    <select class="form-control" id="curso_id" name="curso_id">
                        @foreach($cursos as $curso)
                            <option value="{{ $curso->id }}">
                                {{ $curso->category->period->organization->name }} - {{ $curso->nombre }}
                            </option>
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
