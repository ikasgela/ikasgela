@extends('layouts.app')

@section('content')

    @include('partials.titular', ['titular' => __('Resources: Image uploads')])

    <div class="mb-3">
        @include('partials.cabecera_actividad')
    </div>

    @include('partials.subtitulo', ['subtitulo' => __('Assigned resources')])

    @if(count($file_uploads) > 0 )
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="thead-dark">
                <tr>
                    <th>#</th>
                    <th>{{ __('Title') }}</th>
                    <th class="text-center">{{ __('Show title') }}</th>
                    <th>{{ __('Description') }}</th>
                    <th class="text-center">{{ __('Show description') }}</th>
                    <th class="text-center">{{ __('Maximum') }}</th>
                    <th>{{ __('Actions') }}</th>
                </tr>
                </thead>
                <tbody>
                @foreach($file_uploads as $file_upload)
                    <tr>
                        <td>{{ $file_upload->id }}</td>
                        <td>{{ $file_upload->titulo }}</td>
                        <td class="text-center">
                            @include('partials.toggler', [
                                'resource' => 'file_upload',
                                'field' => 'titulo_visible',
                            ])
                        </td>
                        <td>{{ $file_upload->descripcion }}</td>
                        <td class="text-center">
                            @include('partials.toggler', [
                                'resource' => 'file_upload',
                                'field' => 'descripcion_visible',
                            ])
                        </td>
                        <td class="text-center">{{ $file_upload->max_files }}</td>
                        <td>
                            <form method="POST"
                                  action="{{ route('file_uploads.desasociar', ['actividad' => $actividad->id, 'file_upload' => $file_upload->id]) }}">
                                @csrf
                                @method('DELETE')
                                <div class='btn-group'>
                                    <a title="{{ __('Show') }}"
                                       href="{{ route('file_uploads.show', [$file_upload->id]) }}"
                                       class='btn btn-light btn-sm'><i class="bi bi-eye"></i></a>
                                    <a title="{{ __('Edit') }}"
                                       href="{{ route('file_uploads.edit', [$file_upload->id]) }}"
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
        <form method="POST" action="{{ route('file_uploads.asociar', ['actividad' => $actividad->id]) }}">
            @csrf

            <div class="table-responsive">
                <table class="table">
                    <thead class="thead-dark">
                    <tr>
                        <th></th>
                        <th>#</th>
                        <th>{{ __('Title') }}</th>
                        <th>{{ __('Description') }}</th>
                        <th>{{ __('Maximum') }}</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($disponibles as $file_upload)
                        <tr>
                            <td><input type="checkbox" name="seleccionadas[]" value="{{ $file_upload->id }}"></td>
                            <td>{{ $file_upload->id }}</td>
                            <td>{{ $file_upload->titulo }}</td>
                            <td>{{ $file_upload->descripcion }}</td>
                            <td>{{ $file_upload->max_files }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>

            @include('partials.paginador', ['coleccion' => $disponibles])

            @include('layouts.errors', ['margenes' => 'mt-0 mb-3'])

            <div class="mb-4">
                <button type="submit" class="btn btn-primary me-2">{{ __('Save assigment') }}</button>
                <a class="btn btn-secondary" href="{{ route('file_uploads.create') }}">{{ __('New image upload') }}</a>
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
