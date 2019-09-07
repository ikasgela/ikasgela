<?php

namespace App\Mail;

use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NuevoUsuario extends Mailable
{
    use Queueable, SerializesModels;

    public $usuario;
    public $hostName;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(User $usuario, $hostName)
    {
        $this->usuario = $usuario;
        $this->hostName = $hostName;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this
            ->subject(__('New user registered'))
            ->markdown('emails.nuevo_usuario');
    }
}
