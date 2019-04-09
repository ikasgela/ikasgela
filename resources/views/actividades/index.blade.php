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
                {{-- <th>{{ __('Score') }}</th> --}}
                <th>{{ __('Next') }}</th>
                <th>{{ __('Resources') }}</th>
                <th>{{ __('Actions') }}</th>
            </tr>
            </thead>
            <tbody>
            @foreach($actividades as $actividad)
                <tr>
                    <td class="py-3">{{ $actividad->id }}</td>
                    <td class="py-3">{!! $actividad->plantilla ? '<i class="far fa-copy"></i>' : '' !!}</td>
                    <td class="py-3">{{ $actividad->unidad->nombre }}</td>
                    <td class="py-3">{{ $actividad->nombre }}</td>
                    <td class="py-3">{{ $actividad->slug }}</td>
                    {{-- <td class="py-3">{{ $actividad->puntuacion }}</td> --}}
                    <td class="py-3">
                        {!! !is_null($actividad->siguiente) ? $actividad->final
                        ? '<i class="fas fa-times text-danger"></i>'
                        : '<i class="fas fa-arrow-right text-success"></i>'
                        : '' !!}
                        &nbsp;
                        @if( !is_null($actividad->siguiente) )
                            {{ $actividad->siguiente->slug . ' ('.$actividad->siguiente->id.')' }}
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('youtube_videos.actividad', [$actividad->id]) }}"
                           class='btn btn-outline-dark'>Youtube</a>
                        <a href="{{ route('intellij_projects.actividad', [$actividad->id]) }}"
                           class='btn btn-outline-dark'>IntelliJ</a>
                    </td>
                    <td>
                        <form method="POST" action="{{ route('actividades.destroy', [$actividad->id]) }}">
                            @csrf
                            @method('DELETE')
                            <div class='btn-group'>
                                <a href="{{ route('actividades.show', [$actividad->id]) }}"
                                   class='btn btn-light btn-sm'><i class="fas fa-eye"></i></a>
                                <a href="{{ route('actividades.edit', [$actividad->id]) }}"
                                   class='btn btn-light btn-sm'><i class="fas fa-edit"></i></a>
                                <button type="submit" onclick="return confirm('¿Seguro?')"
                                        class="btn btn-light btn-sm"><i class="fas fa-trash text-danger"></i>
                                </button>
                            </div>
                        </form>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endsection
