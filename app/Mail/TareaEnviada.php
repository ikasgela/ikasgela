<?php

namespace App\Mail;

use App\Models\Tarea;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Request;

class TareaEnviada extends Mailable
{
    use Queueable, SerializesModels;

    public $tarea;
    public $hostName;

    public function __construct(Tarea $tarea)
    {
        $this->tarea = $tarea;
        $this->hostName = Request::getHost();
    }

    public function build()
    {
        return $this
            ->subject(__('New submission received'))
            ->markdown('emails.tarea_enviada');
    }
}
