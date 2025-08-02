@extends('layouts.app')

@section('content')

    @include('partials.titular', ['titular' => __('Resources: Image uploads')])

    @if(Auth::user()->hasAnyRole(['admin']))
        @include('partials.recursos.filtro_curso', ['ruta' => 'file_uploads.index.filtro'])
    @endif

    <div class="mb-3">
        <a class="btn btn-primary" href="{{ route('file_uploads.create') }}">{{ __('New image upload') }}</a>
    </div>

    <div class="table-responsive">
        <table class="table table-hover">
            <thead class="thead-dark">
            <tr>
                <th>#</th>
                <th>{{ __('Title') }}</th>
                <th>{{ __('Description') }}</th>
                <th class="text-center">{{ __('Maximum') }}</th>
                <th class="text-center">{{ __('Template') }}</th>
                <th>{{ __('Actions') }}</th>
            </tr>
            </thead>
            <tbody>
            @foreach($file_uploads as $file_upload)
                <tr>
                    <td>{{ $file_upload->id }}</td>
                    <td>{{ $file_upload->titulo }}</td>
                    <td>{{ $file_upload->descripcion }}</td>
                    <td class="text-center">{{ $file_upload->max_files }}</td>
                    <td class="text-center">{!! $file_upload->plantilla ? '<i class="bi bi-check-lg text-success"></i>' : '<i class="bi bi-x text-danger"></i>' !!}</td>
                    <td class="text-nowrap">
                        <div class='btn-group'>
                            @include('partials.boton_mostrar', ['ruta' => 'file_uploads', 'recurso' => $file_upload])
                            @include('partials.boton_editar', ['ruta' => 'file_uploads', 'recurso' => $file_upload])
                            @include('partials.boton_duplicar', ['ruta' => 'file_uploads.duplicar', 'id' => $file_upload->id, 'middle' => true])
                            {{ html()->form('DELETE', route('file_uploads.destroy', $file_upload->id))->open() }}
                            @include('partials.boton_borrar', ['last' => true])
                            {{ html()->form()->close() }}
                        </div>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endsection
