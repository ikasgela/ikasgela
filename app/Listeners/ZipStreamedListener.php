<?php

namespace App\Listeners;

use Illuminate\Support\Facades\Storage;
use STS\ZipStream\Events\ZipStreamed;

class ZipStreamedListener
{
    public function handle(ZipStreamed $event)
    {
        // Borrar el directorio temporal
        Storage::disk('temp')->deleteDirectory(session('_delete_me'));
        session()->forget('_delete_me');
    }
}
