<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Request;

class RepositorioClonado extends Mailable
{
    use Queueable, SerializesModels;

    public $usuario;
    public $hostName;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($usuario)
    {
        $this->usuario = $usuario;
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
            ->subject(__('Repository cloned'))
            ->markdown('emails.repositorio_clonado');
    }
}
