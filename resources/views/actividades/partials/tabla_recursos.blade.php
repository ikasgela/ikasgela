<div class="table-responsive">
    <table class="table table-hover">
        <thead class="thead-dark">
        <tr>
            <th>{{ __('Resource') }}</th>
            <th>{{ __('Type') }}</th>
            <th class="text-center">{{ __('Columns') }}</th>
            <th></th>
            <th>{{ __('Order') }}</th>
        </tr>
        </thead>
        <tbody>
        @foreach($actividad->recursos as $recurso)
            <tr class="table-cell-click" data-href="{{ route('actividades.preview', [$actividad->id]) }}">
                <td>{{ $recurso->titulo }}</td>
                <td>@include('actividades.partials.nombre_recurso')</td>
                <td class="text-center">{{ $recurso->pivot->columnas }}</td>
                <td>
                    <div class='btn-group'>
                        {{ html()->form('POST', route('actividades.recurso_modificar_columnas', $actividad->id))->open() }}
                        <button title="{{ __('Minus') }}"
                                type="submit"
                                {{ $recurso->pivot->columnas > 1 ? '' : 'disabled' }}
                                class="btn {{ $recurso->pivot->columnas > 1 ? 'btn-primary' : 'btn-light' }} btn-sm me-1">
                            <i class="bi bi-dash-lg"></i>
                        </button>
                        <input type="hidden" name="recurso" value="{{ $ids[$loop->index] }}">
                        <input type="hidden" name="accion" value="restar">
                        {{ html()->form()->close() }}

                        {{ html()->form('POST', route('actividades.recurso_modificar_columnas', $actividad->id))->open() }}
                        <button title="{{ __('Plus') }}"
                                type="submit"
                                {{ $recurso->pivot->columnas < 12 ? '' : 'disabled' }}
                                class="btn {{ $recurso->pivot->columnas < 12 ? 'btn-primary' : 'btn-light' }} btn-sm">
                            <i class="bi bi-plus-lg"></i>
                        </button>
                        <input type="hidden" name="recurso" value="{{ $ids[$loop->index] }}">
                        <input type="hidden" name="accion" value="sumar">
                        {{ html()->form()->close() }}
                    </div>
                </td>
                <td>
                    <div class='btn-group'>
                        {{ html()->form('POST', route('actividades.reordenar_recursos', $actividad->id))->open() }}
                        <button title="{{ __('Up') }}"
                                type="submit"
                                {{ !isset($ids[$loop->index-1]) ? 'disabled' : '' }}
                                class="btn {{ !isset($ids[$loop->index-1]) ? 'btn-light' : 'btn-primary' }} btn-sm">
                            <i class="bi bi-arrow-up"></i>
                        </button>
                        <input type="hidden" name="a1" value="{{ $ids[$loop->index] }}">
                        <input type="hidden" name="a2" value="{{ $ids[$loop->index-1] ?? -1 }}">
                        {{ html()->form()->close() }}

                        {{ html()->form('POST', route('actividades.reordenar_recursos', $actividad->id))->open() }}
                        <button title="{{ __('Down') }}"
                                type="submit"
                                {{ !isset($ids[$loop->index+1]) ? 'btn-light disabled' : '' }}
                                class="btn {{ !isset($ids[$loop->index+1]) ? 'btn-light' : 'btn-primary' }} btn-sm ms-1">
                            <i class="bi bi-arrow-down"></i>
                        </button>
                        <input type="hidden" name="a1" value="{{ $ids[$loop->index] }}">
                        <input type="hidden" name="a2" value="{{ $ids[$loop->index+1] ?? -1 }}">
                        {{ html()->form()->close() }}
                    </div>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
