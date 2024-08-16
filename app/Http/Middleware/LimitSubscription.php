<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class LimitSubscription
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
        if($request->user()->lock_subscription)
        {
            return back()->with(['type' => 'error',  'message' => 'Este usuario ja esta processando uma assinatura!']);
        }
        return $next($request);
    }
}
