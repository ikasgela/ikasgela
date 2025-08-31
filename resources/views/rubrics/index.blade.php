@extends('layouts.app')

@section('content')

    @include('partials.titular', ['titular' => __('Rubrics')])

    @if(Auth::user()->hasAnyRole(['admin']))
        @include('partials.recursos.filtro_curso', ['ruta' => 'rubrics.index.filtro'])
    @endif

    <div class="mb-3">
        <a class="btn btn-primary" href="{{ route('rubrics.create') }}">{{ __('New rubric') }}</a>
    </div>

    <div class="table-responsive">
        <table class="table table-hover">
            <thead class="thead-dark">
            <tr>
                <th>#</th>
                <th>{{ __('Title') }}</th>
                <th>{{ __('Description') }}</th>
                <th>{{ __('Template') }}</th>
                <th>{{ trans_choice("tasks.completed", 1) }}</th>
                <th>{{ __('Actions') }}</th>
            </tr>
            </thead>
            <tbody>
            @foreach($rubrics as $rubric)
                <tr>
                    <td>{{ $rubric->id }}</td>
                    <td>{{ $rubric->titulo }}</td>
                    <td>{{ $rubric->descripcion }}</td>
                    <td>{!! $rubric->plantilla ? '<i class="bi bi-check-lg text-success"></i>' : '<i class="bi bi-x text-danger"></i>' !!}</td>
                    <td>{!! $rubric->completada ? '<i class="bi bi-check-lg text-success"></i>' : '<i class="bi bi-x text-danger"></i>' !!}</td>
                    <td class="text-nowrap">
                        <div class='btn-group'>
                            @include('partials.boton_mostrar', ['ruta' => 'rubrics', 'recurso' => $rubric])
                            @include('partials.boton_duplicar', ['ruta' => 'rubrics.duplicar', 'id' => $rubric->id, 'middle' => true])
                            {{ html()->form('DELETE', route('rubrics.destroy', $rubric->id))->open() }}
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
