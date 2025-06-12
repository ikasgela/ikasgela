<?php

namespace App\Livewire;

use App\Jobs\ExportarUsuarioJob;
use App\Models\UserExport;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;

class ExportarUsuario extends Component
{
    public bool $exporting = false;
    public bool $already_exported = false;
    public string $url;

    public function mount()
    {
        $usuario = Auth::user();

        if (!is_null($usuario->user_export)) {

            if ($usuario->user_export->fichero == null) {
                $this->exporting = true;
            } else if ($usuario->user_export->fecha?->greaterThan(now())) {
                $this->already_exported = true;
                $this->url = $usuario->user_export->url;
            } else {
                Storage::disk('s3')->delete($usuario->user_export->fichero);
                $usuario->user_export->delete();
            }
        }
    }

    public function render()
    {
        return view('livewire.exportar-usuario');
    }

    public function export()
    {
        if (!$this->exporting) {
            $this->exporting = true;

            // Registrar el comienzo de la exportación
            UserExport::updateOrCreate(['user_id' => Auth::user()->id], [
                'fecha' => now(),
            ]);

            ExportarUsuarioJob::dispatch(Auth::user());
        }
    }
}
