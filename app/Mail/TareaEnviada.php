<?php

namespace App\Mail;

use App\Models\Tarea;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Request;

class TareaEnviada extends Mailable
{
    use Queueable, SerializesModels;

    public $tarea;
    public $hostName;
    public $locale;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Tarea $tarea, $locale = 'en')
    {
        $this->tarea = $tarea;
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
            ->subject(__('New submission received'))
            ->markdown('emails.tarea_enviada');
    }
}
