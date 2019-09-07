<?php

namespace App\Mail;

use App\Tarea;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Request;

class FeedbackRecibido extends Mailable
{
    use Queueable, SerializesModels;

    public $tarea;
    public $hostName;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Tarea $tarea)
    {
        $this->tarea = $tarea;
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
            ->subject(__('Review completed'))
            ->markdown('emails.feedback_recibido');
    }
}
