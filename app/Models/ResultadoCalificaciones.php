<?php

namespace App\Models;

class ResultadoCalificaciones
{
    public $skills_curso = null;
    public $resultados = null;
    public $resultados_unidades = null;

    public $nota_numerica = null;

    public function normalizar_nota($rango, $nota)
    {
        if (!is_null($rango) && $rango['max'] > 0) {
            $nota = ($nota - $rango['min']) / ($rango['max'] - $rango['min']) * 10;
        }
        return $nota;
    }

    public function nota_numerica_normalizada($rango = null)
    {
        return $this->normalizar_nota($rango, $this->nota_numerica);
    }

    public function nota_final($rango = null)
    {
        $nota = $this->normalizar_nota($rango, $this->nota_numerica);

        return formato_decimales($nota, 2);
    }

    public function nota_publicar($milestone = null, $rango = null)
    {
        $nota = $this->normalizar_nota($rango, $this->nota_numerica);

        if (!$milestone?->truncate) {
            return formato_decimales(min($nota, 10), $milestone->decimals ?? 2);
        } else {
            return truncar_decimales(min($nota, 10), $milestone->decimals ?? 2);
        }
    }

    public $actividades_obligatorias_superadas = null;
    public $num_actividades_obligatorias = null;
    public $numero_actividades_completadas = null;
    public $pruebas_evaluacion = null;
    public $num_pruebas_evaluacion = null;
    public $competencias_50_porciento = null;
    public $minimo_competencias = null;
    public $minimo_examenes = null;
    public $minimo_examenes_finales = null;
    public $evaluacion_continua_superada = null;
    public $hayExamenes = null;
    public $examen_final = null;
    public $examen_final_superado = null;

    public $hay_nota_manual = false;
    public $nota_manual_superada = false;
}
