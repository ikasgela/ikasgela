@extends('layouts.app')

@section('content')

    @include('partials.titular', ['titular' => __('Resources: Questionnaires')])

    @include('partials.cabecera_actividad')

    @include('partials.subtitulo', ['subtitulo' => __('Assigned resources')])

    @if(count($cuestionarios) > 0 )
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
                @foreach($cuestionarios as $cuestionario)
                    <tr>
                        <td>{{ $cuestionario->id }}</td>
                        <td>{{ $cuestionario->titulo }}</td>
                        <td>{{ $cuestionario->descripcion }}</td>
                        <td>{!! $cuestionario->plantilla ? '<i class="fas fa-check text-success"></i>' : '<i class="fas fa-times text-danger"></i>' !!}</td>
                        <td>
                            <form method="POST"
                                  action="{{ route('cuestionarios.desasociar', ['actividad' => $actividad->id, 'cuestionario' => $cuestionario->id]) }}">
                                @csrf
                                @method('DELETE')
                                <div class='btn-group'>
                                    <a title="{{ __('Show') }}"
                                       href="{{ route('cuestionarios.show', [$cuestionario->id]) }}"
                                       class='btn btn-light btn-sm'><i class="fas fa-eye"></i></a>
                                    <a title="{{ __('Edit') }}"
                                       href="{{ route('cuestionarios.edit', [$cuestionario->id]) }}"
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
        <form method="POST" action="{{ route('cuestionarios.asociar', ['actividad' => $actividad->id]) }}">
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
                    @foreach($disponibles as $cuestionario)
                        <tr>
                            <td><input type="checkbox" name="seleccionadas[]" value="{{ $cuestionario->id }}"></td>
                            <td>{{ $cuestionario->id }}</td>
                            <td>{{ $cuestionario->titulo }}</td>
                            <td>{{ $cuestionario->descripcion }}</td>
                            <td>{!! $cuestionario->plantilla ? '<i class="fas fa-check text-success"></i>' : '<i class="fas fa-times text-danger"></i>' !!}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>

            @include('partials.paginador', ['coleccion' => $disponibles])

            @include('layouts.errors')

            <div class="mb-4">
                <button type="submit" class="btn btn-primary mr-2">{{ __('Save assigment') }}</button>
                <a class="btn btn-secondary"
                   href="{{ route('cuestionarios.create') }}">{{ __('New questionnaire') }}</a>
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
