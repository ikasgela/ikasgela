<?php

namespace App\Livewire;

use App\Models\Criteria;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Attributes\Reactive;
use Livewire\Component;

class CriteriaComponent extends Component
{
    #[Reactive]
    public Criteria $criteria;

    /**
     * Señal de cambio de posición (ADR-013): hash de los `orden` de las filas
     * adyacentes (UUID, ADR-005) en la lista del grupo. Livewire 4 omite el
     * re-renderizado de un hijo cuyas props `#[Reactive]` no cambian; el modelo
     * `criteria` no lo detecta fiablemente (comparación por flags dirty/changed).
     * La firma cambia exactamente en las filas afectadas por mover (swap),
     * añadir (la anterior última) o borrar (los adyacentes), y fuerza el
     * re-renderizado para refrescar el estado de los botones. No se usa en el
     * Blade: solo sirve de señal de cambio.
     */
    #[Reactive]
    public $orden_vecinos;

    #[Reactive]
    public $rubric_is_editing = false;

    #[Reactive]
    public $rubric_is_qualifying = false;

    public function render()
    {
        return view('livewire.criteria');
    }

    #[On('hideModal')]
    public function refresh()
    {
    }

    #[Computed]
    public function is_first_in_group()
    {
        $criteria = Criteria::find($this->criteria->id);
        $orden = $criteria->criteria_group?->criterias()->min('orden');
        return $criteria->orden == $orden;
    }

    #[Computed]
    public function is_last_in_group()
    {
        $criteria = Criteria::find($this->criteria->id);
        $orden = $criteria->criteria_group?->criterias()->max('orden');
        return $criteria->orden == $orden;
    }

    #[Computed]
    public function is_rubric_completed()
    {
        $criteria = Criteria::find($this->criteria->id);
        return $criteria->criteria_group?->rubric?->completada;
    }
}
