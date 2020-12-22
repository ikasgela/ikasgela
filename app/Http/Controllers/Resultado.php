<?php

namespace App\Http\Controllers;

class Resultado
{
    public $tarea = 0;
    public $actividad = 0;

    public $puntos_tarea = 0;
    public $puntos_totales_tarea = 0;
    public $num_tareas = 0;

    public $puntos_examen = 0;
    public $puntos_totales_examen = 0;
    public $num_examenes = 0;

    public $porcentaje = 0;

    public $peso_examen = 0;

    public function porcentaje_tarea()
    {
        return $this->puntos_totales_tarea > 0 ? $this->puntos_tarea / $this->puntos_totales_tarea * 100 : 0;
    }

    public function porcentaje_examen()
    {
        return $this->puntos_totales_examen > 0 ? $this->puntos_examen / $this->puntos_totales_examen * 100 : 0;
    }

    public function porcentaje_competencia()
    {
        return $this->porcentaje_tarea() * (100 - $this->peso_examen) / 100
            + $this->porcentaje_examen() * $this->peso_examen / 100;
    }
}
