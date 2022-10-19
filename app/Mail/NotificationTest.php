<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NotificationTest extends Mailable
{
    use Queueable, SerializesModels;

    public function build()
    {
        return $this
            ->subject(__('Notification test'))
            ->markdown('emails.notification_test');
    }
}
