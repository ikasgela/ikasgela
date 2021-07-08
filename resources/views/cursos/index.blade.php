@extends('layouts.app')

@section('content')

    <div class="row mb-3">
        <div class="col-md">
            <h1>{{ __('Courses') }}</h1>
        </div>
    </div>

    <div class="mb-3">
        <a class="btn btn-primary" href="{{ route('cursos.create') }}">{{ __('New course') }}</a>

        <form action="{{ route('cursos.import') }}" enctype="multipart/form-data" method="post">
            @csrf
            <div class="form-group">
                <input type="file" name="file" id="file">
                <span class="help-block text-danger">{{ $errors->first('file') }}</span>
            </div>
            <button class="btn btn-primary single_click">
                <i class="fas fa-spinner fa-spin" style="display:none;"></i> {{ __('Import course') }}</button>
        </form>
    </div>

    <div class="table-responsive">
        <table class="table table-hover">
            <thead class="thead-dark">
            <tr>
                <th>#</th>
                <th>{{ __('Organization') }}</th>
                <th>{{ __('Period') }}</th>
                <th>{{ __('Category') }}</th>
                <th>{{ __('Name') }}</th>
                <th>{{ __('Description') }}</th>
                <th>{{ __('Slug') }}</th>
                <th>{{ __('Simultaneous activities') }}</th>
                <th>{{ __('Activity deadline') }}</th>
                <th>{{ __('Minimum completed percent') }}</th>
                <th>{{ __('Minimum skills percent') }}</th>
                <th>{{ __('Minimum exams percent') }}</th>
                <th>{{ __('Mandatory exams') }}</th>
                <th>{{ __('Maximum recoverable percent') }}</th>
                <th>{{ __('Actions') }}</th>
            </tr>
            </thead>
            <tbody>
            @foreach($cursos as $curso)
                <tr>
                    <td>{{ $curso->id }}</td>
                    <td>{{ $curso->category?->period->organization->name }}</td>
                    <td>{{ $curso->category?->period->name }}</td>
                    <td>{{ $curso->category?->name }}</td>
                    <td>{{ $curso->nombre }}</td>
                    <td>{{ $curso->descripcion }}</td>
                    <td>{{ $curso->slug }}</td>
                    <td>{{ $curso->max_simultaneas ?? __('Undefined') }}</td>
                    <td>{{ $curso->plazo_actividad ?? __('Undefined') }}</td>
                    <td>{{ $curso->minimo_entregadas ?? __('Undefined') }}</td>
                    <td>{{ $curso->minimo_competencias ?? __('Undefined') }}</td>
                    <td>{{ $curso->minimo_examenes ?? __('Undefined') }}</td>
                    <td>{{ $curso->examenes_obligatorios ? __('Yes') : __('No') }}</td>
                    <td>{{ $curso->maximo_recuperable_examenes_finales ?? __('Undefined') }}</td>
                    <td>
                        <div class='btn-group'>
                            <a title="{{ __('Edit') }}"
                               href="{{ route('cursos.edit', [$curso->id]) }}"
                               class='btn btn-light btn-sm'><i class="fas fa-edit"></i></a>

                            {!! Form::open(['route' => ['cursos.export', [$curso->id]], 'method' => 'POST']) !!}
                            {!! Form::button('<i class="fas fa-download"></i>', ['type' => 'submit',
                                'class' => 'btn btn-light btn-sm', 'title' => __('Export course')
                            ]) !!}
                            {!! Form::close() !!}

                            {!! Form::open(['route' => ['cursos.destroy', [$curso->id]], 'method' => 'DELETE']) !!}
                            @include('partials.boton_borrar')
                            {!! Form::close() !!}
                        </div>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endsection
