<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Request;

class NuevoUsuario extends Mailable
{
    use Queueable, SerializesModels;

    public $usuario;
    public $hostName;

    public function __construct(User $usuario)
    {
        $this->usuario = $usuario;
        $this->hostName = Request::getHost();
    }

    public function build()
    {
        return $this
            ->subject(__('New user registered'))
            ->markdown('emails.nuevo_usuario');
    }
}
