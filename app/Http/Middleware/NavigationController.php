<?php

namespace App\Http\Middleware;

use Closure;

class NavigationController
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        memorizar_ruta();

        return $next($request);
    }
}
