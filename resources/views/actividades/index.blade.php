@extends('layouts.app')

@section('content')

    @include('partials.titular', ['titular' => __('Activities')])

    <div class="mb-3">
        <a class="btn btn-primary" href="{{ route('actividades.create') }}">{{ __('New activity') }}</a>
        @if(Route::currentRouteName() == 'actividades.index')
            {!! link_to_route('actividades.plantillas', $title = 'Ver solo plantillas', $parameters = [],
                    $attributes = ['class' => 'btn btn-link text-secondary']); !!}
        @else
            {!! link_to_route('actividades.index', $title = 'Ver todas las actividades', $parameters = [],
                    $attributes = ['class' => 'btn btn-link text-secondary']); !!}
        @endif
    </div>

    <div class="table-responsive">
        <table class="table table-hover">
            <thead class="thead-dark">
            <tr>
                <th>#</th>
                <th></th>
                <th>{{ __('Unit') }}</th>
                <th>{{ __('Name') }}</th>
                <th>{{ __('Slug') }}</th>
                <th>{{ __('Score') }}</th>
                <th class="text-center">{{ __('Auto') }}</th>
                <th>{{ __('Next') }}</th>
                <th>{{ __('Resources') }}</th>
                <th>{{ __('Actions') }}</th>
            </tr>
            </thead>
            <tbody>
            @foreach($actividades as $actividad)
                <tr>
                    <td>{{ $actividad->id }}</td>
                    <td>{!! $actividad->plantilla ? '<i class="fas fa-file"></i>' : '' !!}</td>
                    <td>{{ $actividad->unidad->nombre }}</td>
                    <td>{{ $actividad->nombre }}</td>
                    <td>{{ $actividad->slug }}</td>
                    <td>{{ $actividad->puntuacion }}</td>
                    <td class="text-center">{!! $actividad->auto_avance ? '<i class="fas fa-check text-success"></i>' : '<i class="fas fa-times text-danger"></i>' !!}</td>
                    <td>
                        {!! !is_null($actividad->siguiente) ? $actividad->final
                        ? '<i class="fas fa-times text-danger"></i>'
                        : '<i class="fas fa-arrow-right text-success"></i>'
                        : '' !!}
                        &nbsp;
                        @if( !is_null($actividad->siguiente) )
                            {{ $actividad->siguiente->slug . ' ('.$actividad->siguiente->id.')' }}
                        @endif
                    </td>
                    @include('partials.botones_recursos')
                    <td>
                        <form method="POST" action="{{ route('actividades.destroy', [$actividad->id]) }}">
                            @csrf
                            @method('DELETE')
                            <div class='btn-group'>
                                <a title="{{ __('Preview') }}"
                                   href="{{ route('actividades.preview', [$actividad->id]) }}"
                                   class='btn btn-light btn-sm'><i class="fas fa-eye"></i></a>
                                <a title="{{ __('Edit') }}"
                                   href="{{ route('actividades.edit', [$actividad->id]) }}"
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
