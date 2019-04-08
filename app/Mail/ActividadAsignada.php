<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class ActividadAsignada extends Mailable
{
    use Queueable, SerializesModels;

    public $usuario;
    public $asignadas;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($usuario, $asignadas)
    {
        $this->usuario = $usuario;
        $this->asignadas = $asignadas;
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
