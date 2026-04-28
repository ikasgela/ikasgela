<?php

namespace App\Observers;

use App\Models\Organization;
use Illuminate\Support\Facades\Cache;

class OrganizationObserver
{
    public function saved(Organization $organization)
    {
        Cache::tags('organization_' . $organization->slug)->flush();
    }

    public function deleted(Organization $organization)
    {
        Cache::tags('organization_' . $organization->slug)->flush();
    }
}
