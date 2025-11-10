@extends('layouts.app')

@section('content')

    @include('partials.titular', ['titular' => __('Resources: Test results')])

    <div class="mb-3">
        @include('partials.cabecera_actividad')
    </div>

    @include('partials.subtitulo', ['subtitulo' => __('Assigned resources')])

    @if(count($test_results) > 0 )
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="thead-dark">
                <tr>
                    <th>#</th>
                    <th>{{ __('Title') }}</th>
                    <th class="text-center">{{ __('Show title') }}</th>
                    <th>{{ __('Description') }}</th>
                    <th class="text-center">{{ __('Show description') }}</th>
                    <th>{{ __('Template') }}</th>
                    <th>{{ __('Actions') }}</th>
                </tr>
                </thead>
                <tbody>
                @foreach($test_results as $test_result)
                    <tr>
                        <td>{{ $test_result->id }}</td>
                        <td>{{ $test_result->titulo }}</td>
                        <td class="text-center">
                            @include('partials.toggler', [
                                'resource' => 'test_result',
                                'field' => 'titulo_visible',
                            ])
                        </td>
                        <td>{{ $test_result->descripcion }}</td>
                        <td class="text-center">
                            @include('partials.toggler', [
                                'resource' => 'test_result',
                                'field' => 'descripcion_visible',
                            ])
                        </td>
                        <td>{!! $test_result->plantilla ? '<i class="bi bi-check-lg text-success"></i>' : '<i class="bi bi-x text-danger"></i>' !!}</td>
                        <td>
                            <form method="POST"
                                  action="{{ route('test_results.desasociar', ['actividad' => $actividad->id, 'test_result' => $test_result->id]) }}">
                                @csrf
                                @method('DELETE')
                                <div class='btn-group'>
                                    <a title="{{ __('Show') }}"
                                       href="{{ route('test_results.show', [$test_result->id]) }}"
                                       class='btn btn-light btn-sm'><i class="bi bi-eye"></i></a>
                                    <a title="{{ __('Edit') }}"
                                       href="{{ route('test_results.edit', [$test_result->id]) }}"
                                       class='btn btn-light btn-sm'><i class="bi bi-pencil-square"></i></a>
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
        <form method="POST" action="{{ route('test_results.asociar', ['actividad' => $actividad->id]) }}">
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
                    @foreach($disponibles as $test_result)
                        <tr>
                            <td><input type="checkbox" name="seleccionadas[]" value="{{ $test_result->id }}"></td>
                            <td>{{ $test_result->id }}</td>
                            <td>{{ $test_result->titulo }}</td>
                            <td>{{ $test_result->descripcion }}</td>
                            <td>{!! $test_result->plantilla ? '<i class="bi bi-check-lg text-success"></i>' : '<i class="bi bi-x text-danger"></i>' !!}</td>
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
                   href="{{ route('test_results.create') }}">{{ __('New test result') }}</a>
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
