<?php

namespace App\Livewire;

use App\Models\Actividad;
use App\Models\CriteriaGroup;
use App\Models\Rubric;
use App\Traits\OrdenUuid;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;

class RubricComponent extends Component
{
    use OrdenUuid;

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
            'orden' => Str::uuid7(),
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
        // Fila anterior en la serie `(orden, id)` (ADR-005): robusta frente a
        // `orden` empatados — `max(orden < X)` saltaría el empate y elegiría un
        // miembro arbitrario del bloque de empatados.
        $c2 = $this->rubric->criteria_groups()
            ->where(fn ($q) => $q->where('orden', '<', $c1->orden)
                ->orWhere(fn ($q) => $q->where('orden', $c1->orden)->where('id', '<', $c1->id)))
            ->reorder()
            ->orderByDesc('orden')
            ->orderByDesc('id')
            ->first();

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
        // Fila siguiente en la serie `(orden, id)` (ADR-005): el `orderBy('id')`
        // desempata los empates de `orden` (la relación ya ordena por `orden`).
        $c2 = $this->rubric->criteria_groups()
            ->where(fn ($q) => $q->where('orden', '>', $c1->orden)
                ->orWhere(fn ($q) => $q->where('orden', $c1->orden)->where('id', '>', $c1->id)))
            ->orderBy('id')
            ->first();

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

        // Toda la operación en una transacción: el clonador guarda la fila del
        // grupo y luego las criterias copia (con sus `orden` copiados) ANTES de
        // que esta función reasigne los `orden` únicos, así que sin transacción
        // el estado intermedio (grupo visible + criterias duplicadas) sería
        // observable por consumidores concurrentes de la base de datos.
        DB::transaction(function () use ($criteria_group) {
            $clon = $criteria_group->duplicate();
            // Intercalado ADR-005 (trait OrdenUuid): la copia queda justo después
            // del grupo duplicado; si no hay hueco, se regenera la serie completa.
            $clon->orden = $this->ordenTras(
                $criteria_group,
                fn() => $this->rubric->criteria_groups()->orderBy('orden')->get()->values()
            );
            $clon->save();

            foreach ($clon->criterias()->orderBy('orden')->get() as $criteria) {
                $criteria->orden = Str::uuid7();
                $criteria->save();
            }
        });
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
