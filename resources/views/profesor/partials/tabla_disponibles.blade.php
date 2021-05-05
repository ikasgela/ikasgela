<div class="table-responsive">
    <table class="table table-hover">
        <thead class="thead-dark">
        <tr>
            <th class="p-0"></th>
            <th>
                <input type="checkbox" id="seleccionar_actividades">
            </th>
            <th>#</th>
            <th>{{ __('Name') }}</th>
            <th>{{ __('Slug') }}</th>
            <th>{{ __('Score') }}</th>
            <th class="text-center">{{ __('Auto') }}</th>
            <th>{{ __('Next') }}</th>
            @if(Auth::user()->hasRole('admin'))
                <th>{{ __('Resources') }}</th>
            @endif
            <th>{{ __('Actions') }}</th>
        </tr>
        </thead>
        <tbody>
        @foreach($disponibles as $actividad)
            <tr class="table-cell-click" data-href="{{ route('actividades.preview', [$actividad->id]) }}">
                <td class="p-0 pl-1 {{ $actividad->destacada ? 'bg-warning' : '' }}">&nbsp;</td>
                <td>
                    <input type="checkbox" name="seleccionadas[{{ $actividad->id }}]" value="{{ $actividad->id }}">
                </td>
                <td class="clickable">{{ $actividad->id }}</td>
                <td class="clickable">
                    @include('actividades.partials.nombre_con_etiquetas')
                    @include('actividades.partials.caducada')
                </td>
                <td class="clickable">{{ $actividad->unidad->slug.'/'.$actividad->slug }}</td>
                <td class="clickable">{{ formato_decimales($actividad->puntuacion * ($actividad->multiplicador ?: 1)) }}</td>
                <td class="text-center clickable">{!! $actividad->auto_avance ? '<i class="fas fa-check text-success"></i>' : '<i class="fas fa-times text-danger"></i>' !!}</td>
                @include('profesor.partials.siguiente_actividad')
                @if(Auth::user()->hasRole('admin'))
                    @include('partials.botones_recursos')
                @endif
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

<div class="d-flex justify-content-center">
    @if(isset($actividades))
        {{ $disponibles->appends(['asignadas' => $actividades->currentPage()])->links() }}
    @else
        {{ $disponibles->links() }}
    @endif
</div>
