<?php

namespace App\Traits;

trait CalcularFechaEntregaActividad
{
    public function calcularFechaEntrega($actividad): void
    {
        $ahora = now();

        if (!isset($actividad->fecha_disponibilidad)) {
            $actividad->fecha_disponibilidad = $ahora;
        }

        if (!isset($actividad->fecha_entrega)) {
            $plazo_actividad_curso = $actividad->unidad->curso->plazo_actividad;

            if ($plazo_actividad_curso > 0) {
                $plazo = $ahora->addDays($plazo_actividad_curso);
                $actividad->fecha_entrega = $plazo;
                $actividad->fecha_limite = $plazo;
            }
        }
    }
}
