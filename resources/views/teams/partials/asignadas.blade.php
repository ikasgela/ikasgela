@include('partials.subtitulo', ['subtitulo' => __('Assigned activities')])

@if($disponibles->count() > 0)
    @include('teams.partials.tabla_asignadas')
@else
    <div class="row">
        <div class="col-md">
            <p>No hay ninguna actividad asignada.</p>
        </div>
    </div>
@endif
