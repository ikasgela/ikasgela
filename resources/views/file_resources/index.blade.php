@extends('layouts.app')

@section('content')

    @include('partials.titular', ['titular' => __('Resources: Files')])

    @if(Auth::user()->hasAnyRole(['admin']))
        {!! Form::open(['route' => ['file_resources.index.filtro'], 'method' => 'POST']) !!}
        @include('partials.desplegable_cursos')
        {!! Form::close() !!}
    @endif

    <div class="mb-3">
        <a class="btn btn-primary" href="{{ route('file_resources.create') }}">{{ __('New files resource') }}</a>
    </div>

    <div class="table-responsive">
        <table class="table table-hover">
            <thead class="thead-dark">
            <tr>
                <th>#</th>
                <th>{{ __('Title') }}</th>
                <th>{{ __('Description') }}</th>
                <th>{{ __('Actions') }}</th>
            </tr>
            </thead>
            <tbody>
            @foreach($file_resources as $file_resource)
                <tr>
                    <td>{{ $file_resource->id }}</td>
                    <td>{{ $file_resource->titulo }}</td>
                    <td>{{ $file_resource->descripcion }}</td>
                    <td class="text-nowrap">
                        <div class='btn-group'>
                            @include('partials.boton_mostrar', ['ruta' => 'file_resources', 'recurso' => $file_resource])
                            @include('partials.boton_editar', ['ruta' => 'file_resources', 'recurso' => $file_resource])
                            {!! Form::open(['route' => ['file_resources.duplicar', $file_resource->id], 'method' => 'POST']) !!}
                            @include('partials.boton_duplicar')
                            {!! Form::close() !!}
                            {!! Form::open(['route' => ['file_resources.destroy', $file_resource->id], 'method' => 'DELETE']) !!}
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
