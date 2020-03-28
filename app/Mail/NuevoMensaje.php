<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Request;

class NuevoMensaje extends Mailable
{
    use Queueable, SerializesModels;

    public $hostName;
    public $preview_mensaje;
    public $usuario;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($preview_mensaje, $usuario)
    {
        $this->hostName = Request::getHost();
        $this->preview_mensaje = $preview_mensaje;
        $this->usuario = $usuario;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this
            ->subject(__('New message'))
            ->markdown('emails.nuevo_mensaje');
    }
}
