<?php

namespace App\Observers;

use App\Models\Group;

class GroupObserver
{
    public function saved(Group $group)
    {
    }

    public function deleted(Group $group)
    {
    }
}
