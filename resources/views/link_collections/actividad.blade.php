@extends('layouts.app')

@section('content')

    @include('partials.titular', ['titular' => __('Resources: Link collections')])

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

    @if(count($link_collections) > 0 )
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
                @foreach($link_collections as $link_collection)
                    <tr>
                        <td>{{ $link_collection->id }}</td>
                        <td>{{ $link_collection->titulo }}</td>
                        <td class="text-center">
                            @include('partials.toggler', [
                                'resource' => 'link_collection',
                                'field' => 'titulo_visible',
                            ])
                        </td>
                        <td>{{ $link_collection->descripcion }}</td>
                        <td class="text-center">
                            @include('partials.toggler', [
                                'resource' => 'link_collection',
                                'field' => 'descripcion_visible',
                            ])
                        </td>
                        <td>
                            <form method="POST"
                                  action="{{ route('link_collections.desasociar', ['actividad' => $actividad->id, 'link_collection' => $link_collection->id]) }}">
                                @csrf
                                @method('DELETE')
                                <div class='btn-group'>
                                    <a title="{{ __('Show') }}"
                                       href="{{ route('link_collections.show', [$link_collection->id]) }}"
                                       class='btn btn-light btn-sm'><i class="fas fa-eye"></i></a>
                                    <a title="{{ __('Edit') }}"
                                       href="{{ route('link_collections.edit', [$link_collection->id]) }}"
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
        <form method="POST" action="{{ route('link_collections.asociar', ['actividad' => $actividad->id]) }}">
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
                    @foreach($disponibles as $link_collection)
                        <tr>
                            <td><input type="checkbox" name="seleccionadas[]" value="{{ $link_collection->id }}"></td>
                            <td>{{ $link_collection->id }}</td>
                            <td>{{ $link_collection->titulo }}</td>
                            <td>{{ $link_collection->descripcion }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>

            @include('partials.paginador', ['coleccion' => $disponibles])

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
