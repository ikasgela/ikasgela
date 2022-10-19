<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Request;

class RepositorioClonadoError extends Mailable
{
    use Queueable, SerializesModels;

    public $hostName;

    public function __construct()
    {
        $this->hostName = Request::getHost();
    }

    public function build()
    {
        return $this
            ->subject(__('Repository cloning error'))
            ->markdown('emails.repositorio_clonado_error');
    }
}
