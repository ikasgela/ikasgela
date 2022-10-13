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
                    {!! Form::open(['route' => ['rules.destroy', $rule->id], 'method' => 'DELETE']) !!}
                    <div class='btn-group'>
                        <a title="{{ __('Edit') }}"
                           href="{{ route('rules.edit', [$rule->id]) }}"
                           class='btn btn-light btn-sm'><i class="fas fa-edit"></i></a>
                        @include('partials.boton_borrar')
                    </div>
                    {!! Form::close() !!}
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
