@extends('layouts.app')

@section('content')

    @include('partials.titular', ['titular' => __('Resources: Flashcards')])

    <div class="mb-3">
        @include('partials.cabecera_actividad')
    </div>

    @include('partials.subtitulo', ['subtitulo' => __('Assigned resources')])

    @if(count($flash_decks) > 0 )
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="thead-dark">
                <tr>
                    <th>#</th>
                    <th>{{ __('Title') }}</th>
                    <th>{{ __('Description') }}</th>
                    <th>{{ __('Template') }}</th>
                    <th>{{ __('Actions') }}</th>
                </tr>
                </thead>
                <tbody>
                @foreach($flash_decks as $flash_deck)
                    <tr>
                        <td>{{ $flash_deck->id }}</td>
                        <td>{{ $flash_deck->titulo }}</td>
                        <td>{{ $flash_deck->descripcion }}</td>
                        <td>{!! $flash_deck->plantilla ? '<i class="bi bi-check-lg text-success"></i>' : '<i class="bi bi-x text-danger"></i>' !!}</td>
                        <td>
                            <form method="POST"
                                  action="{{ route('flash_decks.desasociar', ['actividad' => $actividad->id, 'flash_deck' => $flash_deck->id]) }}">
                                @csrf
                                @method('DELETE')
                                <div class='btn-group'>
                                    <a title="{{ __('Show') }}"
                                       href="{{ route('flash_decks.show', [$flash_deck->id]) }}"
                                       class='btn btn-light btn-sm'><i class="bi bi-eye"></i></a>
                                    <a title="{{ __('Edit') }}"
                                       href="{{ route('flash_decks.edit', [$flash_deck->id]) }}"
                                       class='btn btn-light btn-sm'><i class="bi bi-pencil-square"></i></a>
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
        <form method="POST" action="{{ route('flash_decks.asociar', ['actividad' => $actividad->id]) }}">
            @csrf

            <div class="table-responsive">
                <table class="table">
                    <thead class="thead-dark">
                    <tr>
                        <th></th>
                        <th>#</th>
                        <th>{{ __('Title') }}</th>
                        <th>{{ __('Description') }}</th>
                        <th>{{ __('Template') }}</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($disponibles as $flash_deck)
                        <tr>
                            <td><input class="form-check-input" type="checkbox" name="seleccionadas[]"
                                       value="{{ $flash_deck->id }}"></td>
                            <td>{{ $flash_deck->id }}</td>
                            <td>{{ $flash_deck->titulo }}</td>
                            <td>{{ $flash_deck->descripcion }}</td>
                            <td>{!! $flash_deck->plantilla ? '<i class="bi bi-check-lg text-success"></i>' : '<i class="bi bi-x text-danger"></i>' !!}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>

            @include('partials.paginador', ['coleccion' => $disponibles])

            @include('layouts.errors', ['margenes' => 'mt-0 mb-3'])

            <div class="mb-4">
                <button type="submit" class="btn btn-primary me-2">{{ __('Save assigment') }}</button>
                <a class="btn btn-secondary"
                   href="{{ route('flash_decks.create') }}">{{ __('New flash_deck') }}</a>
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
