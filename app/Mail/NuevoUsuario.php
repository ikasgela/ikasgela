<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Request;

class NuevoUsuario extends Mailable
{
    use Queueable, SerializesModels;

    public $usuario;
    public $hostName;
    public $locale;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(User $usuario, $locale = 'en')
    {
        $this->usuario = $usuario;
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
            ->subject(__('New user registered'))
            ->markdown('emails.nuevo_usuario');
    }
}
