@extends('layouts.app')

@section('content')

    @include('partials.titular', ['titular' => __('Resources: Link collections')])

    @if(Auth::user()->hasAnyRole(['admin']))
        {!! Form::open(['route' => ['link_collections.index.filtro'], 'method' => 'POST']) !!}
        @include('partials.desplegable_cursos')
        {!! Form::close() !!}
    @endif

    <div class="mb-3">
        <a class="btn btn-primary" href="{{ route('link_collections.create') }}">{{ __('New link collection') }}</a>
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
            @foreach($link_collections as $link_collection)
                <tr>
                    <td>{{ $link_collection->id }}</td>
                    <td>{{ $link_collection->titulo }}</td>
                    <td>{{ $link_collection->descripcion }}</td>
                    <td class="text-nowrap">
                        <div class='btn-group'>
                            @include('partials.boton_mostrar', ['ruta' => 'link_collections', 'recurso' => $link_collection])
                            @include('partials.boton_editar', ['ruta' => 'link_collections', 'recurso' => $link_collection])
                            {!! Form::open(['route' => ['link_collections.duplicar', $link_collection->id], 'method' => 'POST']) !!}
                            @include('partials.boton_duplicar')
                            {!! Form::close() !!}
                            {!! Form::open(['route' => ['link_collections.destroy', $link_collection->id], 'method' => 'DELETE']) !!}
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
