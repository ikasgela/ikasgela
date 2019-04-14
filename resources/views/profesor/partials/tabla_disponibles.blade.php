<div class="table-responsive">
    <table class="table table-hover">
        <thead class="thead-dark">
        <tr>
            <th>
                <input type="checkbox" id="seleccionar_actividades">
            </th>
            <th>#</th>
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
        @foreach($disponibles as $actividad)
            <tr>
                <td>
                    <input type="checkbox" name="seleccionadas[]" value="{{ $actividad->id }}">
                </td>
                <td>{{ $actividad->id }}</td>
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
                    </div>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
