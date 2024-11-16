@use(Illuminate\Support\Str)
<h2>{{ __('Completed activities') }}</h2>

@if($unidades->count() > 0)
    <table class="tabla-datos">
        <tr>
            <th class="text-start">{{ __('Unit') }}</th>
            <th class="text-center">{{ __('Base') }}</th>
            <th class="text-center">{{ __('Extra') }}</th>
            <th class="text-center">{{ __('Revisit') }}</th>
        </tr>
        @foreach($unidades as $unidad)
            @if(!$unidad->hasEtiqueta('examen'))
                <tr>
                    <td>
                        @if(Str::length($unidad->codigo) > 0)
                            {{ $unidad->codigo }} -
                        @endif
                        @include('unidades.partials.nombre_con_etiquetas')
                    </td>
                    <td class="text-center {{ $unidad->num_actividades('base') > 0 ? $user->num_completadas('base', $unidad->id, $milestone) < $unidad->num_actividades('base') * $curso?->minimo_entregadas / 100 ? 'bg-warning text-dark' : 'bg-success' : '' }}">
                        {{ $user->num_completadas('base', $unidad->id, $milestone).'/'. $unidad->num_actividades('base') }}
                    </td>
                    <td class="text-center">
                        {{ $user->num_completadas('extra', $unidad->id, $milestone) }}
                    </td>
                    <td class="text-center">
                        {{ $user->num_completadas('repaso', $unidad->id, $milestone) }}
                    </td>
                </tr>
            @endif
        @endforeach
        <tr>
            <th colspan="4" class="text-start">
                @include('results.partials.completadas')
            </th>
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
