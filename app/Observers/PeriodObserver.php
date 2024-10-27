<?php

namespace App\Observers;

use App\Models\Period;

class PeriodObserver
{
    public function saved(Period $period)
    {
    }

    public function deleted(Period $period)
    {
    }
}
