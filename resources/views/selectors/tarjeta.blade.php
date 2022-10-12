<div class="card">
    <div class="card-header d-flex justify-content-between">
        <div><i class="fas fa-code-branch mr-2"></i>{{ __('Selector') }}</div>
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
                            <td class="text-center">{{ $rule_group->actividad()->nombre }}</td>
                            @if(Auth::user()->hasRole('profesor') && Route::currentRouteName() == 'selectors.show')
                                <td class="text-center">
                                    <div class='btn-group'>
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
    {{--
    @if(Auth::user()->hasRole('profesor') && Route::currentRouteName() == 'selectors.show')
        <hr class="my-0">
        <div class="card-body">
            {!! Form::open(['route' => ['rule_groups.store']]) !!}

            {{ Form::campoTexto('url', __('URL'), '', ['placeholder' => 'https://ikasgela.com']) }}
            {{ Form::campoTexto('descripcion', __('Description')) }}
            {!! Form::hidden('selector_id', $selector->id) !!}

            <span class="help-block text-danger">{{ $errors->first('url') }}</span>

            <button class="btn btn-primary single_click">
                <i class="fas fa-spinner fa-spin" style="display:none;"></i> {{ __('Add') }}
            </button>

            {!! Form::close() !!}
        </div>
    @endif
    --}}
</div>
