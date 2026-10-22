@php
    $acciones = Auth::user()->hasAnyRole(['admin', 'profesor'])
        && in_array(Route::currentRouteName(), ['selectors.show', 'selectors.edit']);

    // Se recuerda de dónde se edita el grupo de reglas para poder volver allí
    $volver = Route::currentRouteName() === 'selectors.edit' ? 'edit' : 'show';
@endphp

@if(count($rule_groups) > 0)
    <div class="table-responsive">
        <table class="table table-bordered small">
            <thead class="thead-dark">
            <tr>
                <th colspan="5">{{ __('Rule group') }}</th>
                @if($acciones)
                    <th class="text-center">{{ __('Actions') }}</th>
                @endif
            </tr>
            </thead>
            <tbody>
            @foreach ($rule_groups as $rule_group)
                <tr>
                    <td class="text-center">{{ $rule_group->id }}</td>
                    <td class="text-center">{{ $rule_group->operador }}</td>
                    <td class="text-center">
                        @foreach ($rule_group->rules as $rule)
                            <div>{{ $rule->propiedad }} {{ $rule->operador }} {{ $rule->valor }}</div>
                        @endforeach
                    </td>
                    <td class="text-center">{{ $rule_group->accion }}</td>
                    <td class="text-center">{{ $rule_group->actividad()->nombre ?? '?' }}</td>
                    @if($acciones)
                        <td class="text-center">
                            <div class='btn-group'>
                                @include('partials.boton_editar', ['ruta' => 'rule_groups', 'recurso' => $rule_group, 'parametros_url' => '?volver=' . $volver])
                                @include('partials.boton_duplicar', ['ruta' => 'rule_groups.duplicar', 'id' => $rule_group->id, 'middle' => true])
                                {{ html()->form('DELETE', route('rule_groups.destroy', $rule_group->id))->open() }}
                                @include('partials.boton_borrar', ['last' => true])
                                {{ html()->form()->close() }}
                            </div>
                        </td>
                    @endif
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@else
    <p class="text-muted">{{ __('This selector has no rule groups yet.') }}</p>
@endif

@if($acciones)
    <div class="mb-3">
        <a class="btn btn-primary"
           href="{{ route('rule_groups.anyadir', ['selector' => $selector]) }}">{{ __('New rule group') }}</a>
    </div>
@endif
