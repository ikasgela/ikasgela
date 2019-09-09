<?php

namespace App\Http\Middleware;

use Closure;

class CheckBlocked
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
        if (auth()->check()) {
            if (auth()->user()->blocked_date != null) {
                $message = __('Account blocked, contact your administrator.');
                auth()->logout();
                return redirect()->route('login')->withMessage($message);
            }
        }
        return $next($request);
    }
}
