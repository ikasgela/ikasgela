<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Request;

class ActividadAsignada extends Mailable
{
    use Queueable, SerializesModels;

    public $usuario;
    public $asignadas;
    public $hostName;
    public $locale;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($usuario, $asignadas, $locale = 'en')
    {
        $this->usuario = $usuario;
        $this->asignadas = $asignadas;
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
            ->subject(__('New activities assigned'))
            ->markdown('emails.actividad_asignada');
    }
}
