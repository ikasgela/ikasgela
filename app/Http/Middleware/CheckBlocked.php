<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Closure;

class CheckBlocked
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (auth()->user()?->isBlocked()) {
            auth()->logout();
            return redirect()->route('blocked');
        }

        return $next($request);
    }
}
