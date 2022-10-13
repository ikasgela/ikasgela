@extends('layouts.app')

@section('content')

    @include('partials.titular', ['titular' => __('Selectors')])

    @if(Auth::user()->hasAnyRole(['admin']))
        {!! Form::open(['route' => ['selectors.index.filtro'], 'method' => 'POST']) !!}
        @include('partials.desplegable_cursos')
        {!! Form::close() !!}
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
                            <a title="{{ __('Show') }}"
                               href="{{ route('selectors.show', [$selector->id]) }}"
                               class='btn btn-light btn-sm'><i class="fas fa-eye"></i></a>
                            <a title="{{ __('Edit') }}"
                               href="{{ route('selectors.edit', [$selector->id]) }}"
                               class='btn btn-light btn-sm'><i class="fas fa-edit"></i></a>
                            {!! Form::open(['route' => ['selectors.duplicar', $selector->id], 'method' => 'POST']) !!}
                            @include('partials.boton_duplicar')
                            {!! Form::close() !!}
                            {!! Form::open(['route' => ['selectors.destroy', $selector->id], 'method' => 'DELETE']) !!}
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
