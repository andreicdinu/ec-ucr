<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Log;
use Monolog\Logger;

class DashboardTokenMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */

    protected function validateToken($hash)
    {
        $salt = substr($hash, 0, 4);
        $clientHash = substr($hash, 4);

        $date = date("Ymd");
        $localToken = env('DASHBOARD_TOKEN');
        $localHasH = hash("SHA512", $salt . $localToken . $date);

        if($clientHash == $localHasH) {
            return true;
        }

        return false;
    }

    public function handle($request, Closure $next)
    {
        if(!$this->validateToken($request->header('UCR-TOKEN'))) {
            Log::channel('api_requests')->error('Wrong DASHBOARD token provided');
//            abort(403, 'Unauthorized');
        }
        return $next($request);
    }
}
