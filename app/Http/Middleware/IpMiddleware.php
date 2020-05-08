<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Log;

class IpMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {

        $allowedIPS = explode(',', env('DASHBOARD_ALLOWED_IPS'));
        if(!in_array($request->ip(), $allowedIPS)) {
            Log::channel('api_requests')->error('Your IP is not allowed', ['ip' => $request->ip()]);
//            abort(403, 'Unauthorized');
        }

        return $next($request);
    }
}
