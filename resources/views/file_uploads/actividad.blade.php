@extends('layouts.app')

@section('content')

    @include('partials.titular', ['titular' => __('Resources: File uploads')])

    <div class="row">
        <div class="col-md-12">
            {{-- Tarjeta --}}
            <div class="card">
                <div class="card-header">{{ $actividad->unidad->slug.'/'.$actividad->slug }}</div>
                <div class="card-body pb-1">
                    <h2>{{ $actividad->nombre }}</h2>
                    <p>{{ $actividad->descripcion }}</p>
                </div>
            </div>
            {{-- Fin tarjeta--}}
        </div>
    </div>

    @include('partials.subtitulo', ['subtitulo' => __('Assigned resources')])

    @if(count($file_uploads) > 0 )
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="thead-dark">
                <tr>
                    <th>#</th>
                    <th>{{ __('Title') }}</th>
                    <th>{{ __('Description') }}</th>
                    <th>{{ __('Maximum') }}</th>
                    <th>{{ __('Actions') }}</th>
                </tr>
                </thead>
                <tbody>
                @foreach($file_uploads as $file_upload)
                    <tr>
                        <td>{{ $file_upload->id }}</td>
                        <td>{{ $file_upload->titulo }}</td>
                        <td>{{ $file_upload->descripcion }}</td>
                        <td>{{ $file_upload->max_files }}</td>
                        <td>
                            <form method="POST"
                                  action="{{ route('file_uploads.desasociar', ['actividad' => $actividad->id, 'file_upload' => $file_upload->id]) }}">
                                @csrf
                                @method('DELETE')
                                <div class='btn-group'>
                                    <a title="{{ __('Show') }}"
                                       href="{{ route('file_uploads.show', [$file_upload->id]) }}"
                                       class='btn btn-light btn-sm'><i class="fas fa-eye"></i></a>
                                    <a title="{{ __('Edit') }}"
                                       href="{{ route('file_uploads.edit', [$file_upload->id]) }}"
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
            @include('layouts.errors')

            <div>
                <button type="submit" class="btn btn-primary mb-4">{{ __('Save assigment') }}</button>
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
