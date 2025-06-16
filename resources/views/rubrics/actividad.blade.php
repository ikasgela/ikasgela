@extends('layouts.app')

@section('content')

    @include('partials.titular', ['titular' => __('Resources: Rubrics')])

    <div class="mb-3">
        @include('partials.cabecera_actividad')
    </div>

    @include('partials.subtitulo', ['subtitulo' => __('Assigned resources')])

    @if(count($rubrics) > 0 )
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="thead-dark">
                <tr>
                    <th>#</th>
                    <th>{{ __('Title') }}</th>
                    <th>{{ __('Description') }}</th>
                    <th>{{ __('Template') }}</th>
                    <th>{{ __('Actions') }}</th>
                </tr>
                </thead>
                <tbody>
                @foreach($rubrics as $rubric)
                    <tr>
                        <td>{{ $rubric->id }}</td>
                        <td>{{ $rubric->titulo }}</td>
                        <td>{{ $rubric->descripcion }}</td>
                        <td>{!! $rubric->plantilla ? '<i class="fas fa-check text-success"></i>' : '<i class="fas fa-times text-danger"></i>' !!}</td>
                        <td>
                            <form method="POST"
                                  action="{{ route('rubrics.desasociar', ['actividad' => $actividad->id, 'rubric' => $rubric->id]) }}">
                                @csrf
                                @method('DELETE')
                                <div class='btn-group'>
                                    <a title="{{ __('Show') }}"
                                       href="{{ route('rubrics.show', [$rubric->id]) }}"
                                       class='btn btn-light btn-sm'><i class="fas fa-eye"></i></a>
                                    <a title="{{ __('Edit') }}"
                                       href="{{ route('rubrics.edit', [$rubric->id]) }}"
                                       class='btn btn-light btn-sm'><i class="fas fa-edit"></i></a>
                                    @include('partials.boton_borrar')
                                </div>
                            </form>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="row">
            <div class="col-md">
                <p>No hay elementos.</p>
            </div>
        </div>
    @endif

    @include('partials.subtitulo', ['subtitulo' => __('Available resources')])

    @if(count($disponibles) > 0)
        <form method="POST" action="{{ route('rubrics.asociar', ['actividad' => $actividad->id]) }}">
            @csrf

            <div class="table-responsive">
                <table class="table">
                    <thead class="thead-dark">
                    <tr>
                        <th></th>
                        <th>#</th>
                        <th>{{ __('Title') }}</th>
                        <th>{{ __('Description') }}</th>
                        <th>{{ __('Template') }}</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($disponibles as $rubric)
                        <tr>
                            <td><input type="checkbox" name="seleccionadas[]" value="{{ $rubric->id }}"></td>
                            <td>{{ $rubric->id }}</td>
                            <td>{{ $rubric->titulo }}</td>
                            <td>{{ $rubric->descripcion }}</td>
                            <td>{!! $rubric->plantilla ? '<i class="fas fa-check text-success"></i>' : '<i class="fas fa-times text-danger"></i>' !!}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>

            @include('partials.paginador', ['coleccion' => $disponibles])

            @include('layouts.errors', ['margenes' => 'mt-0 mb-3'])

            <div class="mb-4">
                <button type="submit" class="btn btn-primary me-2">{{ __('Save assigment') }}</button>
                <a class="btn btn-secondary"
                   href="{{ route('rubrics.create') }}">{{ __('New rubric') }}</a>
            </div>

        </form>
    @else
        <div class="row">
            <div class="col-md">
                <p>No hay elementos.</p>
            </div>
        </div>
    @endif

    <div>
        @include('partials.backbutton')
    </div>
@endsection
