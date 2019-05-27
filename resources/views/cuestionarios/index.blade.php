@extends('layouts.app')

@section('content')

    @include('partials.titular', ['titular' => __('Questionnaires')])

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
                <th>{{ __('Actions') }}</th>
                <th>{{ __('Answered') }}</th>
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
                        {!! Form::open(['route' => ['cuestionarios.destroy', $cuestionario->id], 'method' => 'DELETE']) !!}
                        <div class='btn-group'>
                            <a title="{{ __('Show') }}"
                               href="{{ route('cuestionarios.show', [$cuestionario->id]) }}"
                               class='btn btn-light btn-sm'><i class="fas fa-eye"></i></a>
                            <a title="{{ __('Edit') }}"
                               href="{{ route('cuestionarios.edit', [$cuestionario->id]) }}"
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
