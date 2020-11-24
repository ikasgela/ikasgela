<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Request;

class PlazoAmpliado extends Mailable
{
    use Queueable, SerializesModels;

    public $usuario;
    public $actividad;
    public $hostName;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($usuario, $actividad)
    {
        $this->usuario = $usuario;
        $this->actividad = $actividad;
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
            ->subject(__('Deadline extended'))
            ->markdown('emails.plazo_ampliado');
    }
}
