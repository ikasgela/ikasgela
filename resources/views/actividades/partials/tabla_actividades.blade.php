@use(Illuminate\Support\Str)
<div class="table-responsive">
    <table class="table table-hover">
        <thead class="thead-dark">
        <tr>
            <th class="p-0"></th>
            <th>
                <input type="checkbox" id="seleccionar_actividades">
            </th>
            <th>#</th>
            @if(Route::currentRouteName() == 'actividades.index')
                <th></th>
            @endif
            <th>{{ __('Unit') }}</th>
            @if(Str::startsWith(Route::currentRouteName(), 'actividades.plantillas'))
                <th>{{ __('Order') }}</th>
            @endif
            <th>{{ __('Name') }}</th>
            <th class="text-center">{{ __('Score') }}</th>
            <th class="text-center">{{ __('Auto') }}</th>
            <th>{{ __('Next') }}</th>
            <th colspan="2">{{ __('Resources') }}</th>
            <th>{{ __('Actions') }}</th>
        </tr>
        </thead>
        <tbody>
        @foreach($actividades as $actividad)
            <tr class="table-cell-click" data-href="{{ route('actividades.preview', [$actividad->id]) }}">
                <td class="p-0 ps-1 {{ $actividad->destacada ? 'bg-warning' : '' }}">&nbsp;</td>
                <td>
                    <input form="multiple"
                           data-chkbox-shiftsel="grupo1"
                           type="checkbox" name="seleccionadas[]" value="{{ $actividad->id }}">
                </td>
                <td class="clickable">{{ $actividad->id }}</td>
                @if(Route::currentRouteName() == 'actividades.index')
                    <td>{!! $actividad->plantilla ? '<i class="bi bi-file-earmark"></i>' : '' !!}</td>
                @endif
                <td class="clickable">{{ $actividad->unidad->full_name }}</td>
                @if(Str::startsWith(Route::currentRouteName(), 'actividades.plantillas'))
                    <td>
                        <div class='btn-group'>
                            <button title="{{ __('Move selected activities here') }}"
                                    form="multiple"
                                    type="submit"
                                    name="action" value="mm_{{ $actividad->id }}"
                                    class="btn btn-light btn-sm me-3 rounded">
                                <i class="bi bi-arrow-right"></i>
                            </button>
                            {{ html()->form('POST', route('actividades.reordenar', [$ids[$loop->index], $ids[$loop->index-1] ?? -1]))->open() }}
                            <button title="{{ __('Up') }}"
                                    type="submit"
                                    {{ !isset($ids[$loop->index-1]) ? 'disabled' : '' }}
                                    class="btn btn-light btn-sm">
                                <i class="bi bi-arrow-up"></i>
                            </button>
                            {{ html()->form()->close() }}
                            {{ html()->form('POST', route('actividades.reordenar', [$ids[$loop->index], $ids[$loop->index+1] ?? -1]))->open() }}
                            <button title="{{ __('Down') }}"
                                    type="submit"
                                    {{ !isset($ids[$loop->index+1]) ? 'disabled' : '' }}
                                    class="btn btn-light btn-sm ms-1">
                                <i class="bi bi-arrow-down"></i>
                            </button>
                            {{ html()->form()->close() }}
                        </div>
                    </td>
                @endif
                <td class="clickable">
                    <div>
                        <div class="d-flex align-items-center">
                            @include('actividades.partials.nombre_con_etiquetas')
                            @include('actividades.partials.caducada')
                        </div>
                        <span class="small text-secondary">{{ $actividad->slug }}</span>
                    </div>
                </td>
                <td class="text-center clickable">{{ formato_decimales($actividad->puntuacion * ($actividad->multiplicador ?: 1)) }}</td>
                <td class="text-center clickable">@include('partials.check_yes_no', ['checked' => $actividad->auto_avance])</td>
                <td class="clickable">
                    @include('actividades.partials.siguiente')
                </td>
                @include('partials.botones_recursos')
                <td>
                    <div class='btn-group'>
                        <a title="{{ __('Sort resources') }}"
                           href="{{ route('actividades.show', [$actividad->id]) }}"
                           class='btn btn-light btn-sm'><i class="bi bi-arrow-down-up"></i></a>
                    </div>
                </td>
                <td>
                    <div class='btn-group'>
                        <a title="{{ __('Preview') }}"
                           href="{{ route('actividades.preview', [$actividad->id]) }}"
                           class='btn btn-light btn-sm'><i class="bi bi-eye"></i></a>
                        <a title="{{ __('Edit') }}"
                           href="{{ route('actividades.edit', [$actividad->id]) }}"
                           class='btn btn-light btn-sm'><i class="bi bi-pencil-square"></i></a>
                        {{ html()->form('POST', route('actividades.duplicar', $actividad->id))->open() }}
                        <button title="{{ __('Duplicate') }}"
                                type="submit"
                                class="btn btn-light btn-sm rounded-0"><i class="fas fa-copy"></i>
                        </button>
                        {{ html()->form()->close() }}
                        {{ html()->form('DELETE', route('actividades.destroy', $actividad->id))->open() }}
                        @include('partials.boton_borrar', ['last' => true])
                        {{ html()->form()->close() }}
                    </div>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>

{{ html()->form('POST', route('actividades.duplicar_grupo'))->id('multiple')->open() }}
<div class="row row-cols-lg-auto g-3 align-items-center mb-3">
    <div class="col-12">
        <button title="{{ __('Move') }}"
                type="submit"
                name="action" value="move"
                class="btn btn-light btn-sm"><i class="fas fa-file-import"></i>
        </button>
        <button title="{{ __('Duplicate') }}"
                type="submit"
                name="action" value="duplicate"
                class="btn btn-light btn-sm"><i class="fas fa-copy"></i>
        </button>
    </div>
    <div class="col-12">
        {{ __('to unit') }}
    </div>
    <div class="col-12">
        <select class="form-select" id="unidad_id" name="unidad_id">
            <option value="">{{ __('--- None ---') }}</option>
            @foreach($todas_unidades as $unidad)
                <option
                    value="{{ $unidad->id }}">
                    {{ $unidad->curso->category->period->organization->name }}
                    - {{ $unidad->curso->category->period->name }}
                    - {{ $unidad->curso->nombre }} -
                    @isset($unidad->codigo)
                        {{ $unidad->codigo }} -
                    @endisset
                    {{ $unidad->nombre }} {{ $unidad->visible ? '' : '(' . __('hidden') . ')' }}
                </option>
            @endforeach
        </select>
    </div>
</div>
{{ html()->form()->close() }}
