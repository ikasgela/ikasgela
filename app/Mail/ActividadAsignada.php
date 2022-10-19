<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Request;

class ActividadAsignada extends Mailable
{
    use Queueable, SerializesModels;

    public $usuario;
    public $asignadas;
    public $hostName;

    public function __construct($usuario, $asignadas)
    {
        $this->usuario = $usuario;
        $this->asignadas = $asignadas;
        $this->hostName = Request::getHost();
    }

    public function build()
    {
        return $this
            ->subject(__('New activities assigned'))
            ->markdown('emails.actividad_asignada');
    }
}
