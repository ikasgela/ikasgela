<?php

namespace App\Observers;

use App\Models\Period;

class PeriodObserver
{
    public function saved(Period $period)
    {
        Period::flushCache();
    }

    public function deleted(Period $period)
    {
        Period::flushCache();
    }
}
