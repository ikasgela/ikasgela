<?php

namespace App\Livewire;

use App\Models\Actividad;
use App\Models\CriteriaGroup;
use App\Models\Rubric;
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;

class RubricComponent extends Component
{
    public ?Actividad $actividad;
    public Rubric $rubric;
    public $rubric_is_editing = false;
    public $rubric_is_qualifying = false;

    public $is_editing_cabecera = false;
    public $titulo;
    public $descripcion;

    public $titulo_visible;
    public $descripcion_visible;
    public $excluir_no_seleccionadas;

    public function mount(Rubric $rubric)
    {
        $this->rubric = $rubric;
        $this->titulo = $rubric->titulo;
        $this->titulo_visible = $rubric->titulo_visible;
        $this->descripcion = $rubric->descripcion;
        $this->descripcion_visible = $rubric->descripcion_visible;
        $this->excluir_no_seleccionadas = $rubric->excluir_no_seleccionadas;
    }

    public function add_criteria_group()
    {
        CriteriaGroup::create([
            'titulo' => '',
            'descripcion' => '',
            'orden' => Str::orderedUuid(),
            'rubric_id' => $this->rubric->id,
        ]);
    }

    public function delete_criteria_group($criteria_group_id)
    {
        $criteria_group = CriteriaGroup::findOrFail($criteria_group_id);
        $criteria_group->delete();
    }

    public function up_criteria_group($criteria_group_id)
    {
        $c1 = CriteriaGroup::findOrFail($criteria_group_id);
        $orden = $this->rubric->criteria_groups()->where('orden', '<', $c1->orden)->max('orden');
        $c2 = $this->rubric->criteria_groups()->where('orden', $orden)->first();

        if ($c2 != null) {
            $temp = $c1->orden;
            $c1->orden = $c2->orden;
            $c2->orden = $temp;

            $c1->save();
            $c2->save();
        }
    }

    public function down_criteria_group($criteria_group_id)
    {
        $c1 = CriteriaGroup::findOrFail($criteria_group_id);
        $c2 = $this->rubric->criteria_groups()->where('orden', '>', $c1->orden)->first();

        if ($c2 != null) {
            $temp = $c1->orden;
            $c1->orden = $c2->orden;
            $c2->orden = $temp;

            $c1->save();
            $c2->save();
        }
    }

    public function duplicate_criteria_group($criteria_group_id)
    {
        $criteria_group = CriteriaGroup::findOrFail($criteria_group_id);

        $clon = $criteria_group->duplicate();
        $clon->orden = $this->ordenTrasCriteriaGroup($criteria_group);
        $clon->save();

        foreach ($clon->criterias()->orderBy('orden')->get() as $criteria) {
            $criteria->orden = Str::orderedUuid();
            $criteria->save();
        }
    }

    /**
     * Calcula un `orden` (UUID ordenable, ADR-005) que ordena justo después de
     * $criteria_group dentro de la rúbrica — es decir, entre ese grupo y su
     * siguiente vecino — para que la copia duplicada aparezca como su fila
     * siguiente. Si la serie no tiene hueco para intercalar, se regenera la
     * serie completa de `orden` de los grupos.
     */
    private function ordenTrasCriteriaGroup(CriteriaGroup $criteria_group): string
    {
        if (empty($criteria_group->orden)) {
            return Str::orderedUuid();
        }

        $siguiente = $this->rubric->criteria_groups()
            ->where('orden', '>', $criteria_group->orden)
            ->orderBy('orden')
            ->first();

        $ts_origen = self::tsOrden($criteria_group->orden);
        $ts_clon = $ts_origen + 1;
        $cola = substr($criteria_group->orden, 14); // versión/variante/azar (grupos 3-5)

        if ($siguiente !== null && !empty($siguiente->orden)) {
            $hueco = self::tsOrden($siguiente->orden) - $ts_origen;

            if ($hueco < 2) {
                return $this->reordenarSerieTras($criteria_group, $cola);
            }

            $ts_clon = $ts_origen + ($hueco >> 1);
        }

        return self::ordenConTs($ts_clon, $cola);
    }

    /**
     * No hay hueco entre el grupo y su vecino (tras muchas inserciones en el
     * mismo punto, ADR-005): regenera la serie completa de `orden` de los grupos
     * de la rúbrica, con la copia interpolada justo después de $original, y
     * devuelve el `orden` correspondiente a la copia.
     */
    private function reordenarSerieTras(CriteriaGroup $original, string $cola): string
    {
        $ts_actual = self::tsOrdenAhora();
        $ts_max = $this->rubric->criteria_groups()
            ->get()
            ->filter(fn ($g) => !empty($g->orden))
            ->map(fn ($g) => self::tsOrden($g->orden))
            ->max();

        $base = max($ts_actual, $ts_max ?? 0) + 1;

        $grupos = $this->rubric->criteria_groups()->orderBy('orden')->get()->values();
        $orden_clon = null;

        foreach ($grupos as $grupo) {
            $grupo->orden = self::ordenConTs($base++, $cola);
            $grupo->save();

            if ($grupo->id === $original->id && $orden_clon === null) {
                $orden_clon = self::ordenConTs($base++, $cola);
            }
        }

        return $orden_clon ?? self::ordenConTs($base, $cola);
    }

    /**
     * Extrae los 48 bits de timestamp (primera parte del UUID) de un `orden`
     * generado con `Str::orderedUuid()` (COMB «timestamp first»: 12 hex =
     * segundos*1e5 + µs/10, seguido de azar).
     */
    private static function tsOrden(string $orden): int
    {
        return hexdec(substr($orden, 0, 8)) * 0x10000 + hexdec(substr($orden, 9, 4));
    }

    /**
     * Timestamp actual con la misma resolución que `Str::orderedUuid()`
     * (100 µs): segundos*1e5 + µs/10.
     */
    private static function tsOrdenAhora(): int
    {
        $tiempo = explode(' ', microtime(false));

        return (int) $tiempo[1] * 100000 + (int) substr($tiempo[0], 2, 5);
    }

    /**
     * Construye un `orden` con el timestamp dado y el resto (versión/variante/
     * azar) tomado de $cola (el mismo formato que `Str::orderedUuid()`).
     */
    private static function ordenConTs(int $ts, string $cola = '4000-8000-000000000000'): string
    {
        return sprintf('%08x-%04x-%s', $ts >> 16, $ts & 0xffff, $cola);
    }

    public function toggle_edit()
    {
        $this->rubric_is_editing = !$this->rubric_is_editing;
    }

    public function render()
    {
        return view('livewire.rubric-show');
    }

    public function toggle_edit_cabecera()
    {
        $this->is_editing_cabecera = !$this->is_editing_cabecera;
    }

    public function toggle_titulo_visible()
    {
        $this->titulo_visible = !$this->titulo_visible;
        $this->rubric->titulo_visible = $this->titulo_visible;
        $this->rubric->save();
    }

    public function toggle_descripcion_visible()
    {
        $this->descripcion_visible = !$this->descripcion_visible;
        $this->rubric->descripcion_visible = $this->descripcion_visible;
        $this->rubric->save();
    }

    public function toggle_excluir_no_seleccionadas()
    {
        $this->excluir_no_seleccionadas = !$this->excluir_no_seleccionadas;
        $this->rubric->excluir_no_seleccionadas = $this->excluir_no_seleccionadas;
        $this->rubric->save();
    }

    public function save()
    {
        $this->is_editing_cabecera = false;
        $this->rubric->titulo = $this->titulo;
        $this->rubric->descripcion = $this->descripcion;
        $this->rubric->save();
    }

    #[Computed]
    public function total()
    {
        $total = 0;
        foreach ($this->rubric->criteria_groups as $criteria_group) {
            $total += $criteria_group->criterias()->where('seleccionado', true)->sum('puntuacion');
        }
        return $total;
    }

    #[Computed]
    public function max_total()
    {
        $total = 0;
        foreach ($this->rubric->criteria_groups as $criteria_group) {
            if (!$this->rubric->excluir_no_seleccionadas) {
                $total += $criteria_group->criterias()->max('puntuacion');
            } else {
                $hay_seleccionados = $criteria_group->criterias()->where('seleccionado', true)->count() > 0;
                if ($hay_seleccionados) {
                    $total += $criteria_group->criterias()->max('puntuacion');
                }
            }
        }
        return $total;
    }

    #[On('hideModal')]
    public function refresh()
    {
    }
}
