@extends('layouts.app')

@section('content')

    @include('partials.titular', ['titular' => __('Units')])

    @if(Auth::user()->hasAnyRole(['admin']))
        <div class="mb-3">
            {{ html()->form('POST', route('unidades.index.filtro'))->open() }}
            @include('partials.desplegable_cursos')
            {{ html()->form()->close() }}
        </div>
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
                <th class="text-center">{{ __('Activities') }}</th>
                <th>{{ __('Order') }}</th>
                <th class="text-center">{{ __('Visible') }}</th>
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
                    <td class="text-center">{{ $unidad->actividades()->plantilla()->count() }}</td>
                    <td>
                        @include('partials.botones_reordenar', ['ruta' => 'unidades.reordenar'])
                    </td>
                    <td class="text-center">@include('partials.check_yes_no', ['checked' => $unidad->visible])</td>
                    <td>
                        <div class='btn-group'>
                            <a title="{{ __('Edit') }}"
                               href="{{ route('unidades.edit', [$unidad->id]) }}"
                               class='btn btn-light btn-sm'><i class="bi bi-pencil-square"></i></a>

                            {{ html()->form('DELETE', route('unidades.destroy', $unidad->id))->open() }}
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
