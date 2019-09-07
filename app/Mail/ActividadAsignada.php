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

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($usuario, $asignadas)
    {
        $this->usuario = $usuario;
        $this->asignadas = $asignadas;
        $this->hostName = Request::getHost();
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this
            ->subject(__('New activities assigned'))
            ->markdown('emails.actividad_asignada');
    }
}
