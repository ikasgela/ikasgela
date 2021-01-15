@extends('layouts.app')

@section('content')

    <div class="row mb-3">
        <div class="col-md">
            <h1>{{ __('Courses') }}</h1>
        </div>
    </div>

    <div class="mb-3">
        <a class="btn btn-primary" href="{{ route('cursos.create') }}">{{ __('New course') }}</a>
    </div>

    <div class="table-responsive">
        <table class="table table-hover">
            <thead class="thead-dark">
            <tr>
                <th>#</th>
                <th>{{ __('Organization') }}</th>
                <th>{{ __('Period') }}</th>
                <th>{{ __('Category') }}</th>
                <th>{{ __('Name') }}</th>
                <th>{{ __('Description') }}</th>
                <th>{{ __('Slug') }}</th>
                <th>{{ __('Simultaneous activities') }}</th>
                <th>{{ __('Activity deadline') }}</th>
                <th>{{ __('Minimum completed percent') }}</th>
                <th>{{ __('Minimum skills percent') }}</th>
                <th>{{ __('Minimum exams percent') }}</th>
                <th>{{ __('Mandatory exams') }}</th>
                <th>{{ __('Maximum recoverable percent') }}</th>
                <th>{{ __('Actions') }}</th>
            </tr>
            </thead>
            <tbody>
            @foreach($cursos as $curso)
                <tr>
                    <td>{{ $curso->id }}</td>
                    <td>{{ $curso->category->period->organization->name }}</td>
                    <td>{{ $curso->category->period->name }}</td>
                    <td>{{ $curso->category->name }}</td>
                    <td>{{ $curso->nombre }}</td>
                    <td>{{ $curso->descripcion }}</td>
                    <td>{{ $curso->slug }}</td>
                    <td>{{ $curso->max_simultaneas ?? __('Undefined') }}</td>
                    <td>{{ $curso->plazo_actividad ?? __('Undefined') }}</td>
                    <td>{{ $curso->minimo_entregadas ?? __('Undefined') }}</td>
                    <td>{{ $curso->minimo_competencias ?? __('Undefined') }}</td>
                    <td>{{ $curso->minimo_examenes ?? __('Undefined') }}</td>
                    <td>{{ $curso->examenes_obligatorios ? __('Yes') : __('No') }}</td>
                    <td>{{ $curso->maximo_recuperable_examenes_finales ?? __('Undefined') }}</td>
                    <td>
                        <form method="POST" action="{{ route('cursos.destroy', [$curso->id]) }}">
                            @csrf
                            @method('DELETE')
                            <div class='btn-group'>
                                <a title="{{ __('Edit') }}"
                                   href="{{ route('cursos.edit', [$curso->id]) }}"
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
