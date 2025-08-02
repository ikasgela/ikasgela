<div class="card mb-3">
    <div class="card-header d-flex justify-content-between">
        <div><i class="bi bi-shuffle me-2"></i>{{ __('Selector') }}</div>
        <div>
            @include('partials.ver_recurso', ['recurso' => $selector, 'ruta' => 'selectors'])
            @include('partials.modificar_recursos', ['ruta' => 'selectors'])
            @include('partials.editar_recurso', ['recurso' => $selector, 'ruta' => 'selectors'])
        </div>
    </div>
    <div class="card-body">
        @include('partials.cabecera_recurso', ['recurso' => $selector, 'ruta' => 'selectors'])
        @if(count($selector->rule_groups) > 0)
            <div class="table-responsive">
                <table class="table table-bordered small">
                    <thead class="thead-dark">
                    <tr>
                        <th colspan="5">{{ __('Rule group') }}</th>
                        @if(Auth::user()->hasRole('profesor') && Route::currentRouteName() == 'selectors.show')
                            <th class="text-center">{{ __('Actions') }}</th>
                        @endif
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($selector->rule_groups as $rule_group)
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
                            @if(Auth::user()->hasRole('profesor') && Route::currentRouteName() == 'selectors.show')
                                <td class="text-center">
                                    <div class='btn-group'>
                                        @include('partials.boton_editar', ['ruta' => 'rule_groups', 'recurso' => $rule_group])
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
        @endif
    </div>
    @if(Auth::user()->hasRole('profesor') && Route::currentRouteName() == 'selectors.show')
        <hr class="my-0">
        <div class="card-body">
            <a class="btn btn-primary"
               href="{{ route('rule_groups.anyadir', ['selector' => $selector]) }}">{{ __('New rule group') }}</a>
        </div>
    @endif
</div>
