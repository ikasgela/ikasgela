<?php

namespace App\Livewire;

use App\Models\Milestone;
use App\Models\Qualification;
use Livewire\Component;

/**
 * Formulario de creación/edición de milestone.
 *
 * Con el ajuste proporcional 'fijo' se muestra el número de actividades
 * obligatorias (denominador fijo del ajuste) vía wire:model.
 */
class MilestoneForm extends Component
{
    public ?Milestone $milestone = null;

    public ?int $curso_id = null;

    public string $name = '';
    public ?string $date = null;
    public bool $published = false;
    public ?string $decimals = null;
    public bool $truncate = false;
    public bool $normalizar_nota = false;
    public ?string $ajuste_proporcional_nota = null;
    public string $etiquetas = 'base';
    public ?string $num_actividades_obligatorias = null;
    public ?string $qualification_id = null;

    public function mount(?Milestone $milestone = null, ?int $curso_id = null)
    {
        $this->milestone = $milestone;

        $this->curso_id = $curso_id ?? $milestone?->curso_id ?? setting_usuario('curso_actual');

        if ($milestone) {
            $this->name = $milestone->name;
            $this->date = $milestone->date?->format('Y-m-d\TH:i:s');
            $this->published = (bool) $milestone->published;
            $this->decimals = $milestone->decimals;
            $this->truncate = (bool) $milestone->truncate;
            $this->normalizar_nota = (bool) $milestone->normalizar_nota;
            $this->ajuste_proporcional_nota = $milestone->ajuste_proporcional_nota;
            $this->etiquetas = $milestone->etiquetas ?? 'base';
            $this->num_actividades_obligatorias = $milestone->num_actividades_obligatorias;
            $this->qualification_id = $milestone->qualification_id;
        }
    }

    public function save()
    {
        $this->validate([
            'curso_id' => 'required',
            'name' => 'required',
            'date' => 'required',
            'decimals' => 'nullable|integer',
            'etiquetas' => 'nullable|string|max:255',
            'ajuste_proporcional_nota' => 'nullable|in:media,mediana,fijo',
            'num_actividades_obligatorias' => 'required_if:ajuste_proporcional_nota,fijo|nullable|integer|min:1',
            'qualification_id' => 'nullable|integer|exists:qualifications,id',
        ]);

        $data = [
            'curso_id' => $this->curso_id,
            'name' => $this->name,
            'date' => $this->date,
            'published' => $this->published,
            'decimals' => $this->decimals !== '' ? $this->decimals : null,
            'truncate' => $this->truncate,
            'normalizar_nota' => $this->normalizar_nota,
            'ajuste_proporcional_nota' => in_array($this->ajuste_proporcional_nota, ['media', 'mediana', 'fijo']) ? $this->ajuste_proporcional_nota : null,
            'etiquetas' => $this->etiquetas !== '' ? $this->etiquetas : 'base',
            'num_actividades_obligatorias' => $this->ajuste_proporcional_nota === 'fijo' ? $this->num_actividades_obligatorias : null,
            'qualification_id' => $this->qualification_id !== '' ? $this->qualification_id : null,
        ];

        if ($this->milestone) {
            $this->milestone->update($data);
        } else {
            Milestone::create($data);
        }

        // Sin `navigate: true`: la navegación en cliente (Livewire) reemplaza
        // <head>/<body> sin recargar la página, lo que arranca el atributo
        // `data-bs-theme` de <html> y vuelve la página a modo claro.
        // Un redirect completo es coherente con el resto de la app (ADR-009).
        $this->redirect(route('milestones.index'));
    }

    public function render()
    {
        $qualifications = Qualification::where('curso_id', $this->curso_id)->orderBy('name')->get();

        return view('livewire.milestone-form', [
            'qualifications' => $qualifications,
        ]);
    }
}
