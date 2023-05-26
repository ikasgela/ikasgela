<?php

namespace App\Models;

use Bkwld\Cloner\Cloneable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Log;

/**
 * @mixin IdeHelperSelector
 */
class Selector extends Model
{
    use HasFactory;
    use Cloneable;

    protected $cloneable_relations = ['rule_groups'];

    protected $fillable = [
        'titulo', 'descripcion', 'curso_id',
    ];

    public function actividades()
    {
        return $this
            ->belongsToMany(Actividad::class)
            ->withTimestamps();
    }

    public function curso()
    {
        return $this->belongsTo(Curso::class);
    }

    public function rule_groups()
    {
        return $this->hasMany(RuleGroup::class);
    }

    public function pivote(Actividad $actividad)
    {
        return $actividad->selectors()->find($this->id)->pivot;
    }

    public function calcularResultado(Actividad $actividad, Tarea $tarea)
    {
        $resultado = null;

        foreach ($this->rule_groups()->get() as $rule_group) {

            switch (Str::lower($rule_group->operador)) {
                case 'and':
                    $resultado_grupo = true;
                    break;
                case 'or':
                    $resultado_grupo = false;
                    break;
                default:
                    abort(400, __('Invalid rule'));
            }

            Log::debug('Selector', ['operador' => $rule_group->operador]);

            foreach ($rule_group->rules()->get() as $rule) {

                switch (Str::lower($rule->propiedad)) {
                    case 'puntuacion':
                        $propiedad = 'puntuacion';
                        break;
                    case 'intentos':
                        $propiedad = 'intentos';
                        break;
                    default:
                        abort(400, __('Invalid rule'));
                }

                switch ($rule->operador) {
                    case '>':
                        $resultado_regla_actual = $tarea->$propiedad > $rule->valor;
                        break;
                    case '<':
                        $resultado_regla_actual = $tarea->$propiedad < $rule->valor;
                        break;
                    case '>=':
                        $resultado_regla_actual = $tarea->$propiedad >= $rule->valor;
                        break;
                    case '<=':
                        $resultado_regla_actual = $tarea->$propiedad <= $rule->valor;
                        break;
                    case '==':
                        $resultado_regla_actual = $tarea->$propiedad == $rule->valor;
                        break;
                    case '!=':
                        $resultado_regla_actual = $tarea->$propiedad != $rule->valor;
                        break;
                    default:
                        abort(400, __('Invalid rule'));
                }

                Log::debug('Selector', ['regla' => $rule->propiedad . $rule->operador . $rule->valor, 'resultado' => $resultado_regla_actual]);

                switch (Str::lower($rule_group->operador)) {
                    case 'and':
                        $resultado_grupo = $resultado_grupo && $resultado_regla_actual;
                        break;
                    case 'or':
                        $resultado_grupo = $resultado_grupo || $resultado_regla_actual;
                        break;
                    default:
                        abort(400, __('Invalid rule'));
                }
            }

            if ($resultado_grupo) {
                $resultado = $rule_group->resultado;
            }
        }

        return $resultado;
    }
}
