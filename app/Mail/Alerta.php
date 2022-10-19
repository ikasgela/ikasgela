<?php

namespace App\Mail;

use Cmgmyr\Messenger\Models\Message;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Str;

class Alerta extends Mailable
{
    use Queueable, SerializesModels;

    public $hostName;
    public $titulo;
    public $preview;
    public $usuario;

    public function __construct(Message $mensaje)
    {
        $this->hostName = Request::getHost();

        $this->titulo = $mensaje->thread->subject;

        $this->preview = Str::substr($mensaje->body, 0, config('ikasgela.message_preview_max_length'));

        if (Str::length($mensaje->body) > config('ikasgela.message_preview_max_length')) {
            $this->preview .= "\n\n...";
        }

        $this->usuario = $mensaje->user->name;
    }

    public function build()
    {
        return $this
            ->subject(__('Important notice'))
            ->markdown('emails.alerta');
    }
}
