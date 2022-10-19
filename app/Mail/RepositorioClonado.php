<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Request;

class RepositorioClonado extends Mailable
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
            ->subject(__('Repository cloned'))
            ->markdown('emails.repositorio_clonado');
    }
}
