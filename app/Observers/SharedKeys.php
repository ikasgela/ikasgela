<?php

namespace App\Observers;

trait SharedKeys
{
    protected $keys = [
        'num_actividades_nuevas_',
        'num_actividades_ocultas_',
        'num_actividades_aceptadas_',
        'num_actividades_caducadas_',
        'num_actividades_en_curso_',
        'num_actividades_en_curso_autoavance_',
        'num_actividades_enviadas_noautoavance_',
        'num_actividades_enviadas_noautoavance_noexamen_',
        'num_actividades_revisadas_',
        'num_actividades_archivadas_',
        'num_actividades_completadas_',
        'num_actividades_sin_completar_',
        'num_actividades_asignadas_total_',
        'siguiente_actividad_',

        'num_actividades_en_curso_examen_',
        'num_actividades_en_curso_seb_',
        'num_actividades_enviadas_seb_',
        'num_actividades_en_curso_no_extra_examen_',
        'num_actividades_en_curso_extra_',
        'num_actividades_en_curso_enviadas_',

        'calificaciones_',
    ];
}
