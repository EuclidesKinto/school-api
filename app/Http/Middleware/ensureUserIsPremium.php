<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ensureUserIsPremium
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if($request->user()->is_premium())
        {
            return $next($request);
        } else {
            return $next($request);
        }
        
    }
}
