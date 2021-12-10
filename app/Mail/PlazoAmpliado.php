<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Request;

class PlazoAmpliado extends Mailable
{
    use Queueable, SerializesModels;

    public $usuario;
    public $actividad;
    public $hostName;
    public $locale;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($usuario, $actividad, $locale = 'en')
    {
        $this->usuario = $usuario;
        $this->actividad = $actividad;
        $this->hostName = Request::getHost();
        $this->locale = $locale;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        App::setLocale($this->locale);

        return $this
            ->subject(__('Deadline extended'))
            ->markdown('emails.plazo_ampliado');
    }
}
