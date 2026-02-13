<td class="text-center clickable">
    <div class="d-flex justify-content-center align-items-center">
        {!! $actividad->is_expired ? (!$actividad->tarea->is_completada ? '' : ($actividad->tarea->is_completada_archivada ? '<i class="bi bi-exclamation-triangle-fill text-secondary"></i>' : '<i class="bi bi-x text-secondary"></i>')) : '<i class="bi bi-x text-secondary"></i>' !!}
        @if($actividad->is_expired && !$actividad->tarea->is_completada)
            {{ html()->form('PUT', route('actividades.estado', $actividad->tarea->id))->open() }}
            <div class='btn-group'>
                <button type="submit" name="nuevoestado" value="63"
                        title="{{ __('Extend deadline') }}"
                        class="btn btn-sm text-bg-warning">
                    +{{ $actividad->unidad->curso->plazo_actividad ?? 7 }}</button>
            </div>
            <input type="hidden" name="ampliacion_plazo"
                   value="{{ $actividad->unidad->curso->plazo_actividad ?? 7 }}"/>
            {{ html()->form()->close() }}
        @endif
    </div>
</td>
