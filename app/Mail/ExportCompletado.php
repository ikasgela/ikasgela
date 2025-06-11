<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ExportCompletado extends Mailable
{
    use Queueable, SerializesModels;

    public $url;

    public function __construct(string $url)
    {
        $this->url = $url;
    }

    public function build()
    {
        return $this
            ->subject(__('Export completed'))
            ->markdown('emails.export_completado');
    }
}
