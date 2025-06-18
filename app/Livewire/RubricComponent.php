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

    public function mount(Rubric $rubric)
    {
        $this->rubric = $rubric;
        $this->titulo = $rubric->titulo;
        $this->descripcion = $rubric->descripcion;
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
        $c2 = CriteriaGroup::where('orden', $orden)->first();

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
            $total += $criteria_group->criterias()->max('puntuacion');
        }
        return $total;
    }

    #[On('hideModal')]
    public function refresh()
    {
    }
}
