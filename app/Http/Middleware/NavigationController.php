<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class NavigationController
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
        memorizar_ruta();

        return $next($request);
    }
}
