<div class="table-responsive">
    <table class="table table-hover">
        <thead class="thead-dark">
        <tr>
            <th>
                <input type="checkbox" id="seleccionar_actividades">
            </th>
            <th>#</th>
            <th>{{ __('Name') }}</th>
            <th>{{ __('Description') }}</th>
            <th>{{ __('Slug') }}</th>
        </tr>
        </thead>
        <tbody>
        @foreach($disponibles as $actividad)
            <tr>
                <td>
                    <input type="checkbox" name="seleccionadas[]" value="{{ $actividad->id }}">
                </td>
                <td>{{ $actividad->id }}</td>
                <td>{{ $actividad->nombre }}</td>
                <td>{{ $actividad->descripcion }}</td>
                <td>{{ $actividad->unidad->slug.'/'.$actividad->slug }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
