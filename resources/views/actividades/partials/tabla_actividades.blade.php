<div class="table-responsive">
    <table class="table table-hover">
        <thead class="thead-dark">
        <tr>
            <th>#</th>
            @if(Route::currentRouteName() == 'actividades.index')
                <th></th>
            @endif
            <th>{{ __('Course') }}</th>
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
                @if(Route::currentRouteName() == 'actividades.index')
                    <td>{!! $actividad->plantilla ? '<i class="fas fa-file"></i>' : '' !!}</td>
                @endif
                <td>{{ $actividad->unidad->curso->category->period->organization->name }}
                    - {{ $actividad->unidad->curso->category->period->name }}
                    - {{ $actividad->unidad->curso->nombre }}</td>
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
                    <div class='btn-group'>
                        <a title="{{ __('Preview') }}"
                           href="{{ route('actividades.preview', [$actividad->id]) }}"
                           class='btn btn-light btn-sm'><i class="fas fa-eye"></i></a>
                        <a title="{{ __('Edit') }}"
                           href="{{ route('actividades.edit', [$actividad->id]) }}"
                           class='btn btn-light btn-sm'><i class="fas fa-edit"></i></a>
                        {!! Form::open(['route' => ['actividades.duplicar', $actividad->id], 'method' => 'POST']) !!}
                        <button title="{{ __('Duplicate') }}"
                                type="submit"
                                class="btn btn-light btn-sm"><i class="fas fa-copy"></i>
                        </button>
                        {!! Form::close() !!}
                        {!! Form::open(['route' => ['actividades.destroy', $actividad->id], 'method' => 'DELETE']) !!}
                        @include('partials.boton_borrar')
                        {!! Form::close() !!}
                    </div>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
