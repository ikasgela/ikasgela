<?php

namespace App\Observers;

use App\Models\Curso;
use App\Models\Group;

class GroupObserver
{
    public function saved(Group $group)
    {
        Group::flushCache();
        Curso::flushCache();
    }

    public function deleted(Group $group)
    {
        Group::flushCache();
        Curso::flushCache();
    }
}
