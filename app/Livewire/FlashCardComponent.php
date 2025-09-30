<?php

namespace App\Livewire;

use App\Models\Criteria;
use App\Models\FlashCard;
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Attributes\Reactive;
use Livewire\Component;

class FlashCardComponent extends Component
{
    public FlashCard $flash_card;

    #[Reactive]
    public $rubric_is_editing = false;

    #[Reactive]
    public $rubric_is_qualifying = false;

    public $is_editing = false;

    public $titulo;
    public $descripcion;

    public function mount()
    {
        $this->titulo = $this->flash_card->titulo;
        $this->descripcion = $this->flash_card->descripcion;
    }

    public function render()
    {
        return view('livewire.flash-card-component');
    }

    #[Computed]
    public function is_first_flash_card($flash_card_id)
    {
        $flash_card = FlashCard::findOrFail($flash_card_id);
        $orden = $flash_card->rubric->flash_cards()->min('orden');
        return $flash_card->orden == $orden;
    }

    #[Computed]
    public function is_last_flash_card($flash_card_id)
    {
        $flash_card = FlashCard::findOrFail($flash_card_id);
        $orden = $flash_card->rubric->flash_cards()->max('orden');
        return $flash_card->orden == $orden;
    }

    public function seleccionar($criteria_id)
    {
        $criteria = Criteria::findOrFail($criteria_id);
        $criteria->seleccionado = !$criteria->seleccionado;
        $criteria->save();

        $flash_card = $criteria->flash_card;
        foreach ($flash_card->criterias as $other_criteria) {
            if ($other_criteria->id != $criteria->id) {
                $other_criteria->seleccionado = false;
                $other_criteria->save();
            }
        }

        $flash_card->rubric->completada = true;
        $flash_card->rubric->save();

        $this->dispatch('hideModal');
        $this->dispatch('$parent.$refresh');
    }

    public function add_criteria($flash_card_id)
    {
        $flash_card = FlashCard::findOrFail($flash_card_id);

        Criteria::create([
            'texto' => __('Write your criteria here'),
            'puntuacion' => 0,
            'orden' => Str::orderedUuid(),
            'flash_card_id' => $flash_card->id,
        ]);
    }

    public function delete_criteria($criteria_id)
    {
        $criteria = Criteria::findOrFail($criteria_id);
        $criteria->delete();
    }

    public function left_criteria($criteria_id)
    {
        $c1 = Criteria::findOrFail($criteria_id);
        $orden = $c1->flash_card->criterias()->where('orden', '<', $c1->orden)->max('orden');
        $c2 = Criteria::where('orden', $orden)->first();

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
        $c2 = $c1->flash_card->criterias()->where('orden', '>', $c1->orden)->first();

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

    public function save()
    {
        $this->is_editing = false;
        $this->flash_card->titulo = $this->titulo;
        $this->flash_card->descripcion = $this->descripcion;
        $this->flash_card->save();
    }

    #[Computed]
    public function total()
    {
        return $this->flash_card->criterias()->where('seleccionado', true)->sum('puntuacion');
    }

    #[Computed]
    public function max_total()
    {
        return $this->flash_card->criterias()->max('puntuacion') ?: 0;
    }

    #[On('hideModal')]
    public function refresh()
    {
    }

    #[Computed]
    public function is_rubric_completed()
    {
        $flash_card = FlashCard::find($this->flash_card->id);
        return $flash_card?->rubric?->completada;
    }
}
