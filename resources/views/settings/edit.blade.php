@extends('layouts.app')

@section('content')

    @include('partials.titular', ['titular' => __('Settings')])

    <div class="card">
        <div class="card-body">
            {!! Form::open(['route' => ['settings.guardar']]) !!}

            <div class="form-group row">
                {!! Form::label('curso_id', __('Current course'), ['class' => 'col-sm-2 col-form-label']) !!}
                <div class="col-sm-10">
                    <select class="form-control" id="curso_id" name="curso_id">
                        @foreach($cursos as $curso)
                            <option value="{{ $curso->id }}" <?php if (@setting('curso_actual') == $curso->id) echo 'selected'; ?>>{{ $curso->nombre }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            @include('partials.guardar_cancelar')

            @include('layouts.errors')
            {!! Form::close() !!}

        </div>
    </div>
@endsection
