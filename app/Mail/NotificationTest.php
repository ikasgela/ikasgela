<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\App;

class NotificationTest extends Mailable
{
    use Queueable, SerializesModels;

    public $locale;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($locale = 'en')
    {
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
            ->subject(__('Notification test'))
            ->markdown('emails.notification_test');
    }
}
