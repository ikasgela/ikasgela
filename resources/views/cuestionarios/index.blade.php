@extends('layouts.app')

@section('content')

    @include('partials.titular', ['titular' => __('Questionnaires')])

    @if(Auth::user()->hasAnyRole(['admin']))
        {!! Form::open(['route' => ['cuestionarios.index.filtro'], 'method' => 'POST']) !!}
        @include('partials.desplegable_cursos')
        {!! Form::close() !!}
    @endif

    <div class="mb-3">
        <a class="btn btn-primary" href="{{ route('cuestionarios.create') }}">{{ __('New questionnaire') }}</a>
    </div>

    <div class="table-responsive">
        <table class="table table-hover">
            <thead class="thead-dark">
            <tr>
                <th>#</th>
                <th>{{ __('Title') }}</th>
                <th>{{ __('Description') }}</th>
                <th>{{ __('Template') }}</th>
                <th>{{ __('Answered') }}</th>
                <th>{{ __('Actions') }}</th>
            </tr>
            </thead>
            <tbody>
            @foreach($cuestionarios as $cuestionario)
                <tr>
                    <td>{{ $cuestionario->id }}</td>
                    <td>{{ $cuestionario->titulo }}</td>
                    <td>{{ $cuestionario->descripcion }}</td>
                    <td>{!! $cuestionario->plantilla ? '<i class="fas fa-check text-success"></i>' : '<i class="fas fa-times text-danger"></i>' !!}</td>
                    <td>{!! $cuestionario->respondido ? '<i class="fas fa-check text-success"></i>' : '<i class="fas fa-times text-danger"></i>' !!}</td>
                    <td class="text-nowrap">
                        <div class='btn-group'>
                            @include('partials.boton_mostrar', ['ruta' => 'cuestionarios', 'recurso' => $cuestionario])
                            @include('partials.boton_editar', ['ruta' => 'cuestionarios', 'recurso' => $cuestionario])
                            {!! Form::open(['route' => ['cuestionarios.duplicar', $cuestionario->id], 'method' => 'POST']) !!}
                            @include('partials.boton_duplicar')
                            {!! Form::close() !!}
                            {!! Form::open(['route' => ['cuestionarios.destroy', $cuestionario->id], 'method' => 'DELETE']) !!}
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
