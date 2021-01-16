@include('partials.subtitulo', ['subtitulo' => __('Completed activities')])

@if($unidades->count() > 0)
    <div class="table-responsive mb-2">
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
                <th colspan="4">{{ __('Completed activities') }}: {{ $calificaciones->numero_actividades_completadas }}
                    - {{ __('Group mean') }}: {{ $media_actividades_grupo }}</th>
            </tr>
            </tfoot>
        </table>
    </div>

    @include('results.partials.desarrollo_contenidos')

@else
    <div class="row">
        <div class="col-md-12">
            <p>No hay unidades.</p>
        </div>
    </div>
@endif
