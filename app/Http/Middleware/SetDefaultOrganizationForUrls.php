<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\URL;

class SetDefaultOrganizationForUrls
{
    public function handle($request, Closure $next)
    {
        URL::defaults(['organization' => $request->route('organization') ?: 'www']);

        return $next($request);
    }
}
