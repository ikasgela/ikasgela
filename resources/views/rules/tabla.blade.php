<div class="table-responsive">
    <table class="table table-hover">
        <thead class="thead-dark">
        <tr>
            <th>#</th>
            <th>{{ __('Property') }}</th>
            <th>{{ __('Operator') }}</th>
            <th>{{ __('Value') }}</th>
            <th>{{ __('Actions') }}</th>
        </tr>
        </thead>
        <tbody>
        @foreach($rules as $rule)
            <tr>
                <td>{{ $rule->id }}</td>
                <td>{{ $rule->propiedad }}</td>
                <td>{{ $rule->operador }}</td>
                <td>{{ $rule->valor }}</td>
                <td>
                    <div class='btn-group'>
                        @include('partials.boton_editar', ['ruta' => 'rules', 'recurso' => $rule])
                        @include('partials.boton_duplicar', ['ruta' => 'rules.duplicar', 'id' => $rule->id, 'middle' => true])
                        {{ html()->form('DELETE', route('rules.destroy', $rule->id))->open() }}
                        @include('partials.boton_borrar', ['last' => true])
                        {{ html()->form()->close() }}
                    </div>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
