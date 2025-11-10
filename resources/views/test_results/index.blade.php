@extends('layouts.app')

@section('content')

    @include('partials.titular', ['titular' => __('Test results')])

    @if(Auth::user()->hasAnyRole(['admin']))
        @include('partials.recursos.filtro_curso', ['ruta' => 'test_results.index.filtro'])
    @endif

    <div class="mb-3">
        <a class="btn btn-primary" href="{{ route('test_results.create') }}">{{ __('New test result') }}</a>
    </div>

    <div class="table-responsive">
        <table class="table table-hover">
            <thead class="thead-dark">
            <tr>
                <th>#</th>
                <th>{{ __('Title') }}</th>
                <th>{{ __('Description') }}</th>
                <th>{{ __('Template') }}</th>
                <th>{{ trans_choice("decks.completed", 1) }}</th>
                <th>{{ __('Actions') }}</th>
            </tr>
            </thead>
            <tbody>
            @foreach($test_results as $test_result)
                <tr>
                    <td>{{ $test_result->id }}</td>
                    <td>{{ $test_result->titulo }}</td>
                    <td>{{ $test_result->descripcion }}</td>
                    <td>{!! $test_result->plantilla ? '<i class="bi bi-check-lg text-success"></i>' : '<i class="bi bi-x text-danger"></i>' !!}</td>
                    <td>{!! $test_result->completada ? '<i class="bi bi-check-lg text-success"></i>' : '<i class="bi bi-x text-danger"></i>' !!}</td>
                    <td class="text-nowrap">
                        <div class='btn-group'>
                            @include('partials.boton_mostrar', ['ruta' => 'test_results', 'recurso' => $test_result])
                            @include('partials.boton_editar', ['ruta' => 'test_results', 'recurso' => $test_result])
                            @include('partials.boton_duplicar', ['ruta' => 'test_results.duplicar', 'id' => $test_result->id, 'middle' => true])
                            {{ html()->form('DELETE', route('test_results.destroy', $test_result->id))->open() }}
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
