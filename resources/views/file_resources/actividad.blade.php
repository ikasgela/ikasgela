@extends('layouts.app')

@section('content')

    @include('partials.titular', ['titular' => __('Resources: Files')])

    <div class="mb-3">
        @include('partials.cabecera_actividad')
    </div>

    @include('partials.subtitulo', ['subtitulo' => __('Assigned resources')])

    @if(count($file_resources) > 0 )
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="thead-dark">
                <tr>
                    <th>#</th>
                    <th>{{ __('Title') }}</th>
                    <th class="text-center">{{ __('Show title') }}</th>
                    <th>{{ __('Description') }}</th>
                    <th class="text-center">{{ __('Show description') }}</th>
                    <th>{{ __('Actions') }}</th>
                </tr>
                </thead>
                <tbody>
                @foreach($file_resources as $file_resource)
                    <tr>
                        <td>{{ $file_resource->id }}</td>
                        <td>{{ $file_resource->titulo }}</td>
                        <td class="text-center">
                            @include('partials.toggler', [
                                'resource' => 'file_resource',
                                'field' => 'titulo_visible',
                            ])
                        </td>
                        <td>{{ $file_resource->descripcion }}</td>
                        <td class="text-center">
                            @include('partials.toggler', [
                                'resource' => 'file_resource',
                                'field' => 'descripcion_visible',
                            ])
                        </td>
                        <td>
                            <form method="POST"
                                  action="{{ route('file_resources.desasociar', ['actividad' => $actividad->id, 'file_resource' => $file_resource->id]) }}">
                                @csrf
                                @method('DELETE')
                                <div class='btn-group'>
                                    <a title="{{ __('Show') }}"
                                       href="{{ route('file_resources.show', [$file_resource->id]) }}"
                                       class='btn btn-light btn-sm'><i class="bi bi-eye"></i></a>
                                    <a title="{{ __('Edit') }}"
                                       href="{{ route('file_resources.edit', [$file_resource->id]) }}"
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
        <form method="POST" action="{{ route('file_resources.asociar', ['actividad' => $actividad->id]) }}">
            @csrf

            <div class="table-responsive">
                <table class="table">
                    <thead class="thead-dark">
                    <tr>
                        <th></th>
                        <th>#</th>
                        <th>{{ __('Title') }}</th>
                        <th>{{ __('Description') }}</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($disponibles as $file_resource)
                        <tr>
                            <td><input class="form-check-input" type="checkbox" name="seleccionadas[]"
                                       value="{{ $file_resource->id }}"></td>
                            <td>{{ $file_resource->id }}</td>
                            <td>{{ $file_resource->titulo }}</td>
                            <td>{{ $file_resource->descripcion }}</td>
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
                   href="{{ route('file_resources.create') }}">{{ __('New files resource') }}</a>
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
