<?php

namespace App\Livewire;

use App\Models\Criteria;
use App\Models\CriteriaGroup;
use App\Traits\OrdenUuid;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Attributes\Reactive;
use Livewire\Component;

class CriteriaGroupComponent extends Component
{
    use OrdenUuid;

    public CriteriaGroup $criteria_group;

    /**
     * Señal de cambio de posición (ADR-013): hash de los `orden` de los grupos
     * adyacentes (UUID, ADR-005) en la rúbrica. Livewire 4 omite el re-renderizado
     * de un hijo cuyas props `#[Reactive]` no cambian; el modelo `criteria_group`
     * no lo detecta fiablemente (comparación por flags dirty/changed). La firma
     * cambia exactamente en los grupos afectados por mover (swap), añadir (el
     * anterior último) o borrar (los adyacentes), y fuerza el re-renderizado para
     * refrescar el estado de los botones. No se usa en el Blade: solo sirve de
     * señal de cambio.
     */
    #[Reactive]
    public $orden_vecinos;

    #[Reactive]
    public $rubric_is_editing = false;

    #[Reactive]
    public $rubric_is_qualifying = false;

    public $is_editing = false;

    public $titulo;
    public $descripcion;
    public $cabecera_horizontal;

    public function mount()
    {
        $this->titulo = $this->criteria_group->titulo;
        $this->descripcion = $this->criteria_group->descripcion;
        $this->cabecera_horizontal = $this->criteria_group->cabecera_horizontal;
    }

    public function render()
    {
        return view('livewire.criteria-group-component');
    }

    #[Computed]
    public function is_first_criteria_group($criteria_group_id)
    {
        $criteria_group = CriteriaGroup::findOrFail($criteria_group_id);
        $orden = $criteria_group->rubric->criteria_groups()->min('orden');
        return $criteria_group->orden == $orden;
    }

    #[Computed]
    public function is_last_criteria_group($criteria_group_id)
    {
        $criteria_group = CriteriaGroup::findOrFail($criteria_group_id);
        $orden = $criteria_group->rubric->criteria_groups()->max('orden');
        return $criteria_group->orden == $orden;
    }

    public function seleccionar($criteria_id)
    {
        $criteria = Criteria::findOrFail($criteria_id);
        $criteria->seleccionado = !$criteria->seleccionado;
        $criteria->save();

        $criteria_group = $criteria->criteria_group;
        foreach ($criteria_group->criterias as $other_criteria) {
            if ($other_criteria->id != $criteria->id) {
                $other_criteria->seleccionado = false;
                $other_criteria->save();
            }
        }

        $criteria_group->rubric->completada = true;
        $criteria_group->rubric->save();

        $this->dispatch('hideModal');
        $this->dispatch('$parent.$refresh');
    }

    public function add_criteria($criteria_group_id)
    {
        $criteria_group = CriteriaGroup::findOrFail($criteria_group_id);

        Criteria::create([
            'texto' => __('Write your criteria here'),
            'puntuacion' => 0,
            'orden' => Str::orderedUuid(),
            'criteria_group_id' => $criteria_group->id,
        ]);
    }

    public function delete_criteria($criteria_id)
    {
        $criteria = Criteria::findOrFail($criteria_id);
        $criteria->delete();
    }

    /**
     * Duplica un criterio: la copia (mismo `texto`/`puntuacion`; `seleccionado`
     * excluido, ADR-008) queda **justo a la derecha** del original — su `orden`
     * UUID se intercala entre el criterio y su siguiente vecino dentro del grupo
     * (ADR-005, trait OrdenUuid); si no hay hueco, se regenera la serie del grupo.
     * El `orden` se calcula antes de guardar la copia, de modo que la serie
     * consultada no la incluye.
     */
    public function duplicate_criteria($criteria_id)
    {
        $criteria = Criteria::findOrFail($criteria_id);
        $grupo = $criteria->criteria_group;

        $orden = $this->ordenTras($criteria, function () use ($grupo) {
            return $grupo?->criterias()->orderBy('orden')->get()->values() ?? new Collection();
        });

        $copia = $criteria->duplicate();
        $copia->orden = $orden;
        $copia->save();
    }

    public function left_criteria($criteria_id)
    {
        $c1 = Criteria::findOrFail($criteria_id);
        $orden = $c1->criteria_group->criterias()->where('orden', '<', $c1->orden)->max('orden');
        $c2 = $c1->criteria_group->criterias()->where('orden', $orden)->first();

        if ($c2 != null) {
            $temp = $c1->orden;
            $c1->orden = $c2->orden;
            $c2->orden = $temp;

            $c1->save();
            $c2->save();
        }
    }

    public function right_criteria($criteria_id)
    {
        $c1 = Criteria::findOrFail($criteria_id);
        $c2 = $c1->criteria_group->criterias()->where('orden', '>', $c1->orden)->first();

        if ($c2 != null) {
            $temp = $c1->orden;
            $c1->orden = $c2->orden;
            $c2->orden = $temp;

            $c1->save();
            $c2->save();
        }
    }

    public function toggle_edit()
    {
        $this->is_editing = !$this->is_editing;
    }

    public function toggle_cabecera_horizontal()
    {
        $this->cabecera_horizontal = !$this->cabecera_horizontal;
        $this->criteria_group->cabecera_horizontal = $this->cabecera_horizontal;
        $this->criteria_group->save();
    }

    public function save()
    {
        $this->is_editing = false;
        $this->criteria_group->titulo = $this->titulo;
        $this->criteria_group->descripcion = $this->descripcion;
        $this->criteria_group->save();
    }

    #[Computed]
    public function total()
    {
        return $this->criteria_group->criterias()->where('seleccionado', true)->sum('puntuacion');
    }

    #[Computed]
    public function max_total()
    {
        return $this->criteria_group->criterias()->max('puntuacion') ?: 0;
    }

    #[On('hideModal')]
    public function refresh()
    {
    }

    #[Computed]
    public function is_rubric_completed()
    {
        $criteria_group = CriteriaGroup::find($this->criteria_group->id);
        return $criteria_group?->rubric?->completada;
    }
}
