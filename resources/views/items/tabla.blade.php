<div class="table-responsive">
    <table class="table table-hover">
        <thead class="thead-dark">
        <tr>
            <th>#</th>
            <th>{{ __('Text') }}</th>
            <th>{{ __('Correct') }}</th>
            <th>{{ __('Selected') }}</th>
            <th>{{ __('Feedback') }}</th>
            <th>{{ __('Order') }}</th>
            <th>{{ __('Actions') }}</th>
        </tr>
        </thead>
        <tbody>
        @foreach($items as $item)
            <tr>
                <td>{{ $item->id }}</td>
                <td>{{ $item->texto }}</td>
                <td>{!! $item->correcto ? '<i class="fas fa-check text-success"></i>' : '<i class="fas fa-times text-danger"></i>' !!}</td>
                <td>{!! $item->seleccionado ? '<i class="fas fa-check text-success"></i>' : '<i class="fas fa-times text-danger"></i>' !!}</td>
                <td>{{ $item->feedback }}</td>
                <td>
                    @include('partials.botones_reordenar', ['ruta' => 'items.reordenar'])
                </td>
                <td>
                    {!! Form::open(['route' => ['items.destroy', $item->id], 'method' => 'DELETE']) !!}
                    <div class='btn-group'>
                        <a title="{{ __('Edit') }}"
                           href="{{ route('items.edit', [$item->id]) }}"
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
