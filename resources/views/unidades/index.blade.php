@extends('layouts.app')

@section('content')

    @include('partials.titular', ['titular' => __('Units')])

    @if(Auth::user()->hasAnyRole(['admin']))
        {!! Form::open(['route' => ['unidades.index.filtro'], 'method' => 'POST']) !!}
        @include('partials.desplegable_cursos')
        {!! Form::close() !!}
    @endif

    <div class="mb-3">
        <a class="btn btn-primary" href="{{ route('unidades.create') }}">{{ __('New unit') }}</a>
    </div>

    <div class="table-responsive">
        <table class="table table-hover">
            <thead class="thead-dark">
            <tr>
                <th>#</th>
                <th>{{ __('Course') }}</th>
                <th>{{ __('Code') }}</th>
                <th>{{ __('Name') }}</th>
                <th>{{ __('Slug') }}</th>
                <th>{{ __('Order') }}</th>
                <th>{{ __('Actions') }}</th>
            </tr>
            </thead>
            <tbody>
            @foreach($unidades as $unidad)
                <tr>
                    <td>{{ $unidad->id }}</td>
                    <td>{{ $unidad->curso->full_name }}</td>
                    <td>{{ $unidad->codigo }}</td>
                    <td>
                        @include('unidades.partials.nombre_con_etiquetas')
                    </td>
                    <td>{{ $unidad->slug }}</td>
                    <td>
                        @include('partials.botones_reordenar', ['ruta' => 'unidades.reordenar'])
                    </td>
                    <td>
                        <form method="POST" action="{{ route('unidades.destroy', [$unidad->id]) }}">
                            @csrf
                            @method('DELETE')
                            <div class='btn-group'>
                                <a title="{{ __('Edit') }}"
                                   href="{{ route('unidades.edit', [$unidad->id]) }}"
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
@endsection
