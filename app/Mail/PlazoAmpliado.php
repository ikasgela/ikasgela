<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Request;

class PlazoAmpliado extends Mailable
{
    use Queueable, SerializesModels;

    public $hostName;

    public function __construct(public $usuario, public $actividad)
    {
        $this->hostName = Request::getHost();
    }

    public function build()
    {
        return $this
            ->subject(__('Deadline extended'))
            ->markdown('emails.plazo_ampliado');
    }
}
