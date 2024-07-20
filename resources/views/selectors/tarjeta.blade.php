<div class="card">
    <div class="card-header d-flex justify-content-between">
        <div><i class="fas fa-code-branch me-2"></i>{{ __('Selector') }}</div>
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
                                        <a title="{{ __('Edit') }}"
                                           href="{{ route('rule_groups.edit', [$rule_group->id]) }}"
                                           class='btn btn-light btn-sm'><i class="fas fa-edit"></i></a>
                                        {!! Form::open(['route' => ['rule_groups.destroy', $rule_group->id], 'method' => 'DELETE']) !!}
                                        @include('partials.boton_borrar')
                                        {!! Form::close() !!}
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
