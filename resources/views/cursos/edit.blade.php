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
                            <option value="{{ $category->id }}" <?php if ($curso->category_id == $category->id) echo 'selected'; ?>>{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{ Form::campoTexto('nombre', __('Name')) }}
            {{ Form::campoTexto('descripcion', __('Description')) }}
            {{ Form::campoTexto('slug', __('Slug')) }}

            @include('partials.guardar_cancelar')

            @include('layouts.errors')
            {!! Form::close() !!}

        </div>
    </div>
@endsection
