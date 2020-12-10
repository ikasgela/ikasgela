@include('partials.subtitulo', ['subtitulo' => __('Completed activities')])

@if($unidades->count() > 0)
    <div class="table-responsive">
        <table class="table table-hover">
            <thead class="thead-dark">
            <tr>
                <th>{{ __('Unit') }}</th>
                <th class="text-center">{{ __('Base') }}</th>
                <th class="text-center">{{ __('Extra') }}</th>
                <th class="text-center">{{ __('Revisit') }}</th>
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
                        <td class="align-middle text-center {{ $unidad->num_actividades('base') > 0 ? $user->num_completadas('base', $unidad->id) < $unidad->num_actividades('base') ? 'bg-warning text-dark' : 'bg-success' : '' }}">
                            {{ $user->num_completadas('base', $unidad->id).'/'. $unidad->num_actividades('base') }}
                        </td>
                        <td class="align-middle text-center">
                            {{ $user->num_completadas('extra', $unidad->id) }}
                        </td>
                        <td class="align-middle text-center">
                            {{ $user->num_completadas('repaso', $unidad->id) }}
                        </td>
                    </tr>
                @endif
            @endforeach
            </tbody>
            <tfoot class="thead-dark">
            <tr>
                <th colspan="4">{{ __('Completed activities') }}: {{ $numero_actividades_completadas }}
                    - {{ __('Group mean') }}: {{ $media_actividades_grupo }}</th>
            </tr>
            </tfoot>
        </table>
    </div>

    @include('partials.subtitulo', ['subtitulo' => __('Content development')])

    {{-- Tarjeta --}}
    <div class="card">
        <div class="card-body">
            @foreach ($unidades as $unidad)
                <h5 class="card-title">
                    @isset($unidad->codigo)
                        {{ $unidad->codigo }} -
                    @endisset
                    @include('unidades.partials.nombre_con_etiquetas')
                </h5>
                <p class="ml-5">{{ $unidad->descripcion }}</p>
                <div class="ml-5 progress" style="height: 24px;">
                    @php($porcentaje = $resultados_unidades[$unidad->id]->actividad > 0 ? round($resultados_unidades[$unidad->id]->tarea/$resultados_unidades[$unidad->id]->actividad*100) : 0)
                    <div class="progress-bar {{ $porcentaje<50 ? 'bg-warning text-dark' : 'bg-success' }}"
                         role="progressbar"
                         style="width: {{ $porcentaje }}%;"
                         aria-valuenow="{{ $porcentaje }}"
                         aria-valuemin="0"
                         aria-valuemax="100">@if($porcentaje>0){{ $porcentaje }}&thinsp;%@endif
                    </div>
                </div>
                <div class="text-muted small text-right">
                    {{ $resultados_unidades[$unidad->id]->tarea + 0
                    }}/{{ $resultados_unidades[$unidad->id]->actividad + 0 }}
                </div>
                @if(!$loop->last)
                    <hr>
                @endif
            @endforeach
        </div>
    </div>
    {{-- Fin tarjeta--}}

@else
    <div class="row">
        <div class="col-md-12">
            <p>No hay unidades.</p>
        </div>
    </div>
@endif
