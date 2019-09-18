@extends('layouts.app')

@section('content')

    @include('partials.titular', ['titular' => __('Edit course')])

    <div class="card">
        <div class="card-body">

            {!! Form::model($curso, ['route' => ['cursos.update', $curso->id], 'method' => 'PUT']) !!}

            <div class="form-group row">
                {!! Form::label('category_id', __('Category'), ['class' => 'col-sm-2 col-form-label']) !!}
                <div class="col-sm-10">
                    <select class="form-control" id="category_id" name="category_id">
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" <?php if ($curso->category_id == $category->id) echo 'selected'; ?>>{{ $category->period->organization->name }}
                                - {{ $category->period->name }} - {{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{ Form::campoTexto('nombre', __('Name')) }}
            {{ Form::campoTexto('descripcion', __('Description')) }}
            {{ Form::campoTexto('slug', __('Slug')) }}

            <div class="form-group row">
                {!! Form::label('qualification_id', __('Qualification'), ['class' => 'col-sm-2 col-form-label']) !!}
                <div class="col-sm-10">
                    <select class="form-control" id="qualification_id" name="qualification_id">
                        <option value="">{{ __('--- None ---') }}</option>
                        @foreach($qualifications as $qualification)
                            <option value="{{ $qualification->id }}" <?php if ($curso->qualification_id == $qualification->id) echo 'selected'; ?>>{{ $qualification->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{ Form::campoTexto('max_simultaneas', __('Simultaneous activities')) }}

            @include('partials.guardar_cancelar')

            @include('layouts.errors')
            {!! Form::close() !!}

        </div>
    </div>
@endsection
