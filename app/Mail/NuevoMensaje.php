<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Request;

class NuevoMensaje extends Mailable
{
    use Queueable, SerializesModels;

    public $hostName;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct()
    {
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
            ->subject(__('New message'))
            ->markdown('emails.nuevo_mensaje');
    }
}
