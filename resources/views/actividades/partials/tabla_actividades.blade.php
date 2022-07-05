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
            <th>{{ __('Course') }}</th>
            <th>{{ __('Unit') }}</th>
            @if(\Illuminate\Support\Str::startsWith(Route::currentRouteName(), 'actividades.plantillas'))
                <th>{{ __('Order') }}</th>
            @endif
            <th>{{ __('Name') }}</th>
            <th>{{ __('Slug') }}</th>
            <th>{{ __('Score') }}</th>
            <th class="text-center">{{ __('Auto') }}</th>
            <th>{{ __('Next') }}</th>
            <th colspan="2">{{ __('Resources') }}</th>
            <th>{{ __('Actions') }}</th>
        </tr>
        </thead>
        <tbody>
        @foreach($actividades as $actividad)
            <tr class="table-cell-click" data-href="{{ route('actividades.preview', [$actividad->id]) }}">
                <td class="p-0 pl-1 {{ $actividad->destacada ? 'bg-warning' : '' }}">&nbsp;</td>
                <td>
                    <input form="multiple" type="checkbox" name="seleccionadas[]" value="{{ $actividad->id }}">
                </td>
                <td class="clickable">{{ $actividad->id }}</td>
                @if(Route::currentRouteName() == 'actividades.index')
                    <td>{!! $actividad->plantilla ? '<i class="fas fa-file"></i>' : '' !!}</td>
                @endif
                <td class="clickable">{{ $actividad->unidad->curso->full_name }}</td>
                <td class="clickable">{{ $actividad->unidad->full_name }}</td>
                @if(\Illuminate\Support\Str::startsWith(Route::currentRouteName(), 'actividades.plantillas'))
                    <td>
                        <div class='btn-group'>
                            <button title="{{ __('Move selected activities here') }}"
                                    form="multiple"
                                    type="submit"
                                    name="action" value="mm_{{ $actividad->id }}"
                                    class="btn btn-light btn-sm mr-3">
                                <i class="fas fa-arrow-right"></i>
                            </button>
                            {!! Form::open(['route' => ['actividades.reordenar', $ids[$loop->index], $ids[$loop->index-1] ?? -1], 'method' => 'POST']) !!}
                            <button title="{{ __('Up') }}"
                                    type="submit"
                                    {{ !isset($ids[$loop->index-1]) ? 'disabled' : '' }}
                                    class="btn btn-light btn-sm">
                                <i class="fas fa-arrow-up"></i>
                            </button>
                            {!! Form::close() !!}
                            {!! Form::open(['route' => ['actividades.reordenar', $ids[$loop->index], $ids[$loop->index+1] ?? -1], 'method' => 'POST']) !!}
                            <button title="{{ __('Down') }}"
                                    type="submit"
                                    {{ !isset($ids[$loop->index+1]) ? 'disabled' : '' }}
                                    class="btn btn-light btn-sm ml-1">
                                <i class="fas fa-arrow-down"></i>
                            </button>
                            {!! Form::close() !!}
                        </div>
                    </td>
                @endif
                <td class="clickable">
                    @include('actividades.partials.nombre_con_etiquetas')
                    @include('actividades.partials.caducada')
                </td>
                <td class="clickable">{{ $actividad->slug }}</td>
                <td class="clickable">{{ formato_decimales($actividad->puntuacion * ($actividad->multiplicador ?: 1)) }}</td>
                <td class="text-center clickable">@include('partials.check_yes_no', ['checked' => $actividad->auto_avance])</td>
                <td class="clickable">
                    @include('actividades.partials.siguiente')
                </td>
                @include('partials.botones_recursos')
                <td>
                    <div class='btn-group'>
                        <a title="{{ __('Sort') }}"
                           href="{{ route('actividades.show', [$actividad->id]) }}"
                           class='btn btn-light btn-sm'><i class="fas fa-sort"></i></a>
                    </div>
                </td>
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
        <tfoot>
        <tr class="bg-dark">
            <th colspan="42" class="m-0 py-1"></th>
            @if(Route::currentRouteName() == 'actividades.index')
                <th class="m-0 py-1"></th>
            @endif
        </tr>
        <tr>
            <td colspan="42">
                <div class="form-inline">
                    {!! Form::open(['route' => ['actividades.duplicar_grupo'], 'method' => 'POST', 'id' => 'multiple']) !!}
                    <button title="{{ __('Move') }}"
                            type="submit"
                            name="action" value="move"
                            class="btn btn-light btn-sm mr-2"><i class="fas fa-file-import"></i>
                    </button>
                    <button title="{{ __('Duplicate') }}"
                            type="submit"
                            name="action" value="duplicate"
                            class="btn btn-light btn-sm mr-2"><i class="fas fa-copy"></i>
                    </button>
                    {{ __('to unit') }}
                    <select class="form-control ml-2" id="unidad_id" name="unidad_id">
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
                                {{ $unidad->nombre }} {{ $unidad->visible ? '' : '(' . __('hidden') . ')' }}</option>
                            </option>
                        @endforeach
                    </select>
                    {!! Form::close() !!}
                </div>
            </td>
        </tr>
        </tfoot>
    </table>
</div>
