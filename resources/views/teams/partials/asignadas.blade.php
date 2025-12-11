@include('partials.subtitulo', ['subtitulo' => __('Assigned activities')])

@include('teams.partials.selector_unidad',['nombre_variable' => 'unidad_id_asignadas'])

@if($asignadas->count() > 0)
    @include('teams.partials.tabla_asignadas')
@else
    <div class="row">
        <div class="col-md">
            <p>No hay ninguna actividad asignada.</p>
        </div>
    </div>
@endif
