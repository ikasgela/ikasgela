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
                        {!! Form::open(['route' => ['link_collections.destroy', $link_collection->id], 'method' => 'DELETE']) !!}
                        <div class='btn-group'>
                            <a title="{{ __('Show') }}"
                               href="{{ route('link_collections.show', [$link_collection->id]) }}"
                               class='btn btn-light btn-sm'><i class="fas fa-eye"></i></a>
                            <a title="{{ __('Edit') }}"
                               href="{{ route('link_collections.edit', [$link_collection->id]) }}"
                               class='btn btn-light btn-sm'><i class="fas fa-edit"></i></a>
                            @include('partials.boton_borrar')
                        </div>
                        {!! Form::close() !!}
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endsection
