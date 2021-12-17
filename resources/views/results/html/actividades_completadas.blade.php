@include('partials.subtitulo', ['subtitulo' => __('Completed activities')])

@if($unidades->count() > 0)
    <div class="table-responsive mb-2">
        <table class="table table-hover">
            <thead class="thead-dark">
            <tr>
                <th>{{ __('Unit') }}</th>
                <th class="text-center">{{ __('Base') }}</th>
                @if(!Auth::user()->baja_ansiedad)
                    <th class="text-center">{{ __('Extra') }}</th>
                    <th class="text-center">{{ __('Revisit') }}</th>
                @endif
            </tr>
            </thead>
            <tbody>
            @foreach($unidades as $unidad)
                @if(!$unidad->hasEtiqueta('examen'))
                    <tr>
                        <td class="align-middle">
                            @isset($unidad->codigo)
                                {{ $unidad->codigo }} -
                            @endisset
                            @include('unidades.partials.nombre_con_etiquetas')
                        </td>
                        <td class="align-middle text-center {{ $unidad->num_actividades('base') > 0 ? $user->num_completadas('base', $unidad->id, $milestone) < $unidad->num_actividades('base') * $curso?->minimo_entregadas / 100 ? 'bg-warning text-dark' : 'bg-success' : '' }}">
                            {{ $user->num_completadas('base', $unidad->id, $milestone).'/'. $unidad->num_actividades('base') }}
                        </td>
                        @if(!Auth::user()->baja_ansiedad)
                            <td class="align-middle text-center">
                                {{ $user->num_completadas('extra', $unidad->id, $milestone) }}
                            </td>
                            <td class="align-middle text-center">
                                {{ $user->num_completadas('repaso', $unidad->id, $milestone) }}
                            </td>
                        @endif
                    </tr>
                @endif
            @endforeach
            </tbody>
            <tfoot class="thead-dark">
            <tr>
                <th colspan="4">{{ __('Completed activities') }}: {{ $calificaciones->numero_actividades_completadas }}
                    @if(!Auth::user()->baja_ansiedad)
                        - {{ __('Group mean') }}: {{ $media_actividades_grupo }}
                    @endif
                </th>
            </tr>
            </tfoot>
        </table>
    </div>

    @if(!Auth::user()->baja_ansiedad)
        @include('results.html.desarrollo_contenidos')
    @endif
@else
    <div class="row">
        <div class="col-md-12">
            <p>No hay unidades.</p>
        </div>
    </div>
@endif
