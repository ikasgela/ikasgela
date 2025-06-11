<?php

namespace App\Livewire;

use App\Jobs\ExportarUsuarioJob;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class ExportarUsuario extends Component
{
    public bool $exporting = false;

    public function render()
    {
        return view('livewire.exportar-usuario');
    }

    public function export()
    {
        $this->exporting = false;

        ExportarUsuarioJob::dispatch(Auth::user());
    }
}
