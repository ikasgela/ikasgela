@extends('layouts.app')

@section('content')

    @include('partials.titular', ['titular' => __('Skills')])

    @if(Auth::user()->hasAnyRole(['admin']))
        {!! Form::open(['route' => ['skills.index.filtro'], 'method' => 'POST']) !!}
        @include('partials.desplegable_cursos')
        {!! Form::close() !!}
    @endif

    <div class="mb-3">
        <a class="btn btn-primary" href="{{ route('skills.create') }}">{{ __('New skill') }}</a>
    </div>

    <div class="table-responsive">
        <table class="table table-hover">
            <thead class="thead-dark">
            <tr>
                <th>#</th>
                <th>{{ __('Course') }}</th>
                <th>{{ __('Name') }}</th>
                <th>{{ __('Description') }}</th>
                <th>{{ __('Exam weight') }}</th>
                <th>{{ __('Minimum percent') }}</th>
                <th>{{ __('Actions') }}</th>
            </tr>
            </thead>
            <tbody>
            @foreach($skills as $skill)
                <tr>
                    <td>{{ $skill->id }}</td>
                    <td>{{ $skill->curso->full_name }}</td>
                    <td>{{ $skill->name }}</td>
                    <td>{{ $skill->description }}</td>
                    <td>{{ $skill->peso_examen }}</td>
                    <td>{{ $skill->minimo_competencias }}</td>
                    <td class="text-nowrap">
                        {!! Form::open(['route' => ['skills.destroy', $skill->id], 'method' => 'DELETE']) !!}
                        <div class='btn-group'>
                            <a title="{{ __('Edit') }}"
                               href="{{ route('skills.edit', [$skill->id]) }}"
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
