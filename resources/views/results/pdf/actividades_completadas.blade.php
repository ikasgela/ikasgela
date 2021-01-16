<h2>{{ __('Completed activities') }}</h2>

@if($unidades->count() > 0)
    <table class="tabla-datos">
        <tr>
            <th class="text-left">{{ __('Unit') }}</th>
            <th class="text-center">{{ __('Base') }}</th>
            <th class="text-center">{{ __('Extra') }}</th>
            <th class="text-center">{{ __('Revisit') }}</th>
        </tr>
        @foreach($unidades as $unidad)
            @if(!$unidad->hasEtiqueta('examen'))
                <tr>
                    <td>
                        @isset($unidad->codigo)
                            {{ $unidad->codigo }} -
                        @endisset
                        @include('unidades.partials.nombre_con_etiquetas')
                    </td>
                    <td class="text-center {{ $unidad->num_actividades('base') > 0 ? $user->num_completadas('base', $unidad->id) < $unidad->num_actividades('base') ? 'bg-warning text-dark' : 'bg-success' : '' }}">
                        {{ $user->num_completadas('base', $unidad->id).'/'. $unidad->num_actividades('base') }}
                    </td>
                    <td class="text-center">
                        {{ $user->num_completadas('extra', $unidad->id) }}
                    </td>
                    <td class="text-center">
                        {{ $user->num_completadas('repaso', $unidad->id) }}
                    </td>
                </tr>
            @endif
        @endforeach
        <tr>
            <th colspan="4" class="text-left">{{ __('Completed activities') }}
                : {{ $calificaciones->numero_actividades_completadas }}
                - {{ __('Group mean') }}: {{ $media_actividades_grupo }}</th>
        </tr>
    </table>

    @include('results.pdf.desarrollo_contenidos')

@else
    <div class="row">
        <div class="col-md-12">
            <p>No hay unidades.</p>
        </div>
    </div>
@endif
