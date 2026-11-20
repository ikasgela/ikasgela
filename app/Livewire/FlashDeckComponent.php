<?php

namespace App\Livewire;

use App\Models\Actividad;
use App\Models\FlashCard;
use App\Models\FlashDeck;
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;

class FlashDeckComponent extends Component
{
    public ?Actividad $actividad;
    public FlashDeck $flash_deck;
    public $flash_deck_is_editing = false;
    public $flash_deck_is_qualifying = false;

    public $is_editing_cabecera = false;
    public $titulo;
    public $descripcion;

    public function mount(FlashDeck $flash_deck)
    {
        $this->flash_deck = $flash_deck;
        $this->titulo = $flash_deck->titulo;
        $this->descripcion = $flash_deck->descripcion;
    }

    public function add_flash_card()
    {
        FlashCard::create([
            'titulo' => '',
            'descripcion' => '',
            'anverso' => '',
            'reverso' => '',
            'orden' => Str::uuid7(),
            'flash_deck_id' => $this->flash_deck->id,
        ]);
    }

    public function delete_flash_card($flash_card_id)
    {
        $flash_card = FlashCard::findOrFail($flash_card_id);
        $flash_card->delete();
    }

    public function up_flash_card($flash_card_id)
    {
        $c1 = FlashCard::findOrFail($flash_card_id);
        // Tarjeta anterior en la serie `(orden, id)` del mazo (ADR-005): robusta
        // frente a `orden` empatados y acotada al mazo (antes consultaba la tabla
        // completa y podía intercambiar con una tarjeta de otro mazo).
        $c2 = $this->flash_deck->flash_cards()
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

    public function down_flash_card($flash_card_id)
    {
        $c1 = FlashCard::findOrFail($flash_card_id);
        // Tarjeta siguiente en la serie `(orden, id)` del mazo (ADR-005): el
        // `orderBy('id')` desempata los empates de `orden` (la relación ya ordena
        // por `orden`).
        $c2 = $this->flash_deck->flash_cards()
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

    public function duplicate_flash_card($flash_card_id)
    {
        $flash_card = FlashCard::findOrFail($flash_card_id);

        $clon = $flash_card->duplicate();
        $clon->orden = Str::uuid7();
        $clon->save();
    }

    public function toggle_edit()
    {
        $this->flash_deck_is_editing = !$this->flash_deck_is_editing;
    }

    public function render()
    {
        return view('livewire.flash-deck-show');
    }

    public function toggle_edit_cabecera()
    {
        $this->is_editing_cabecera = !$this->is_editing_cabecera;
    }

    public function save()
    {
        $this->is_editing_cabecera = false;
        $this->flash_deck->titulo = $this->titulo;
        $this->flash_deck->descripcion = $this->descripcion;
        $this->flash_deck->save();
    }

    #[Computed]
    public function total()
    {
        $total = 0;
        return $total;
    }

    #[Computed]
    public function max_total()
    {
        $total = 0;
        return $total;
    }

    #[On('hideModal')]
    public function refresh()
    {
    }
}
