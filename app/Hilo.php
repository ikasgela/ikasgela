<?php

namespace App;

use Cmgmyr\Messenger\Models\Thread;

class Hilo extends Thread
{
    protected $fillable = ['subject', 'owner_id', 'noreply'];
}
