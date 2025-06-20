<?php

namespace App\Traits;

use DateTimeInterface;

trait SerializarFechas
{
    protected function serializeDate(DateTimeInterface $date): string
    {
        return $date->format('Y-m-d H:i:s');
    }
}
