@extends('layouts.app')

@section('content')

    @include('partials.titular', ['titular' => __('Flashcards')])

    @if(Auth::user()->hasAnyRole(['admin']))
        @include('partials.recursos.filtro_curso', ['ruta' => 'flash_decks.index.filtro'])
    @endif

    <div class="mb-3">
        <a class="btn btn-primary" href="{{ route('flash_decks.create') }}">{{ __('New deck') }}</a>
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
            @foreach($flash_decks as $flash_deck)
                <tr>
                    <td>{{ $flash_deck->id }}</td>
                    <td>{{ $flash_deck->titulo }}</td>
                    <td>{{ $flash_deck->descripcion }}</td>
                    <td>{!! $flash_deck->plantilla ? '<i class="bi bi-check-lg text-success"></i>' : '<i class="bi bi-x text-danger"></i>' !!}</td>
                    <td>{!! $flash_deck->completada ? '<i class="bi bi-check-lg text-success"></i>' : '<i class="bi bi-x text-danger"></i>' !!}</td>
                    <td class="text-nowrap">
                        <div class='btn-group'>
                            @include('partials.boton_mostrar', ['ruta' => 'flash_decks', 'recurso' => $flash_deck])
                            @include('partials.boton_duplicar', ['ruta' => 'flash_decks.duplicar', 'id' => $flash_deck->id, 'middle' => true])
                            {{ html()->form('DELETE', route('flash_decks.destroy', $flash_deck->id))->open() }}
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
