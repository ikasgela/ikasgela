<?php

namespace App\Observers;

use App\Models\Organization;

class OrganizationObserver
{
    public function saved(Organization $organization)
    {
        Organization::flushCache();
    }

    public function deleted(Organization $organization)
    {
        Organization::flushCache();
    }
}
