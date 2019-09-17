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
            <th>{{ __('Order') }}</th>
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
                <td class="p-0 pl-1 {{ $actividad->destacada ? 'bg-warning' : '' }}">&nbsp;</td>
                <td>
                    <input form="multiple" type="checkbox" name="seleccionadas[]" value="{{ $actividad->id }}">
                </td>
                <td>{{ $actividad->id }}</td>
                @if(Route::currentRouteName() == 'actividades.index')
                    <td>{!! $actividad->plantilla ? '<i class="fas fa-file"></i>' : '' !!}</td>
                @endif
                <td>{{ $actividad->unidad->curso->category->period->organization->name }}
                    - {{ $actividad->unidad->curso->category->period->name }}
                    - {{ $actividad->unidad->curso->nombre }}</td>
                <td>{{ $actividad->unidad->nombre }}</td>
                <td>
                    <div class='btn-group'>
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
                <td>{{ $actividad->nombre }}</td>
                <td>{{ $actividad->slug }}</td>
                <td>{{ $actividad->puntuacion }}</td>
                <td class="text-center">{!! $actividad->auto_avance ? '<i class="fas fa-check text-success"></i>' : '<i class="fas fa-times text-danger"></i>' !!}</td>
                <td>
                    @include('actividades.partials.siguiente')
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
        <tfoot>
        <tr class="bg-dark">
            <th colspan="13" class="m-0 py-1"></th>
        </tr>
        <tr>
            <td colspan="13">
                <div class="form-inline">
                    {!! Form::open(['route' => ['actividades.duplicar_grupo'], 'method' => 'POST', 'id' => 'multiple']) !!}
                    <button title="{{ __('Duplicate') }}"
                            type="submit"
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
                                {{ $unidad->nombre }}</option>
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
