@extends('layouts.app')

@section('content')

    @include('partials.titular', ['titular' => __('Selectors')])

    @if(Auth::user()->hasAnyRole(['admin']))
        @include('partials.recursos.filtro_curso', ['ruta' => 'selectors.index.filtro'])
    @endif

    <div class="mb-3">
        <a class="btn btn-primary" href="{{ route('selectors.create') }}">{{ __('New selector') }}</a>
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
            @foreach($selectors as $selector)
                <tr>
                    <td>{{ $selector->id }}</td>
                    <td>{{ $selector->titulo }}</td>
                    <td>{{ $selector->descripcion }}</td>
                    <td class="text-nowrap">
                        <div class='btn-group'>
                            @include('partials.boton_mostrar', ['ruta' => 'selectors', 'recurso' => $selector])
                            @include('partials.boton_editar', ['ruta' => 'selectors', 'recurso' => $selector])
                            @include('partials.boton_duplicar', ['ruta' => 'selectors.duplicar', 'id' => $selector->id, 'middle' => true])
                            {{ html()->form('DELETE', route('selectors.destroy', $selector->id))->open() }}
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
