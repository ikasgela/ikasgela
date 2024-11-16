@extends('layouts.app')

@section('content')

    @include('partials.titular', ['titular' => __('Skills')])

    @if(Auth::user()->hasAnyRole(['admin']))
        @include('partials.recursos.filtro_curso', ['ruta' => 'skills.index.filtro'])
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
                        <div class='btn-group'>
                            @include('partials.boton_editar', ['ruta' => 'skills', 'recurso' => $skill])
                            {{ html()->form('DELETE', route('skills.destroy', $skill->id))->open() }}
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
