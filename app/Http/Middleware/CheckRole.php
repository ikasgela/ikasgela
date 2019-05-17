<?php

namespace App\Http\Middleware;

use Closure;

class CheckRole
{
    public function handle($request, Closure $next, $roles)
    {
        if (!is_null($request->user()) && !$request->user()->hasAnyRole(explode('|', $roles))) {
            abort(403, __("Sorry, you are not authorized to access this page."));
        }

        return $next($request);
    }
}
