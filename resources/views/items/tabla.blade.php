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
                <td>{!! $item->correcto ? '<i class="bi bi-check-lg text-success"></i>' : '<i class="bi bi-x text-danger"></i>' !!}</td>
                <td>{!! $item->seleccionado ? '<i class="bi bi-check-lg text-success"></i>' : '<i class="bi bi-x text-danger"></i>' !!}</td>
                <td>{{ $item->feedback }}</td>
                <td>
                    @include('partials.botones_reordenar', ['ruta' => 'items.reordenar'])
                </td>
                <td>
                    <div class='btn-group'>
                        @include('partials.boton_editar', ['ruta' => 'items', 'recurso' => $item])
                        @include('partials.boton_duplicar', ['ruta' => 'items.duplicar', 'id' => $item->id, 'middle' => true])
                        {{ html()->form('DELETE', route('items.destroy', $item->id))->open() }}
                        @include('partials.boton_borrar', ['last' => true])
                        {{ html()->form()->close() }}
                    </div>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
