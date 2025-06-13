@extends('layouts.app')

@section('content')

    @include('partials.titular', ['titular' => __('Resources: Selectors')])

    <div class="mb-3">
        @include('partials.cabecera_actividad')
    </div>

    @include('partials.subtitulo', ['subtitulo' => __('Assigned resources')])

    @if(count($selectors) > 0 )
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
                        <td>
                            <form method="POST"
                                  action="{{ route('selectors.desasociar', ['actividad' => $actividad->id, 'selector' => $selector->id]) }}">
                                @csrf
                                @method('DELETE')
                                <div class='btn-group'>
                                    <a title="{{ __('Show') }}"
                                       href="{{ route('selectors.show', [$selector->id]) }}"
                                       class='btn btn-light btn-sm'><i class="fas fa-eye"></i></a>
                                    <a title="{{ __('Edit') }}"
                                       href="{{ route('selectors.edit', [$selector->id]) }}"
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
    @else
        <div class="row">
            <div class="col-md">
                <p>No hay elementos.</p>
            </div>
        </div>
    @endif

    @include('partials.subtitulo', ['subtitulo' => __('Available resources')])

    @if(count($disponibles) > 0)
        <form method="POST" action="{{ route('selectors.asociar', ['actividad' => $actividad->id]) }}">
            @csrf

            <div class="table-responsive">
                <table class="table">
                    <thead class="thead-dark">
                    <tr>
                        <th></th>
                        <th>#</th>
                        <th>{{ __('Title') }}</th>
                        <th>{{ __('Description') }}</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($disponibles as $selector)
                        <tr>
                            <td><input type="checkbox" name="seleccionadas[]" value="{{ $selector->id }}"></td>
                            <td>{{ $selector->id }}</td>
                            <td>{{ $selector->titulo }}</td>
                            <td>{{ $selector->descripcion }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>

            @include('partials.paginador', ['coleccion' => $disponibles])

            @include('layouts.errors', ['margenes' => 'mt-0 mb-3'])

            <div class="mb-4">
                <button type="submit" class="btn btn-primary me-2">{{ __('Save assigment') }}</button>
                <a class="btn btn-secondary" href="{{ route('selectors.create') }}">{{ __('New selector') }}</a>
            </div>

        </form>
    @else
        <div class="row">
            <div class="col-md">
                <p>No hay elementos.</p>
            </div>
        </div>
    @endif

    <div>
        @include('partials.backbutton')
    </div>
@endsection
