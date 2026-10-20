@php($curso_del_profesor = Auth::user()->curso_actual())
@php($num_env = $user->num_actividades_enviadas_noautoavance_noexamen($curso_del_profesor))
@php($num_env_examen = $user->num_actividades_enviadas_noautoavance_examen($curso_del_profesor))

@if($num_env > 0)
    <span class="badge text-bg-danger ms-2">{{ $num_env }}</span>
@endif

@if($num_env_examen > 0)
    <span class="badge text-bg-info ms-2">{{ $num_env_examen }}</span>
@endif
